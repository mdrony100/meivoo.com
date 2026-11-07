<nav class="navbar navbar-expand-lg sticky-top navbar-custom py-2">
    <div class="container">
        @if (!empty($settings->site_logo))
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none"
                href="{{ route('frontend.home') }}">
                <img src="{{ asset($settings->site_logo) }}" alt="Logo" style="height:50px; width:auto;">
                {{-- <div class="navbar-brand-icon">
                    <i class="fa-solid fa-tree"></i>
                </div>
                <span class="brand-text">Family Legacy</span> --}}
            </a>
        @endif

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <i class="fa-solid fa-bars fs-5"></i>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav mx-auto gap-lg-3">
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('frontend.home') }}">Home</a>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('frontend.home') }}#tree">Family
                        Tree</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom"
                        href="{{ route('frontend.home') }}#features">Features</a></li>
                {{-- <li class="nav-item"><a class="nav-link nav-link-custom"
                        href="{{ route('frontend.home') }}#ai-photos">AI Photos</a></li> --}}
                <li class="nav-item"><a class="nav-link nav-link-custom"
                        href="{{ route('frontend.home') }}#testimonials">Testimonials</a></li>
            </ul>
            <div class="mt-3 mt-lg-0">
                @auth
                    <a href="{{ route('user.dashboard') }}"
                        class="btn-nav-signup text-nowrap d-block d-lg-inline-block w-100 w-lg-auto text-center mb-2 mb-lg-0">
                        Dashboard
                    </a>
                @else
                    <div
                        class="d-flex flex-column flex-lg-row gap-2 justify-content-center align-items-center mb-2 mb-lg-0">
                        <a href="{{ route('login') }}"
                            class="btn-nav-login text-nowrap text-decoration-none w-100 w-lg-auto text-center border rounded-5 px-3 py-2">
                            Log In
                        </a>
                        <a href="{{ route('register') }}"
                            class="btn-nav-signup text-nowrap text-decoration-none w-100 w-lg-auto text-center px-3 py-2">
                            Get Started Free
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>
