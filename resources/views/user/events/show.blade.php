<!-- Offcanvas Custom UI Styling -->
<style>
    .offcanvas-event-wrapper {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: #1e293b;
        background-color: #f8fafc;
        min-height: 100%;
    }

    /* Hero Banner / Cover Section */
    .event-hero-header {
        position: relative;
        width: 100%;
        height: 220px;
        background-color: #4f46e5;
        overflow: hidden;
    }

    .event-hero-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .event-hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.1) 0%, rgba(15, 23, 42, 0.75) 100%);
    }

    .event-hero-badge {
        position: absolute;
        top: 16px;
        left: 16px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        color: #4f46e5;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        padding: 5px 12px;
        border-radius: 20px;
        text-transform: uppercase;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    }

    /* Content Area */
    .event-body {
        padding: 20px 20px 30px 20px;
    }

    .event-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.3;
        margin-bottom: 16px;
    }

    /* Meta Cards Grid */
    .event-meta-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 20px;
    }

    .meta-card {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #ffffff;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .meta-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .meta-icon.date {
        background: #e0e7ff;
        color: #4338ca;
    }

    .meta-icon.location {
        background: #fee2e2;
        color: #dc2626;
    }

    .meta-icon.creator {
        background: #f1f5f9;
        color: #475569;
    }

    .meta-text label {
        display: block;
        font-size: 0.7rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 1px;
    }

    .meta-text span {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
    }

    /* Description Box */
    .event-description-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .event-description-box h6 {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .event-description {
        font-size: 0.9rem;
        line-height: 1.6;
        color: #334155;
        white-space: pre-line;
        margin: 0;
    }

    /* RSVP Section */
    .rsvp-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
    }

    .status-alert {
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 12px;
    }

    .status-alert.going {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .status-alert.maybe {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .status-alert.no {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .rsvp-buttons {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }

    .btn-rsvp {
        border: none;
        padding: 9px 8px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s ease;
    }

    .btn-rsvp-going {
        background: #10b981;
        color: #ffffff;
    }

    .btn-rsvp-going:hover:not(:disabled) {
        background: #059669;
        transform: translateY(-1px);
    }

    .btn-rsvp-maybe {
        background: #f59e0b;
        color: #ffffff;
    }

    .btn-rsvp-maybe:hover:not(:disabled) {
        background: #d97706;
        transform: translateY(-1px);
    }

    .btn-rsvp-no {
        background: #ef4444;
        color: #ffffff;
    }

    .btn-rsvp-no:hover:not(:disabled) {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .btn-rsvp:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    .creator-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }

    /* Attendance List Section */
    .attendance-container {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
    }

    .attendance-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }

    .attendance-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }

    .attendance-pills {
        display: flex;
        gap: 6px;
    }

    .pill-count {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 12px;
    }

    .pill-going {
        background: #d1fae5;
        color: #065f46;
    }

    .pill-maybe {
        background: #fef3c7;
        color: #92400e;
    }

    .pill-no {
        background: #fee2e2;
        color: #991b1b;
    }

    .group-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin: 12px 0 8px 0;
    }

    .user-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .chip-user {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px 4px 4px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 500;
        color: #334155;
    }

    .chip-avatar {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        object-fit: cover;
    }

    .chip-initials {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #cbd5e1;
        color: #334155;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .empty-attendees {
        text-align: center;
        padding: 20px 10px;
        color: #94a3b8;
    }
</style>

