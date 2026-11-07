@php
    $admin = auth('admin')->user();
@endphp
<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto align-items-center">
            <li class="nav-item d-none d-md-block">
                <a href="{{ route('admin.cacheClear') }}" class="nav-link" data-bs-toggle="tooltip" title="Clear cache">
                    <i class="bi bi-brush-fill"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button"
                    data-bs-toggle="dropdown">
                    @if (!empty($admin->image) && file_exists(public_path($admin->image)))
                        <img src="{{ asset($admin->image) }}" alt="{{ $admin->name }}" class="avatar"
                            style="height: 2rem; width: 2rem; object-fit: cover; border-radius: 50%;">
                    @else
                        <span class="avatar"
                            style="height: 2rem; width: 2rem; display: flex; align-items: center; justify-content: center; background-color: #e9ecef; border-radius: 50%;">
                            {{ strtoupper(substr($admin->name ?? 'A', 0, 1)) }}
                        </span>
                    @endif
                    <span class="d-none d-md-inline">{{ $admin->name ?? 'Admin' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i
                                class="bi bi-person me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i
                                class="bi bi-gear me-2"></i>Edit profile</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger" type="submit"><i
                                    class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
