<div class="post-header mb-2 border-bottom border-light">
    <div class="post-av">
        <a href="{{ route('user.member.profile', $post->user?->id) }}"
            style="display: flex; align-items: center; text-decoration: none; color: inherit;">

            @if ($post->user->image)
                <img src="{{ getPhoto($post->user->image) }}" alt="{{ $post->user->name }}" style="display: block;">
            @else
                <div class="user-avatar-placeholder fs-6"
                    style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px;">
                    {{ substr($post->user->name ?? 'U', 0, 1) }}
                </div>
            @endif
        </a>
    </div>
    <div style="flex:1;min-width:0;">
        <a href="{{ route('user.member.profile', $post->user?->id) }}">
            <p class="post-av-name text-black">{{ $post->user?->name }}</p>
        </a>
        <div class="d-flex align-items-center">
            <p class="post-av-time mb-0">{{ $post->created_at->diffForHumans() }}</p>

            <span class="badge text-muted" style="font-size: 0.75rem;">
                @if ($post->privacy === 'public')
                    <i class="fa-solid fa-globe" style="cursor: help" title="Public" data-bs-toggle="tooltip"
                        data-bs-placement="top"></i>
                @else
                    <i class="fa-solid fa-lock" style="cursor: help" title="Private" data-bs-toggle="tooltip"
                        data-bs-placement="top"></i>
                @endif
            </span>
        </div>
    </div>
    @if ((int) $post->user_id === (int) auth()->id())
        <div class="dropdown">
            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-ellipsis"></i> <!-- FontAwesome 3-dot icon -->
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <form id="delete-form-{{ $post->id }}" action="{{ route('user.feed.posts.destroy', $post) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="dropdown-item text-danger" onclick="confirmDelete('{{ $post->id }}')">
                            <i class="fa-solid fa-trash me-2"></i> Delete Post
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    @endif
</div>

@if ($post->content)
    <div class="content-box px-3">
        <div class="text-area">
            {!! nl2br(e($post->content)) !!}
        </div>
        <span onclick="toggleText(this)" style="color: #7c3aed; cursor: pointer;">See more</span>
    </div>
@endif


@php
    $imageMedia = $post->media->where('file_type', 'image');
    $audioMedia = $post->media->where('file_type', 'audio')->first();
    // $likedByMe = $post->likes->contains('user_id', auth()->id());
    $imageCount = $imageMedia->count();
    $visibleImages = $imageMedia->values()->take($imageCount >= 4 ? 4 : $imageCount);
    $moreImagesCount = max(0, $imageCount - 4);
    $galleryItems = $imageMedia
        ->values()
        ->map(function ($media) {
            return [
                'src' => asset($media->file_path),
                'thumb' => asset($media->file_path),
            ];
        })
        ->values();
    $gridClass = match (true) {
        $imageCount === 1 => 'timeline-media-grid-1',
        $imageCount === 2 => 'timeline-media-grid-2',
        $imageCount === 3 => 'timeline-media-grid-3',
        default => 'timeline-media-grid-4',
    };
@endphp

@if ($imageMedia->count())
    <div class="post-images post-media-shell js-lightgallery" id="post-gallery-{{ $post->id }}"
        data-gallery-items='@json($galleryItems)'>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3 row-cols-xl-4 {{ $gridClass }}">
            @foreach ($visibleImages as $index => $media)
                <div class="col">
                    <div class="timeline-media-item"  data-gallery-index="{{ $index }}">
                        <img src="{{ asset($media->file_path) }}" alt="Post image">
                        @if ($imageCount >= 4 && $index === 3 && $moreImagesCount > 0)
                            <span class="media-more-overlay">
                                <span>+{{ $moreImagesCount }}</span>
                            </span>
                        @endif
                    </div>
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