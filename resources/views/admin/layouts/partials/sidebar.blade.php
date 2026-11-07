<aside class="app-sidebar shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            {{-- <span class="brand-image avatar" style="height: 2rem; width: 2rem;">M</span> --}}
            <span class="brand-text">Admin Panel</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column">

                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link @yield('dashboard')">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link @yield('users')">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Users</p>
                    </a>
                </li>

                {{-- <li class="nav-item">
                    <a href="{{ route('admin.admins.index') }}" class="nav-link @yield('users')">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Admin Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link @yield('roles')">
                        <i class="nav-icon bi bi-shield-check"></i>
                        <p>Roles</p>
                    </a>
                </li> --}}

                {{-- <li class="nav-item">
                    <a href="{{ route('admin.faq.index') }}" class="nav-link @yield('faq')">
                        <i class="nav-icon bi bi-question-circle"></i>
                        <p>FAQ</p>
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a href="{{ route('admin.testimonial.index') }}"
                    class="nav-link @yield('testimonials')">
                        <i class="nav-icon bi bi-chat-quote"></i>
                        <p>Testimonials</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.cpage.index') }}" class="nav-link @yield('cpage')">
                        <i class="nav-icon bi bi-file-code"></i>
                        <p>Custom Pages</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.contact.index') }}" class="nav-link @yield('contact')">
                        <i class="nav-icon bi bi-envelope"></i>
                        <p>Contacts</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.subscribers.index') }}" class="nav-link @yield('newsletter')">
                        <i class="nav-icon bi bi-envelope-paper"></i>
                        <p>Subscribers</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link @yield('settings')">
                        <i class="nav-icon bi bi-sliders"></i>
                        <p>Settings</p>
                    </a>
                </li>


                <li class="nav-item">
                    <a href="{{ route('admin.profile') }}" class="nav-link @yield('profile')">
                        <i class="nav-icon bi bi-person-badge"></i>
                        <p>Profile</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
