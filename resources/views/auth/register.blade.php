@extends('layouts.frontend')
@section('title', 'Sign Up')
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

    <div class="signup-page has-visual">
        @include('frontend.partials.auth_visual')
        <main class="signup-shell">
            <div class="container py-4 py-lg-5">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-7 col-xl-6 col-xxl-5">
                        <div class="signup-header text-center mb-3 mb-md-4" data-aos="fade-up">
                            <a class="signup-brand justify-content-center mb-4 text-decoration-none"
                                href="{{ route('frontend.home') }}">
                                @if(!empty($settings->site_logo))
                                    <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none justify-content-center" href="{{ route('frontend.home') }}">
                                            <img src="{{ asset($settings->site_logo) }}" class="img-fluid"
                                                alt="Logo"
                                                style="height:40px; width:auto;">
                                        {{-- <div class="navbar-brand-icon">
                                            <i class="fa-solid fa-tree"></i>
                                        </div>
                                        <span class="brand-text">Family Legacy</span> --}}
                                    </a>
                                @endif
                                {{-- <span class="signup-brand-mark"><i class="fa-solid fa-tree"></i></span>
                                <span class="signup-brand-text">Family Legacy</span> --}}
                            </a>

                            <h1 class="signup-title">Start Your Journey</h1>
                            <p class="signup-subtitle mt-2 mb-0">Join 50,000+ families preserving their heritage</p>
                        </div>

                        <div class="signup-card" data-aos="fade-up" data-aos-delay="80">
                            <div class="signup-card-body">

                                <div class="d-grid gap-3 mb-4">
                                    <a href="{{ route('google.login') }}"
                                        class="btn btn-auth-google d-flex align-items-center justify-content-center gap-2 text-decoration-none">
                                        <svg style="height: 20px;" viewBox="0 0 24 24" aria-hidden="true">
                                            <path fill="#4285F4"
                                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z">
                                            </path>
                                            <path fill="#34A853"
                                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z">
                                            </path>
                                            <path fill="#FBBC05"
                                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z">
                                            </path>
                                            <path fill="#EA4335"
                                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z">
                                            </path>
                                        </svg>
                                        Continue with Google
                                    </a>
                                </div>

                                <div class="auth-divider mb-4">or sign in with email</div>

                                <form action="{{ route('register') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="signupName" class="form-label signup-form-label">Full Name</label>
                                        <input type="text" id="signupName" name="name" value="{{ old('name') }}"
                                            class="form-control signup-input @error('name') is-invalid @enderror"
                                            placeholder="Enter your full name" autocomplete="name" required>

                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="signupEmail" class="form-label signup-form-label">Email Address</label>
                                        <input type="email" id="signupEmail" name="email" value="{{ old('email') }}"
                                            class="form-control signup-input @error('email') is-invalid @enderror"
                                            placeholder="Enter your email address" autocomplete="email" required>

                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="signupPassword" class="form-label signup-form-label">Password</label>
                                        <div class="signup-password-wrap">
                                            <input type="password" id="signupPassword" name="password"
                                                class="form-control signup-input @error('password') is-invalid @enderror"
                                                placeholder="Create a secure password" autocomplete="new-password" required>

                                            <button type="button" class="signup-password-toggle"
                                                data-password-toggle="signupPassword">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                        </div>

                                        @error('password')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mb-0">
                                        <label for="signupConfirmPassword" class="form-label signup-form-label">
                                            Confirm Password
                                        </label>

                                        <div class="signup-password-wrap">
                                            <input type="password" id="signupConfirmPassword" name="password_confirmation"
                                                class="form-control signup-input @error('password_confirmation') is-invalid @enderror"
                                                placeholder="Confirm your password" autocomplete="new-password" required>

                                            <button type="button" class="signup-password-toggle"
                                                data-password-toggle="signupConfirmPassword">
                                                <i class="fa-regular fa-eye"></i>
                                            </button>
                                        </div>

                                        @error('password_confirmation')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div
                                        class="signup-footer-row d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center justify-content-between gap-3 mt-4">
                                        <div class="text-center text-sm-start">
                                            <a href="{{ route('login') }}" class="mb-0 text-muted small">Already have an
                                                account?
                                            </a>
                                        </div>

                                        <button type="submit" class="btn btn-signup-primary px-4">
                                            Create Account
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach(function(button) {
            button.addEventListener('click', function() {
                var targetId = this.getAttribute('data-password-toggle');
                var input = document.getElementById(targetId);
                var icon = this.querySelector('i');
                var isPassword = input.type === 'password';

                input.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            });
        });
    </script>
@endpush
