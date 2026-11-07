@extends('layouts.frontend')
@section('title', 'Reset Password')
@section('hide_navbar', true)
@section('hide_footer', true)

@section('meta')
    <meta property="og:title" content="{{ $settings->seo_meta_title }}">
    <meta property="og:description" content="{{ $settings->seo_meta_description }}">
    <meta property="og:image" content="{{ asset($settings->seo_image) }}">
    <meta name="description" content="{{ $settings->seo_meta_description }}">
    <meta name="keywords" content="{{ $settings->seo_keywords }}">
@endsection

@section('content')
<div class="forgot-page">
    <main class="forgot-shell">
        <div class="container py-4 py-lg-5">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-7 col-xl-6 col-xxl-5">

                    <!-- Brand -->
                    <div class="forgot-top text-center mb-0 mb-md-2" data-aos="fade-up">
                        @if(!empty($settings->site_logo))
                            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('frontend.home') }}">
                                    <img src="{{ asset($settings->site_logo) }}"
                                        alt="Logo"
                                        style="height:40px; width:auto;">
                                {{-- <div class="navbar-brand-icon">
                                    <i class="fa-solid fa-tree"></i>
                                </div>
                                <span class="brand-text">Family Legacy</span> --}}
                            </a>
                        @endif
                    </div>

                    <!-- Card -->
                    <div class="forgot-card text-center" data-aos="fade-up" data-aos-delay="80">

                        <!-- Icon -->
                        <div class="forgot-icon-wrap mx-auto mb-4">
                            <i class="fa-solid fa-lock"></i>
                        </div>

                        <!-- Title -->
                        <h1 class="forgot-title mb-2">Reset Password</h1>

                        <!-- Subtitle -->
                        <p class="forgot-subtitle mb-2 mb-sm-3 mb-md-4 mx-auto">
                            Enter your new password to secure your account.
                        </p>

                        <!-- Form -->
                        <form method="POST" action="{{ route('password.update') }}" class="text-start">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <!-- Email -->
                            <div class="mb-4">
                                <label class="form-label forgot-form-label">Email Address</label>
                                <input type="email"
                                       name="email"
                                       value="{{ $email ?? old('email') }}"
                                       class="form-control forgot-input @error('email') is-invalid @enderror"
                                       placeholder="your@email.com"
                                       required>

                                @error('email')
                                    <span class="invalid-feedback d-block">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label class="form-label forgot-form-label">New Password</label>
                                <input type="password"
                                       name="password"
                                       class="form-control forgot-input @error('password') is-invalid @enderror"
                                       placeholder="New password"
                                       required>

                                @error('password')
                                    <span class="invalid-feedback d-block">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label class="form-label forgot-form-label">Confirm Password</label>
                                <input type="password"
                                       name="password_confirmation"
                                       class="form-control forgot-input"
                                       placeholder="Confirm password"
                                       required>
                            </div>

                            <!-- Button -->
                            <button type="submit"
                                    class="btn btn-forgot-primary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                Reset Password
                                <i class="fa-solid fa-arrow-right"></i>
                            </button>

                        </form>

                    </div>

                    <!-- Back -->
                    <a href="{{ route('login') }}"
                       class="forgot-back-link d-inline-flex align-items-center justify-content-center gap-2 mt-4">
                        <i class="fa-solid fa-arrow-left"></i>
                        Back to Sign In
                    </a>

                </div>
            </div>
        </div>
    </main>
</div>
@endsection
