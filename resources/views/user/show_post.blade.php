<div class="post-preview">
    @if ($post->shared_post_id)
        @include('user.partials.shared_post', ['post' => $post])
    @else
        @include('user.partials.main_post', ['post' => $post])
    @endif

    <hr>

    <div class="d-flex gap-2 justify-content-between align-items-center">
        <div>
            <!-- Facebook Button -->
            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('user.posts.show', $post->id)) }}"
                target="_blank" class="m-1 btn btn-outline-primary btn-sm">
                <i class="fa-brands fa-facebook"></i>
                <span class="d-none d-sm-inline"> Facebook</span>
            </a>

            <!-- LinkedIn Button -->
            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('user.posts.show', $post->id)) }}"
                target="_blank" class="m-1 btn btn-outline-info btn-sm">
                <i class="fa-brands fa-linkedin"></i>
                <span class="d-none d-sm-inline"> LinkedIn</span>
            </a>

            <!-- Copy Link Button -->
            <button class="m-1 btn btn-outline-secondary btn-sm"
                onclick="copyToClipboard(this, '{{ route('user.posts.show', $post->id) }}')">
                <span class="btn-text">
                    <i class="fa-solid fa-copy"></i>
                    <span class="btn-label d-none d-sm-inline"> Copy Link</span>
                </span>
            </button>
        </div>

        <!-- Share to Feed / Login Button -->
        @auth
            <form action="{{ route('user.posts.share', $post->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn-post w-100" style="padding-top: 10px; padding-bottom: 10px;">
                    <i class="fa-solid fa-share-nodes"></i>
                    <span class="ms-1">Share</span>
                    <span class="d-none d-sm-inline">to My Feed</span>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="btn-post" style="padding-top: 10px; padding-bottom: 10px;">
                <i class="fa-solid fa-share-nodes"></i>
                <span class="ms-1"> Login to Share</span>
            </a>
        @endauth
    </div>
</div>

<script>
    function copyToClipboard(buttonElement, url) {
        const linkWithAction = url + '?action=share';
        const btnSpan = buttonElement.querySelector('.btn-text');

        navigator.clipboard.writeText(linkWithAction).then(() => {
            const originalContent = btnSpan.innerHTML;

            btnSpan.innerHTML =
                '<i class="fa-solid fa-check"></i><span class="d-none d-sm-inline"> Copied!</span>';

            setTimeout(() => {
                btnSpan.innerHTML = originalContent;
            }, 3000);
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }
</script>
