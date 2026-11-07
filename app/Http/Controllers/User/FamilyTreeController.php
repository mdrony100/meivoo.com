<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mail\FamilyMemberWelcomeMail;
use App\Models\Family;
use App\Models\FamilyGallery;
use App\Models\FamilyMember;
use App\Models\FamilyRelationship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FamilyTreeController extends Controller
{
    public function familyTree($memberId = null)
    {
        $user = Auth::user();
        $familyId = $user->family_id;
        $familyMemberCount = $familyId ? FamilyMember::where('family_id', $familyId)->count() : 0;
        $displayMemberCount = max(1, $familyMemberCount);
        $showDemoTree = $displayMemberCount <= 1;
        $demoPlaceholderCount = $showDemoTree ? max(0, 10 - $displayMemberCount) : 0;

        $current = $memberId
            ? FamilyMember::findOrFail($memberId)
            : FamilyMember::where('user_id', $user->id)->first();

        if (! $current) {
            $current = new FamilyMember;
            $current->setRelation('user', $user);
            $current->setRelation('spouses', collect());
            $current->setRelation('children', collect());
        }

        $getChildren = function ($parentId) use ($familyId, &$getChildren) {
            $childIds = FamilyRelationship::where('family_id', $familyId)
                ->where('member_id', $parentId)
                ->whereIn('relation_type', ['father', 'mother'])
                ->pluck('related_member_id')
                ->unique();

            return FamilyMember::whereIn('id', $childIds)->get()->map(function ($child) use ($getChildren, $familyId) {
                $child->spouses = $this->getSpouses($child->id, $familyId);
                $child->children = $getChildren($child->id);

                return $child;
            });
        };

        $current->spouses = $this->getSpouses($current->id, $familyId);
        $current->children = $getChildren($current->id);

        $parentRelation = FamilyRelationship::where('family_id', $familyId)
            ->where('related_member_id', $current->id)
            ->whereIn('relation_type', ['father', 'mother'])
            ->first();

        $parentMember = $parentRelation ? FamilyMember::find($parentRelation->member_id) : null;

        if (! $parentMember) {
            $spouses = $this->getSpouses($current->id, $familyId);

            foreach ($spouses as $spouse) {
                $spouseParentRelation = FamilyRelationship::where('family_id', $familyId)
                    ->where('related_member_id', $spouse->id)
                    ->whereIn('relation_type', ['father', 'mother'])
                    ->first();

                if ($spouseParentRelation) {
                    $parentMember = FamilyMember::find($spouseParentRelation->member_id);
                    break;
                }
            }
        }

        return view('user.family-tree', compact(
            'current',
            'parentMember',
            'showDemoTree',
            'familyMemberCount',
            'displayMemberCount',
            'demoPlaceholderCount'
        ));
    }

    private function getSpouses($mId, $familyId)
    {
        $rel = FamilyRelationship::where('family_id', $familyId)
            ->where(function ($q) use ($mId) {
                $q->where('member_id', $mId)->orWhere('related_member_id', $mId);
            })
            ->where('relation_type', 'spouse')->get();

        $ids = $rel->map(fn ($r) => $r->member_id == $mId ? $r->related_member_id : $r->member_id);

        return FamilyMember::whereIn('id', $ids)->get();
    }

    private function femaleHasHusband(FamilyMember $member, ?int $familyId = null): bool
    {
        if ($member->gender !== 'female') {
            return false;
        }

        $familyId = $familyId ?? $member->family_id;

        if (! $familyId || ! $member->id) {
            return false;
        }

        return $this->getSpouses($member->id, $familyId)
            ->contains(fn ($spouse) => $spouse->gender === 'male');
    }

    public function searchMember(Request $request)
    {
        $query = $request->get('term');
        $user = Auth::user();

        $member = FamilyMember::where('family_id', $user->family_id)
            ->whereHas('user', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })->first();

        if ($member) {
            return response()->json(['id' => $member->id]);
        }

        return response()->json(['id' => null]);
    }

    public function members()
    {
        $user = auth()->user();

        $memberExsit = FamilyMember::where('user_id', $user->id)->first();
        $familyExsit = $memberExsit?->family;

        $familyId = $user->family_id
            ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        $members = collect();
        $familyMembers = collect();
        $relationships = collect();
        $family = null;
        $totalMembers = 0;
        $marriedCouples = 0;
        $childrenCount = 0;
        $passedAwayCount = 0;

        if ($familyId) {
            $members = FamilyMember::with([
                'user:id,name,image,email',
                'spouses.user:id,name,image',
            ])
                ->withCount(['children', 'spouses'])
                ->where('family_id', $familyId)
                ->get();

            $family = Family::find($familyId);
            $familyMembers = $members;

            $allRelationships = FamilyRelationship::where('family_id', $familyId)->get();
            $relationships = $allRelationships;

            $totalMembers = $members->count();
            $marriedCouples = (int) ceil($members->where('marital_status', 'married')->count() / 2);

            $childrenCount = $allRelationships
                ->whereIn('relation_type', ['father', 'mother'])
                ->unique('related_member_id')
                ->count();

            $passedAwayCount = $members->where('living_status', 'dead')->count();

            $members = $members->map(function ($member) use ($allRelationships, $familyId) {
                $fatherRelation = $allRelationships->where('related_member_id', $member->id)
                    ->where('relation_type', 'father')->first();
                $motherRelation = $allRelationships->where('related_member_id', $member->id)
                    ->where('relation_type', 'mother')->first();

                $member->has_father = (bool) $fatherRelation;
                $member->has_mother = (bool) $motherRelation;

                $hasSpouse = function ($memberId) use ($allRelationships) {
                    return $allRelationships->where('relation_type', 'spouse')
                        ->where(fn ($q) => $q->member_id == $memberId || $q->related_member_id == $memberId)
                        ->isNotEmpty();
                };

                $fatherHasSpouse = $fatherRelation ? $hasSpouse($fatherRelation->member_id) : false;
                $motherHasSpouse = $motherRelation ? $hasSpouse($motherRelation->member_id) : false;

                $member->can_add_father = ! $member->has_father && ! $motherHasSpouse;
                $member->can_add_mother = ! $member->has_mother && ! $fatherHasSpouse;
                $member->has_spouse = $member->spouses->isNotEmpty();
                $member->has_husband = $this->femaleHasHusband($member, $familyId);
                $member->can_add_spouse = ! ($member->gender === 'female' && $member->has_husband)
                    && $member->spouses->count() < 2;
                $member->can_add_child = in_array($member->gender, ['male', 'female'], true) && $member->has_spouse;

                return $member;
            });
        }

        return view('user.members', compact(
            'members',
            'familyMembers',
            'relationships',
            'family',
            'totalMembers',
            'marriedCouples',
            'childrenCount',
            'passedAwayCount',
            'familyExsit'
        ));
    }

    // public function members()
    // {
    //     $user = auth()->user();

    //     $memberExsit = FamilyMember::where('user_id', $user->id)->first();
    //     $familyExsit = $memberExsit?->family;

    //     $familyId = $user->family_id
    //         ?? FamilyMember::where('user_id', $user->id)->value('family_id');

    //     $members = collect();
    //     $familyMembers = collect();
    //     $relationships = collect();
    //     $family = null;
    //     $totalMembers = 0;
    //     $marriedCouples = 0;
    //     $childrenCount = 0;

    //     if ($familyId) {
    //         $members = FamilyMember::with([
    //             'user:id,name,image,email',
    //             'spouses.user:id,name,image',
    //         ])
    //             ->withCount(['children', 'spouses'])
    //             ->where('family_id', $familyId)
    //             ->get();

    //         $family = Family::find($familyId);
    //         $familyMembers = $members;

    //         $allRelationships = FamilyRelationship::where('family_id', $familyId)->get();
    //         $relationships = $allRelationships;

    //         $totalMembers = $members->count();
    //         $marriedCouples = (int) ceil($members->where('marital_status', 'married')->count() / 2);

    //         $childrenCount = $allRelationships
    //             ->whereIn('relation_type', ['father', 'mother'])
    //             ->unique('related_member_id')
    //             ->count();

    //         $members = $members->map(function ($member) use ($allRelationships, $familyId) {
    //             $fatherRelation = $allRelationships->where('related_member_id', $member->id)
    //                 ->where('relation_type', 'father')->first();
    //             $motherRelation = $allRelationships->where('related_member_id', $member->id)
    //                 ->where('relation_type', 'mother')->first();

    //             $member->has_father = (bool) $fatherRelation;
    //             $member->has_mother = (bool) $motherRelation;

    //             $hasSpouse = function ($memberId) use ($allRelationships) {
    //                 return $allRelationships->where('relation_type', 'spouse')
    //                     ->where(fn ($q) => $q->member_id == $memberId || $q->related_member_id == $memberId)
    //                     ->isNotEmpty();
    //             };

    //             $fatherHasSpouse = $fatherRelation ? $hasSpouse($fatherRelation->member_id) : false;
    //             $motherHasSpouse = $motherRelation ? $hasSpouse($motherRelation->member_id) : false;

    //             $member->can_add_father = ! $member->has_father && ! $motherHasSpouse;
    //             $member->can_add_mother = ! $member->has_mother && ! $fatherHasSpouse;
    //             $member->has_spouse = $member->spouses->isNotEmpty();
    //             $member->has_husband = $this->femaleHasHusband($member, $familyId);
    //             $member->can_add_spouse = ! ($member->gender === 'female' && $member->has_husband)
    //                 && $member->spouses->count() < 2;
    //             $member->can_add_child = in_array($member->gender, ['male', 'female'], true) && $member->has_spouse;

    //             return $member;
    //         });
    //     }

    //     return view('user.members', compact(
    //         'members',
    //         'familyMembers',
    //         'relationships',
    //         'family',
    //         'totalMembers',
    //         'marriedCouples',
    //         'childrenCount',
    //         'familyExsit'
    //     ));
    // }

    public function memberCreate(Request $request)
    {
        $user = auth()->user();
        $member = FamilyMember::where('user_id', $user->id)->first();
        $family = $member?->family;
        $relationType = $request->query('relation_type');
        $relatedMemberId = $request->query('related_member_id');
        $relatedMember = null;
        $prefillGender = old('gender');
        $prefillMaritalStatus = old('marital_status');
        $pageTitle = 'Add Family Member';
        $pageHint = null;

        if ($family && $relationType && $relatedMemberId) {
            $relatedMember = FamilyMember::where('family_id', $family->id)
                ->find($relatedMemberId);

            if ($relatedMember) {
                if ($relationType === 'father') {
                    $prefillGender = 'male';
                    $prefillMaritalStatus = 'married';
                    $pageTitle = 'Add Father';
                    $pageHint = "This member will be saved as {$relatedMember->name}'s father.";
                } elseif ($relationType === 'mother') {
                    $prefillGender = 'female';
                    $prefillMaritalStatus = 'married';
                    $pageTitle = 'Add Mother';
                    $pageHint = "This member will be saved as {$relatedMember->name}'s mother.";
                } elseif ($relationType === 'spouse') {
                    if ($this->femaleHasHusband($relatedMember, $family->id)) {
                        return redirect()
                            ->route('user.members')
                            ->with('error', 'This female member already has a husband, so another spouse cannot be added.');
                    }

                    $pageTitle = 'Add Spouse';
                    $prefillGender = $relatedMember->gender === 'male'
                        ? 'female'
                        : ($relatedMember->gender === 'female' ? 'male' : old('gender'));
                    $prefillMaritalStatus = 'married';
                    $pageHint = "This member will be linked as a spouse of {$relatedMember->name}.";
                } elseif ($relationType === 'child') {
                    $pageTitle = 'Add Child';
                    $pageHint = "This member will be saved as a child of {$relatedMember->name}.";
                }
            }
        }

        return view('user.member_create', compact(
            'family',
            'relationType',
            'relatedMember',
            'prefillGender',
            'prefillMaritalStatus',
            'pageTitle',
            'pageHint'
        ));
    }

    public function memberEdit(FamilyMember $member)
    {
        $user = auth()->user();
        $familyId = $user->family_id
            ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        abort_unless($member->family_id === $familyId, 403);

        $member->load('user');

        $family = $member->family;

        $galleryImages = FamilyGallery::where('member_id', $member->id)
            ->latest()
            ->get();

        return view('user.member_edit', compact('member', 'family', 'galleryImages'));
    }

    public function storeRelation(Request $request, FamilyMember $member)
    {

        $validated = $request->validate([
            'relation_type' => 'required|in:father,mother,spouse,child',
            'related_member_id' => 'required|exists:family_members,id',
        ]);

        try {

            DB::beginTransaction();
            $user = auth()->user();

            $familyId = $user->family_id
                ?? FamilyMember::where('user_id', $user->id)->value('family_id');

            abort_unless($member->family_id === $familyId, 403);

            $relatedMember = FamilyMember::where('family_id', $familyId)
                ->findOrFail($validated['related_member_id']);

            if ($member->id === $relatedMember->id) {
                throw ValidationException::withMessages([
                    'related_member_id' => 'A member cannot be related to themselves.',
                ]);
            }

            $type = $validated['relation_type'];

            /*
            ======================================================
            RULE:
            member_id = SOURCE (parent or main person)
            related_member_id = TARGET
            ======================================================
            */

            // ================= SPOUSE =================
            if ($type === 'spouse') {
                if ($this->femaleHasHusband($member, $familyId)) {
                    throw ValidationException::withMessages([
                        'related_member_id' => 'This female member already has a husband, so another spouse cannot be added.',
                    ]);
                }

                $exists = FamilyRelationship::where('family_id', $familyId)
                    ->where('relation_type', 'spouse')
                    ->where(function ($q) use ($member, $relatedMember) {
                        $q->where(function ($x) use ($member, $relatedMember) {
                            $x->where('member_id', $member->id)
                                ->where('related_member_id', $relatedMember->id);
                        })->orWhere(function ($x) use ($member, $relatedMember) {
                            $x->where('member_id', $relatedMember->id)
                                ->where('related_member_id', $member->id);
                        });
                    })
                    ->exists();

                if (! $exists) {

                    FamilyRelationship::create([
                        'family_id' => $familyId,
                        'member_id' => $member->id,
                        'related_member_id' => $relatedMember->id,
                        'relation_type' => 'spouse',
                    ]);

                    FamilyRelationship::create([
                        'family_id' => $familyId,
                        'member_id' => $relatedMember->id,
                        'related_member_id' => $member->id,
                        'relation_type' => 'spouse',
                    ]);
                }
            }

            // ================= CHILD =================
            elseif ($type === 'child') {

                // parent = selected member
                // child = related member
                $relationType = $member->gender === 'female' ? 'mother' : 'father';

                $exists = FamilyRelationship::where('family_id', $familyId)
                    ->where('member_id', $member->id)
                    ->where('related_member_id', $relatedMember->id)
                    ->where('relation_type', $relationType)
                    ->exists();

                if (! $exists) {

                    FamilyRelationship::create([
                        'family_id' => $familyId,
                        'member_id' => $member->id,            // PARENT
                        'related_member_id' => $relatedMember->id, // CHILD
                        'relation_type' => $relationType,
                    ]);
                }
            }

            // ================= FATHER / MOTHER =================
            else {

                $exists = FamilyRelationship::where('family_id', $familyId)
                    ->where('member_id', $relatedMember->id)
                    ->where('related_member_id', $member->id)
                    ->where('relation_type', $type)
                    ->exists();

                if (! $exists) {

                    FamilyRelationship::create([
                        'family_id' => $familyId,
                        'member_id' => $relatedMember->id,
                        'related_member_id' => $member->id,
                        'relation_type' => $type,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Relation added successfully');
        } catch (ValidationException $e) {
            DB::rollBack();

            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('open_relation_modal', $member->id);
        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Relation Error: '.$e->getMessage(), [
                'member_id' => $member->id,
                'family_id' => $member->family_id,
            ]);

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function updateRelation(Request $request, FamilyRelationship $relationship)
    {
        $validated = $request->validate([
            'relation_type' => 'required|in:father,mother,spouse,child',
            'related_member_id' => 'required|exists:family_members,id',
        ]);

        try {
            $user = auth()->user();
            $familyId = $user->family_id
                ?? FamilyMember::where('user_id', $user->id)->value('family_id');

            abort_unless($relationship->family_id === $familyId, 403);

            DB::beginTransaction();

            $sourceMember = FamilyMember::where('family_id', $familyId)->findOrFail($relationship->member_id);
            $relatedMember = FamilyMember::where('family_id', $familyId)->findOrFail($validated['related_member_id']);

            if ($sourceMember->id === $relatedMember->id) {
                DB::rollBack();

                return back()
                    ->withInput()
                    ->with('error', 'A member cannot be related to themselves.');
            }

            if ($relationship->relation_type === 'spouse') {
                FamilyRelationship::where('family_id', $familyId)
                    ->where('relation_type', 'spouse')
                    ->where(function ($query) use ($sourceMember, $relationship) {
                        $query->where(function ($q) use ($sourceMember, $relationship) {
                            $q->where('member_id', $sourceMember->id)
                                ->where('related_member_id', $relationship->related_member_id);
                        })->orWhere(function ($q) use ($sourceMember, $relationship) {
                            $q->where('member_id', $relationship->related_member_id)
                                ->where('related_member_id', $sourceMember->id);
                        });
                    })
                    ->delete();
            } else {
                $relationship->delete();
            }

            if ($validated['relation_type'] === 'spouse') {
                if ($relationship->relation_type !== 'spouse' && $this->femaleHasHusband($sourceMember, $familyId)) {
                    throw ValidationException::withMessages([
                        'related_member_id' => 'This female member already has a husband, so another spouse cannot be added.',
                    ]);
                }

                $exists = FamilyRelationship::where('family_id', $familyId)
                    ->where('relation_type', 'spouse')
                    ->where(function ($query) use ($sourceMember, $relatedMember) {
                        $query->where(function ($q) use ($sourceMember, $relatedMember) {
                            $q->where('member_id', $sourceMember->id)
                                ->where('related_member_id', $relatedMember->id);
                        })->orWhere(function ($q) use ($sourceMember, $relatedMember) {
                            $q->where('member_id', $relatedMember->id)
                                ->where('related_member_id', $sourceMember->id);
                        });
                    })
                    ->exists();

                if (! $exists) {
                    FamilyRelationship::create([
                        'family_id' => $familyId,
                        'member_id' => $sourceMember->id,
                        'related_member_id' => $relatedMember->id,
                        'relation_type' => 'spouse',
                    ]);

                    FamilyRelationship::create([
                        'family_id' => $familyId,
                        'member_id' => $relatedMember->id,
                        'related_member_id' => $sourceMember->id,
                        'relation_type' => 'spouse',
                    ]);
                }
            } elseif ($validated['relation_type'] === 'child') {
                $relationType = $sourceMember->gender === 'female' ? 'mother' : 'father';

                FamilyRelationship::create([
                    'family_id' => $familyId,
                    'member_id' => $sourceMember->id,
                    'related_member_id' => $relatedMember->id,
                    'relation_type' => $relationType,
                ]);
            } else {
                FamilyRelationship::create([
                    'family_id' => $familyId,
                    'member_id' => $sourceMember->id,
                    'related_member_id' => $relatedMember->id,
                    'relation_type' => $validated['relation_type'],
                ]);
            }

            DB::commit();

            return back()->with('success', 'Relation updated successfully');
        } catch (ValidationException $e) {
            DB::rollBack();

            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('open_edit_relation', $relationship->id);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Relation Update Error: '.$e->getMessage(), [
                'relationship_id' => $relationship->id,
                'family_id' => $relationship->family_id,
            ]);

            return back()->with('error', 'Something went wrong!');
        }
    }

    public function destroyRelation(FamilyRelationship $relationship)
    {
        try {
            $user = auth()->user();
            $familyId = $user->family_id
                ?? FamilyMember::where('user_id', $user->id)->value('family_id');

            abort_unless($relationship->family_id === $familyId, 403);

            DB::beginTransaction();

            if ($relationship->relation_type === 'spouse') {
                FamilyRelationship::where('family_id', $familyId)
                    ->where('relation_type', 'spouse')
                    ->where(function ($query) use ($relationship) {
                        $query->where(function ($q) use ($relationship) {
                            $q->where('member_id', $relationship->member_id)
                                ->where('related_member_id', $relationship->related_member_id);
                        })->orWhere(function ($q) use ($relationship) {
                            $q->where('member_id', $relationship->related_member_id)
                                ->where('related_member_id', $relationship->member_id);
                        });
                    })
                    ->delete();
            } else {
                $relationship->delete();
            }

            DB::commit();

            return back()->with('success', 'Relation deleted successfully');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Relation Delete Error: '.$e->getMessage(), [
                'relationship_id' => $relationship->id,
                'family_id' => $relationship->family_id,
            ]);

            return back()->with('error', 'Could not delete relation');
        }
    }

    public function updateMember(Request $request, FamilyMember $member)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$member->user_id,
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female',
            'dob' => 'nullable|date',
            'blood_group' => 'nullable|string|max:5',
            'profession' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:255',
            'living_status' => 'nullable|in:alive,dead',
            'date_of_death' => 'nullable|date|required_if:living_status,dead',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'marriage_date' => 'nullable|date',
            'divorced_date' => 'nullable|date',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:4096',
        ]);

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $familyId = $user->family_id
                ?? FamilyMember::where('user_id', $user->id)->value('family_id');

            abort_unless($member->family_id === $familyId, 403);

            $profilePath = null;

            if ($request->hasFile('profile_image')) {
                $profilePath = uploadGeneralImage($request->file('profile_image'), 'members/profile');
            }

            $member->phone = $request->phone;
            $member->gender = $request->gender;
            $member->birth_date = $request->dob;
            $member->profession = $request->profession;
            $member->living_status = $request->living_status === 'dead' ? 'passed' : 'alive';
            $member->date_of_death = $request->living_status === 'dead' ? $request->date_of_death : null;
            $member->phone = $request->phone;
            $member->education = $request->education;
            $member->living_status = $request->living_status;
            $member->marital_status = $request->marital_status;
            $member->address = $request->address;
            $member->bio = $request->bio;
            $member->blood_group = $request->blood_group;
            $member->marriage_date = $request->marriage_date;
            $member->divorced_date = $request->divorced_date;
            $member->save();

            if ($member->user) {
                $member->user->name = $request->name;
                $member->user->email = $request->email;

                if ($profilePath) {
                    $member->user->image = $profilePath;
                }

                if ($request->filled('password')) {
                    $member->user->password = bcrypt($request->password);
                }

                $member->user->save();
            } elseif ($request->email) {
                $linkedUser = User::where('email', $request->email)->first();

                if ($linkedUser) {
                    $member->user_id = $linkedUser->id;
                    if ($profilePath) {
                        $linkedUser->image = $profilePath;
                        $linkedUser->save();
                    }
                    $member->save();
                } else {
                    // $plainPassword = $request->password ?? '12345678';

                    $linkedUser = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        // 'password' => bcrypt($plainPassword),
                    ]);

                    $member->user_id = $linkedUser->id;
                    if ($profilePath) {
                        $linkedUser->image = $profilePath;
                        $linkedUser->save();
                    }
                    $member->save();
                }
            }

            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $image) {
                    $path = uploadGeneralImage($image, 'members/gallery');

                    FamilyGallery::create([
                        'family_id' => $member->family_id,
                        'member_id' => $member->id,
                        'created_by' => $user->id,
                        'image' => $path,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('user.members')->with('success', 'Member updated successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();

            // dd($e);
            Log::error('Member Update Error: '.$e->getMessage(), [
                'member_id' => $member->id,
                'family_id' => $member->family_id,
            ]);

            return redirect()->back()
                ->withInput($request->except(['profile_image']))
                ->with('error', 'Something went wrong!');
        }
    }

    public function destroyGalleryImage(FamilyGallery $gallery)
    {
        $user = auth()->user();
        $familyId = $user->family_id
            ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        if ($gallery->family_id !== $familyId) {
            abort(403);
        }

        try {
            $galleryPath = $gallery->image ?? $gallery->image_path ?? null;

            if ($galleryPath) {
                $galleryFile = public_path($galleryPath);

                if (File::exists($galleryFile)) {
                    File::delete($galleryFile);
                }
            }

            $gallery->delete();

            return redirect()->back()->with('success', 'Gallery image removed successfully');
        } catch (\Throwable $e) {
            Log::error('Gallery Delete Error: '.$e->getMessage(), [
                'gallery_id' => $gallery->id,
                'family_id' => $gallery->family_id,
            ]);

            return redirect()->back()->with('error', 'Could not remove gallery image');
        }
    }

    public function storeFamily(Request $request)
    {
        $user = auth()->user();

        $family = Family::create([
            'name' => $request->name,
            'owner_user_id' => $user->id,
        ]);

        FamilyMember::create([
            'family_id' => $family->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'is_self' => 1,
            'created_by' => $user->id,
        ]);

        return redirect()->back()->with('success', 'Family created successfully');
    }

    public function storeMember(Request $request)
    {
        $user = auth()->user();
        $memberCheck = FamilyMember::where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'gender' => 'required|in:male,female',
            'dob' => 'nullable|date',
            'blood_group' => 'nullable|string|max:5',
            'profession' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:255',
            'living_status' => 'nullable|in:alive,dead',
            'date_of_death' => 'nullable|date|required_if:living_status,dead',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'marriage_date' => 'nullable|date',
            'divorced_date' => 'nullable|date',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'profile_image' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:4096',
        ]);

        try {

            DB::beginTransaction();

            // ================= FAMILY CHECK =================
            $family = $memberCheck?->family;

            if (! $family) {

                $family = Family::create([
                    'name' => $user->name.' Family',
                    'owner_user_id' => $user->id,
                ]);

                FamilyMember::create([
                    'family_id' => $family->id,
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'is_self' => 1,
                    'created_by' => $user->id,
                ]);
            }

            $relationType = $request->input('relation_type');
            $relatedMemberId = $request->input('related_member_id');
            $contextMember = null;

            if ($relationType && $relatedMemberId) {
                $contextMember = FamilyMember::where('family_id', $family->id)
                    ->find($relatedMemberId);
            }

            $gender = $request->gender;
            $maritalStatus = $request->marital_status;

            if ($relationType === 'father') {
                $gender = 'male';
                $maritalStatus = 'married';
            } elseif ($relationType === 'mother') {
                $gender = 'female';
                $maritalStatus = 'married';
            } elseif ($relationType === 'spouse' && $contextMember) {
                $gender = $contextMember->gender === 'male'
                    ? 'female'
                    : ($contextMember->gender === 'female' ? 'male' : $gender);
                $maritalStatus = 'married';
            }

            // ================= USER CREATE / LINK =================
            $linkedUserId = null;

            if ($request->email) {
                $existingUser = User::where('email', $request->email)->first();

                if ($existingUser) {
                    if ($existingUser->family_id != $family->id) {
                        throw ValidationException::withMessages([
                            'email' => 'Email already exists in another family.',
                        ]);
                    }

                    $linkedUserId = $existingUser->id;
                } else {
                    $plainPassword = $request->password ?? '12345678';

                    $newUser = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'family_id' => $family->id,
                        'password' => bcrypt($plainPassword),
                    ]);

                    $linkedUserId = $newUser->id;

                    try {
                        Mail::to($newUser->email)->send(
                            new FamilyMemberWelcomeMail($newUser, $plainPassword, $family)
                        );
                    } catch (\Throwable $e) {
                        Log::warning('Family welcome mail failed: '.$e->getMessage(), [
                            'user_id' => $newUser->id,
                            'email' => $newUser->email,
                            'family_id' => $family->id,
                        ]);
                    }
                }
            }

            // ================= PROFILE IMAGE =================
            $profilePath = null;

            if ($request->hasFile('profile_image')) {
                $profilePath = uploadGeneralImage($request->file('profile_image'), 'members/profile');
            }

            // ================= CREATE MEMBER =================
            $member = FamilyMember::create([
                'family_id' => $family->id,
                'user_id' => $linkedUserId,
                'phone' => $request->phone,
                'gender' => $gender,
                'dob' => $request->dob,
                'profession' => $request->profession,
                'education' => $request->education,
                'living_status' => $request->living_status,
                'birth_date' => $request->dob,
                'marital_status' => $maritalStatus,
                'address' => $request->address,
                'bio' => $request->bio,
                'blood_group' => $request->blood_group,
                'date_of_death' => $request->date_of_death,
                'marriage_date' => $request->marriage_date,
                'divorced_date' => $request->divorced_date,
                'created_by' => $user->id,
            ]);

            $memberUser = User::find($linkedUserId);
            $memberUser->image = $profilePath;
            $memberUser->save();

            // ================= GALLERY IMAGES =================
            if ($request->hasFile('gallery')) {

                foreach ($request->file('gallery') as $image) {
                    $path = uploadGeneralImage($image, 'members/gallery');

                    FamilyGallery::create([
                        'family_id' => $family->id,
                        'member_id' => $member->id,
                        'created_by' => $user->id,
                        'image' => $path,
                    ]);
                }
            }

            if ($relationType && $contextMember) {
                if ($relationType === 'spouse') {
                    if ($this->femaleHasHusband($contextMember, $family->id)) {
                        throw ValidationException::withMessages([
                            'related_member_id' => 'This female member already has a husband, so another spouse cannot be added.',
                        ]);
                    }

                    $exists = FamilyRelationship::where('family_id', $family->id)
                        ->where('relation_type', 'spouse')
                        ->where(function ($query) use ($member, $contextMember) {
                            $query->where(function ($q) use ($member, $contextMember) {
                                $q->where('member_id', $member->id)
                                    ->where('related_member_id', $contextMember->id);
                            })->orWhere(function ($q) use ($member, $contextMember) {
                                $q->where('member_id', $contextMember->id)
                                    ->where('related_member_id', $member->id);
                            });
                        })
                        ->exists();

                    if (! $exists) {
                        FamilyRelationship::create([
                            'family_id' => $family->id,
                            'member_id' => $member->id,
                            'related_member_id' => $contextMember->id,
                            'relation_type' => 'spouse',
                        ]);

                        FamilyRelationship::create([
                            'family_id' => $family->id,
                            'member_id' => $contextMember->id,
                            'related_member_id' => $member->id,
                            'relation_type' => 'spouse',
                        ]);
                    }

                    $member->marital_status = 'married';
                    $member->save();

                    if ($contextMember->marital_status !== 'married') {
                        $contextMember->marital_status = 'married';
                        $contextMember->save();
                    }
                } elseif ($relationType === 'father' || $relationType === 'mother') {
                    $parentRelationType = $relationType === 'father' ? 'mother' : 'father';

                    $exists = FamilyRelationship::where('family_id', $family->id)
                        ->where('member_id', $member->id)
                        ->where('related_member_id', $contextMember->id)
                        ->where('relation_type', $relationType)
                        ->exists();

                    if (! $exists) {
                        FamilyRelationship::create([
                            'family_id' => $family->id,
                            'member_id' => $member->id,
                            'related_member_id' => $contextMember->id,
                            'relation_type' => $relationType,
                        ]);
                    }

                    $existingParentRelationship = FamilyRelationship::with('member')
                        ->where('family_id', $family->id)
                        ->where('related_member_id', $contextMember->id)
                        ->where('relation_type', $parentRelationType)
                        ->first();

                    if ($existingParentRelationship?->member) {
                        $existingParent = $existingParentRelationship->member;

                        $spouseExists = FamilyRelationship::where('family_id', $family->id)
                            ->where('relation_type', 'spouse')
                            ->where(function ($q) use ($member, $existingParent) {
                                $q->where(function ($x) use ($member, $existingParent) {
                                    $x->where('member_id', $member->id)
                                        ->where('related_member_id', $existingParent->id);
                                })->orWhere(function ($x) use ($member, $existingParent) {
                                    $x->where('member_id', $existingParent->id)
                                        ->where('related_member_id', $member->id);
                                });
                            })
                            ->exists();

                        if (! $spouseExists) {
                            FamilyRelationship::create([
                                'family_id' => $family->id,
                                'member_id' => $member->id,
                                'related_member_id' => $existingParent->id,
                                'relation_type' => 'spouse',
                            ]);

                            FamilyRelationship::create([
                                'family_id' => $family->id,
                                'member_id' => $existingParent->id,
                                'related_member_id' => $member->id,
                                'relation_type' => 'spouse',
                            ]);
                        }

                        $member->marital_status = 'married';
                        $member->save();

                        if ($existingParent->marital_status !== 'married') {
                            $existingParent->marital_status = 'married';
                            $existingParent->save();
                        }
                    }
                } elseif ($relationType === 'child') {
                    if (! in_array($contextMember->gender, ['male', 'female'], true)) {
                        throw ValidationException::withMessages([
                            'related_member_id' => 'Selected member gender must be male or female to add a child.',
                        ]);
                    }

                    $spouse = $contextMember->spouses->first();
                    if (! $spouse) {
                        throw ValidationException::withMessages([
                            'related_member_id' => 'Please add spouse first before adding a child.',
                        ]);
                    }

                    $hasParent = FamilyRelationship::where('related_member_id', $contextMember->id)
                        ->whereIn('relation_type', ['father', 'mother'])
                        ->exists();

                    $spouseHasParent = FamilyRelationship::where('related_member_id', $spouse->id)
                        ->whereIn('relation_type', ['father', 'mother'])
                        ->exists();

                    $actualParent = $contextMember;
                    if (! $hasParent && $spouseHasParent) {
                        $actualParent = $spouse;
                    }

                    FamilyRelationship::updateOrCreate([
                        'family_id' => $family->id,
                        'member_id' => $actualParent->id,
                        'related_member_id' => $member->id,
                    ], [
                        'relation_type' => ($actualParent->gender == 'male' ? 'father' : 'mother'),
                    ]);

                    FamilyRelationship::updateOrCreate([
                        'family_id' => $family->id,
                        'member_id' => $spouse->id,
                        'related_member_id' => $member->id,
                    ], [
                        'relation_type' => ($spouse->gender == 'male' ? 'father' : 'mother'),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('user.members')->with('success', 'Member created successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            // dd($e);
            Log::error('Member Create Error: '.$e->getMessage());

            return redirect()->back()
                ->withInput($request->except(['profile_image', 'gallery']))
                ->with('error', 'Something went wrong!');
        }
    }
    // public function storeMember(Request $request)
    // {
    //     try {

    //         DB::beginTransaction();

    //         $user = auth()->user();

    //         // ================= VALIDATION =================
    //         $request->validate([
    //             'name' => 'required|string|max:255',
    //             'email' => 'required|email',
    //             'password' => 'required|min:6',
    //             'phone' => 'nullable|string|max:20',
    //             'gender' => 'required|in:male,female',
    //             'dob' => 'nullable|date',
    //             'profile_image' => 'nullable|image|max:2048',
    //             'gallery.*' => 'nullable|image|max:4096',
    //         ]);

    //         // ================= FAMILY CHECK =================
    //         $memberCheck = FamilyMember::where('user_id', $user->id)->first();
    //         $family = $memberCheck?->family;

    //         if (! $family) {

    //             $family = Family::create([
    //                 'name' => $user->name.' Family',
    //                 'owner_user_id' => $user->id,
    //             ]);

    //             FamilyMember::create([
    //                 'family_id' => $family->id,
    //                 'user_id' => $user->id,
    //                 'name' => $user->name,
    //                 'is_self' => 1,
    //                 'created_by' => $user->id,
    //             ]);
    //         }

    //         $relationType = $request->input('relation_type');
    //         $relatedMemberId = $request->input('related_member_id');
    //         $contextMember = null;

    //         if ($relationType && $relatedMemberId) {
    //             $contextMember = FamilyMember::where('family_id', $family->id)
    //                 ->find($relatedMemberId);
    //         }

    //         $gender = $request->gender;
    //         $maritalStatus = $request->marital_status;

    //         if ($relationType === 'father') {
    //             $gender = 'male';
    //             $maritalStatus = 'married';
    //         } elseif ($relationType === 'mother') {
    //             $gender = 'female';
    //             $maritalStatus = 'married';
    //         } elseif ($relationType === 'spouse' && $contextMember) {
    //             $gender = $contextMember->gender === 'male'
    //                 ? 'female'
    //                 : ($contextMember->gender === 'female' ? 'male' : $gender);
    //             $maritalStatus = 'married';
    //         }

    //         // ================= USER CREATE / LINK =================
    //         $linkedUserId = null;

    //         if ($request->email) {

    //             $existingUser = User::where('email', $request->email)->first();

    //             if ($existingUser) {
    //                 $linkedUserId = $existingUser->id;
    //             } else {
    //                 $plainPassword = $request->password ?? '12345678';

    //                 $newUser = User::create([
    //                     'name' => $request->name,
    //                     'email' => $request->email,
    //                     'family_id' => $family->id,
    //                     'password' => bcrypt($plainPassword),
    //                 ]);

    //                 $linkedUserId = $newUser->id;

    //                 try {
    //                     Mail::to($newUser->email)->send(
    //                         new FamilyMemberWelcomeMail($newUser, $plainPassword, $family)
    //                     );
    //                 } catch (\Throwable $e) {
    //                     Log::warning('Family welcome mail failed: '.$e->getMessage(), [
    //                         'user_id' => $newUser->id,
    //                         'email' => $newUser->email,
    //                         'family_id' => $family->id,
    //                     ]);
    //                 }
    //             }
    //         }

    //         // ================= PROFILE IMAGE =================
    //         $profilePath = null;

    //         if ($request->hasFile('profile_image')) {
    //             $profilePath = uploadGeneralImage($request->file('profile_image'), 'members/profile');
    //         }

    //         // ================= CREATE MEMBER =================
    //         $member = FamilyMember::create([
    //             'family_id' => $family->id,
    //             'user_id' => $linkedUserId,
    //             'phone' => $request->phone,
    //             'gender' => $gender,
    //             'dob' => $request->dob,
    //             'profession' => $request->profession,
    //             'education' => $request->education,
    //             'living_status' => $request->living_status,
    //             'birth_date' => $request->dob,
    //             'marital_status' => $maritalStatus,
    //             'address' => $request->address,
    //             'bio' => $request->bio,
    //             'blood_group' => $request->blood_group,
    //             'date_of_death' => $request->date_of_death,
    //             'marriage_date' => $request->marriage_date,
    //             'divorced_date' => $request->divorced_date,
    //             'created_by' => $user->id,
    //         ]);

    //         $memberUser = User::find($linkedUserId);
    //         $memberUser->image = $profilePath;
    //         $memberUser->save();

    //         // ================= GALLERY IMAGES =================
    //         if ($request->hasFile('gallery')) {

    //             foreach ($request->file('gallery') as $image) {
    //                 $path = uploadGeneralImage($image, 'members/gallery');

    //                 FamilyGallery::create([
    //                     'family_id' => $family->id,
    //                     'member_id' => $member->id,
    //                     'created_by' => $user->id,
    //                     'image' => $path,
    //                 ]);
    //             }
    //         }

    //         if ($relationType && $contextMember) {
    //             if ($relationType === 'spouse') {
    //                 $exists = FamilyRelationship::where('family_id', $family->id)
    //                     ->where('relation_type', 'spouse')
    //                     ->where(function ($query) use ($member, $contextMember) {
    //                         $query->where(function ($q) use ($member, $contextMember) {
    //                             $q->where('member_id', $member->id)
    //                                 ->where('related_member_id', $contextMember->id);
    //                         })->orWhere(function ($q) use ($member, $contextMember) {
    //                             $q->where('member_id', $contextMember->id)
    //                                 ->where('related_member_id', $member->id);
    //                         });
    //                     })
    //                     ->exists();

    //                 if (! $exists) {
    //                     FamilyRelationship::create([
    //                         'family_id' => $family->id,
    //                         'member_id' => $member->id,
    //                         'related_member_id' => $contextMember->id,
    //                         'relation_type' => 'spouse',
    //                     ]);

    //                     FamilyRelationship::create([
    //                         'family_id' => $family->id,
    //                         'member_id' => $contextMember->id,
    //                         'related_member_id' => $member->id,
    //                         'relation_type' => 'spouse',
    //                     ]);
    //                 }

    //                 $member->marital_status = 'married';
    //                 $member->save();

    //                 if ($contextMember->marital_status !== 'married') {
    //                     $contextMember->marital_status = 'married';
    //                     $contextMember->save();
    //                 }
    //             } elseif ($relationType === 'father' || $relationType === 'mother') {
    //                 $parentRelationType = $relationType === 'father' ? 'mother' : 'father';

    //                 $exists = FamilyRelationship::where('family_id', $family->id)
    //                     ->where('member_id', $member->id)
    //                     ->where('related_member_id', $contextMember->id)
    //                     ->where('relation_type', $relationType)
    //                     ->exists();

    //                 if (! $exists) {
    //                     FamilyRelationship::create([
    //                         'family_id' => $family->id,
    //                         'member_id' => $member->id,
    //                         'related_member_id' => $contextMember->id,
    //                         'relation_type' => $relationType,
    //                     ]);
    //                 }

    //                 $existingParentRelationship = FamilyRelationship::with('member')
    //                     ->where('family_id', $family->id)
    //                     ->where('related_member_id', $contextMember->id)
    //                     ->where('relation_type', $parentRelationType)
    //                     ->first();

    //                 if ($existingParentRelationship?->member) {
    //                     $existingParent = $existingParentRelationship->member;

    //                     $spouseExists = FamilyRelationship::where('family_id', $family->id)
    //                         ->where('relation_type', 'spouse')
    //                         ->where(function ($q) use ($member, $existingParent) {
    //                             $q->where(function ($x) use ($member, $existingParent) {
    //                                 $x->where('member_id', $member->id)
    //                                     ->where('related_member_id', $existingParent->id);
    //                             })->orWhere(function ($x) use ($member, $existingParent) {
    //                                 $x->where('member_id', $existingParent->id)
    //                                     ->where('related_member_id', $member->id);
    //                             });
    //                         })
    //                         ->exists();

    //                     if (! $spouseExists) {
    //                         FamilyRelationship::create([
    //                             'family_id' => $family->id,
    //                             'member_id' => $member->id,
    //                             'related_member_id' => $existingParent->id,
    //                             'relation_type' => 'spouse',
    //                         ]);

    //                         FamilyRelationship::create([
    //                             'family_id' => $family->id,
    //                             'member_id' => $existingParent->id,
    //                             'related_member_id' => $member->id,
    //                             'relation_type' => 'spouse',
    //                         ]);
    //                     }

    //                     $member->marital_status = 'married';
    //                     $member->save();

    //                     if ($existingParent->marital_status !== 'married') {
    //                         $existingParent->marital_status = 'married';
    //                         $existingParent->save();
    //                     }
    //                 }
    //             } elseif ($relationType === 'child') {
    //                 if (! in_array($contextMember->gender, ['male', 'female'], true)) {
    //                     throw ValidationException::withMessages([
    //                         'related_member_id' => 'Selected member gender must be male or female to add a child.',
    //                     ]);
    //                 }

    //                 $spouse = $contextMember->spouses->first();
    //                 if (! $spouse) {
    //                     throw ValidationException::withMessages([
    //                         'related_member_id' => 'Please add spouse first before adding a child.',
    //                     ]);
    //                 }

    //                 $hasParent = FamilyRelationship::where('related_member_id', $contextMember->id)
    //                     ->whereIn('relation_type', ['father', 'mother'])
    //                     ->exists();

    //                 $spouseHasParent = FamilyRelationship::where('related_member_id', $spouse->id)
    //                     ->whereIn('relation_type', ['father', 'mother'])
    //                     ->exists();

    //                 $actualParent = $contextMember;
    //                 if (! $hasParent && $spouseHasParent) {
    //                     $actualParent = $spouse;
    //                 }

    //                 FamilyRelationship::updateOrCreate([
    //                     'family_id' => $family->id,
    //                     'member_id' => $actualParent->id,
    //                     'related_member_id' => $member->id,
    //                 ], [
    //                     'relation_type' => ($actualParent->gender == 'male' ? 'father' : 'mother'),
    //                 ]);

    //                 FamilyRelationship::updateOrCreate([
    //                     'family_id' => $family->id,
    //                     'member_id' => $spouse->id,
    //                     'related_member_id' => $member->id,
    //                 ], [
    //                     'relation_type' => ($spouse->gender == 'male' ? 'father' : 'mother'),
    //                 ]);
    //             }
    //         }

    //         DB::commit();

    //         return redirect()->route('user.members')->with('success', 'Member created successfully');
    //     } catch (ValidationException $e) {
    //         DB::rollBack();
    //         throw $e;
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         // dd($e);
    //         Log::error('Member Create Error: '.$e->getMessage());

    //         return redirect()->back()
    //             ->withInput($request->except(['profile_image', 'gallery']))
    //             ->with('error', 'Something went wrong!');
    //     }
    // }

    public function memberFullProfile()
    {
        return view('user.member-full-profile');
    }

    public function getMemberInfo($id)
    {
        $user = Auth::user();

        $member = FamilyMember::where('family_id', $user->family_id)
            ->with('user')
            ->findOrFail($id);

        return view('user.partials.member-info', compact('member'));
    }
}
