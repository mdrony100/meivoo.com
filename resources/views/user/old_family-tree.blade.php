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

        .offcanvas-body{
            font-family: 'Poppins', sans-serif;
        }
    </style>
@endpush
@section('content')
    <main class="main-content p-0">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-12">
                <div id="family-tree" class="ft-tree">
                    @if ($parentMember)
                        <div class="go-up-wrapper mb-3">
                            <a href="{{ route('user.family-tree', $parentMember->id) }}" class="btn-add-member go-up-btn"
                                data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Go to {{ $parentMember->user->name }} Family">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
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
    <div class="offcanvas offcanvas-end" tabindex="-1" id="memberOffcanvas" aria-labelledby="memberOffcanvasLabel">
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
