@auth
    <aside class="sidebar d-flex flex-column h-100" id="sidebar" style="z-index: 1030;">

        <div
            class="sidebar-brand text-center py-3 border-bottom d-flex align-items-center justify-content-center flex-shrink-0">
            <img src="{{ getLogo($settings->site_logo) }}" height="48" alt="Logo">
        </div>

        <nav class="sidebar-nav py-3 flex-grow-1 overflow-y-auto d-flex flex-column">

            {{-- Main Menu --}}
            <div class="nav-group mb-3">
                <div class="nav-label px-3 mb-2 text-uppercase text-muted fw-bold"
                    style="font-size: 10px; letter-spacing: 0.8px;">Main Menu</div>

                <a href="{{ route('user.dashboard') }}"
                    class="nav-item-custom {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('user.members') }}"
                    class="nav-item-custom {{ request()->routeIs('user.members') || request()->routeIs('user.member.create') ? 'active' : '' }}">
                    <i data-lucide="users"></i>
                    <span>Family Members</span>
                </a>

                <a href="{{ route('user.family-tree') }}"
                    class="nav-item-custom {{ request()->routeIs('user.family-tree') ? 'active' : '' }}">
                    <i data-lucide="git-branch-plus"></i>
                    <span>Family Tree</span>
                </a>

                <a href="{{ route('user.feed') }}"
                    class="nav-item-custom {{ request()->routeIs('user.feed') ? 'active' : '' }}">
                    <i data-lucide="newspaper"></i>
                    <span>Family Feed</span>
                </a>
            </div>

            <div class="nav-group mb-3">
                <div class="nav-label px-3 mb-2 text-uppercase text-muted fw-bold"
                    style="font-size: 10px; letter-spacing: 0.8px;">Tools & Events</div>

                <a href="{{ route('user.messages') }}"
                    class="nav-item-custom {{ request()->routeIs('user.messages') ? 'active' : '' }}">
                    <i data-lucide="messages-square"></i>
                    <span>Messages</span>
                </a>

                <a href="{{ route('user.events') }}"
                    class="nav-item-custom {{ request()->routeIs('user.events') ? 'active' : '' }}">
                    <i data-lucide="calendar-days"></i>
                    <span>Events</span>
                </a>

                <a href="{{ route('user.legacy-names') }}"
                    class="nav-item-custom {{ request()->routeIs('user.legacy-names*') ? 'active' : '' }}">
                    <i data-lucide="history"></i>
                    <span>Legacy Names</span>
                </a>

                <a href="{{ route('user.ai-photo.index') }}"
                    class="nav-item-custom {{ request()->routeIs('user.ai-photo*') ? 'active' : '' }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path
                            d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z" />
                    </svg>
                    <span>AI Photo Restore</span>
                </a>
            </div>

            {{-- Settings --}}
            <div class="nav-group mb-3">
                <div class="nav-label px-3 mb-2 text-uppercase text-muted fw-bold"
                    style="font-size: 10px; letter-spacing: 0.8px;">Settings</div>

                <a href="{{ route('user.member.profile', Auth::id()) }}"
                    class="nav-item-custom {{ request()->routeIs('user.settings') || (request()->routeIs('user.member.profile') && request()->route('id') == Auth::id()) ? 'active' : '' }} mb-2 ms-0 me-0">
                    <i data-lucide="settings"></i>
                    <span>Profile Settings</span>
                </a>
            </div>

            <div class="sidebar-footer d-none d-md-block mt-auto">
                <div class="p-3 rounded-3"
                    style="background: rgba(152, 75, 243, 0.08); border: 1px border-dashed rgba(152, 75, 243, 0.2);">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i data-lucide="sparkles" style="color: #984bf3; width: 18px;"></i>
                        <strong class="small" style="color: #984bf3;">Need Help?</strong>
                    </div>
                    <p class="text-muted extra-small mb-0" style="font-size: 11px;">Explore family tree features or contact
                        support anytime.</p>
                </div>
            </div>

        </nav>

        <div class="pt-3 border-top flex-shrink-0 bg-white mb-md-3">
            <div
                class="user-card d-flex align-items-center justify-content-between p-2 rounded-3 border bg-light mx-3 mb-3">
                <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                    @if (auth()->user()->image)
                        <img src="{{ getPhoto(auth()->user()->image) }}" class="rounded-circle flex-shrink-0"
                            style="width: 40px; height: 40px; object-fit: cover;" alt="{{ auth()->user()->name }}">
                    @else
                        <div class="user-avatar-placeholder fs-6 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                            style="width: 40px; height: 40px;">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                    @endif

                    <div class="user-info text-truncate">
                        <h6 class="mb-0 text-truncate fw-semibold" style="font-size: 13px;">{{ Auth::user()->name }}</h6>
                        <span class="text-muted text-truncate d-block"
                            style="font-size: 11px;">{{ Auth::user()->email }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit"
                        class="btn btn-sm btn-outline-danger p-1 rounded-2 border-0 d-flex align-items-center justify-content-center"
                        title="Logout" style="width: 32px; height: 32px;">
                        <i data-lucide="log-out" style="width: 18px; height: 18px;"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>
@endauth
