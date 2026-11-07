@extends('layouts.user')
@section('title', 'LegacyTree – Dashboard')
@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
    <style>
        .reply-form-wrapper {
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: all 0.4s ease-in-out;
            visibility: hidden;
        }

        .reply-form-wrapper.is-open {
            max-height: 100px;
            opacity: 1;
            visibility: visible;
            margin-top: 10px;
        }

        .dashboard-hero {
            background:
                radial-gradient(circle at top right, rgba(78, 110, 242, 0.14), transparent 36%),
                linear-gradient(135deg, #ffffff 0%, #faf7ff 100%);
            border: 1px solid rgba(128, 138, 199, 0.14);
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 18px 45px rgba(18, 38, 63, 0.06);
        }

        .timeline-media-list {
            display: grid;
            gap: 14px;
        }

        .timeline-media-grid {
            display: grid;
            gap: 12px;
        }

        .timeline-media-grid-1 {
            grid-template-columns: 1fr;
        }

        .timeline-media-grid-2,
        .timeline-media-grid-3,
        .timeline-media-grid-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .timeline-media-item {
            display: block;
            overflow: hidden;
            border-radius: 18px;
            background: #f4f6fb;
            position: relative;
        }

        .timeline-media-item img {
            width: 100%;
            display: block;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            object-position: center;
        }

        .timeline-media-grid-1 .timeline-media-item img {
            aspect-ratio: 16 / 10;
        }

        .timeline-media-grid-3 .timeline-media-item:first-child {
            grid-column: 1 / -1;
        }

        .timeline-media-grid-3 .timeline-media-item:first-child img {
            aspect-ratio: 16 / 10;
        }

        .media-more-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.12), rgba(15, 23, 42, 0.72));
            color: #fff;
            font-size: clamp(1.4rem, 2vw, 2rem);
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .media-more-overlay span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 76px;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(6px);
            font-size: 23px;
            font-weight: 300;
        }

        .post-body {
            margin-right: 10px;
            padding-right: 4px;
            margin-bottom: 14px;
            color: #334155;
            line-height: 1.8;
            font-size: 0.98rem;
        }

        .post-media-shell {
            padding: 17px;
            margin-bottom: 14px;
            border-radius: 18px;
        }

        .recording-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid rgba(220, 38, 38, 0.18);
            background: rgba(220, 38, 38, 0.08);
            color: #b91c1c;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .recording-panel {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding: 14px 16px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(220, 38, 38, 0.10), rgba(248, 113, 113, 0.08));
            border: 1px solid rgba(220, 38, 38, 0.16);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55);
        }

        .recording-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .recording-badge {
            width: 42px;
            height: 42px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #dc2626;
            color: #fff;
            flex: 0 0 auto;
            box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.35);
            animation: pulseRed 1.6s infinite;
        }

        .recording-title {
            margin: 0;
            font-weight: 700;
            color: #991b1b;
            line-height: 1.2;
        }

        .recording-subtitle {
            margin: 3px 0 0;
            font-size: 0.86rem;
            color: #b91c1c;
        }

        .recording-wave {
            display: inline-flex;
            align-items: flex-end;
            gap: 3px;
            height: 18px;
            margin-left: 10px;
        }

        .recording-wave span {
            width: 3px;
            border-radius: 999px;
            background: #dc2626;
            animation: wavePulse 1s infinite ease-in-out;
        }

        .recording-wave span:nth-child(1) {
            height: 6px;
            animation-delay: 0s;
        }

        .recording-wave span:nth-child(2) {
            height: 12px;
            animation-delay: 0.12s;
        }

        .recording-wave span:nth-child(3) {
            height: 16px;
            animation-delay: 0.24s;
        }

        .recording-wave span:nth-child(4) {
            height: 10px;
            animation-delay: 0.36s;
        }

        .recording-wave span:nth-child(5) {
            height: 14px;
            animation-delay: 0.48s;
        }

        .recording-wave span:nth-child(6) {
            height: 8px;
            animation-delay: 0.60s;
        }

        .recording-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            flex: 0 0 auto;
        }

        .recording-cancel-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            border-radius: 999px;
            border: 1px solid rgba(220, 38, 38, 0.18);
            background: #fff;
            color: #b91c1c;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .recording-cancel-btn:hover {
            background: rgba(220, 38, 38, 0.08);
            border-color: rgba(220, 38, 38, 0.28);
            color: #991b1b;
        }

        .recorded-preview-tools {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 12px;
            padding: 12px 14px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .recorded-preview-info {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .recorded-preview-info .name {
            margin: 0;
            font-weight: 700;
            color: #0f172a;
        }

        .recorded-preview-info .meta {
            margin: 2px 0 0;
            font-size: 0.85rem;
            color: #64748b;
        }

        .compose-btn.btn-danger {
            background: #dc2626;
            border-color: #dc2626;
            color: #fff;
        }

        .compose-btn.btn-danger:hover,
        .compose-btn.btn-danger:focus {
            background: #b91c1c;
            border-color: #b91c1c;
            color: #fff;
        }

        @keyframes pulseRed {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.38);
            }

            70% {
                box-shadow: 0 0 0 12px rgba(220, 38, 38, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 38, 38, 0);
            }
        }

        @keyframes wavePulse {

            0%,
            100% {
                transform: scaleY(0.55);
                opacity: 0.55;
            }

            50% {
                transform: scaleY(1);
                opacity: 1;
            }
        }

        .reaction-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #f8fafc;
            color: #475569;
            font-weight: 600;
            font-size: 0.86rem;
        }

        .post-actions-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            padding: 8px 0 2px;
        }

        .post-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #334155;
            border-radius: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .post-action-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .post-footer {
            display: grid;
            gap: 14px;
            margin-top: 14px;
            padding: 14px;
            border-radius: 20px;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            border: 1px solid #e2e8f0;
        }

        .comment-stream {
            display: grid;
            gap: 12px;
        }

        .comment-item {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .comment-bubble {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 12px 14px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.03);
        }

        .comment-replies {
            margin-left: 42px;
            padding-left: 14px;
            border-left: 2px solid #e2e8f0;
            display: grid;
            gap: 12px;
        }

        .comment-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 6px;
            flex-wrap: wrap;
        }

        .comment-meta .btn-link {
            text-decoration: none;
            font-weight: 600;
            color: #4e6ef2;
        }

        .dropdown-item {
            transition: all 0.3s ease;
            color: #495057;
        }

        .dropdown-item:hover {
            background-color: #7c3aed23 !important;
            color: #7c3aed !important;
        }

        .dropdown-menu {
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .text-area {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .text-area.show-all {
            -webkit-line-clamp: unset;
        }

        /* Modern Recording Panel Styling */
        .rec-dot {
            width: 10px;
            height: 10px;
            background-color: #ef4444;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-red 1.5s infinite;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* Audio Wave Bars */
        .rec-wave-container .wave-bar {
            width: 3px;
            height: 12px;
            background-color: #984bf3;
            border-radius: 2px;
            animation: sound-wave 1.2s infinite ease-in-out;
        }

        .rec-wave-container .wave-bar:nth-child(1) {
            animation-delay: 0.1s;
        }

        .rec-wave-container .wave-bar:nth-child(2) {
            animation-delay: 0.3s;
        }

        .rec-wave-container .wave-bar:nth-child(3) {
            animation-delay: 0.2s;
        }

        .rec-wave-container .wave-bar:nth-child(4) {
            animation-delay: 0.4s;
        }

        @keyframes sound-wave {

            0%,
            100% {
                height: 4px;
            }

            50% {
                height: 16px;
            }
        }

        /* Hover effect on close button */
        .hover-danger:hover {
            background-color: #fee2e2 !important;
            color: #ef4444 !important;
        }

        .fs-7 {
            font-size: 0.875rem;
        }

        .fs-8 {
            font-size: 0.75rem;
        }

        /* Recorded Preview Responsive Fixes */
        #recordedPreviewTools {
            width: 100% !important;
            box-sizing: border-box;
        }

        #recordedPreviewTools .min-w-0 {
            min-width: 0 !important;
        }


        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
@endpush
@section('content')
    <!-- ── MAIN CONTENT ── -->
    <main class="main-content">
        <!-- Page title -->
        <div class="dashboard-hero mb-4" data-aos="fade-up" data-aos-duration="500">
            <div class="d-flex flex-column align-items-center flex-lg-row justify-content-between gap-3">
                <div>
                    <h1 class="page-title mb-2">Welcome back, <span class="text-gradient">{{ auth()->user()->name }}</span>
                        👋
                    </h1>
                    <p class="page-sub mb-0">Your private memory timeline lives here.</p>
                </div>
                {{-- <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('user.settings') }}" class="btn-post">
                        <i class="fa-solid fa-pen-to-square me-1"></i>
                        Edit Profile
                    </a>
                </div> --}}
            </div>
        </div>


        <div class="row g-2 g-sm-3 mb-4">
            {{-- Total Members --}}
            <div class="col-6 col-lg-3">
                <div class="stat-card d-flex align-items-center p-3 p-md-4" data-aos="fade-up">
                    <div class="stat-icon me-2 me-sm-3 flex-shrink-0" style="background:#eef0fd">
                        <i class="fa-solid fa-users" style="color:#4e6ef2"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-label text-truncate">Total Members</div>
                        <div class="stat-val">{{ $totalMembers ?? 0 }}</div>
                    </div>
                </div>
            </div>

            {{-- Married Couples --}}
            <div class="col-6 col-lg-3">
                <div class="stat-card d-flex align-items-center p-3 p-md-4" data-aos="fade-up">
                    <div class="stat-icon me-2 me-sm-3 flex-shrink-0" style="background:#e1f5ee">
                        <i class="fa-solid fa-heart" style="color:#0f6e56"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-label text-truncate">Married Couples</div>
                        <div class="stat-val">{{ $marriedCouples ?? 0 }}</div>
                    </div>
                </div>
            </div>

            {{-- Children --}}
            <div class="col-6 col-lg-3">
                <div class="stat-card d-flex align-items-center p-3 p-md-4" data-aos="fade-up">
                    <div class="stat-icon me-2 me-sm-3 flex-shrink-0" style="background:#faece7">
                        <i class="fa-solid fa-child" style="color:#993c1d"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-label text-truncate">Children</div>
                        <div class="stat-val">{{ $childrenCount ?? 0 }}</div>
                    </div>
                </div>
            </div>

            {{-- Total Memories --}}
            <div class="col-6 col-lg-3">
                <div class="stat-card d-flex align-items-center p-3 p-md-4" data-aos="fade-up">
                    <div class="stat-icon me-2 me-sm-3 flex-shrink-0" style="background:#eef0fd">
                        <i class="fa-solid fa-images" style="color:#4e6ef2"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="stat-label text-truncate">Total Memories</div>
                        <div class="stat-val">{{ isset($posts) ? $posts->count() : 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── FEED + RIGHT PANEL ── -->
        <div class="row g-3 g-lg-4">

            <!-- Feed col -->
            <div class="col-lg-8">
                {{-- <div class="card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header py-3">
                        <h4 class="card-title m-0">Your Recent Memories</h4>
                    </div> --}}
                <div class="card-body">
                    <div class="d-flex flex-column gap-3 gap-lg-4">
                        <div id="mobilePostPlace" class="d-block d-lg-none mb-3" data-aos="fade-up" data-aos-delay="100">
                        </div>

                        @forelse ($posts as $post)
                            <div class="post-card" data-aos="fade-up" data-aos-delay="200">
                                @if ($post->shared_post_id)
                                    @include('user.partials.shared_post', ['post' => $post])
                                @else
                                    @include('user.partials.main_post', ['post' => $post])
                                @endif
                                <div class="post-reactions">
                                    <span class="count text-bold">{{ $post->likes_count }} reactions</span>
                                    <span class="comments-count text-bold">{{ $post->comments_count }} comments</span>
                                    <span>
                                        <a href="javascript:void(0)"
                                            class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 me-2 view-btn"
                                            style="font-size: 0.75rem;"
                                            data-url="{{ route('user.posts.show', $post->id) }}">
                                            <i class="fa-solid fa-share"></i> Share
                                        </a>
                                    </span>
                                </div>
                            </div>
                            {{-- @include('user.partials.post') --}}
                        @empty
                            <div class="dash-card empty-state text-center" data-aos="fade-up" data-aos-delay="200">
                                <div class="empty-icon">
                                    <i class="ti ti-photo-heart"></i>
                                </div>

                                <h4 class="mt-3 mb-2">No Memories Yet</h4>

                                <p class="text-muted mb-4">
                                    Your family memories will appear here. Share your first photo,
                                    story, or special moment from the dashboard.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
                {{-- </div> --}}

            </div>

            <!-- Right panel -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 90px;">

                    <div class="d-flex flex-column gap-3 right-panel" data-aos="fade-up" data-aos-delay="100">
                        <div id="desktopPostPlace" class="d-none d-lg-block">

                            <div id="postFormContainer" class="dash-card">
                                @include('user.partials.create_post')
                            </div>
                        </div>

                        <!-- Birthdays -->
                        <div class="widget-card">

                            <p class="widget-title">
                                <span>🎂</span> This Month Birthdays
                            </p>

                            @forelse($members as $member)
                                <div class="bday-item">
                                    <div class="bday-av">
                                        <a href="{{ route('user.member.profile', $member->id) }}"
                                            class="text-decoration-none text-white">
                                            @if ($member->user?->image)
                                                <img src="{{ asset($member->user?->image) }}"
                                                    alt="{{ $member->user?->name }}">
                                            @else
                                                <div class="avatar-fallback">
                                                    {{ strtoupper(substr($member->user?->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </a>
                                    </div>

                                    <div style="flex:1;min-width:0;">
                                        <p class="bday-name">
                                            {{ $member->user?->name }}
                                        </p>

                                        <p class="bday-date">
                                            {{ \Carbon\Carbon::parse($member->birth_date)->format('M d') }}
                                        </p>
                                    </div>
                                    @if ($member->days_left == 0)
                                        <span class="bday-badge today">Today 🎉</span>
                                    @elseif($member->days_left == 1)
                                        <span class="bday-badge soon">Tomorrow</span>
                                    @else
                                        <span class="bday-badge later">{{ $member->days_left }} Day</span>
                                    @endif
                                </div>
                            @empty
                                <p style="padding:10px;color:#999;">
                                    No birthdays this month 🎂
                                </p>
                            @endforelse

                        </div>

                        {{-- Upcoming Events --}}
                        @if (isset($upcomingEvents) && $upcomingEvents->isNotEmpty())
                            <div class="card border-0 shadow-sm rounded-4" data-aos="fade-up" data-aos-delay="300">
                                <div class="card-body p-3">
                                    <!-- Header -->
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold mb-0 text-dark">Upcoming Events</h6>
                                        @if (isset($upcomingEvents) && $upcomingEvents->count() > 0)
                                            <a href="{{ route('user.events') }}"
                                                class="text-decoration-none small fw-semibold"
                                                style="color: #984bf3;">View All</a>
                                        @endif
                                    </div>


                                    @foreach ($upcomingEvents as $event)
                                        @php
                                            $eventDate = \Carbon\Carbon::parse($event->event_date);
                                        @endphp

                                        <div class="d-flex align-items-center mb-3 w-100 overflow-hidden">
                                            <!-- Event Date Badge -->
                                            <div class="rounded-3 p-2 text-center me-3 flex-shrink-0"
                                                style="background-color: #f5edff; color: #984bf3; min-width: 48px;">
                                                <span class="d-block fw-bold text-uppercase extra-small"
                                                    style="font-size: 11px;">
                                                    {{ $eventDate->format('M') }}
                                                </span>
                                                <span class="d-block h6 mb-0 fw-bold">
                                                    {{ $eventDate->format('d') }}
                                                </span>
                                            </div>

                                            <!-- Event Info Container -->
                                            <div class="flex-grow-1" style="min-width: 0;">
                                                <!-- Title -->
                                                <div class="fw-semibold text-dark small text-truncate"
                                                    title="{{ $event->title }}">
                                                    {{ $event->title }}
                                                </div>

                                                <!-- Creator Name / Subtitle -->
                                                <div class="text-secondary text-truncate" style="font-size: 11px;">
                                                    By <span
                                                        class="fw-medium text-dark">{{ $event->creator->name ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach


                                </div>
                            </div>
                        @else
                            <div class="widget-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="60">
                                <p class="widget-title">Quick Actions</p>
                                <a href="{{ route('user.members') }}" class="quick-btn purple">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-users-icon lucide-users">
                                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                            <path d="M16 3.128a4 4 0 0 1 0 7.744" />
                                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                            <circle cx="9" cy="7" r="4" />
                                        </svg>
                                    </span>
                                    Add Family Member</a>
                                <a href="{{ route('user.family-tree') }}" class="quick-btn amber">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" data-lucide="git-branch-plus"
                                            aria-hidden="true" class="lucide lucide-git-branch-plus">
                                            <path d="M6 3v12"></path>
                                            <path d="M18 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
                                            <path d="M6 21a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
                                            <path d="M15 6a9 9 0 0 0-9 9"></path>
                                            <path d="M18 15v6"></path>
                                            <path d="M21 18h-6"></path>
                                        </svg>
                                    </span>
                                    Family Tree
                                </a>
                            </div>
                        @endif

                        <!-- Recent activity -->
                        <div class="widget-card" data-aos="fade-up" data-aos-delay="120">
                            <p class="widget-title">Recent Activity</p>
                            @forelse($recentActivities as $activity)
                                <div class="activity-item">
                                    <span class="activity-emoji">
                                        <i class="{{ $activity['icon'] }}"></i>
                                    </span>
                                    <div>
                                        <p class="activity-text">{{ $activity['title'] }}</p>
                                        <p class="activity-time">
                                            {{ $activity['subtitle'] }} &middot; {{ $activity['time'] }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="activity-item">
                                    <span class="activity-emoji">
                                        <i class="fa-regular fa-clock"></i>
                                    </span>
                                    <div>
                                        <p class="activity-text">No recent activity yet</p>
                                        <p class="activity-time">New posts, members, and relations will appear here.
                                        </p>
                                    </div>
                                </div>
                            @endforelse

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>


    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share Post</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="view-modal-content">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.min.js"></script>

    <script>
        function toggleReplySmooth(formId) {
            const formWrapper = document.getElementById(formId);

            formWrapper.classList.toggle('is-open');

            if (formWrapper.classList.contains('is-open')) {
                setTimeout(() => {
                    formWrapper.querySelector('input').focus();
                }, 100);
            }
        }
    </script>
    <script>
        function sharePost(url, title) {
            if (navigator.share) {
                navigator.share({
                    title: title,
                    url: url
                }).then(() => {
                    console.log('Thanks for sharing!');
                }).catch(console.error);
            } else {
                navigator.clipboard.writeText(url).then(() => {
                    alert("Link copied to clipboard!");
                });
            }
        }
    </script>
    <script>
        function updatePrivacy(value, iconClass) {
            document.getElementById('privacyValue').value = value;

            const button = document.getElementById('privacyBtn');
            button.innerHTML = `<i class="${iconClass}"></i> ${value.charAt(0).toUpperCase() + value.slice(1)}`;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.text-area').forEach(div => {
                if (div.scrollHeight > 70) {
                    div.nextElementSibling.style.display = 'block';
                } else {
                    div.nextElementSibling.style.display = 'none';
                }
            });
        });

        function toggleText(btn) {
            let textDiv = btn.previousElementSibling;
            textDiv.classList.toggle('show-all');
            btn.innerText = textDiv.classList.contains('show-all') ? 'See less' : 'See more';
        }
    </script>

    <script>
        $(document).on('click', '.view-btn', function() {
            let url = $(this).data('url');
            $.get(url, function(data) {
                $('#view-modal-content').html(data);
                $('#viewModal').modal('show');
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            function arrangePostForm() {
                let formContainer = document.getElementById('postFormContainer');
                let mobilePlace = document.getElementById('mobilePostPlace');
                let desktopPlace = document.getElementById('desktopPostPlace');

                if (!formContainer || !mobilePlace || !desktopPlace) return;

                if (window.innerWidth < 992) {
                    if (!mobilePlace.contains(formContainer)) {
                        mobilePlace.appendChild(formContainer);
                    }
                } else {
                    if (!desktopPlace.contains(formContainer)) {
                        desktopPlace.appendChild(formContainer);
                    }
                }
            }

            arrangePostForm();

            window.addEventListener('resize', arrangePostForm);
        });
    </script>
@endpush
