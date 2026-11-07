@extends('layouts.frontend')
@section('title', 'LegacyTree - Public Profile')
@section('hide_navbar')
@endsection
@section('hide_footer')
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
    <style>
        .profile-shell {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 16px 72px;
        }

        .profile-hero {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            background:
                radial-gradient(circle at top right, rgba(78, 110, 242, 0.20), transparent 32%),
                linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid rgba(128, 138, 199, 0.12);
            box-shadow: 0 18px 45px rgba(18, 38, 63, 0.08);
        }

        .profile-hero-cover {
            height: 170px;
            background:
                linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(78, 110, 242, 0.70)),
                url('https://images.unsplash.com/photo-1511895426328-dc8714191300?auto=format&fit=crop&w=1400&q=80');
            background-size: cover;
            background-position: center;
        }

        .profile-hero-body {
            padding: 0 24px 24px;
            margin-top: -54px;
        }

        .profile-avatar {
            width: 108px;
            height: 108px;
            border-radius: 28px;
            border: 5px solid #fff;
            object-fit: cover;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.14);
            background: #fff;
        }

        .profile-name {
            font-size: clamp(1.8rem, 3vw, 2.5rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin: 0;
            color: #0f172a;
        }

        .profile-subtitle {
            color: #64748b;
            margin: 6px 0 0;
        }

        .profile-meta-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: 20px;
        }

        .meta-card {
            padding: 14px 16px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid rgba(148, 163, 184, 0.16);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.04);
        }

        .meta-label {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            margin-bottom: 4px;
        }

        .meta-value {
            font-size: 0.98rem;
            font-weight: 700;
            color: #0f172a;
        }

        .profile-timeline {
            display: grid;
            gap: 16px;
        }

        .timeline-media-list {
            display: grid;
            gap: 14px;
        }

        .timeline-media-item {
            display: block;
            overflow: hidden;
            border-radius: 18px;
            background: #f4f6fb;
        }

        .timeline-media-item img {
            width: 100%;
            display: block;
            aspect-ratio: 4 / 5;
            object-fit: cover;
            object-position: center;
        }

        .profile-side-card {
            background: #fff;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 24px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        }

        .post-card {
            background: #fff;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 24px;
            padding: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05);
        }

        .post-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 14px;
            margin-bottom: 14px;
        }

        .post-av,
        .comment-av {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            overflow: hidden;
            flex: 0 0 auto;
            background: #e2e8f0;
        }

        .post-av img,
        .comment-av img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .post-av-name {
            margin: 0;
            font-size: 0.98rem;
            font-weight: 700;
            color: #0f172a;
        }

        .post-av-time,
        .comment-time {
            margin: 2px 0 0;
            font-size: 0.82rem;
            color: #64748b;
        }

        .post-body {
            color: #334155;
            line-height: 1.75;
            font-size: 0.98rem;
            margin-bottom: 14px;
            white-space: pre-line;
        }

        .post-actions-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
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

        .post-images {
            margin-top: 4px;
        }

        .comment-stream {
            display: grid;
            gap: 14px;
        }

        .comment-item {
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .comment-bubble {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 12px 14px;
        }

        .comment-name {
            margin: 0 0 4px;
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
        }

        .comment-text {
            margin: 0;
            color: #334155;
            line-height: 1.6;
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
            padding: 0;
        }

        .comment-replies {
            margin-left: 56px;
            padding-left: 14px;
            border-left: 2px solid #e2e8f0;
            display: grid;
            gap: 12px;
            margin-top: 12px;
        }

        .comment-compose {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 16px;
        }

        .comment-input-field {
            width: 100%;
            min-height: 44px;
            border: 1px solid #cbd5e1;
            padding: 0.7rem 1rem;
            font-size: 0.95rem;
            box-shadow: none;
        }

        .comment-input-field:focus {
            border-color: #4e6ef2;
            box-shadow: 0 0 0 0.2rem rgba(78, 110, 242, 0.12);
        }

        .profile-section-title {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
    </style>
@endpush

@section('content')
    <main>
        <div class="profile-shell">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4" data-aos="fade-up">
                <div>
                    <h1 class="page-title mb-2">Public Profile</h1>
                    <p class="page-sub mb-0">A view-only page for this family member&apos;s memories and details.</p>
                </div>

                @auth
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Back to Dashboard
                    </a>
                @else
                    <a href="{{ route('frontend.home') }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-arrow-left me-1"></i>
                        Back Home
                    </a>
                @endauth
            </div>

            <div class="profile-hero mb-4" data-aos="fade-up" data-aos-duration="500">
                <div class="profile-hero-cover"></div>
                <div class="profile-hero-body">
                    <div class="d-flex flex-column flex-md-row align-items-md-end gap-3">
                        <img src="{{ getPhoto($member->user?->image) }}" alt="{{ $member->user?->name ?? 'Member' }}"
                            class="profile-avatar">

                        <div class="flex-grow-1">
                            <h1 class="profile-name">{{ $member->user?->name ?? 'Unknown Member' }}</h1>
                            <p class="profile-subtitle">
                                {{ $member->profession ?: 'Family member profile' }}
                                @if ($member->bio)
                                    <span class="mx-1">&bull;</span>
                                    {{ \Illuminate\Support\Str::limit($member->bio, 90) }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="profile-meta-grid">
                        <div class="meta-card">
                            <div class="meta-label">Posts</div>
                            <div class="meta-value">{{ $posts->count() }}</div>
                        </div>
                        <div class="meta-card">
                            <div class="meta-label">Family Role</div>
                            <div class="meta-value">{{ $member->profession ?: 'Member' }}</div>
                        </div>
                        <div class="meta-card">
                            <div class="meta-label">Birth Date</div>
                            <div class="meta-value">
                                {{ $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->format('M d, Y') : 'Not set' }}
                            </div>
                        </div>
                        <div class="meta-card">
                            <div class="meta-label">Status</div>
                            <div class="meta-value">{{ ucfirst($member->living_status ?? 'unknown') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 g-lg-4">
                <div class="col-lg-4" data-aos="fade-up">
                    <div class="profile-side-card p-4 mb-4">
                        <h5 class="mb-3">About</h5>

                        <div class="mb-3">
                            <div class="text-muted small mb-1">Email</div>
                            <div class="fw-semibold">{{ $member->user?->email ?? 'Not shared' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small mb-1">Phone</div>
                            <div class="fw-semibold">{{ $member->phone ?? 'Not shared' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small mb-1">Address</div>
                            <div class="fw-semibold">{{ $member->address ?? 'Not shared' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small mb-1">Education</div>
                            <div class="fw-semibold">{{ $member->education ?? 'Not shared' }}</div>
                        </div>

                        <div class="mb-0">
                            <div class="text-muted small mb-1">Bio</div>
                            <div class="fw-semibold lh-lg">{{ $member->bio ?? 'No bio added yet.' }}</div>
                        </div>
                    </div>

                    <div class="profile-side-card p-4">
                        <h5 class="mb-3">Family Info</h5>

                        <div class="mb-3">
                            <div class="text-muted small mb-1">Gender</div>
                            <div class="fw-semibold">{{ $member->gender ? ucfirst($member->gender) : 'Not set' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small mb-1">Marital Status</div>
                            <div class="fw-semibold">{{ $member->marital_status ? ucfirst($member->marital_status) : 'Not set' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small mb-1">Blood Group</div>
                            <div class="fw-semibold">{{ $member->blood_group ?? 'Not set' }}</div>
                        </div>

                        <div class="mb-0">
                            <div class="text-muted small mb-1">Birth Date</div>
                            <div class="fw-semibold">
                                {{ $member->birth_date ? \Carbon\Carbon::parse($member->birth_date)->format('M d, Y') : 'Not set' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-3" data-aos="fade-up">
                        <div>
                            <h3 class="profile-section-title mb-1">Memories</h3>
                            <p class="text-muted mb-0">Photos and voice memories shared by this member.</p>
                        </div>
                    </div>

                    <div class="profile-timeline">
                        @forelse ($posts as $post)
                            <div class="post-card" data-aos="fade-up">
                                <div class="post-header mb-2 border-bottom border-light">
                                    <div class="post-av">
                                        <img src="{{ getPhoto($member->user?->image) }}"
                                            alt="{{ $member->user?->name ?? 'Member' }}">
                                    </div>
                                    <div style="flex:1;min-width:0;">
                                        <p class="post-av-name">{{ $member->user?->name ?? 'Unknown Member' }}</p>
                                        <p class="post-av-time">{{ $post->created_at->diffForHumans() }}</p>
                                    </div>
                                    @auth
                                        @if ((int) $post->user_id === (int) auth()->id())
                                            <form action="{{ route('user.feed.posts.destroy', $post) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this post?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        @endif
                                    @endauth
                                </div>

                                @if ($post->content)
                                    <div class="post-body">
                                        {!! nl2br(e($post->content)) !!}
                                    </div>
                                @endif

                                @php
                                    $imageMedia = $post->media->where('file_type', 'image');
                                    $audioMedia = $post->media->where('file_type', 'audio')->first();
                                @endphp

                                @if ($imageMedia->count())
                                    <div class="post-images mb-3 js-lightgallery" id="profile-gallery-{{ $post->id }}">
                                        <div class="timeline-media-list">
                                            @foreach ($imageMedia as $media)
                                                <a class="timeline-media-item" href="{{ asset($media->file_path) }}"
                                                    data-src="{{ asset($media->file_path) }}">
                                                    <img src="{{ asset($media->file_path) }}" alt="Memory image">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($audioMedia)
                                    <div class="mb-3">
                                        <audio controls style="width:100%;">
                                            <source src="{{ asset($audioMedia->file_path) }}">
                                        </audio>
                                    </div>
                                @endif

                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pt-2 border-top border-light">
                                    <span class="text-muted">
                                        <i class="fa-solid fa-heart me-1" style="color:#ec4899;"></i>
                                        {{ $post->likes_count }} reactions
                                    </span>
                                    <span class="text-muted">
                                        <i class="fa-regular fa-comment-dots me-1" style="color:#4e6ef2;"></i>
                                        {{ $post->comments_count }} comments
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="dash-card text-center" data-aos="fade-up">
                                <p class="mb-0 text-muted p-4">No memories added yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/thumbnail/lg-thumbnail.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/plugins/zoom/lg-zoom.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.js-lightgallery').forEach(function(gallery) {
                lightGallery(gallery, {
                    selector: 'a',
                    plugins: [lgZoom, lgThumbnail],
                    speed: 300,
                    download: false,
                });
            });
        });
    </script>
@endpush
