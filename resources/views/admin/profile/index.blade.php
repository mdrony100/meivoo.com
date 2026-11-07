@extends('admin.layouts.app')
@section('profile', 'active')
@section('content')
@php
    $admin = auth('admin')->user();
@endphp
<div class="row g-3 justify-content-center mt-3">
    <div class="col-lg-4">
        <div class="card content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-title mb-0">Profile Details</h2>
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary btn-sm btn-icon"><i class="bi bi-pencil"></i>Edit</a>
            </div>
            <div class="card-body text-center">
                <img src="{{ getPhoto($admin->image ?? null) }}" class="rounded-circle mb-3" width="104" height="104" alt="Admin avatar">
                <h2 class="h4 mb-1">{{ $admin->name ?? 'Admin' }}</h2>
                <p class="text-secondary mb-3">{{ $admin->email ?? '-' }}</p>
                <span class="badge {{ ($admin->status ?? 1) ? 'text-bg-success' : 'text-bg-secondary' }}">{{ ($admin->status ?? 1) ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>
    </div>
    {{-- <div class="col-lg-8">
        <div class="card content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-title mb-0">Profile Details</h2>
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary btn-sm btn-icon"><i class="bi bi-pencil"></i>Edit</a>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Name</dt>
                    <dd class="col-sm-9">{{ $admin->name ?? '-' }}</dd>
                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9">{{ $admin->email ?? '-' }}</dd>
                    <dt class="col-sm-3">Roles</dt>
                    <dd class="col-sm-9">
                        @forelse($admin?->roles ?? collect() as $role)
                            <span class="badge text-bg-primary">{{ $role->name }}</span>
                        @empty
                            <span class="text-secondary">No role assigned</span>
                        @endforelse
                    </dd>
                </dl>
            </div>
        </div>
    </div> --}}
</div>
@endsection
