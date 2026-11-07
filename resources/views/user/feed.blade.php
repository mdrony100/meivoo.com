@extends('layouts.user')
@section('title', 'LegacyTree - Family Feed')

@section('meta')
    <meta property="og:title" content="{{ $settings->seo_meta_title }}">
    <meta property="og:description" content="{{ $settings->seo_meta_description }}">
    <meta name="description" content="{{ $settings->seo_meta_description }}">
    <meta name="keywords" content="{{ $settings->seo_keywords }}">
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css" />
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

        @media screen and (max-width:768px) {
            .recording-panel {
                display: block;
            }
        }

        .post-action-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.3s ease;
        }

        .post-action-btn.liked {
            color: #7c3aed;
            font-weight: bold;
        }

        .post-action-btn.liked i {
            color: #7c3aed;
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

        .comment-wrapper {
            transition: all 0.4s ease-in-out;
            opacity: 1;
            max-height: 1000px;
            overflow: hidden;
        }

        .comment-wrapper.hidden-comment {
            opacity: 0;
            max-height: 0;
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
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
    @auth
        <main class="main-content">
            <div class="mb-4" data-aos="fade-up" data-aos-duration="500">
                <h1 class="page-title">Family Feed</h1>
                <p class="page-sub">Recent memories, updates, and conversations from the family.</p>
            </div>

            <div class="row">
                <div class="col-xl-4">
                    <div class="sticky-top" style="top:5rem; z-index: 1">
                        <div class="dash-card mb-3" data-aos="fade-up" data-aos-delay="0">
                            @include('user.partials.create_post')
                        </div>

                        <div class="row row-cards mb-4 d-none d-md-flex">
                            <div class="col-sm-6 mb-3" data-aos="fade-up" data-aos-delay="100">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="h3 mb-0">{{ number_format($stats['posts']) }}</div>
                                                <div class="text-secondary">Total Posts</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3" data-aos="fade-up" data-aos-delay="100">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="h3 mb-0">{{ number_format($stats['photos']) }}</div>
                                                <div class="text-secondary">Photos</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 mb-3" data-aos="fade-up" data-aos-delay="200">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <div class="h3 mb-0">{{ number_format($stats['reactions']) }}</div>
                                                <div class="text-secondary">Reactions</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Upcoming Events Widget with #984bf3 Primary Color -->
                        <div class="card border-0 shadow-sm rounded-4 mt-3 mb-4" data-aos="fade-up" data-aos-delay="300">
                            <div class="card-body p-3">
                                <!-- Header -->
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold mb-0 text-dark">Upcoming Events</h6>
                                    @if (isset($upcomingEvents) && $upcomingEvents->count() > 0)
                                        <a href="{{ route('user.events') }}" class="text-decoration-none small fw-semibold"
                                            style="color: #984bf3;">View All</a>
                                    @endif
                                </div>

                                <!-- Dynamic Event List -->
                                @if (isset($upcomingEvents) && $upcomingEvents->isNotEmpty())
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
                                @else
                                    <!-- Empty State -->
                                    <div class="text-center py-2 text-muted">
                                        <span class="small">No upcoming events found</span>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    @forelse ($posts as $post)
                        @include('user.partials.post')
                    @empty
                        <div class="dash-card text-center">
                            <p class="mb-0 text-muted p-4">No posts yet. Share your first family update.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    @else
        <div
            style="position: fixed; top: 65px; left: 0; width: 100%; height: calc(100vh - 65px); z-index: 90; background: #f8f9fa; overflow-y: auto;">
            <div class="hero-container px-3 py-4 d-flex align-items-center justify-content-center" style="min-height: 100%;">
                <div class="hero-content text-center my-auto" style="max-width: 550px; width: 100%;">

                    <!-- Icon -->
                    <div class="mb-3 mb-md-4">
                        <i class="fa-solid fa-tree fa-4x fa-md-5x" style="color: #7c3aed;"></i>
                    </div>

                    <!-- Title -->
                    <h1 class="fw-bold mb-3 fs-2 fs-md-1" style="color: #1a1a2e;">
                        Welcome to {{ $settings->site_name }}
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-muted mb-4 mb-md-5 lead fs-6 fs-md-5">
                        Your family memories, preserved for generations.
                    </p>

                    <!-- Buttons -->
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 gap-sm-3">
                        <a href="{{ route('login') }}" class="btn btn-lg px-4 px-md-5 text-white"
                            style="background-color: #7c3aed; border-radius: 50px;">
                            Get Started
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-lg px-4 px-md-5 btn-outline-secondary"
                            style="border-radius: 50px;">
                            Create Account
                        </a>
                    </div>

                </div>
            </div>
        </div>
    @endauth


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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        document.querySelectorAll('.toggle-comments').forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.getAttribute('data-post-id');
                const container = document.getElementById('comment-section-' + postId);
                const hiddenComments = container.querySelectorAll('.hidden-comment');

                if (hiddenComments.length > 0) {
                    hiddenComments.forEach(el => el.classList.remove('hidden-comment'));
                    this.innerText = 'View less comments';
                } else {
                    const allComments = container.querySelectorAll('.comment-wrapper');
                    allComments.forEach((el, index) => {
                        if (index >= 2) {
                            el.classList.add('hidden-comment');
                        }
                    });
                    this.innerText = 'View more comments';
                }
            });
        });
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
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            let postId = urlParams.get('view-memories');

            @if (session('view-memories'))
                postId = "{{ session('view-memories') }}";
            @endif

            if (postId) {
                let url = "{{ route('user.posts.show', ':id') }}".replace(':id', postId);
                $.get(url + '?action=share', function(data) {
                    $('#view-modal-content').html(data);
                    $('#viewModal').modal('show');
                });
            }

            $('#viewModal').on('hidden.bs.modal', function() {
                    @guest
                    window.location.href = "{{ route('frontend.home') }}";
                @endguest
            });
        });
    </script>

    <script>
        function confirmDelete(postId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this post?",
                icon: 'warning',
                width: 400,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#7c3aed',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + postId).submit();
                }
            })
        }
    </script>
@endpush