<div class="offcanvas-event-wrapper">
    <!-- Header Cover Image Section -->
    <div class="event-hero-header">
        @if ($event->cover_image)
            <img src="{{ asset($event->cover_image) }}" alt="{{ $event->title }}" class="event-hero-img">
        @else
            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white-50">
                <i class="fa-solid fa-calendar-day fa-3x"></i>
            </div>
        @endif
        <div class="event-hero-overlay"></div>
        <span class="event-hero-badge">
            {{ ucwords(str_replace('_', ' ', $event->type)) }}
        </span>
    </div>

    <!-- Main Details Body -->
    <div class="event-body">
        <h3 class="event-title">{{ $event->title }}</h3>

        <!-- Meta Grid -->
        <div class="event-meta-grid">
            <div class="meta-card">
                <div class="meta-icon date">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div class="meta-text">
                    <label>Date & Time</label>
                    <span>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y \a\t g:i A') }}</span>
                </div>
            </div>

            @if ($event->location)
                <div class="meta-card">
                    <div class="meta-icon location">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="meta-text">
                        <label>Location</label>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            @endif

            @if ($event->creator)
                <div class="meta-card">
                    <div class="meta-icon creator">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div class="meta-text">
                        <label>Host</label>
                        <span>{{ $event->creator->id === auth()->id() ? 'You' : $event->creator->name }}</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Description Section -->
        @if ($event->description)
            <div class="event-description-box">
                <h6>About Event</h6>
                <p class="event-description">{!! nl2br(e($event->description)) !!}</p>
            </div>
        @endif

        <!-- RSVP & User Action Box -->
        <div class="rsvp-card">
            @php
                $currentUserAttendance = $event->attendances->firstWhere('user_id', auth()->id());
                $currentUserStatus = $currentUserAttendance ? $currentUserAttendance->status : null;
            @endphp

            @if ($currentUserStatus)
                <div class="status-alert {{ $currentUserStatus }}">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Your RSVP: <strong>{{ ucfirst($currentUserStatus) }}</strong></span>
                </div>
            @endif

            @if (Auth::id() !== $event->creator_user_id)
                <div class="rsvp-buttons">
                    <form action="{{ route('user.events.rsvp', $event->id) }}" method="POST" class="w-100"
                        onsubmit="return handleRsvp(event, 'going', '{{ $currentUserStatus === 'going' ? 'already' : '' }}')">
                        @csrf
                        <input type="hidden" name="status" value="going">
                        <button type="submit"
                            class="btn-rsvp btn-rsvp-going w-100 {{ $currentUserStatus === 'going' ? 'disabled' : '' }}"
                            {{ $currentUserStatus === 'going' ? 'disabled' : '' }}>
                            <i class="fa-solid fa-check"></i> Going
                        </button>
                    </form>

                    <form action="{{ route('user.events.rsvp', $event->id) }}" method="POST" class="w-100"
                        onsubmit="return handleRsvp(event, 'maybe', '{{ $currentUserStatus === 'maybe' ? 'already' : '' }}')">
                        @csrf
                        <input type="hidden" name="status" value="maybe">
                        <button type="submit"
                            class="btn-rsvp btn-rsvp-maybe w-100 {{ $currentUserStatus === 'maybe' ? 'disabled' : '' }}"
                            {{ $currentUserStatus === 'maybe' ? 'disabled' : '' }}>
                            <i class="fa-solid fa-question"></i> Maybe
                        </button>
                    </form>

                    <form action="{{ route('user.events.rsvp', $event->id) }}" method="POST" class="w-100"
                        onsubmit="return handleRsvp(event, 'no', '{{ $currentUserStatus === 'no' ? 'already' : '' }}')">
                        @csrf
                        <input type="hidden" name="status" value="no">
                        <button type="submit"
                            class="btn-rsvp btn-rsvp-no w-100 {{ $currentUserStatus === 'no' ? 'disabled' : '' }}"
                            {{ $currentUserStatus === 'no' ? 'disabled' : '' }}>
                            <i class="fa-solid fa-xmark"></i> No
                        </button>
                    </form>
                </div>
            @endif

            @if ($event->creator && $event->creator->id === auth()->id())
                <div class="creator-actions">
                    <a href="javascript:void(0)" class="edit-btn btn btn-sm btn-outline-secondary w-100 fw-semibold"
                        data-url="{{ route('user.events.edit', $event->id) }}">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Event
                    </a>
                    <form id="delete-form-{{ $event->id }}" action="{{ route('user.events.destroy', $event->id) }}"
                        method="POST" class="w-100">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-outline-danger w-100 fw-semibold"
                            onclick="confirmDelete('{{ $event->id }}')">
                            <i class="fa-solid fa-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Attendance List Box -->
        <div class="attendance-container">
            <div class="attendance-header">
                <h5 class="attendance-title">Responses</h5>
                <div class="attendance-pills">
                    <span class="pill-count pill-going">{{ $event->attendances->where('status', 'going')->count() }}
                        Going</span>
                    <span class="pill-count pill-maybe">{{ $event->attendances->where('status', 'maybe')->count() }}
                        Maybe</span>
                    <span class="pill-count pill-no">{{ $event->attendances->where('status', 'no')->count() }}
                        No</span>
                </div>
            </div>

            @php
                $goingUsers = $event->attendances->where('status', 'going');
                $maybeUsers = $event->attendances->where('status', 'maybe');
                $noUsers = $event->attendances->where('status', 'no');
            @endphp

            @if ($goingUsers->isNotEmpty())
                <div class="group-label">Going</div>
                <div class="user-chips">
                    @foreach ($goingUsers as $attendance)
                        <div class="chip-user">
                            @if ($attendance->user->image)
                                <img src="{{ asset($attendance->user->image) }}" alt="{{ $attendance->user->name }}"
                                    class="chip-avatar">
                            @else
                                <div class="chip-initials">{{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ $attendance->user->name }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($maybeUsers->isNotEmpty())
                <div class="group-label">Maybe</div>
                <div class="user-chips">
                    @foreach ($maybeUsers as $attendance)
                        <div class="chip-user">
                            @if ($attendance->user->image)
                                <img src="{{ asset($attendance->user->image) }}" alt="{{ $attendance->user->name }}"
                                    class="chip-avatar">
                            @else
                                <div class="chip-initials">{{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ $attendance->user->name }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($noUsers->isNotEmpty())
                <div class="group-label">Can't Attend</div>
                <div class="user-chips">
                    @foreach ($noUsers as $attendance)
                        <div class="chip-user">
                            @if ($attendance->user->image)
                                <img src="{{ asset($attendance->user->image) }}" alt="{{ $attendance->user->name }}"
                                    class="chip-avatar">
                            @else
                                <div class="chip-initials">{{ strtoupper(substr($attendance->user->name, 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ $attendance->user->name }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($goingUsers->isEmpty() && $maybeUsers->isEmpty() && $noUsers->isEmpty())
                <div class="empty-attendees">
                    <i class="fa-regular fa-paper-plane fa-2x mb-2 opacity-50"></i>
                    <p class="mb-0 fs-7">No responses yet. Be the first to respond!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- SweetAlert RSVP JS Helper -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function handleRsvp(event, status, alreadySubmitted) {
        if (alreadySubmitted === 'already') {
            event.preventDefault();
            let form = event.target;
            Swal.fire({
                title: 'Change RSVP?',
                text: 'You have already selected "' + status.charAt(0).toUpperCase() + status.slice(1) +
                    '". Do you want to confirm updating your response?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Yes, Update',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
            return false;
        }
        return true;
    }
</script>
