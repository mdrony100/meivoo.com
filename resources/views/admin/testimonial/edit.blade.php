@extends('admin.layouts.app')

@section('testimonials', 'active')

@section('content')
<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">Edit Testimonial</h2>
        <a href="{{ route('admin.testimonial.index') }}" class="btn btn-primary btn-gradient btn-sm">
            Back
        </a>
    </div>

    <form action="{{ route('admin.testimonial.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            @include('admin.testimonial._form', ['testimonial' => $testimonial])
        </div>
        <div class="card-footer text-end">
            {{-- <a href="{{ route('admin.testimonial.index') }}" class="btn btn-light">Cancel</a> --}}
            <button type="submit" class="btn btn-primary btn-icon">
                <i class="bi bi-check2"></i>Update Testimonial
            </button>
        </div>
    </form>
</div>
@endsection
