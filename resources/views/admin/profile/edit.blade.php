@extends('admin.layouts.app')

@section('content')
@php
    $admin = auth('admin')->user();
@endphp
<div class="row g-3">
    <div class="col-lg-7">
        <div class="card content-card">
            <div class="card-header">
                <h2 class="card-title mb-0">Edit Profile</h2>
            </div>
            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required" for="name">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $admin->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label required" for="email">Email</label>
                        <input type="email" name="email" id="email" readonly value="{{ old('email', $admin->email ?? '') }}" class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="image">Profile Image</label>
                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary btn-icon"><i class="bi bi-check2"></i>Save Profile</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card content-card">
            <div class="card-header">
                <h2 class="card-title mb-0">Change Password</h2>
            </div>
            <form method="POST" action="{{ route('admin.password.update') }}">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label required" for="password">New Password</label>
                        <input type="password" name="password" id="password" placeholder="New password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label required" for="confirm_password">Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" class="form-control @error('confirm_password') is-invalid @enderror" required>
                        @error('confirm_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-outline-primary btn-icon"><i class="bi bi-key"></i>Update Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
