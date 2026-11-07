@extends('admin.layouts.app')

@section('content')
<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">Roles</h2>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm btn-icon"><i class="bi bi-plus-lg"></i>Create Role</a>
    </div>
    <div class="card-body p-0">
        @if($roles->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Permissions</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td>{{ ($i ?? 0) + $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $role->name }}</td>
                            <td><span class="badge text-bg-light">{{ $role->guard_name }}</span></td>
                            <td>{{ $role->permissions_count ?? $role->permissions->count() }}</td>
                            <td>{{ optional($role->created_at)->format('M d, Y') ?? '-' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-primary btn-icon"><i class="bi bi-pencil"></i>Edit</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('admin.components.empty-state', ['title' => 'No roles found', 'message' => 'Create a role to organize admin permissions.', 'icon' => 'bi-shield'])
        @endif
    </div>
    @if($roles->hasPages())
        <div class="card-footer">
            {{ $roles->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
