<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\FamilyMemberWelcomeMail;
use App\Models\Family;
use App\Models\FamilyGallery;
use App\Models\FamilyMember;
use App\Models\FamilyRelationship;
use App\Models\FeedPost;
use App\Models\PostMedia;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class FamilyTreeController extends Controller
{
    public function familyTree($memberId = null)
    {
        $user = Auth::user();
        $familyId = $user->family_id;

        $current = $memberId
            ? FamilyMember::with('user:id,name,image')->findOrFail($memberId)
            : FamilyMember::with('user:id,name,image')->where('user_id', $user->id)->first();

        if (! $current) {
            $current = new FamilyMember();
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

            return FamilyMember::with('user:id,name,image')
                ->whereIn('id', $childIds)
                ->get()
                ->map(function ($child) use ($getChildren, $familyId) {
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

        $parentMember = $parentRelation ? FamilyMember::with('user:id,name,image')->find($parentRelation->member_id) : null;

        if (! $parentMember) {
            $spouses = $this->getSpouses($current->id, $familyId);

            foreach ($spouses as $spouse) {
                $spouseParentRelation = FamilyRelationship::where('family_id', $familyId)
                    ->where('related_member_id', $spouse->id)
                    ->whereIn('relation_type', ['father', 'mother'])
                    ->first();

                if ($spouseParentRelation) {
                    $parentMember = FamilyMember::with('user:id,name,image')->find($spouseParentRelation->member_id);
                    break;
                }
            }
        }

        return ApiResponse::success('Family tree retrieved successfully.', [
            'current' => $current,
            'parent_member' => $parentMember,
        ]);
    }

    public function members()
    {
        $user = Auth::user();
        $familyId = $user->family_id;

        $members = collect();

        if ($familyId) {
            $members = FamilyMember::with('user:id,name,image')
                ->where('family_id', $familyId)
                ->orderBy('id')
                ->get();
        }

        return ApiResponse::success('Family members retrieved successfully.', [
            'members' => $members,
        ]);
    }

    // public function memberCreate(Request $request)
    // {
    //     $user = Auth::user();
    //     $family = $this->familyForUser($user);
    //     $relationType = $request->query('relation_type');
    //     $relatedMemberId = $request->query('related_member_id');
    //     $relatedMember = null;
    //     $prefillGender = $request->query('gender');
    //     $prefillMaritalStatus = $request->query('marital_status');
    //     $pageTitle = 'Add Family Member';
    //     $pageHint = null;

    //     if ($family && $relationType && $relatedMemberId) {
    //         $relatedMember = FamilyMember::where('family_id', $family->id)
    //             ->with('user:id,name,image')
    //             ->find($relatedMemberId);

    //         if ($relatedMember) {
    //             if ($relationType === 'father') {
    //                 $prefillGender = 'male';
    //                 $prefillMaritalStatus = 'married';
    //                 $pageTitle = 'Add Father';
    //                 $pageHint = "This member will be saved as {$relatedMember->name}'s father.";
    //             } elseif ($relationType === 'mother') {
    //                 $prefillGender = 'female';
    //                 $prefillMaritalStatus = 'married';
    //                 $pageTitle = 'Add Mother';
    //                 $pageHint = "This member will be saved as {$relatedMember->name}'s mother.";
    //             } elseif ($relationType === 'spouse') {
    //                 if ($this->femaleHasHusband($relatedMember, $family->id)) {
    //                     return ApiResponse::error(
    //                         'This female member already has a husband, so another spouse cannot be added.',
    //                         null,
    //                         422
    //                     );
    //                 }

    //                 $pageTitle = 'Add Spouse';
    //                 $prefillGender = $relatedMember->gender === 'male'
    //                     ? 'female'
    //                     : ($relatedMember->gender === 'female' ? 'male' : $prefillGender);
    //                 $prefillMaritalStatus = 'married';
    //                 $pageHint = "This member will be linked as a spouse of {$relatedMember->name}.";
    //             } elseif ($relationType === 'child') {
    //                 $pageTitle = 'Add Child';
    //                 $pageHint = "This member will be saved as a child of {$relatedMember->name}.";
    //             }
    //         }
    //     }

    //     return ApiResponse::success('Member create data retrieved successfully.', [
    //         'family' => $family,
    //         'relation_type' => $relationType,
    //         'related_member_id' => $relatedMemberId,
    //         'related_member' => $relatedMember,
    //         'prefill_gender' => $prefillGender,
    //         'prefill_marital_status' => $prefillMaritalStatus,
    //         'page_title' => $pageTitle,
    //         'page_hint' => $pageHint,
    //     ]);
    // }

    public function memberEdit(FamilyMember $member)
    {
        $user = Auth::user();
        $familyId = $user->family_id
            ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        abort_unless($member->family_id === $familyId, 403);

        $member->load('user:id,name,image');

        return ApiResponse::success('Member edit data retrieved successfully.', [
            'member' => $member,
            'family' => $member->family,
            'gallery_images' => FamilyGallery::where('member_id', $member->id)
                ->latest()
                ->get(),
        ]);
    }

    public function storeMember(Request $request)
    {
        $user = Auth::user();
        $memberCheck = FamilyMember::where('user_id', $user->id)->first();

        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email',
            'password'          => 'nullable|min:6',
            'phone'             => 'nullable|string|max:20',
            'gender'            => 'required|in:male,female',
            'dob'               => 'nullable|date',
            'blood_group'       => 'nullable|string|max:5',
            'profession'        => 'nullable|string|max:255',
            'education'         => 'nullable|string|max:255',
            'living_status'     => 'nullable|in:alive,dead',
            'date_of_death'     => 'nullable|date|required_if:living_status,dead',
            'marital_status'    => 'nullable|in:single,married,divorced,widowed',
            'marriage_date'     => 'nullable|date',
            'divorced_date'     => 'nullable|date',
            'address'           => 'nullable|string',
            'bio'               => 'nullable|string',
            'profile_image'     => 'nullable|image|max:2048',
            'gallery.*'         => 'nullable|image|max:4096',
        ]);

        try {
            DB::beginTransaction();

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
                    ->with('spouses')
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
                        'name'      => $request->name,
                        'email'     => $request->email,
                        'family_id' => $family->id,
                        'password'  => bcrypt($plainPassword),
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

            $profilePath = null;

            if ($request->hasFile('profile_image')) {
                $profilePath = uploadGeneralImage($request->file('profile_image'), 'members/profile');
            }

            $member = FamilyMember::create([
                'family_id' => $family->id,
                'user_id' => $linkedUserId,
                'phone' => $request->phone,
                'gender' => $gender,
                'birth_date' => $request->dob,
                'profession' => $request->profession,
                'education' => $request->education,
                'living_status' => $request->living_status,
                'marital_status' => $maritalStatus,
                'address' => $request->address,
                'bio' => $request->bio,
                'blood_group' => $request->blood_group,
                'date_of_death' => $request->date_of_death,
                'marriage_date' => $request->marriage_date,
                'divorced_date' => $request->divorced_date,
                'created_by' => $user->id,
            ]);

            if ($linkedUserId) {
                $memberUser = User::find($linkedUserId);

                if ($memberUser && $profilePath) {
                    $memberUser->image = $profilePath;
                    $memberUser->save();
                }
            }

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

            return ApiResponse::success('Member created successfully.', [
                'member' => $member->load([
                    'user:id,name,image',
                    'spouses.user:id,name,image',
                    'children.user:id,name,image',
                ]),
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return ApiResponse::error('Validation failed.', $e->errors(), 422);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Member Create Error: '.$e->getMessage());

            return ApiResponse::error('Something went wrong!', null, 500);
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

            $user = Auth::user();
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
            $member->living_status = $request->living_status;
            $member->date_of_death = $request->living_status === 'dead' ? $request->date_of_death : null;
            $member->education = $request->education;
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
                    $linkedUser = User::create([
                        'name' => $request->name,
                        'email' => $request->email,
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

            return ApiResponse::success('Member updated successfully.', [
                'member' => $member->load([
                    'user:id,name,image',
                    'spouses.user:id,name,image',
                    'children.user:id,name,image',
                ]),
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();

            return ApiResponse::error('Validation failed.', $e->errors(), 422);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Member Update Error: '.$e->getMessage(), [
                'member_id' => $member->id,
                'family_id' => $member->family_id,
            ]);

            return ApiResponse::error('Something went wrong!', null, 500);
        }
    }

    public function viewMember($id)
    {
        return $this->respondWithAuthorizedMemberDetails($id);
    }

    public function getMemberInfo($id)
    {
        return $this->respondWithAuthorizedMemberDetails($id);
    }

    private function familyForUser($user): ?Family
    {
        $familyId = $user->family_id
            ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        return $familyId ? Family::find($familyId) : null;
    }

    private function respondWithAuthorizedMemberDetails($id)
    {
        $user = Auth::user();

        $member = FamilyMember::whereKey($id)
            ->with([
                'user:id,name,image',
                'spouses.user:id,name,image',
                'children.user:id,name,image',
                'relatedTo',
            ])
            ->first();

        if (! $member) {
            return ApiResponse::error('Member not found.', null, 404);
        }

        if ((int) $member->family_id !== (int) $user->family_id) {
            return ApiResponse::error('Unauthorized.', null, 403);
        }

        $profileUser = $member->user;
        $connections = collect();

        if ($member->id) {
            $connections = FamilyRelationship::with([
                'relatedMember.user:id,name,image',
                'member.user:id,name,image',
            ])
                ->where('family_id', $member->family_id)
                ->where(function ($query) use ($member) {
                    $query->where('member_id', $member->id)
                        ->orWhere('related_member_id', $member->id);
                })
                ->whereIn('relation_type', ['spouse', 'father', 'mother'])
                ->get()
                ->map(function (FamilyRelationship $relationship) use ($member) {
                    $relatedMember = (int) $relationship->member_id === (int) $member->id
                        ? $relationship->relatedMember
                        : $relationship->member;

                    return [
                        'id' => $relationship->id,
                        'relation_type' => $relationship->relation_type,
                        'member' => $relationship->member ? [
                            'id' => $relationship->member->id,
                            'name' => $relationship->member->name ?? $relationship->member->user?->name,
                            'user' => $relationship->member->relationLoaded('user') && $relationship->member->user ? [
                                'id' => $relationship->member->user->id,
                                'name' => $relationship->member->user->name,
                                'image' => $relationship->member->user->image,
                            ] : null,
                        ] : null,
                        'related_member' => $relatedMember ? [
                            'id' => $relatedMember->id,
                            'name' => $relatedMember->name ?? $relatedMember->user?->name,
                            'user' => $relatedMember->relationLoaded('user') && $relatedMember->user ? [
                                'id' => $relatedMember->user->id,
                                'name' => $relatedMember->user->name,
                                'image' => $relatedMember->user->image,
                            ] : null,
                        ] : null,
                    ];
                })
                ->values();
        }

        $postsQuery = FeedPost::where('user_id', $profileUser?->id)
            ->with([
                'user:id,name,image',
                'media:id,post_id,file_type,file_path',
                'likes:id,post_id,user_id',
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->with([
                            'user:id,name,image',
                            'replies.user:id,name,image',
                        ])
                        ->orderBy('id', 'asc');
                },
            ])
            ->withCount(['likes', 'comments'])
            ->latest();

        if ((int) $user->id !== (int) $profileUser?->id && Schema::hasColumn('feed_posts', 'privacy')) {
            $postsQuery->where('privacy', 'public');
        }

        $posts = $profileUser ? $postsQuery->get() : collect();

        $memories = $posts->map(fn (FeedPost $post) => $this->formatProfilePost($post))->values();
        $photos = $posts
            ->filter(fn (FeedPost $post) => $post->media->contains('file_type', 'image'))
            ->map(fn (FeedPost $post) => $this->formatProfilePost($post))
            ->values();
        $voices = $posts
            ->filter(fn (FeedPost $post) => $post->media->contains('file_type', 'audio'))
            ->map(fn (FeedPost $post) => $this->formatProfilePost($post))
            ->values();

        $stats = [
            'memories' => $profileUser ? FeedPost::where('user_id', $profileUser->id)->count() : 0,
            'photos' => $profileUser
                ? PostMedia::whereHas('post', function ($query) use ($profileUser) {
                    $query->where('user_id', $profileUser->id);
                })->where('file_type', 'image')->count()
                : 0,
            'voices' => $profileUser
                ? PostMedia::whereHas('post', function ($query) use ($profileUser) {
                    $query->where('user_id', $profileUser->id);
                })->where('file_type', 'audio')->count()
                : 0,
            'families' => $connections->count(),
        ];

        return ApiResponse::success('Family member retrieved successfully.', [
            'member' => $member,
            'family' => $member->family,
            'stats' => $stats,
            'memories' => $memories,
            'photos' => $photos,
            'voices' => $voices,
            'families' => $connections,
        ]);
    }

    private function formatProfilePost(FeedPost $post): array
    {
        return [
            'id' => $post->id,
            'user_id' => $post->user_id,
            'content' => $post->content,
            'type' => $post->type,
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'user' => $post->relationLoaded('user') && $post->user ? [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'image' => $post->user->image,
            ] : null,
            'media' => $post->relationLoaded('media')
                ? $post->media->map(fn (PostMedia $media) => [
                    'id' => $media->id,
                    'post_id' => $media->post_id,
                    'file_type' => $media->file_type,
                    'file_path' => filter_var($media->file_path, FILTER_VALIDATE_URL)
                        ? $media->file_path
                        : asset($media->file_path),
                ])->values()
                : [],
            'likes_count' => $post->likes_count ?? $post->likes->count(),
            'comments_count' => $post->comments_count ?? $post->comments->count(),
            'likes' => $post->relationLoaded('likes')
                ? $post->likes->map(fn ($like) => [
                    'id' => $like->id,
                    'post_id' => $like->post_id,
                    'user_id' => $like->user_id,
                ])->values()
                : [],
            'comments' => $post->relationLoaded('comments')
                ? $post->comments->map(fn ($comment) => [
                    'id' => $comment->id,
                    'post_id' => $comment->post_id,
                    'user_id' => $comment->user_id,
                    'comment' => $comment->comment,
                    'parent_id' => $comment->parent_id,
                    'user' => $comment->relationLoaded('user') && $comment->user ? [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'image' => $comment->user->image,
                    ] : null,
                ])->values()
                : [],
        ];
    }

    private function getSpouses($mId, $familyId)
    {
        $rel = FamilyRelationship::where('family_id', $familyId)
            ->where(function ($q) use ($mId) {
                $q->where('member_id', $mId)->orWhere('related_member_id', $mId);
            })
            ->where('relation_type', 'spouse')->get();

        $ids = $rel->map(fn($r) => $r->member_id == $mId ? $r->related_member_id : $r->member_id);

        return FamilyMember::with('user:id,name,image')
            ->whereIn('id', $ids)
            ->get();
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
            ->contains(fn($spouse) => $spouse->gender === 'male');
    }
}
