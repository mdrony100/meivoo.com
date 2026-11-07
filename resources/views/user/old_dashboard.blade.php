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
    </style>
@endpush
@section('content')

    <!-- ── MAIN CONTENT ── -->
    <main class="main-content">
        <div style="max-width:1200px;margin:0 auto;">

            <!-- Page title -->
            <div class="mb-4" data-aos="fade-up" data-aos-duration="500">
                <h1 class="page-title">Welcome back, <span class="text-gradient">{{ auth()->user()->name }}</span> 👋</h1>
                <p class="page-sub">Here's what's happening with your family today.</p>
            </div>

            <!-- ── STAT CARDS ── -->
            {{-- <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="0">
                    <div class="stat-card justify-content-between align-items-center">
                        <div>
                            <p class="stat-number">47</p>
                            <p class="stat-label">Family Members</p>
                            <p class="stat-note">3 added this month</p>
                        </div>
                        <div class="stat-icon" style="background:rgba(124,58,237,0.1);color:var(--primary);">
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="60">
                    <div class="stat-card justify-content-between align-items-center">
                        <div>
                            <p class="stat-number">284</p>
                            <p class="stat-label">Memories</p>
                            <p class="stat-note">12 new this week</p>
                        </div>
                        <div class="stat-icon" style="background:#fdf2f8;color:#ec4899;">
                            <i class="fa-solid fa-heart"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="120">
                    <div class="stat-card justify-content-between align-items-center">
                        <div>
                            <p class="stat-number">4</p>
                            <p class="stat-label">Upcoming Events</p>
                            <p class="stat-note">Next: May 22nd</p>
                        </div>
                        <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;">
                            <i class="fa-regular fa-calendar-days"></i>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="180">
                    <div class="stat-card justify-content-between align-items-center">
                        <div>
                            <p class="stat-number">3</p>
                            <p class="stat-label">Messages</p>
                            <p class="stat-note">Unread conversations</p>
                        </div>
                        <div class="stat-icon" style="background:rgba(6,182,212,0.1);color:var(--accent);">
                            <i class="fa-regular fa-comment-dots"></i>
                        </div>
                    </div>
                </div>
            </div> --}}

            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card" data-aos="fade-up">
                        <div class="stat-icon me-3" style="background:#eef0fd">
                            <i class="fa-solid fa-users" style="color:#4e6ef2"></i>
                        </div>
                        <div>
                            <div class="stat-label">Total Members</div>
                            <div class="stat-val">{{ $totalMembers ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card" data-aos="fade-up">
                        <div class="stat-icon me-3" style="background:#e1f5ee">
                            <i class="fa-solid fa-heart" style="color:#0f6e56"></i>
                        </div>
                        <div>
                            <div class="stat-label">Married Couples</div>
                            <div class="stat-val">{{ $marriedCouples ?? 0 }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="stat-card" data-aos="fade-up">
                        <div class="stat-icon me-3" style="background:#faece7">
                            <i class="fa-solid fa-child" style="color:#993c1d"></i>
                        </div>
                        <div>
                            <div class="stat-label">Children</div>
                            <div class="stat-val">{{ $childrenCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── FEED + RIGHT PANEL ── -->
            <div class="row g-3 g-lg-4">

                <!-- Feed col -->
                <div class="col-lg-8">
                    <div class="d-flex flex-column gap-3 gap-lg-4">
                        <div class="dash-card" data-aos="fade-up" data-aos-delay="100">
                            <form class="compose-box" action="{{ route('user.feed.posts.store') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="compose-top">
                                    <div class="compose-avatar">
                                        <img src="{{ getPhoto(auth()->user()->image) }}" alt="{{ auth()->user()->name }}">
                                    </div>
                                    <textarea class="compose-input" id="composeInput" required name="content" placeholder="Share a family memory..."
                                        rows="1">{{ old('content') }}</textarea>
                                </div>

                                <div class="compose-actions">
                                    <label class="compose-btn mb-0" for="imagesInput">
                                        <i class="fa-regular fa-image fa-sm"></i>
                                        Photo
                                    </label>
                                    <input type="file" id="imagesInput" name="images[]" class="d-none" accept="image/*"
                                        multiple>

                                    <label class="compose-btn mb-0" for="audioInput">
                                        <i class="fa-solid fa-microphone fa-sm"></i>
                                        Voice <span>Memory</span>
                                    </label>
                                    <input type="file" id="audioInput" name="audio" class="d-none" accept="audio/*">

                                    <button class="btn-post" type="submit">Post</button>
                                </div>

                                <div class="mt-2 small text-muted">
                                    <span id="imagesSelectedText"></span>
                                    <span id="audioSelectedText" class="ms-2"></span>
                                </div>
                            </form>
                        </div>

                        @forelse ($posts as $post)
                            <div class="post-card" data-aos="fade-up" data-aos-delay="200">
                                <div class="post-header mb-2 border-bottom border-light">
                                    <div class="post-av">
                                        <img src="{{ getPhoto($post->user->image) }}"
                                            alt="{{ $post->user->name ?? 'User' }}">
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <p class="post-av-name">{{ $post->user->name }}</p>
                                        <p class="post-av-time">{{ $post->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if ((int) $post->user_id === (int) auth()->id())
                                        <form action="{{ route('user.feed.posts.destroy', $post) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    @endif
                                </div>

                                @if ($post->content)
                                    <div class="post-body">
                                        {{ $post->content }}
                                    </div>
                                @endif

                                @php
                                    $imageMedia = $post->media->where('file_type', 'image');
                                    $audioMedia = $post->media->where('file_type', 'audio')->first();
                                    $likedByMe = $post->likes->contains('user_id', auth()->id());
                                @endphp

                                @if ($imageMedia->count())
                                    <div class="post-images mb-3 p-3 js-lightgallery"
                                        id="post-gallery-{{ $post->id }}">
                                        <div class="row">
                                            @foreach ($imageMedia as $media)
                                                <div class="col-6 col-md-4 col-lg-4 col-xl-3 mb-3">
                                                    <a href="{{ asset($media->file_path) }}"
                                                        data-src="{{ asset($media->file_path) }}">
                                                        <img src="{{ asset($media->file_path) }}" alt="Post image"
                                                            style="width:100%;border-radius:12px;object-fit:cover;max-height:320px;">
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($audioMedia)
                                    <div class="mb-3 p-3">
                                        <audio controls style="width:100%;">
                                            <source src="{{ asset($audioMedia->file_path) }}">
                                        </audio>
                                    </div>
                                @endif

                                <div class="post-reactions">
                                    <span class="count">{{ $post->likes_count }} reactions</span>
                                    <span class="comments-count">{{ $post->comments_count }} comments</span>
                                </div>

                                <div class="post-actions-bar">
                                    <form action="{{ route('user.feed.likes.toggle', $post) }}" method="POST">
                                        @csrf
                                        <button class="post-action-btn" type="submit">
                                            <i class="{{ $likedByMe ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
                                            {{ $likedByMe ? 'Liked' : 'React' }}
                                        </button>
                                    </form>
                                </div>

                                <div class="comment-section" style="display:block;">
                                    @foreach ($post->comments as $comment)
                                        <div class="comment-item">
                                            <div class="comment-av">
                                                <img src="{{ getPhoto($comment->user->image ?? null) }}"
                                                    alt="{{ $comment->user->name ?? 'User' }}">
                                            </div>

                                            <div style="flex:1;">
                                                <div class="comment-bubble">
                                                    <p class="comment-name">{{ $comment->user->name ?? 'Unknown User' }}
                                                    </p>
                                                    <p class="comment-text">{{ $comment->comment }}</p>
                                                </div>

                                                <div class="comment-meta">
                                                    <span
                                                        class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>

                                                    <button type="button" class="btn btn-sm btn-link p-0"
                                                        onclick="toggleReplySmooth('reply-form-{{ $comment->id }}')">
                                                        Reply
                                                    </button>
                                                </div>
                                                <div id="reply-form-{{ $comment->id }}" class="reply-form-wrapper">
                                                    <form
                                                        action="{{ route('user.feed.comments.reply', [$post, $comment]) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="input-group">
                                                            <input type="text" name="comment"
                                                                class="comment-input-field rounded-pill rounded-end-0"
                                                                placeholder="Write a reply..." required>
                                                            <button type="submit"
                                                                class="btn btn-primary btn-sm rounded-pill rounded-start-0 px-4">Sent</button>
                                                        </div>
                                                    </form>
                                                </div>

                                                @foreach ($comment->replies->sortBy('id') as $reply)
                                                    <div class="comment-replies mt-3">
                                                        <div class="comment-item">
                                                            <div class="comment-av">
                                                                <img src="{{ getPhoto($reply->user->image) }}"
                                                                    alt="{{ $reply->user->name }}">
                                                            </div>
                                                            <div style="flex:1;">
                                                                <div class="comment-bubble">
                                                                    <p class="comment-name">
                                                                        {{ $reply->user->name }}</p>
                                                                    <p class="comment-text">{{ $reply->comment }}</p>
                                                                </div>
                                                                <div class="comment-meta">
                                                                    <span
                                                                        class="comment-time">{{ $reply->created_at->diffForHumans() }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach


                                            </div>
                                        </div>
                                    @endforeach

                                    <form class="comment-compose mt-2"
                                        action="{{ route('user.feed.comments.store', $post) }}" method="POST">
                                        @csrf
                                        <div class="comment-av me-2">
                                            <img src="{{ getPhoto(auth()->user()->image) }}"
                                                alt="{{ auth()->user()->name }}">
                                        </div>
                                        <input type="text" name="comment"
                                            class="comment-input-field rounded-pill rounded-end-0"
                                            placeholder="Write a comment..." required>
                                        <button type="submit"
                                            class="btn btn-primary btn-sm rounded-pill rounded-start-0 px-4">Sent</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="dash-card text-center" data-aos="fade-up" data-aos-delay="200">
                                <p class="mb-0 text-muted p-4">No posts yet. Share your first family update.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Right panel -->
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4 right-panel" data-aos="fade-up" data-aos-delay="100">

                        <!-- Birthdays -->
                        <div class="widget-card">

                            <p class="widget-title">
                                <span>🎂</span> This Month Birthdays
                            </p>

                            @forelse($members as $member)
                                <div class="bday-item">
                                    <div class="bday-av">
                                        @if ($member->user?->image)
                                            <img src="{{ asset($member->user?->image) }}"
                                                alt="{{ $member->user?->name }}">
                                        @else
                                            <div class="avatar-fallback">
                                                {{ strtoupper(substr($member->user?->name, 0, 2)) }}
                                            </div>
                                        @endif
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

                        <!-- Quick actions -->
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
                            <a href="{{ route('user.feed') }}" class="quick-btn amber">
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-mic-icon lucide-mic">
                                        <path d="M12 19v3" />
                                        <path d="M19 10v2a7 7 0 0 1-14 0v-2" />
                                        <rect x="9" y="2" width="6" height="13" rx="3" />
                                    </svg>
                                </span>
                                Record Voice Memory
                            </a>
                        </div>

                        <!-- Recent activity -->
                        <div class="widget-card" data-aos="fade-up" data-aos-delay="120">
                            <p class="widget-title">Recent Activity</p>
                            
                            <div class="activity-item">
                                <span class="activity-emoji">📸</span>
                                <div>
                                    <p class="activity-text">Emma uploaded 3 new photos</p>
                                    <p class="activity-time">2h ago</p>
                                </div>
                            </div>
                            
                            <div class="activity-item">
                                <span class="activity-emoji">🎙️</span>
                                <div>
                                    <p class="activity-text">Dorothy added a voice memory</p>
                                    <p class="activity-time">1d ago</p>
                                </div>
                            </div>

                            <div class="activity-item">
                                <span class="activity-emoji">👤</span>
                                <div>
                                    <p class="activity-text">James joined the family tree</p>
                                    <p class="activity-time">2d ago</p>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>

            </div><!-- /row -->
        </div><!-- /max-width -->
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const composeInput = document.getElementById('composeInput');
            const imagesInput = document.getElementById('imagesInput');
            const audioInput = document.getElementById('audioInput');
            const imagesSelectedText = document.getElementById('imagesSelectedText');
            const audioSelectedText = document.getElementById('audioSelectedText');
            const galleries = document.querySelectorAll('.js-lightgallery');

            if (composeInput) {
                composeInput.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            }

            if (imagesInput) {
                imagesInput.addEventListener('change', function() {
                    const count = this.files ? this.files.length : 0;
                    imagesSelectedText.textContent = count ? count + ' image(s) selected' : '';
                });
            }

            if (audioInput) {
                audioInput.addEventListener('change', function() {
                    audioSelectedText.textContent = this.files && this.files[0] ? this.files[0].name : '';
                });
            }

            galleries.forEach(function(gallery) {
                lightGallery(gallery, {
                    selector: 'a',
                    plugins: [lgZoom, lgThumbnail],
                    speed: 300,
                    download: false,
                });
            });
        });
    </script>

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
@endpush
