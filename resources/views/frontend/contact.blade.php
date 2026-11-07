@extends('layouts.frontend')

@section('title', 'Contact Us')

{{-- @section('meta')
    <meta property="og:title" content="{{ $settings->seo_meta_title }}">
    <meta property="og:description" content="{{ $settings->seo_meta_description }}">
    <meta name="description" content="{{ $settings->seo_meta_description }}">
    <meta name="keywords" content="{{ $settings->seo_keywords }}">
@endsection --}}

@push('styles')
    <style>
        .form-control {
            border-radius: 14px;
            border-color: rgba(31, 35, 64, 0.12);
            background: rgba(248, 250, 252, 0.9);
            padding-left: 1rem;
            padding-right: 3rem;
            box-shadow: none !important;
            padding: 12px 10px;
        }

        .form-contro:focus {
            border-color: rgba(124, 58, 237, 0.3);
            box-shadow: 0 0 0 0.2rem rgba(124, 58, 237, 0.1);
            background: #fff;
        }
    </style>
@endpush

@section('content')
    <section class="py-5">
        <div class="container">

            <div class="text-center mb-5">
                <h1 class="fw-bold">Contact Us</h1>
                <p class="text-muted">
                    We'd love to hear from you. Send us a message and we'll respond as soon as possible.
                </p>
            </div>

            <div class="row g-4 d-flex justify-content-center align-items-center">

                <!-- Contact Form -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form action="{{ route('contact.store') }}" method="POST">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name" class="form-control auth-input"
                                            value="{{ old('name') }}" required placeholder="Enter your name">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email" class="form-control auth-input"
                                            value="{{ old('email') }}" required placeholder="Enter your email">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <input type="text" name="subject" class="form-control auth-input"
                                        value="{{ old('subject') }}" placeholder="Enter subject">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Message</label>
                                    <textarea name="message" rows="6" class="form-control auth-input" required
                                        placeholder="Write your message here...">{{ old('message') }}</textarea>
                                </div>

                                <button type="submit" class="btn-hero-cta px-5">
                                    Send Message
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
@endpush
