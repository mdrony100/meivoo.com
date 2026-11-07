<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\FamilyMember;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        $events = Event::query()
            ->whereIn('family_id', $familyIds)
            ->where('creator_user_id', $user->id)
            ->with('creator:id,name,image')
            ->orderBy('event_date', 'asc')
            ->get()
            ->map(fn(Event $event) => $this->formatEvent($event));

        return ApiResponse::success('Events retrieved successfully.', [
            'events' => $events,
        ]);
    }

    public function recent(Request $request)
    {
        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        $limit = (int) $request->query('limit', 10);

        $events = Event::query()
            ->whereIn('family_id', $familyIds)
            ->with('creator:id,name,image')
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->limit($limit)
            ->get()
            ->map(fn(Event $event) => $this->formatEvent($event));

        return ApiResponse::success('Recent events retrieved successfully.', [
            'events' => $events,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', Event::getTypes()),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'event_date' => 'required|date_format:Y-m-d H:i',
            'location' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $user = Auth::user();
        $member = FamilyMember::where('user_id', $user->id)->select('family_id')->first();

        if (! $member || ! $member->family_id) {
            return ApiResponse::error('Family profile is missing.', null, 422);
        }

        try {
            $coverImagePath = null;

            if ($request->hasFile('cover_image')) {
                $coverImagePath = uploadGeneralImage($request->file('cover_image'), 'events/cover');
            }

            $event = Event::create([
                'family_id' => $member->family_id,
                'creator_user_id' => $user->id,
                'type' => $validated['type'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'event_date' => $validated['event_date'],
                'location' => $validated['location'] ?? null,
                'cover_image' => $coverImagePath,
            ]);

            return ApiResponse::success('Event created successfully.', [
                'event' => $this->formatEvent($event->load('creator:id,name,image')),
            ]);
        } catch (\Throwable $th) {
            // dd($th);
            \Log::error('Event store failed: ' . $th->getMessage());

            return ApiResponse::error('Unable to create event right now. Please try again.', null, 500);
        }
    }

    public function show($id)
    {
        $event = Event::find($id);

        if (! $event) {
            return ApiResponse::error('Event not found.', null, 404);
        }

        $user = Auth::user();

        $familyIds = FamilyMember::where('user_id', $user->id)
            ->pluck('family_id');

        if (! $familyIds->contains($event->family_id)) {
            return ApiResponse::error('Unauthorized.', null, 403);
        }

        $event->load([
            'creator:id,name,image',
            'attendances.user:id,name,image',
        ]);

        return ApiResponse::success('Event retrieved successfully.', [
            'event' => $this->formatEvent($event),
            'attendances' => $this->formatAttendances($event),
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        if (! in_array($event->family_id, $familyIds->toArray())) {
            return ApiResponse::error('Unauthorized.', null, 403);
        }

        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', Event::getTypes()),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'event_date' => 'required|date_format:Y-m-d H:i',
            'location' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        try {
            $coverImagePath = $event->cover_image;

            if ($request->hasFile('cover_image')) {
                if (
                    $event->cover_image
                    && ! filter_var($event->cover_image, FILTER_VALIDATE_URL)
                    && File::exists(public_path($event->cover_image))
                ) {
                    File::delete(public_path($event->cover_image));
                }

                $coverImagePath = uploadGeneralImage($request->file('cover_image'), 'events/cover');
            }

            $event->update([
                'type' => $validated['type'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'event_date' => $validated['event_date'],
                'location' => $validated['location'] ?? null,
                'cover_image' => $coverImagePath,
            ]);

            $event->load([
                'creator:id,name,image',
                'attendances.user:id,name,image',
            ]);

            return ApiResponse::success('Event updated successfully.', [
                'event' => $this->formatEvent($event),
            ]);
        } catch (\Throwable $th) {
            // dd($th);
            \Log::error('Event update failed: ' . $th->getMessage());

            return ApiResponse::error('Unable to update event right now. Please try again.', null, 500);
        }
    }

    public function destroy($id)
    {
        $event = Event::find($id);

        if (! $event) {
            return ApiResponse::error('Event not found.', null, 404);
        }

        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        if (! $familyIds->contains($event->family_id)) {
            return ApiResponse::error('Unauthorized.', null, 403);
        }

        try {
            if (
                $event->cover_image &&
                ! filter_var($event->cover_image, FILTER_VALIDATE_URL) &&
                File::exists(public_path($event->cover_image))
            ) {
                File::delete(public_path($event->cover_image));
            }

            $event->attendances()->delete();
            $event->delete();

            return ApiResponse::success('Event deleted successfully.');
        } catch (\Throwable $th) {
            \Log::error('Event delete failed: ' . $th->getMessage());

            return ApiResponse::error('Unable to delete event right now. Please try again.', null, 500);
        }
    }

    public function rsvp(Request $request, $id)
    {
        $event = Event::find($id);

        if (! $event) {
            return ApiResponse::error('Event not found.', null, 404);
        }

        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        if (! $familyIds->contains($event->family_id)) {
            return ApiResponse::error('Unauthorized.', null, 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', EventAttendance::getStatusOptions()),
        ]);

        EventAttendance::updateOrCreate(
            [
                'event_id' => $event->id,
                'user_id' => $user->id,
            ],
            [
                'status' => $validated['status'],
            ]
        );

        $event->load([
            'creator:id,name,image',
            'attendances.user:id,name,image',
        ]);

        return ApiResponse::success('RSVP updated successfully.', [
            'event' => $this->formatEvent($event),
            'attendances' => $this->formatAttendances($event),
        ]);
    }

    public function getAttendances($id)
    {
        $event = Event::find($id);

        if (! $event) {
            return ApiResponse::error('Event not found.', null, 404);
        }

        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        if (! $familyIds->contains($event->family_id)) {
            return ApiResponse::error('Unauthorized.', null, 403);
        }

        $event->load([
            'creator:id,name,image',
            'attendances.user:id,name,image',
        ]);

        return ApiResponse::success('Event attendances retrieved successfully.', [
            'event' => $this->formatEvent($event),
            'attendances' => $this->formatAttendances($event),
        ]);
    }

    private function formatEvent(Event $event): array
    {
        return [
            'id' => $event->id,
            'family_id' => $event->family_id,
            'creator_id' => $event->creator_user_id,
            'type' => $event->type,
            'title' => $event->title,
            'description' => $event->description,
            'event_date' => $event->event_date,
            'location' => $event->location,
            'cover_image' => $event->cover_image ? asset($event->cover_image) : null,
            'created_at' => $event->created_at,
            'updated_at' => $event->updated_at,
            'creator' => $event->relationLoaded('creator') && $event->creator
                ? [
                    'id' => $event->creator->id,
                    'name' => $event->creator->name,
                    'image' => $event->creator->image,
                ]
                : null,
        ];
    }

    private function formatAttendances(Event $event): array
    {
        $attendances = $event->attendances ?? collect();

        $going = $attendances->where('status', EventAttendance::STATUS_GOING)->map(fn($a) => [
            'id' => $a->user_id,
            'name' => $a->user->name,
            'image' => $a->user->image,
        ]);

        $maybe = $attendances->where('status', EventAttendance::STATUS_MAYBE)->map(fn($a) => [
            'id' => $a->user_id,
            'name' => $a->user->name,
            'image' => $a->user->image,
        ]);

        $no = $attendances->where('status', EventAttendance::STATUS_NO)->map(fn($a) => [
            'id' => $a->user_id,
            'name' => $a->user->name,
            'image' => $a->user->image,
        ]);

        return [
            'going' => $going->values(),
            'maybe' => $maybe->values(),
            'no' => $no->values(),
            'counts' => [
                'going' => $going->count(),
                'maybe' => $maybe->count(),
                'no' => $no->count(),
            ],
        ];
    }
}
