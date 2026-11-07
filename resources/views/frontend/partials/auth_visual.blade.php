<aside class="mv-auth-visual d-none d-lg-flex">
    <div class="mv-auth-visual-inner">
        <a class="mv-auth-brand" href="{{ route('frontend.home') }}">
            @if (!empty($settings->site_logo))
                <img src="{{ asset($settings->site_logo) }}" alt="{{ config('app.name') }}" height="38">
            @endif
        </a>

        <h2 class="mv-auth-headline">Every family has a story worth keeping.</h2>
        <p class="mv-auth-sub">Build your tree, save the memories and keep the whole family in one place.</p>

        <ul class="mv-auth-points">
            <li><span class="mv-auth-dot"></span> A private tree for your family only</li>
            <li><span class="mv-auth-dot"></span> Photos, stories and milestones in one timeline</li>
            <li><span class="mv-auth-dot"></span> Message relatives without leaving the app</li>
        </ul>

        <div class="mv-auth-proof">
            <div class="mv-auth-avatars">
                <span>M</span><span>A</span><span>S</span><span>R</span>
            </div>
            <p>Join families preserving their heritage together.</p>
        </div>
    </div>
</aside>
