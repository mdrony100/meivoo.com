<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\FamilyMember;
use App\Models\FamilyRelationship;
use App\Models\FeedPost;
use Illuminate\Support\Facades\Auth;
use App\Support\ApiResponse;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $familyId = $user->family_id ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        $currentMember = FamilyMember::with('user:id,name,image')
            ->where('user_id', $user->id)
            ->first();

        $totalMembers = 0;
        $marriedCouples = 0;
        $childrenCount = 0;
        $family = null;

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
            $totalMembers = $members->count();
            $marriedCouples = (int) ceil($members->where('marital_status', 'married')->count() / 2);
            $childrenCount = FamilyRelationship::where('family_id', $familyId)
                ->whereIn('relation_type', ['father', 'mother'])
                ->distinct('related_member_id')
                ->count('related_member_id');
        }

        $birthdays = collect();

        if ($familyId) {
            $birthdays = FamilyMember::with('user:id,name,image')
                ->where('family_id', $familyId)
                ->whereNotNull('birth_date')
                ->whereRaw('MONTH(birth_date) = ?', [now()->month])
                ->orderByRaw('DAY(birth_date)')
                ->get()
                ->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'family_id' => $member->family_id,
                        'user_id' => $member->user_id,
                        'gender' => $member->gender,
                        'birth_date' => $member->birth_date,
                        'user' => $member->user ? [
                            'id' => $member->user->id,
                            'name' => $member->user->name,
                            'image' => $member->user->image,
                        ] : null,
                    ];
                })
                ->take(5)
                ->values();
        }

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
                        'title' => ($post->user->name ?? 'A member') . ' uploaded a new post',
                        'subtitle' => $post->media->where('file_type', 'image')->count()
                            ? $post->media->where('file_type', 'image')->count() . ' photo(s)'
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
                        'title' => ($member->user->name ?? $member->name ?? 'A member') . ' joined the family',
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
                        'title' => ($relation->member->user->name ?? 'A member') . ' ' . $relationLabel,
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

        return ApiResponse::success('Dashboard data retrieved successfully.', [
            'family' => $family,
            'current_member' => $currentMember,
            'overview' => [
                'members_count' => $totalMembers,
                'married_count' => $marriedCouples,
                'children_count' => $childrenCount,
            ],
            'birthdays' => $birthdays,
            'recent_activity' => $recentActivities,
        ]);
    }

    public function myPostImages()
    {
        $user = Auth::user();

        $images = FeedPost::where('user_id', $user->id)
            ->with(['media' => function ($query) {
                $query->where('file_type', 'image')->orderBy('id');
            }])
            ->latest()
            ->get()
            ->map(function ($post) {
                return $post->media->map(function ($media) use ($post) {
                    return [
                        'id' => $media->id,
                        'post_id' => $post->id,
                        'image' => $media->file_path,
                    ];
                });
            })
            ->collapse()
            ->values();

        return ApiResponse::success('Post images retrieved successfully.', $images);
    }
}
