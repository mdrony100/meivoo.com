@extends('layouts.user')

@section('title', 'Notifications')

@section('content')
    <div class="main-content">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h1 class="page-title">Notifications</h1>
                <p class="page-sub">Everything happening across your family, in one place.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('user.messages') }}" class="btn btn-light btn-sm">
                    <i data-lucide="message-square" style="width:15px;height:15px;"></i> Messages
                </a>
                <a href="{{ route('user.events') }}" class="btn btn-primary btn-sm">
                    <i data-lucide="calendar-plus" style="width:15px;height:15px;"></i> New event
                </a>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-3" id="notifFilters">
            <button type="button" class="mv-chip active" data-filter="all">All</button>
            <button type="button" class="mv-chip" data-filter="message">Messages</button>
            <button type="button" class="mv-chip" data-filter="event">Events</button>
            <button type="button" class="mv-chip" data-filter="birthday">Birthdays</button>
            <button type="button" class="mv-chip" data-filter="post">Posts</button>
        </div>

        @forelse ($groups as $label => $groupItems)
            <div class="card mb-3 notif-group mv-reveal" data-group>
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h2 class="card-title">{{ $label }}</h2>
                    <span class="mv-chip">{{ $groupItems->count() }}</span>
                </div>
                <div class="card-body p-2">
                    @foreach ($groupItems as $item)
                        <a href="{{ $item['url'] }}"
                            class="notif-item d-flex align-items-start gap-3 text-decoration-none notif-row"
                            data-type="{{ $item['type'] }}">
                            <span class="notif-icon">
                                <i data-lucide="{{ $item['icon'] }}" style="width:17px;height:17px;"></i>
                            </span>
                            <span class="flex-grow-1 min-w-0">
                                <span class="d-block notif-title">{{ $item['title'] }}</span>
                                <span class="d-block notif-sub text-truncate">{{ $item['body'] }}</span>
                            </span>
                            <span class="notif-time text-nowrap">
                                {{ $item['at'] ? \Illuminate\Support\Carbon::parse($item['at'])->diffForHumans(null, true) : '' }}
                                ago
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body empty-state">
                    <div class="es-emoji">🌙</div>
                    <h5>All quiet for now</h5>
                    <p class="mb-3">New posts, events and messages will show up here.</p>
                    <a href="{{ route('user.members') }}" class="btn btn-primary btn-sm">Find your family</a>
                </div>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var buttons = document.querySelectorAll('#notifFilters [data-filter]');
            buttons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    buttons.forEach(function (b) { b.classList.remove('active'); });
                    btn.classList.add('active');
                    var filter = btn.getAttribute('data-filter');
                    document.querySelectorAll('.notif-row').forEach(function (row) {
                        var show = filter === 'all' || row.getAttribute('data-type') === filter;
                        row.classList.toggle('d-none', !show);
                    });
                    document.querySelectorAll('[data-group]').forEach(function (group) {
                        var visible = group.querySelectorAll('.notif-row:not(.d-none)').length;
                        group.classList.toggle('d-none', visible === 0);
                    });
                });
            });
        });
    </script>
@endpush
