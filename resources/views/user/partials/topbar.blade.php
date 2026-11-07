@auth
    <header class="topbar" style="z-index: 1029;">
        <button class="d-lg-none topbar-bell" id="sidebarToggle" aria-label="Open menu">
            <i class="fa-solid fa-bars fa-lg"></i>
        </button>

        <div class="mv-search d-none d-sm-block">
            <span class="mv-search-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
                    stroke-linecap="round"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.2-3.2" /></svg>
            </span>
            <input type="search" placeholder="Search family members, posts and events" aria-label="Search"
                autocomplete="off" data-mv-search="{{ route('user.search') }}">
            <kbd>/</kbd>
            <div class="mv-search-results" role="listbox"></div>
        </div>

        <div class="topbar-right ms-auto d-flex align-items-center gap-1 gap-sm-2">

            <a href="{{ route('user.feed') }}" class="btn btn-primary btn-sm d-none d-md-inline-flex">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                    stroke-linecap="round"><path d="M12 5v14M5 12h14" /></svg>
                New post
            </a>

            <button type="button" class="topbar-bell" data-mv-theme-toggle aria-pressed="false"
                title="Toggle dark mode" aria-label="Toggle dark mode">
                <svg class="mv-icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" /></svg>
            </button>

            <div class="dropdown" data-mv-summary="{{ route('user.summary') }}">
                <button class="topbar-bell" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false" aria-label="Notifications">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8a6 6 0 0 0-12 0c0 6-3 7-3 7h18s-3-1-3-7" /><path d="M13.7 21a2 2 0 0 1-3.4 0" /></svg>
                    <span class="bell-dot d-none" data-mv-unread-dot></span>
                </button>

                <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 306px;">
                    <div class="d-flex align-items-center justify-content-between px-2 pb-2 pt-1">
                        <span class="fw-bold" style="font-size:13.5px;">Your updates</span>
                        <a href="{{ route('user.notifications') ?? '#' }}" class="small text-decoration-none">View all</a>
                    </div>

                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('user.messages') }}">
                        <span class="notif-icon"><i data-lucide="message-square" style="width:16px;height:16px;"></i></span>
                        <span class="flex-grow-1">
                            <span class="d-block notif-title">Messages</span>
                            <span class="d-block notif-sub">Unread conversations</span>
                        </span>
                        <span class="badge bg-primary d-none" data-mv-summary-item="messages">0</span>
                    </a>

                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('user.events') }}">
                        <span class="notif-icon"><i data-lucide="calendar-days" style="width:16px;height:16px;"></i></span>
                        <span class="flex-grow-1">
                            <span class="d-block notif-title">Upcoming events</span>
                            <span class="d-block notif-sub">Next 30 days</span>
                        </span>
                        <span class="badge bg-primary d-none" data-mv-summary-item="events">0</span>
                    </a>

                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('user.members') }}">
                        <span class="notif-icon"><i data-lucide="cake" style="width:16px;height:16px;"></i></span>
                        <span class="flex-grow-1">
                            <span class="d-block notif-title">Birthdays this month</span>
                            <span class="d-block notif-sub">Send a little love</span>
                        </span>
                        <span class="badge bg-primary d-none" data-mv-summary-item="birthdays">0</span>
                    </a>

                    <hr class="dropdown-divider my-2">

                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('frontend.home') }}"
                        target="_blank">
                        <span class="notif-icon"><i data-lucide="globe" style="width:16px;height:16px;"></i></span>
                        <span class="flex-grow-1">
                            <span class="d-block notif-title">Visit website</span>
                            <span class="d-block notif-sub">Open the public site</span>
                        </span>
                    </a>
                </div>
            </div>

            <div class="dropdown">
                <button class="topbar-avatar d-flex align-items-center border-0 bg-transparent p-0" type="button"
                    data-bs-toggle="dropdown" aria-expanded="false" aria-label="Account menu">
                    @if (auth()->user()->image)
                        <img src="{{ getPhoto(auth()->user()->image) }}" class="rounded-circle" width="40" height="40"
                            alt="{{ auth()->user()->name }}" data-mv-avatar>
                    @else
                        <span class="user-avatar-placeholder fs-6" style="width:40px;height:40px;">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </span>
                    @endif
                </button>

                <div class="dropdown-menu dropdown-menu-end" style="min-width: 232px;">
                    <div class="px-2 pb-2 pt-1">
                        <div class="fw-semibold" style="font-size:13.5px;">{{ auth()->user()->name }}</div>
                        <div class="text-muted" style="font-size:11.5px;">{{ auth()->user()->email }}</div>
                    </div>
                    <hr class="dropdown-divider">
                    <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                        <i class="fa fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    <a class="dropdown-item" href="{{ route('user.member.profile', auth()->user()->id) }}">
                        <i class="fa fa-user me-2"></i> My profile
                    </a>
                    <a class="dropdown-item" href="{{ route('user.settings') }}">
                        <i class="fa fa-gear me-2"></i> Settings
                    </a>
                    <hr class="dropdown-divider">
                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out-alt me-2"></i> Log out
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </header>
@else
    <header class="topbar w-100 d-flex align-items-center justify-content-between px-3 px-md-5 py-2">
        <a href="{{ route('frontend.home') }}" class="navbar-brand fw-bold text-decoration-none d-flex align-items-center">
            <img src="{{ getLogo($settings->site_logo) }}" class="img-fluid" style="max-height: 38px;" alt="Logo">
        </a>

        <div class="topbar-right d-flex align-items-center gap-2 gap-md-3">
            <button type="button" class="topbar-bell" data-mv-theme-toggle aria-pressed="false"
                title="Toggle dark mode" aria-label="Toggle dark mode">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z" /></svg>
            </button>
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-sign-in-alt"></i> Login
            </a>
        </div>
    </header>
@endauth
