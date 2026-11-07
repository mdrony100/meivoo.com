@php
    $selectedPermissions = old('permissions', isset($role) ? $role->permissions->pluck('name')->toArray() : []);
@endphp
<div class="card-body">
    <div class="mb-3">
        <label class="form-label required" for="name">Role Name</label>
        <input type="text" name="name" id="name" value="{{ old('name', $role->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="settings-section-title">Permissions</div>
    @forelse($permission_groups ?? collect() as $group)
        @php
            $groupPermissions = \App\Models\Admin::getpermissionsByGroupName($group->name);
        @endphp
        <div class="border rounded p-3 mb-3">
            <div class="fw-semibold mb-2">{{ ucwords(str_replace(['-', '_'], ' ', $group->name)) }}</div>
            <div class="row g-2">
                @foreach($groupPermissions as $permission)
                    <div class="col-md-6 col-xl-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->name }}" {{ in_array($permission->name, $selectedPermissions, true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="permission_{{ $permission->id }}">{{ permission_action($permission->name) }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        @include('admin.components.empty-state', ['title' => 'No permissions found', 'message' => 'Create permissions before assigning them to roles.', 'icon' => 'bi-shield'])
    @endforelse
</div>
<div class="card-footer text-end">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary btn-icon"><i class="bi bi-check2"></i>{{ $buttonText ?? 'Save Role' }}</button>
</div>
