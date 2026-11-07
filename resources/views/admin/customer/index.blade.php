@extends('admin.layouts.app')
@section('title') {{ $data['title'] ?? '' }} @endsection
@section('users', 'active')

@php
    $rows = $data['rows'];
@endphp

@section('content')
    <div class="content-wrapper">

        <div class="content">
            <div class="">
                <div class="row">
                    <div class="">
                        <div class="card content-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h2 class="card-title mb-0">Manage {{ $data['title'] ?? '' }}</h2>
                            </div>

                            <div class="card-body table-responsive p-3">
                                <table id="dataTables" class="table table-striped table-hover">
                                    <thead class="table top-school-table mb-0">
                                        <tr>
                                            <th style="width:5%;">SN</th>
                                            <th style="width:10%;">Image</th>
                                            <th style="width:20%;">Name</th>
                                            <th style="width:25%;">Email</th>
                                            <th style="width:15%;">Registered</th>
                                            <th style="width:10%;">Status</th>
                                            <th style="width:15%;">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse ($rows as $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                {{-- Image --}}
                                                <td>
                                                    <img src="{{ getPhoto($row->image) }}" width="40" height="40"
                                                        style="border-radius:50%; object-fit:cover;">
                                                </td>

                                                <td>{{ $row->name ?? '-' }}</td>
                                                <td>{{ $row->email ?? '-' }}</td>

                                                <td>{{ optional($row->created_at)->format('M d, Y') ?? '-' }}</td>

                                                {{-- Status --}}
                                                <td>
                                                    @if ($row->status == 1)
                                                        <span class="text-success">Active</span>
                                                    @else
                                                        <span class="text-danger">Inactive</span>
                                                    @endif
                                                    {{-- <span class="badge {{ $row->status == 1 ? 'text-bg-success' : 'text-bg-secondary' }}">
                                                        {{ $row->status == 1 ? 'Active' : 'Inactive' }}
                                                    </span> --}}
                                                </td>

                                                {{-- Action --}}
                                                <td>
                                                    <div class="d-flex gap-2 justify-content-start">
                                                        
                                                        {{-- <a href="{{ route('admin.users.view', $row->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i> View</a> --}}
                                                        <a href="{{ route('admin.users.edit', $row->id) }}"
                                                        class="btn btn-sm btn-outline-secondary">
                                                           <i class="bi bi-pencil me-1"></i>Edit
                                                        </a>

                                                        {{-- <a href="{{ route('admin.users.delete', $row->id) }}"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure?')">
                                                            Delete
                                                        </a> --}}
                                                    </div>
                                                    
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No customers found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Password edit modal --}}
    <div class="modal fade" id="editPasswordModal" tabindex="-1" aria-labelledby="editPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPasswordModalLabel">{{ __('messages.common.change_password') }}
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.users.password.change') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="form-group">
                            <label for="code" class="form-label">{{ __('messages.common.password') }}</label>
                            <div class="input-group input-group-flat">
                                <input type="password" name="password" id="password-field" class="form-control shadow-none"
                                    placeholder="{{ __('messages.placeholder.your_password') }}" autocomplete="off"
                                    required>
                                <span class="input-group-text">
                                    <a href="javascript:void(0)"
                                        class="link-secondary fa fa-fw fa-eye field-icon toggle-password"
                                        toggle="#password-field">
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-danger me-2"
                                data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                            <button type="submit" class="btn btn-success">{{ __('messages.common.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- view modal --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="view-modal-content">

                </div>
            </div>
        </div>
    </div>

@endsection
@push('script')
    <script type="text/javascript">
        $(document).on('click', '.pass_change', function() {
            let user_id = $(this).data('id');
            $('#user_id').val(user_id);
            $('#editPasswordModal').modal('show');
        });
        // password show hide
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>
    <script>
        $(document).on('click', '.view-btn', function() {
            let url = $(this).data('url');

            $.get(url, function(data) {
                $('#view-modal-content').html(data);
                $('#viewModal').modal('show');
            });
        });
    </script>
@endpush
