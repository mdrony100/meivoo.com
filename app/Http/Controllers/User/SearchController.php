<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\FeedPost;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    /**
     * Family-scoped quick search for the topbar.
     * Members, feed posts and events, grouped for the dropdown.
     */
    public function search(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json(['groups' => []]);
        }

        $user = $request->user();
        $familyId = $user->family_id
            ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        $like = '%' . $term . '%';
        $groups = [];

        // Members
        $members = FamilyMember::query()
            ->with('user:id,name,image')
            ->where('family_id', $familyId)
            ->whereHas('user', fn ($q) => $q->where('name', 'like', $like))
            ->limit(5)
            ->get()
            ->map(fn (FamilyMember $member) => [
                'title' => e($member->user->name ?? 'Family member'),
                'meta' => e($member->profession ?? ''),
                'url' => route('user.member.profile', $member->user_id ?? $member->id),
                'icon' => '👤',
            ]);

        if ($members->isNotEmpty()) {
            $groups[] = ['label' => 'Family members', 'items' => $members];
        }

        // Feed posts
        $posts = FeedPost::query()
            ->with('user:id,name')
            ->where('family_id', $familyId)
            ->where('content', 'like', $like)
            ->latest()
            ->limit(4)
            ->get()
            ->map(fn (FeedPost $post) => [
                'title' => e(Str::limit(strip_tags((string) $post->content), 64)),
                'meta' => e($post->user->name ?? ''),
                'url' => route('user.feed') . '#post-' . $post->id,
                'icon' => '📝',
            ]);

        if ($posts->isNotEmpty()) {
            $groups[] = ['label' => 'Feed posts', 'items' => $posts];
        }

        // Events
        $events = Event::query()
            ->where('family_id', $familyId)
            ->where('title', 'like', $like)
            ->orderByDesc('event_date')
            ->limit(4)
            ->get()
            ->map(fn (Event $event) => [
                'title' => e($event->title),
                'meta' => $event->event_date ? $event->event_date->format('M j, Y') : '',
                'url' => route('user.events'),
                'icon' => '📅',
            ]);

        if ($events->isNotEmpty()) {
            $groups[] = ['label' => 'Events', 'items' => $events];
        }

        return response()->json(['groups' => $groups]);
    }

    /**
     * Small counters for the notification bell.
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $uid = $user->id;

        $familyId = $user->family_id
            ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        $upcomingEvents = Event::query()
            ->where('family_id', $familyId)
            ->whereBetween('event_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->count();

        $birthdays = FamilyMember::query()
            ->where('family_id', $familyId)
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->count();

        // Unread = messages in my conversations newer than my last_read_at.
        $unread = Message::where('to_user_id', $uid)->where('is_read', 0)->count();

        return response()->json([
            'messages' => $unread,
            'events' => $upcomingEvents,
            'birthdays' => $birthdays,
        ]);
    }
}
