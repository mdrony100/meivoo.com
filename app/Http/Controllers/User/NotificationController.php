<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ConversationParticipant;
use App\Models\Event;
use App\Models\FamilyMember;
use App\Models\FeedPost;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    /**
     * Notification centre: a single, honest feed built from real family data
     * (new posts, upcoming events, birthdays, unread conversations).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $familyId = $user->family_id
            ?? FamilyMember::where('user_id', $user->id)->value('family_id');

        $items = collect();

        // New family posts
        FeedPost::with('user:id,name,image')
            ->where('family_id', $familyId)
            ->where('user_id', '!=', $user->id)
            ->latest()
            ->limit(12)
            ->get()
            ->each(function (FeedPost $post) use ($items) {
                $items->push([
                    'type' => 'post',
                    'icon' => 'newspaper',
                    'title' => ($post->user->name ?? 'A family member') . ' shared a new post',
                    'body' => \Illuminate\Support\Str::limit(strip_tags((string) $post->content), 96),
                    'url' => route('user.feed') . '#post-' . $post->id,
                    'at' => $post->created_at,
                    'when' => $post->created_at,
                ]);
            });

        // Upcoming events
        Event::where('family_id', $familyId)
            ->whereBetween('event_date', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
            ->orderBy('event_date')
            ->limit(10)
            ->get()
            ->each(function (Event $event) use ($items) {
                $items->push([
                    'type' => 'event',
                    'icon' => 'calendar-days',
                    'title' => $event->title,
                    'body' => trim(($event->event_date?->format('D, M j · g:i A') ?? '') . ($event->location ? ' · ' . $event->location : '')),
                    'url' => route('user.events'),
                    'at' => $event->event_date,
                    'when' => $event->event_date,
                ]);
            });

        // Birthdays this month
        FamilyMember::with('user:id,name,image')
            ->where('family_id', $familyId)
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->get()
            ->each(function (FamilyMember $member) use ($items) {
                $day = Carbon::parse($member->birth_date);
                $items->push([
                    'type' => 'birthday',
                    'icon' => 'cake',
                    'title' => ($member->user->name ?? 'A family member') . "'s birthday",
                    'body' => $day->format('F j') . ' · ' . $day->age . ' years',
                    'url' => route('user.member.profile', $member->user_id ?? $member->id),
                    'at' => $day->copy()->year(now()->year),
                    'when' => $day,
                ]);
            });

        // Unread conversations
        $participations = ConversationParticipant::where('user_id', $user->id)
            ->whereNull('left_at')
            ->with(['conversation' => fn ($q) => $q->with('participants.user:id,name')])
            ->get();

        foreach ($participations as $participation) {
            $conversation = $participation->conversation;
            if (! $conversation) {
                continue;
            }

            $unread = Message::where('conversation_id', $conversation->id)
                ->where('sender_user_id', '!=', $user->id)
                ->when($participation->last_read_at, fn ($q) => $q->where('created_at', '>', $participation->last_read_at))
                ->latest()
                ->first();

            if (! $unread) {
                continue;
            }

            $items->push([
                'type' => 'message',
                'icon' => 'message-square',
                'title' => ($unread->sender->name ?? 'A family member') . ' sent you a message',
                'body' => \Illuminate\Support\Str::limit((string) $unread->body, 96),
                'url' => route('user.messages') . '?conversation=' . $conversation->id,
                'at' => $unread->created_at,
                'when' => $unread->created_at,
            ]);
        }

        $items = $items->sortByDesc(fn ($item) => $item['at'] ? Carbon::parse($item['at'])->timestamp : 0)->values();

        $groups = $items->groupBy(fn ($item) => match (true) {
            $item['at'] && Carbon::parse($item['at'])->isToday() => 'Today',
            $item['at'] && Carbon::parse($item['at'])->isYesterday() => 'Yesterday',
            $item['at'] && Carbon::parse($item['at'])->gt(now()) => 'Coming up',
            default => 'Earlier',
        });

        return view('user.notifications', compact('items', 'groups'));
    }
}
