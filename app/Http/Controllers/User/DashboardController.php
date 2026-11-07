<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\FamilyRelationship;
use App\Models\FeedPost;
use App\Models\PostMedia;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        View::share('settings', getSetting());
        $this->middleware('auth')->except(['memberFullProfile']);
    }

    private function loadTimelinePosts(int $userId)
    {
        return FeedPost::query()
            ->where('user_id', $userId)
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
            ->orderByDesc('id')
            ->get();
    }

    public function dashboard()
    {
        $user = Auth::user();
        $familyId = Auth::user()->family_id;
        $totalMembers = 0;
        $marriedCouples = 0;
        $childrenCount = 0;
        $currentMember = FamilyMember::with('user:id,name,image')
            ->where('user_id', $user->id)
            ->first();

        $familyId = $user->family_id ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        if ($familyId) {
            $members = FamilyMember::with([
                'user:id,name,image,email',
                'spouses.user:id,name,image',
            ])
                ->withCount(['children', 'spouses'])
                ->where('family_id', $familyId)
                ->orderBy('id')
                ->get();
            $family = Family::find($familyId);
            $familyMembers = $members;
            $totalMembers = $members->count();
            $marriedCouples = (int) ceil($members->where('marital_status', 'married')->count() / 2);
            $childrenCount = FamilyRelationship::where('family_id', $familyId)
                ->whereIn('relation_type', ['father', 'mother'])
                ->distinct('related_member_id')
                ->count('related_member_id');
        }

        $posts = $this->loadTimelinePosts($user->id);
        $recentActivities = collect();

        if ($familyId) {
            $familyMembersForActivity = FamilyMember::with('user:id,name,image')
                ->where('family_id', $familyId)
                ->get();

            $familyUserIds = $familyMembersForActivity
                ->pluck('user_id')
                ->filter()
                ->values();

            $recentPosts = $familyUserIds->isEmpty()
                ? collect()
                : FeedPost::with(['user:id,name,image', 'media:id,post_id,file_type,file_path'])
                    ->whereIn('user_id', $familyUserIds)
                    ->latest('created_at')
                    ->take(3)
                    ->get()
                    ->map(function ($post) {
                        return [
                            'type' => 'post',
                            'icon' => 'fa-regular fa-image',
                            'title' => ($post->user->name ?? 'A member').' uploaded a new post',
                            'subtitle' => $post->media->where('file_type', 'image')->count()
                                ? $post->media->where('file_type', 'image')->count().' photo(s)'
                                : 'New memory shared',
                            'time' => $post->created_at->diffForHumans(),
                            'created_at' => $post->created_at,
                        ];
                    });

            $recentMembers = $familyMembersForActivity
                ->whereNotNull('created_at')
                ->sortByDesc('created_at')
                ->take(3)
                ->map(function ($member) {
                    return [
                        'type' => 'member',
                        'icon' => 'fa-regular fa-user',
                        'title' => ($member->user->name ?? $member->name ?? 'A member').' joined the family',
                        'subtitle' => 'New family member added',
                        'time' => $member->created_at->diffForHumans(),
                        'created_at' => $member->created_at,
                    ];
                });

            $recentRelations = FamilyRelationship::with([
                'member.user:id,name,image',
                'relatedMember.user:id,name,image',
            ])
                ->where('family_id', $familyId)
                ->latest('created_at')
                ->take(3)
                ->get()
                ->map(function ($relation) {
                    $relationLabel = match ($relation->relation_type) {
                        'spouse' => 'added a spouse link',
                        'father' => 'added a father relation',
                        'mother' => 'added a mother relation',
                        'child' => 'added a child relation',
                        default => 'updated the family tree',
                    };

                    return [
                        'type' => 'relation',
                        'icon' => 'fa-solid fa-diagram-project',
                        'title' => ($relation->member->user->name ?? 'A member').' '.$relationLabel,
                        'subtitle' => 'Family tree updated',
                        'time' => $relation->created_at->diffForHumans(),
                        'created_at' => $relation->created_at,
                    ];
                });

            $recentActivities = $recentPosts
                ->concat($recentMembers)
                ->concat($recentRelations)
                ->sortByDesc('created_at')
                ->take(3)
                ->values();
        }

        $members = FamilyMember::with('user')->where('family_id', $familyId)
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->get()
            ->map(function ($member) {

                $birthdayThisYear = Carbon::createFromDate(
                    now()->year,
                    Carbon::parse($member->birth_date)->month,
                    Carbon::parse($member->birth_date)->day
                );

                // if already passed this year → next year
                if ($birthdayThisYear->lt(now())) {
                    $birthdayThisYear->addYear();
                }

                $member->days_left = floor(now()->diffInDays($birthdayThisYear));

                return $member;
            })
            ->sortBy('days_left')
            ->take(5);

        $upcomingEvents = Event::query()
            ->with('creator:id,name')
            ->where(function ($q) use ($user, $familyId) {
                if ($familyId) {
                    $q->where('family_id', $familyId)
                        ->orWhere('creator_user_id', $user->id);
                } else {
                    $q->where('creator_user_id', $user->id);
                }
            })
            ->where('event_date', '>=', Carbon::now())
            ->orderBy('event_date', 'asc')
            ->take(2)
            ->get();

        return view('user.dashboard', compact(
            'posts',
            'members',
            'totalMembers',
            'marriedCouples',
            'childrenCount',
            'currentMember',
            'recentActivities',
            'upcomingEvents'
        ));

    }

    public function settings()
    {
        $user = Auth::user();
        $member = FamilyMember::where('user_id', $user->id)->first();

        return view('user.settings', compact('member'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.Auth::id(),
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'blood_group' => 'nullable|string|max:5',
            'profession' => 'nullable|string|max:255',
            'education' => 'nullable|string|max:255',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'marriage_date' => 'nullable|date',
            'divorced_date' => 'nullable|date',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();

                if ($request->hasFile('image')) {
                    $user->image = uploadGeneralImage($request->file('image'), 'users', $user->image);
                }

                $familyId = $user->family_id;

                if (empty($familyId)) {
                    $family = Family::create([
                        'name' => $request->name.' Family',
                        'owner_user_id' => $user->id,
                    ]);
                    $familyId = $family->id;
                }

                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'family_id' => $familyId,
                ]);

                FamilyMember::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'phone' => $request->phone,
                        'gender' => $request->gender,
                        'family_id' => $familyId,
                        'birth_date' => $request->birth_date,
                        'blood_group' => $request->blood_group,
                        'profession' => $request->profession,
                        'education' => $request->education,
                        'marital_status' => $request->marital_status,
                        'marriage_date' => $request->marriage_date,
                        'divorced_date' => $request->divorced_date,
                        'address' => $request->address,
                        'bio' => $request->bio,
                        'created_by' => $user->id,
                    ]
                );
            });

            return back()->with('success', 'Profile updated successfully!');
        } catch (\Throwable $th) {
            \Log::error($th->getMessage());

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->save();

            return back()->with('success', 'Password updated successfully!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    // public function memberFullProfile($id)
    // {
    //     $user = User::with('member')->findOrFail($id);

    //     $connections = [];
    //     if ($user->member) {
    //         $connections = FamilyRelationship::where('member_id', $user->member->id)
    //             ->with(['relatedMember.user'])
    //             ->get();
    //     }
    //     dd($connections);

    //     $posts = FeedPost::where('user_id', $user->id)
    //         ->with(['media', 'likes', 'comments.user', 'comments.replies.user'])
    //         ->withCount(['likes', 'comments'])
    //         ->latest()
    //         ->get();

    //     return view('user.member-full-profile', compact('user', 'posts', 'connections'));
    // }

    public function memberFullProfile($id)
    {
        $user = User::with('member')->findOrFail($id);
        $authId = Auth::id();

        $connections = [];
        if ($user->member) {
            $connections = FamilyRelationship::with(['relatedMember.user'])
                ->where('member_id', $user->member->id)
                ->whereIn('relation_type', ['spouse', 'father', 'mother'])
                ->get();
        }
        // dd($connections->pluck('relation_type', 'relatedMember.user.name'));

        $stats = [
            'memories' => FeedPost::where('user_id', $user->id)->count(),

            'photos' => PostMedia::whereHas('post', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('file_type', 'image')->count(),

            'voices' => PostMedia::whereHas('post', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('file_type', 'audio')->count(),

            'families' => $connections->count(),
        ];

        // $postsQuery = FeedPost::where('user_id', $user->id)
        //     ->with(['media', 'likes', 'comments.user', 'comments.replies.user'])
        //     ->withCount(['likes', 'comments'])
        //     ->latest();
        $postsQuery = FeedPost::where('user_id', $user->id)
            ->with([
                'media',
                'likes',
                'comments' => function ($query) {
                    $query->whereNull('parent_id')
                        ->with(['user', 'replies.user'])
                        ->orderBy('id', 'desc')
                        ->limit(5);
                },
            ])
            ->withCount(['likes', 'comments'])
            ->latest();

        if ((int) $authId !== (int) $user->id) {
            $postsQuery->where('privacy', 'public');
        }
        $posts = $postsQuery->get();

        return view('user.member-full-profile', compact('user', 'posts', 'connections', 'stats'));
    }
}
