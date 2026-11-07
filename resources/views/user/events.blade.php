@extends('layouts.user')
@section('title', 'Events - LegacyTree')

@push('styles')
    <style>
        .events-page {
            padding: 20px;
        }

        .events-table {
            margin-top: 20px;
        }

        .event-row {
            /* cursor: pointer; */
            transition: background-color 0.2s;
        }

        .event-row:hover {
            background-color: #f8fafc;
        }

        .event-title {
            font-size: 0.875rem;
            /* 14px */
            color: #1f2937;
        }

        .event-type-badge {
            display: inline-block;
            background: #e0e7ff;
            color: #374151;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .event-type-birthday {
            background: #fef3c7;
            color: #92400e;
        }

        .event-type-marriage {
            background: #dbeafe;
            color: #1e40af;
        }

        .event-type-marriage_anniversary {
            background: #f3e8ff;
            color: #5b21b6;
        }

        .event-type-family_reunion {
            background: #dcfce7;
            color: #166534;
        }

        .event-type-death_anniversary {
            background: #fce7e3;
            color: #991b1b;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            opacity: 0.3;
        }

        .dropdown-toggle {
            background: none;
            border: none;
            padding: 4px 8px;
            color: #6b7280;
            cursor: pointer;
            border-radius: 4px;
        }

        .dropdown-toggle:hover {
            color: #374151;
            background-color: #f1f5f9;
        }

        .dropdown-menu {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            min-width: 120px;
        }

        .dropdown-item {
            padding: 6px 12px;
            color: #374151;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .dropdown-item:hover {
            background: #f9fafb;
        }

        .rsvp-status {
            font-size: 0.75rem;
        }

        #eventsTable th,
        #eventsTable td {
            font-size: 0.85rem;
            padding: 10px 12px;
            vertical-align: middle;
        }
    </style>
@endpush

