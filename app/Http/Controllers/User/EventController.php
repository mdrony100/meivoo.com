<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\FamilyMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function events(Request $request)
    {
        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        $events = Event::query()
            ->whereIn('family_id', $familyIds)
            ->with(['creator:id,name,image', 'attendances.user:id,name,image'])
            ->orderBy('event_date', 'asc')
            ->get();

        return view('user.events', compact('events'));
    }

    public function createEvent(Request $request)
    {
        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        return view('user.events.create', compact('familyIds'));
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:'.implode(',', Event::getTypes()),
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'event_date' => 'required|date_format:Y-m-d H:i',
            'location' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $user = Auth::user();
        $member = FamilyMember::where('user_id', $user->id)->select('family_id')->first();

        if (! $member || ! $member->family_id) {
            return back()->with('error', 'Family profile is missing.');
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

            return redirect()->route('user.events')
                ->with('success', 'Event created successfully.');
        } catch (\Throwable $th) {
            Log::error('Event store failed: '.$th->getMessage());

            return back()->with('error', 'Unable to create event right now. Please try again.');
        }
    }

    // public function showEvent($id)
    // {
    //     $event = Event::find($id);

    //     if (! $event) {
    //         return redirect()->route('user.events')
    //             ->with('error', 'Event not found.');
    //     }

    //     $user = Auth::user();
    //     $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

    //     if (! $familyIds->contains($event->family_id)) {
    //         return redirect()->route('user.events')
    //             ->with('error', 'Unauthorized.');
    //     }

    //     $event->load([
    //         'creator:id,name,image',
    //         'attendances.user:id,name,image',
    //     ]);

    //     $myAttendance = EventAttendance::where('event_id', $event->id)
    //         ->where('user_id', $user->id)
    //         ->first();

    //     return view('user.events.show', compact('event', 'myAttendance'));
    // }

    public function showEvent($id, Request $request)
    {
        $event = Event::find($id);

        if (! $event) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Event not found.'], 404);
            }

            return redirect()->route('user.events')->with('error', 'Event not found.');
        }

        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        if (! $familyIds->contains($event->family_id)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Unauthorized.'], 403);
            }

            return redirect()->route('user.events')->with('error', 'Unauthorized.');
        }

        $event->load([
            'creator:id,name,image',
            'attendances.user:id,name,image',
        ]);

        $myAttendance = EventAttendance::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->first();

        // If request comes from AJAX (Offcanvas)
        if ($request->ajax()) {
            return view('user.events.show', compact('event', 'myAttendance'));
        }

        return view('user.events.show', compact('event', 'myAttendance'));
    }

    public function editEvent(Request $request, $id)
    {
        $event = Event::find($id);

        if (! $event) {
            return redirect()->route('user.events')
                ->with('error', 'Event not found.');
        }

        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        if (! in_array($event->family_id, $familyIds->toArray())) {
            return redirect()->route('user.events')
                ->with('error', 'Unauthorized.');
        }

        return view('user.events.edit', compact('event', 'familyIds'));
    }

    public function updateEvent(Request $request, $id)
    {
        $event = Event::find($id);

        if (! $event) {
            return back()->with('error', 'Event not found.');
        }

        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        if (! in_array($event->family_id, $familyIds->toArray())) {
            return back()->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'type' => 'required|in:'.implode(',', Event::getTypes()),
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

            return redirect()->route('user.events')
                ->with('success', 'Event updated successfully.');
        } catch (\Throwable $th) {
            Log::error('Event update failed: '.$th->getMessage());

            return back()->with('error', 'Unable to update event right now. Please try again.');
        }
    }

    public function deleteEvent($id)
    {
        $event = Event::find($id);

        if (! $event) {
            return redirect()->route('user.events')
                ->with('error', 'Event not found.');
        }

        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        if (! $familyIds->contains($event->family_id)) {
            return redirect()->route('user.events')
                ->with('error', 'Unauthorized.');
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

            return redirect()->route('user.events')
                ->with('success', 'Event deleted successfully.');
        } catch (\Throwable $th) {
            Log::error('Event delete failed: '.$th->getMessage());

            return redirect()->route('user.events')
                ->with('error', 'Unable to delete event right now. Please try again.');
        }
    }

    public function rsvp(Request $request, $id)
    {
        $event = Event::find($id);

        if (! $event) {
            return back()->with('error', 'Event not found.');
        }

        $user = Auth::user();
        $familyIds = FamilyMember::where('user_id', $user->id)->pluck('family_id');

        if (! $familyIds->contains($event->family_id)) {
            return back()->with('error', 'Unauthorized.');
        }

        $validated = $request->validate([
            'status' => 'required|in:'.implode(',', EventAttendance::getStatusOptions()),
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

        return back()->with('success', 'RSVP updated successfully.');
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

        $going = $attendances->where('status', EventAttendance::STATUS_GOING)->map(fn ($a) => [
            'id' => $a->user_id,
            'name' => $a->user->name,
            'image' => $a->user->image,
        ]);

        $maybe = $attendances->where('status', EventAttendance::STATUS_MAYBE)->map(fn ($a) => [
            'id' => $a->user_id,
            'name' => $a->user->name,
            'image' => $a->user->image,
        ]);

        $no = $attendances->where('status', EventAttendance::STATUS_NO)->map(fn ($a) => [
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
