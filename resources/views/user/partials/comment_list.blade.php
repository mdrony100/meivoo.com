<div class="comment-item">
    <div class="comment-av">
        <a href="{{ route('user.member.profile', $comment->user?->id) }}" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
            @if ($comment->user && $comment->user->image)
                <img src="{{ getPhoto($comment->user->image) }}" alt="{{ $comment->user->name }}">
            @else
                <div class="user-avatar-placeholder fs-6">
                    {{ substr($comment->user->name ?? 'U', 0, 1) }}
                </div>
            @endif
        </a>
    </div>

    <div style="flex:1;">
        <div class="comment-bubble">
            <a href="{{ route('user.member.profile', $comment->user?->id) }}">
                <p class="comment-name text-black">{{ $comment->user->name ?? 'N/A' }}
                </p>
            </a>
            <div class="content-box">
                <div class="text-area" style="font-size: 14px;">
                    {{ $comment->comment }}
                </div>
                <span onclick="toggleText(this)" style="color: #7c3aed; cursor: pointer; font-size: 14px;">See
                    more</span>
            </div>
        </div>

        <div class="comment-meta">
            <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>

            <button type="button" class="btn btn-sm btn-link p-0" style="cursor: pointer; color: #7c3aed;"
                onclick="toggleReplySmooth('reply-form-{{ $comment->id }}')">
                Reply
            </button>
        </div>
        <div id="reply-form-{{ $comment->id }}" class="reply-form-wrapper">
            <form action="{{ route('user.feed.comments.reply', [$post, $comment]) }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="comment" class="comment-input-field rounded-3 rounded-end-0"
                        placeholder="Write a reply..." required>
                    <button class="btn-post" type="submit"><i
                            class="fa-solid fa-paper-plane fa-sm me-1"></i>Sent</button>
                </div>
            </form>
        </div>

        @foreach ($comment->replies->sortBy('id') as $reply)
            <div class="comment-replies mt-3">
                <div class="comment-item">
                    <div class="comment-av">
                        <a href="{{ route('user.member.profile', $reply->user?->id) }}" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                            @if ($reply->user && $reply->user->image)
                                <img src="{{ getPhoto($reply->user->image) }}" alt="{{ $reply->user->name }}">
                            @else
                                <div class="user-avatar-placeholder fs-6">
                                    {{ substr($reply->user->name ?? 'U', 0, 1) }}
                                </div>
                            @endif
                        </a>
                    </div>

                    <div style="flex:1;">
                        <div class="comment-bubble">
                            <a href="{{ route('user.member.profile', $post->user?->id) }}">
                                <p class="comment-name text-black">
                                    {{ $reply->user?->name ?? 'Deleted User' }}
                                </p>
                            </a>
                            <div class="content-box">
                                <div class="text-area" style="font-size: 14px;">
                                    {{ $reply->comment }}
                                </div>
                                <span onclick="toggleText(this)"
                                    style="color: #7c3aed; cursor: pointer; font-size: 14px;">See more</span>
                            </div>
                        </div>

                        <div class="comment-meta">
                            <span class="comment-time">
                                {{ $reply->created_at?->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