@section('content')
    <main class="main-content">
        <div class="d-sm-flex justify-content-between align-items-center mb-4">
            <div data-aos="fade-right" class="mb-4">
                <h1 class="page-title mb-1 fs-3"><i class="fa-solid fa-calendar-days me-1" style="color: var(--primary);"></i>
                    Events</h1>
                <p class="page-sub mb-0 small text-muted">Family events and celebrations</p>
            </div>
            <div data-aos="fade-left">
                <a href="javascript:void(0)" class="btn-post create-btn" style="padding: 12px 15px"
                    data-url="{{ route('user.events.create') }}">
                    <i class="fa-solid fa-plus me-1"></i> Create Event
                </a>
            </div>
        </div>

        <div class="card events-table shadow-sm border-0">
            <div class="card-header bg-transparent py-3">
                <h5 class="mb-0 fs-6 fw-semibold">All Events</h5>
            </div>

            <div class="card-body p-0 pb-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="eventsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Event</th>
                                <th>Type</th>
                                <th style="width: 120px;">Date</th>
                                <th>Location</th>
                                <th>Created By</th>
                                <th>RSVP</th>
                                <th class="text-center" style="width: 80px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($events as $index => $event)
                                <tr class="event-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{-- <a href="#" class="text-decoration-none">
                                            <span class="text-dark fw-medium">{{ $event->title }}</span>
                                        </a> --}}
                                        <a href="javascript:void(0)"
                                            class="text-decoration-none view-btn"
                                            data-url="{{ route('user.events.show', $event->id) }}">
                                            <span class="text-dark fw-medium">{{ $event->title }}</span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="event-type-badge event-type-{{ $event->type }}">
                                            {{ ucwords(str_replace('_', ' ', $event->type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($event->event_date->format('Y-m-d') === today())
                                            <strong class="text-dark">{{ $event->event_date->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-warning fw-medium" style="font-size: 11px;">Today at
                                                {{ $event->event_date->format('g:i A') }}</small>
                                        @elseif ($event->event_date->format('Y-m-d') === now()->addDay()->format('Y-m-d'))
                                            <strong class="text-dark">{{ $event->event_date->format('M d, Y') }}</strong>
                                            <br>
                                            <small class="text-success fw-medium" style="font-size: 11px;">Tomorrow</small>
                                        @else
                                            <span class="text-dark">{{ $event->event_date->format('M d, Y') }}</span>
                                            <br>
                                            <small class="text-muted"
                                                style="font-size: 11px;">{{ $event->event_date->format('g:i A') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($event->location)
                                            <span class="text-dark">{{ $event->location }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($event->creator)
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($event->creator->image)
                                                    <img src="{{ asset($event->creator->image) }}"
                                                        alt="{{ $event->creator->name }}" width="24" height="24"
                                                        class="rounded-circle object-fit-cover">
                                                @else
                                                    <div class="avatar-circle sm d-flex align-items-center justify-content-center bg-secondary text-white rounded-circle"
                                                        style="width: 24px; height: 24px; font-size: 11px;">
                                                        {{ substr($event->creator->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                <span>{{ $event->creator->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $myAttendance = $event->attendances->firstWhere('user_id', auth()->id());
                                            $rsvpBadge = 'secondary';
                                            if ($myAttendance) {
                                                $rsvpBadge =
                                                    $myAttendance->status === 'going'
                                                        ? 'success'
                                                        : ($myAttendance->status === 'maybe'
                                                            ? 'warning'
                                                            : 'danger');
                                            }
                                        @endphp
                                        @if (auth()->id() === $event->creator_user_id)
                                            <span class="rsvp-status text-muted fw-medium">Creator</span>
                                        @elseif ($myAttendance)
                                            <span class="rsvp-status">
                                                <span class="badge bg-{{ $rsvpBadge }} px-2 py-1"
                                                    style="font-size: 10px;">
                                                    {{ ucfirst($myAttendance->status) }}
                                                </span>
                                            </span>
                                        @else
                                            <span class="rsvp-status text-muted">Not responded</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown d-inline-block">
                                            <button class="dropdown-toggle" type="button"
                                                id="actionDropdown{{ $event->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end text-start"
                                                aria-labelledby="actionDropdown{{ $event->id }}">
                                                <li>
                                                    <a href="javascript:void(0)"
                                                        class="dropdown-item d-flex align-items-center justify-content-start gap-2 view-btn"
                                                        data-url="{{ route('user.events.show', $event->id) }}">
                                                        <i class="fa-solid fa-eye" style="width: 16px;"></i> View
                                                    </a>
                                                </li>

                                                @if (auth()->id() === $event->creator_user_id)
                                                    <li>

                                                        <a href="javascript:void(0)"
                                                            class="edit-btn dropdown-item d-flex align-items-center justify-content-start gap-2"
                                                            data-url="{{ route('user.events.edit', $event->id) }}">
                                                            <i class="fa-solid fa-pen" style="width: 16px;"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form id="delete-form-{{ $event->id }}"
                                                            action="{{ route('user.events.destroy', $event->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="dropdown-item text-danger border-0 w-100 bg-transparent d-flex align-items-center justify-content-start gap-2"
                                                                onclick="confirmDelete('{{ $event->id }}')">
                                                                <i class="fa-solid fa-trash" style="width: 16px;"></i>
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-state-row">
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fa-regular fa-calendar"></i>
                                            <h6 class="mb-1 fw-semibold">No events yet</h6>
                                            <p class="mb-0 small text-muted">Create your first family event to get started.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="eventDetailsOffcanvas" style="width: 480px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold text-dark" id="eventDetailsOffcanvasLabel">
                <i class="fa-solid fa-calendar-check me-2" style="color: var(--primary);"></i>Event Details
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <!-- Content Render Container -->
        <div class="offcanvas-body p-0" id="offcanvas_dynamic_content">
            <!-- Dynamic content from partial blade -->
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="create-modal-content">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="edit-modal-content">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).on('click', '.create-btn', function() {
            let url = $(this).data('url');
            $.get(url, function(data) {
                $('#create-modal-content').html(data);

                $('#createModal').modal('show');

                if (typeof flatpickr !== 'undefined') {
                    flatpickr('#create-modal-content #event_date', {
                        enableTime: true,
                        dateFormat: 'Y-m-d H:i',
                        time_24hr: true
                    });
                }
            });
        });
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();

            let url = $(this).data('url');

            $.get(url, function(data) {
                $('#edit-modal-content').html(data);
                $('#editModal').modal('show');

                if (typeof flatpickr !== 'undefined') {
                    flatpickr('#edit-modal-content #event_date', {
                        enableTime: true,
                        dateFormat: 'Y-m-d H:i',
                        time_24hr: true
                    });
                }
            });
        });

        $(document).on('click', '.view-btn', function(e) {
            e.preventDefault();
            let url = $(this).data('url');

            // Loading spinner
            $('#offcanvas_dynamic_content').html(`
            <div class="d-flex flex-column align-items-center justify-content-center p-5 my-5">
                <div class="spinner-border text-primary mb-3" role="status"></div>
                <small class="text-muted">Loading event details...</small>
            </div>
        `);

            // Show Offcanvas
            let bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance('#eventDetailsOffcanvas');
            bsOffcanvas.show();

            // AJAX Fetch
            $.get(url, function(data) {
                $('#offcanvas_dynamic_content').html(data);
            }).fail(function() {
                $('#offcanvas_dynamic_content').html(`
                <div class="p-4 text-center text-danger">
                    <i class="fa-solid fa-triangle-exclamation fa-2x mb-2"></i>
                    <p class="mb-0">Failed to load event details.</p>
                </div>
            `);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.dropdown-toggle').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

            const table = document.querySelector('#eventsTable');
            if (table) {
                const rows = table.querySelectorAll('tbody tr:not(.empty-state-row)');
                if (rows.length > 0) {
                    new DataTable(table, {
                        responsive: true,
                        pageLength: 10,
                        ordering: true,
                        language: {
                            search: "_INPUT_",
                            searchPlaceholder: "Search events..."
                        }
                    });
                }
            }
        });
    </script>
    <script>
        function confirmDelete(postId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this event?",
                icon: 'warning',
                width: 400,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#7c3aed',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + postId).submit();
                }
            })
        }
    </script>
@endpush
