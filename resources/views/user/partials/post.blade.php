@auth
<div class="post-card mb-3" data-aos="fade-up">
    @if ($post->shared_post_id)
        @include('user.partials.shared_post', ['post' => $post])
    @else
        @include('user.partials.main_post', ['post' => $post])
    @endif

    <div class="post-reactions d-flex flex-wrap justify-content-between align-items-center gap-2 p-2">

        <div class="d-flex align-items-center gap-2">
            <?php
            $likedByMe = $post
                ->likes()
                ->where('user_id', auth()->id())
                ->exists();
            ?>
            <form action="{{ route('user.feed.likes.toggle', $post) }}" method="POST" class="ms-2">
                @csrf
                <button type="submit" class="btn btn-sm border-0 bg-transparent p-0 d-flex align-items-center"
                    style="font-size: 1.2rem; cursor: pointer;">

                    <i class="{{ $likedByMe ? 'fa-solid text-danger' : 'fa-regular' }} fa-heart"></i>

                </button>
            </form>
            <p class="small m-0">{{ $post->likes_count }}</p>

        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted text-nowrap">{{ $post->comments_count }} <span
                    class="d-inline">comments</span></span>
            <a href="javascript:void(0)"
                class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 me-2 view-btn"
                style="font-size: 0.75rem;" data-url="{{ route('user.posts.show', $post->id) }}">
                <i class="fa-solid fa-share"></i> Share
            </a>
        </div>
    </div>


    <div class="comment-section" style="display:block;">
        <div id="comment-section-{{ $post->id }}">
            @php
                $sortedComments = $post->comments->sortByDesc('created_at')->values();
            @endphp

            @foreach ($sortedComments as $index => $comment)
                <div class="comment-wrapper {{ $index >= 2 ? 'hidden-comment' : '' }}">
                    @include('user.partials.comment_list', [
                        'comment' => $comment,
                        'post' => $post,
                    ])
                </div>
            @endforeach
        </div>

        @if ($sortedComments->count() > 2)
            <button type="button" class="btn btn-sm btn-link toggle-comments mb-2" data-post-id="{{ $post->id }}"
                style="color: #7c3aed; padding: 0; font-size: 13px;">
                View more comments
            </button>
        @endif

        <form class="comment-compose mt-2" action="{{ route('user.feed.comments.store', $post) }}" method="POST">
            @csrf
            <div class="comment-av me-2">
                <a href="{{ route('user.member.profile', auth()->user()->id) }}"
                    style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                    @if (auth()->user()->image)
                        <img src="{{ getPhoto(auth()->user()->image) }}" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="user-avatar-placeholder fs-6">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </a>
            </div>
            <input type="text" name="comment" class="comment-input-field rounded-3 rounded-end-0"
                placeholder="Write a comment..." required>
            <button class="btn-post rounded-start-0" type="submit"><i
                    class="fa-solid fa-paper-plane fa-sm me-1"></i>Sent</button>
        </form>
    </div>
</div>
@endauth