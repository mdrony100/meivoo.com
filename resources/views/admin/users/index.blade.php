@extends('admin.layouts.app')

@push('styles')
<style>
    .modal-dialog-scrollable .modal-content {
        overflow: auto;
    }
</style>
@endpush
@section('content')
@php
    $admins = $admins ?? collect();
    $roles = $roles ?? collect();
    $permissions = $permissions ?? collect();
@endphp
<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">Admin Users</h2>
        <button type="button" class="btn btn-primary btn-sm btn-icon" data-bs-toggle="modal" data-bs-target="#adminUserModal" data-mode="create">
            <i class="bi bi-plus-lg"></i>Add Admin
        </button>
    </div>
    <div class="card-body p-0">
        @if($admins->count())
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="adminsTable">
                    <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($admins as $admin)
                        @php
                            $role = $admin->roles->first();
                            $initials = strtoupper(substr($admin->name ?? 'A', 0, 2));
                        @endphp
                        <tr data-user-row="{{ $admin->id }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar">{{ $initials }}</span>
                                    <div>
                                        <div class="fw-semibold" data-user-name>{{ $admin->name }}</div>
                                        <div class="text-secondary small">{{ $admin->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge text-bg-primary" data-user-role>{{ $role->name ?? 'No role' }}</span></td>
                            <td><span data-user-permissions>{{ $admin->getAllPermissions()->count() }}</span></td>
                            <td><span class="badge {{ $admin->status ? 'text-bg-success' : 'text-bg-secondary' }}" data-user-status>{{ $admin->status ? 'Active' : 'Suspended' }}</span></td>
                            <td>{{ optional($admin->created_at)->format('M d, Y') ?? '-' }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-icon" data-edit-user="{{ $admin->id }}"><i class="bi bi-pencil"></i>Edit</button>
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-icon" data-suspend-user="{{ $admin->id }}"><i class="bi bi-pause-circle"></i>{{ $admin->status ? 'Suspend' : 'Activate' }}</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-icon" data-password-user="{{ $admin->id }}" data-user-label="{{ $admin->name }}"><i class="bi bi-key"></i>Password</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('admin.components.empty-state', ['title' => 'No admin users found', 'message' => 'Add an admin user to manage access.', 'icon' => 'bi-people'])
        @endif
    </div>
</div>

<div class="modal fade" id="adminUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form id="adminUserForm">
                @csrf
                <input type="hidden" name="_method" id="adminUserMethod" value="POST">
                <input type="hidden" id="adminUserId">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminUserModalTitle">Add Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="adminUserErrors"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required" for="admin_name">Name</label>
                            <input type="text" name="name" id="admin_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 create-only">
                            <label class="form-label required" for="admin_email">Email</label>
                            <input type="email" name="email" id="admin_email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required" for="admin_role">Role</label>
                            <input list="roleOptions" type="text" name="role" id="admin_role" class="form-control" required>
                            <datalist id="roleOptions">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}"></option>
                                @endforeach
                            </datalist>
                        </div>
                        <div class="col-md-6 create-only">
                            <label class="form-label" for="admin_password">Password</label>
                            <input type="password" name="password" id="admin_password" class="form-control" minlength="6">
                        </div>
                        <div class="col-md-6 edit-only d-none">
                            <label class="form-label required" for="admin_status">Status</label>
                            <select name="status" id="admin_status" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Suspended</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Permissions</label>
                            <div class="row g-2">
                                @forelse($permissions as $groupName => $groupPermissions)
                                    <div class="col-md-6">
                                        <div class="border rounded p-3 h-100">
                                            <div class="fw-semibold mb-2">{{ ucwords(str_replace(['-', '_'], ' ', $groupName ?: 'General')) }}</div>
                                            @foreach($groupPermissions as $permission)
                                                <div class="form-check">
                                                    <input class="form-check-input admin-permission" type="checkbox" name="permissions[]" id="user_permission_{{ $permission->id }}" value="{{ $permission->name }}">
                                                    <label class="form-check-label" for="user_permission_{{ $permission->id }}">{{ permission_action($permission->name) }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-secondary">No permissions available.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-icon"><i class="bi bi-check2"></i>Save Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" id="passwordForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label required" for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required minlength="8">
                    </div>
                    <div>
                        <label class="form-label required" for="password_confirmation">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="8">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-icon"><i class="bi bi-key"></i>Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const userModalElement = document.getElementById('adminUserModal');
    const userModal = bootstrap.Modal.getOrCreateInstance(userModalElement);
    const userForm = document.getElementById('adminUserForm');
    const errorsBox = document.getElementById('adminUserErrors');

    function userRoute(template, id) {
        return template.replace('__ID__', id);
    }

    function showErrors(message) {
        errorsBox.classList.remove('d-none');
        errorsBox.textContent = message;
    }

    userModalElement.addEventListener('show.bs.modal', function (event) {
        if (event.relatedTarget?.dataset.mode !== 'create') {
            return;
        }

        userForm.reset();
        errorsBox.classList.add('d-none');
        document.getElementById('adminUserId').value = '';
        document.getElementById('adminUserMethod').value = 'POST';
        document.getElementById('adminUserModalTitle').textContent = 'Add Admin';
        document.querySelectorAll('.create-only').forEach(element => element.classList.remove('d-none'));
        document.querySelectorAll('.edit-only').forEach(element => element.classList.add('d-none'));
    });

    document.addEventListener('click', async function (event) {
        const editButton = event.target.closest('[data-edit-user]');
        const suspendButton = event.target.closest('[data-suspend-user]');
        const passwordButton = event.target.closest('[data-password-user]');

        if (editButton) {
            const id = editButton.dataset.editUser;
            const response = await fetch(userRoute(@json(route('admin.users.edit', '__ID__')), id), { headers: { 'Accept': 'application/json' } });
            const user = await response.json();
            userForm.reset();
            errorsBox.classList.add('d-none');
            document.getElementById('adminUserId').value = user.id;
            document.getElementById('adminUserMethod').value = 'PUT';
            document.getElementById('adminUserModalTitle').textContent = 'Edit Admin';
            document.getElementById('admin_name').value = user.name || '';
            document.getElementById('admin_role').value = user.role || '';
            document.getElementById('admin_status').value = user.status ? '1' : '0';
            document.querySelectorAll('.create-only').forEach(element => element.classList.add('d-none'));
            document.querySelectorAll('.edit-only').forEach(element => element.classList.remove('d-none'));
            document.querySelectorAll('.admin-permission').forEach(input => {
                input.checked = [...(user.rolePermissions || []), ...(user.userPermissions || [])].includes(input.value);
            });
            userModal.show();
        }

        if (suspendButton) {
            const id = suspendButton.dataset.suspendUser;
            const response = await fetch(userRoute(@json(route('admin.users.suspend', '__ID__')), id), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            });
            if (response.ok) {
                window.location.reload();
            }
        }

        if (passwordButton) {
            document.getElementById('passwordForm').action = userRoute(@json(route('admin.users.change-password', '__ID__')), passwordButton.dataset.passwordUser);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('passwordModal')).show();
        }
    });

    userForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        errorsBox.classList.add('d-none');

        const id = document.getElementById('adminUserId').value;
        const method = document.getElementById('adminUserMethod').value;
        const url = id ? userRoute(@json(route('admin.users.update', '__ID__')), id) : @json(route('admin.users.store'));
        const formData = new FormData(userForm);
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }

        const response = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData
        });

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            showErrors(data.message || 'Unable to save admin user.');
            return;
        }

        window.location.reload();
    });
</script>
@endpush
