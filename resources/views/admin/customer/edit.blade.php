@extends('admin.layouts.app')

@section('title') {{ $data['title'] ?? 'Edit Customer' }} @endsection
@section('users', 'active')

@section('content')
    <div class="card content-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="card-title mb-0">{{ $data['title'] ?? 'Edit Customer' }}</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary btn-icon">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
            @csrf

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label required" for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label" for="image">Profile Image</label>
                        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        @if ($user->image)
                            <div class="mt-2">
                                <img src="{{ asset($user->image) }}" alt="Customer Image" width="80" height="80"
                                    style="border-radius: 50%; object-fit: cover;">
                            </div>
                        @endif
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required" for="status">Status</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary btn-icon"><i class="bi bi-check2"></i> Update Customer</button>
            </div>
        </form>
    </div>
@endsection
