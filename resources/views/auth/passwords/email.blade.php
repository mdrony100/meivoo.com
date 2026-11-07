@extends('layouts.frontend')
@section('title', 'Forgot Password')
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
                        {{-- <div class="forgot-top text-center mb-0 mb-md-2" data-aos="fade-up">
                            @if(!empty($settings->site_logo))
                                <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="{{ route('frontend.home') }}">
                                        <img src="{{ asset($settings->site_logo) }}"
                                            alt="Logo"
                                            style="height:40px; width:auto;"> 
                            @endif
                        </div> --}}

                        <div class="forgot-card text-center" data-aos="fade-up" data-aos-delay="80">
                            <div class="forgot-icon-wrap mx-auto mb-4">
                                <i class="fa-regular fa-envelope"></i>
                            </div>

                            <h1 class="forgot-title mb-2">Forgot Password?</h1>
                            <p class="forgot-subtitle mb-2 mb-sm-3 mb-md-4 mx-auto">
                                No worries! Enter your email and we will send you a link to reset your password.
                            </p>

                            <form action="{{ route('password.email') }}" method="POST" class="text-start">
                                @csrf
                                <div class="mb-4">
                                    <label for="forgotEmail" class="form-label forgot-form-label">Email Address</label>
                                    <input type="email" id="forgotEmail" name="email" value="{{ old('email') }}"
                                        class="form-control forgot-input" placeholder="your@email.com" autocomplete="email"
                                        required>
                                </div>

                                <button type="submit"
                                    class="btn btn-forgot-primary w-100 d-inline-flex align-items-center justify-content-center gap-2">
                                    Send Reset Link
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </form>
                        </div>

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
