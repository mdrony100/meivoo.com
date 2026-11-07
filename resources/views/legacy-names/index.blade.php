@extends('layouts.user')
@section('title', 'Legacy Names - LegacyTree')

@push('styles')
    <style>
        .legacy-names-page {
            padding: 30px 20px 30px;
        }

        /* Page Header */
        .legacy-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 35px;
        }

        .legacy-page-eyebrow {
            display: inline-block;
            margin-bottom: 7px;
            color: #6366f1;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .legacy-page-header .page-title {
            margin: 0;
            color: #202534;
            font-size: 24px;
            font-weight: 800;
        }

        .legacy-page-header .page-sub {
            margin-top: 8px;
            color: #7b8292;
            font-size: 15px;
        }

        /* Grid - Default 4 Columns for Desktop */
        .legacy-names-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        /* Card */
        .legacy-name-card {
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #edf0f5;
            border-radius: 18px;
            box-shadow: 0 6px 20px rgba(31, 41, 55, 0.05);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .legacy-name-card:hover {
            transform: translateY(-5px);
            border-color: #d8dafe;
            box-shadow: 0 14px 30px rgba(31, 41, 55, 0.10);
        }

        /* Cover */
        .legacy-name-cover {
            position: relative;
            height: 130px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, #eef4ff 0%, #f8f1ff 100%);
        }

        .legacy-cover-pattern {
            position: absolute;
            inset: 0;
            opacity: 0.5;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(99, 102, 241, 0.12) 0 2px, transparent 3px),
                radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.12) 0 2px, transparent 3px);
            background-size: 45px 45px;
        }

        .legacy-card-icon {
            position: relative;
            z-index: 1;
            width: 52px;
            height: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25);
        }

        .legacy-card-label {
            position: relative;
            z-index: 1;
            margin-top: 8px;
            color: #6366f1;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Content */
        .legacy-name-content {
            padding: 18px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            justify-content: space-between;
        }

        .legacy-name-title {
            overflow: hidden;
            margin: 0 0 14px;
            color: #252b3a;
            font-size: 18px;
            font-weight: 800;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        /* Details */
        .legacy-card-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 64px;
            margin-bottom: 16px;
        }

        .legacy-card-detail {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4f5667;
            font-size: 13px;
        }

        .legacy-card-detail>i {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #6366f1;
            background: #eef0ff;
            border-radius: 8px;
            font-size: 12px;
        }

        .legacy-card-detail span {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            font-weight: 600;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .legacy-card-detail small {
            margin-bottom: 1px;
            color: #9aa1b1;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* View Button */
        .btn-view-legacy {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 10px 14px;
            color: #6366f1;
            background: #f4f5ff;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-view-legacy i {
            transition: transform 0.25s ease;
        }

        .btn-view-legacy:hover {
            color: #ffffff;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .btn-view-legacy:hover i {
            transform: translateX(4px);
        }

        /* Empty State */
        .legacy-empty-state {
            max-width: 550px;
            margin: 60px auto;
            padding: 55px 30px;
            text-align: center;
            background: #ffffff;
            border: 1px solid #edf0f5;
            border-radius: 22px;
            box-shadow: 0 10px 35px rgba(31, 41, 55, 0.06);
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: #6366f1;
            background: #eef0ff;
            border-radius: 50%;
            font-size: 30px;
        }

        .legacy-empty-state h3 {
            margin-bottom: 10px;
            color: #252b3a;
            font-size: 22px;
            font-weight: 800;
        }

        .legacy-empty-state p {
            margin-bottom: 25px;
            color: #7b8292;
            font-size: 14px;
        }

        /* Pagination */
        .legacy-pagination {
            display: flex;
            justify-content: center;
        }

        .legacy-pagination .pagination {
            gap: 5px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1200px) {
            .legacy-names-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .legacy-names-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .legacy-page-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px !important;
            }

            .legacy-create-btn {
                width: 100%;
                justify-content: center !important;
                align-items: center;
            }

            .legacy-page-header .page-title {
                font-size: 24px;
            }
        }

        @media (max-width: 576px) {
            .legacy-names-page {
                padding: 20px 10px 40px;
            }

            .legacy-names-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .legacy-name-cover {
                height: 140px;
            }
        }

        .legacy-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
        }

        /* Base list resets */
        .legacy-pagination .pagination {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        /* Each item */
        .legacy-pagination .page-item {
            margin: 0;
        }

        /* Default state for numbers & arrows */
        .legacy-pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            height: 42px;
            padding: 0 14px;

            color: #4f5667;
            background-color: #ffffff;
            border: 1px solid #edf0f5;
            border-radius: 12px;

            font-size: 14px;
            font-weight: 600;
            text-decoration: none;

            box-shadow: 0 2px 6px rgba(31, 41, 55, 0.04);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Hover state */
        .legacy-pagination .page-link:hover {
            color: #6366f1;
            background-color: #f4f5ff;
            border-color: #d8dafe;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.12);
        }

        /* Active page state */
        .legacy-pagination .page-item.active .page-link {
            color: #ffffff !important;
            background: linear-gradient(135deg, #6366f1, #8b5cf6) !important;
            border-color: transparent !important;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35) !important;
        }

        /* Disabled state (e.g., Previous on first page) */
        .legacy-pagination .page-item.disabled .page-link {
            color: #c0c6d4;
            background-color: #f8fafc;
            border-color: #edf0f5;
            cursor: not-allowed;
            opacity: 0.7;
            box-shadow: none;
            transform: none;
        }

        /* Focus state for accessibility */
        .legacy-pagination .page-link:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25);
        }

        /* Responsive adjustment for small screens */
        @media (max-width: 576px) {
            .page-title {
                font-size: 20px !important;
            }

            .page-sub {
                font-size: 14px !important;
            }

            .legacy-pagination .page-link {
                min-width: 36px;
                height: 36px;
                padding: 0 10px;
                font-size: 13px;
                border-radius: 10px;
            }

            .legacy-pagination .pagination {
                gap: 5px;
            }
        }


        .legacy-name-cover {
            position: relative;
        }

        .legacy-card-actions {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 6px;
            z-index: 5;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-action.edit-btn {
            color: #4f46e5;
        }

        .btn-action.edit-btn:hover {
            background: #4f46e5;
            color: #ffffff;
        }

        .btn-action.delete-btn {
            color: #ef4444;
        }

        .btn-action.delete-btn:hover {
            background: #ef4444;
            color: #ffffff;
        }
    </style>
@endpush

@section('content')
    <main class="main-content">
        <div class="legacy-names-page card">

            {{-- Page Header --}}
            <div class="legacy-page-header">
                <div>
                    <span class="legacy-page-eyebrow">
                        <i class="fa-solid fa-feather-pointed me-1"></i>
                        Family Memories
                    </span>

                    <h1 class="page-title">Legacy Names</h1>

                    <p class="page-sub">
                        Explore the names, stories, and memories that make your family unique.
                    </p>
                </div>

                <a href="javascript:void(0)" class="btn-post create-btn legacy-create-btn text-center"
                    style="padding: 12px 15px" data-url="{{ route('user.legacy-names.create') }}">
                    <i class="fa-solid fa-plus"></i>
                    <span>Create Legacy Name</span>
                </a>
            </div>

            {{-- Empty State --}}
            @if ($legacyNames->isEmpty())
                <div class="legacy-empty-state shadow-none border w-100 d-flex flex-column align-items-center justify-content-center text-center py-5 my-3"
                    style="width: 100%; max-width: 100%;">
                    <div class="empty-icon mb-3">
                        <i class="fa-solid fa-feather-pointed fa-3x"></i>
                    </div>

                    <h3 class="mb-2">No Legacy Names Yet</h3>

                    <p class="text-muted mb-4">
                        Start preserving your family history by creating your first legacy name.
                    </p>

                    <div class="text-center">
                        <a href="javascript:void(0)" class="btn-post create-btn legacy-create-btn text-center"
                            style="padding: 12px 15px" data-url="{{ route('user.legacy-names.create') }}">
                            <i class="fa-solid fa-plus"></i>
                            <span>Create Legacy Name</span>
                        </a>
                    </div>
                </div>

                {{-- <div class="legacy-empty-state border w-100">
                    <div class="empty-icon">
                        <i class="fa-solid fa-feather-pointed"></i>
                    </div>

                    <h3>No Legacy Names Yet</h3>

                    <p>
                        Start preserving your family history by creating your first legacy name.
                    </p>

                    <a href="{{ route('user.legacy-names.create') }}" class="legacy-create-btn">
                        <i class="fa-solid fa-plus"></i>
                        Create Your First Legacy Name
                    </a>
                </div> --}}
            @else
                {{-- Legacy Names Grid --}}
                <div class="legacy-names-grid">

                    @foreach ($legacyNames as $legacyName)
                        <div class="legacy-name-card position-relative">

                            {{-- Card Cover --}}
                            <div class="legacy-name-cover">
                                <div class="legacy-cover-pattern"></div>

                                <div class="legacy-card-icon">
                                    <i class="fa-solid fa-feather-pointed"></i>
                                </div>

                                <span class="legacy-card-label">
                                    Family Legacy
                                </span>


                                @if (auth()->check() && auth()->id() == optional($legacyName->family)->owner_user_id)
                                    <div class="legacy-card-actions">
                                        {{-- Edit Button --}}
                                        <button type="button" class="btn-action edit-btn"
                                            data-url="{{ route('user.legacy-names.edit', $legacyName->id) }}"
                                            title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        {{-- Delete Button --}}
                                        <form id="delete-form-{{ $legacyName->id }}"
                                            action="{{ route('user.legacy-names.destroy', $legacyName->id) }}"
                                            method="POST" class="d-inline delete-legacy-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn-action delete-btn"
                                                onclick="confirmDelete('{{ $legacyName->id }}')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            {{-- Card Content --}}
                            <div class="legacy-name-content">

                                <div>
                                    <h3 class="legacy-name-title" title="{{ $legacyName->name }}">
                                        {{ $legacyName->name }}
                                    </h3>

                                    <div class="legacy-card-details">

                                        @if ($legacyName->who_gave_this_name)
                                            <div class="legacy-card-detail">
                                                <i class="fa-solid fa-user-plus"></i>
                                                <span>
                                                    <small>Given by</small>
                                                    {{ $legacyName->who_gave_this_name }}
                                                </span>
                                            </div>
                                        @endif

                                        @if ($legacyName->family_generation)
                                            <div class="legacy-card-detail">
                                                <i class="fa-solid fa-people-group"></i>
                                                <span>
                                                    <small>Generation</small>
                                                    {{ $legacyName->family_generation }}
                                                </span>
                                            </div>
                                        @endif

                                    </div>
                                </div>

                                <a href="javascript:void(0)" class="btn-view-legacy view-btn"
                                    data-url="{{ route('user.legacy-names.show', $legacyName) }}">
                                    <span>View Legacy</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>

                            </div>

                        </div>
                    @endforeach

                </div>

                {{-- Pagination --}}
                @if ($legacyNames->hasPages())
                    <div class="legacy-pagination">
                        {{ $legacyNames->links('pagination::bootstrap-4') }}
                    </div>
                @endif

            @endif

        </div>
    </main>

    {{-- Modals --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Legacy Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="create-modal-content"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Legacy Name</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="edit-modal-content"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Legacy Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="view-modal-content">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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

        $(document).on('click', '.view-btn', function() {
            let url = $(this).data('url');
            $.get(url, function(data) {
                $('#view-modal-content').html(data);
                $('#viewModal').modal('show');
            });
        });
    </script>
    <script>
        function confirmDelete(postId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this legacy?",
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
