@extends('layouts.user')
@section('title', 'LegacyTree - Family Tree')
@section('page_wrapper_class', 'tree-page')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/treeflex.css') }}">
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Poppins', sans-serif !important;
        }

        .main-content {
            overflow: auto;
        }

        #family-tree {
            min-width: max-content;
        }

        .main-content {
            overflow: auto;
        }

        .offcanvas-body {
            font-family: 'Poppins', sans-serif;
        }

        .search-sticky-wrapper {
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10;
            background-color: #fdfefe;
            padding: 15px 0;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom: 1px solid #eee;
        }

        .search-container {
            position: relative;
            width: 90%;
            max-width: 400px;
        }

        .modern-search {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border-radius: 30px;
            border: 2px solid #e0e0e0;
            outline: none;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
        }

        .modern-search:hover {
            border-color: #7c3aed;
            background-color: #fff;
        }

        .modern-search:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.2);
            background-color: #fff;
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            transition: color 0.3s ease;
        }

        .modern-search:focus+.search-icon {
            color: #7c3aed;
        }

        .custom-btn-primary {
            background-color: #984bf3;
            color: #ffffff;
            border: 1px solid #984bf3;
            transition: all 0.3s ease;
        }

        .custom-btn-primary:hover {
            background-color: #8332e2;
            color: #ffffff;
            border-color: #8332e2;
            box-shadow: 0 4px 12px rgba(152, 75, 243, 0.3) !important;
        }
    </style>
@endpush
@section('content')
    <main class="main-content p-0">
        <div class="search-sticky-wrapper">
            <div class="search-container">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="userSearch" class="modern-search" placeholder="Search members by name...">
            </div>
        </div>
        <div class="row justify-content-center align-items-center ">
            <div class="col-md-12">
                <div id="tree-scroll-wrapper">
                    <div id="family-tree">
                        @if ($parentMember)
                            <div class="go-up-wrapper mb-3">
                                <a href="{{ route('user.family-tree', $parentMember->id) }}"
                                    class="btn-add-member go-up-btn" data-bs-toggle="tooltip" data-bs-placement="top"
                                    title="Go to {{ $parentMember->user->name }} Family">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m18 15-6-6-6 6" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                        <ul class="tf-tree d-flex flex-column align-items-center">
                            @include('user.partials.family_node', ['member' => $current])
                        </ul>
                    </div>
                </div>
            </div>
    </main>

    {{-- OFFCANVAS --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="memberOffcanvas" aria-labelledby="memberOffcanvasLabel"
        style="width:550px;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="memberOffcanvasLabel">Member Info</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="memebrInfo"></div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#userSearch').on('input', function() {
                let term = $(this).val();

                if (term.length > 5) {
                    $.ajax({
                        url: "{{ route('user.search-member') }}",
                        data: {
                            term: term
                        },
                        success: function(data) {
                            if (data.id) {
                                window.location.href = "{{ route('user.family-tree', '') }}/" +
                                    data.id +
                                    "?search_term=" + encodeURIComponent(term);
                            }
                        }
                    });
                }
            });

            const urlParams = new URLSearchParams(window.location.search);
            const searchTerm = urlParams.get('search_term');
            if (searchTerm) {
                $('#userSearch').val(decodeURIComponent(searchTerm));
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '.viewFamilyMemberInfo', function(noLoad) {
                noLoad.preventDefault();
                const id = $(this).data('id');
                const container = $('#memebrInfo');
                const offcanvasEl = document.getElementById('memberOffcanvas');
                const bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);

                bsOffcanvas.show();

                container.html(
                    '<div class="text-center py-5"><div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div></div>'
                );

                $.ajax({
                    url: "{{ route('user.family-member.info', ':id') }}".replace(':id', id),
                    type: 'GET',
                    success: function(html) {
                        container.html(html);
                    },
                    error: function() {
                        container.html(`
                            <div class="text-center text-danger py-5">
                                <i class="fas fa-exclamation-circle fs-3 mb-2"></i>
                                <p>Failed to load member info.</p>
                            </div>
                        `);
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
@endpush
