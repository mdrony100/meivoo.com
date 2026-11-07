@extends('layouts.frontend')
@section('title', 'Login')
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

    <div class="login-page">
        <main class="auth-shell">
            <div class="row g-0 min-vh-100">
                <section class="col-lg-6 d-none d-lg-flex auth-visual align-items-end p-4 p-xl-5">
                    <div class="auth-visual-content">
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

                        <div class="pb-4 pb-xl-5">
                            <h1 class="display-5 mb-3">Reconnect With Your Family Story</h1>
                            <p class="lead mb-4">
                                Preserve memories, build your family tree, and stay connected across generations.
                            </p>

                            <div class="d-flex flex-wrap align-items-center gap-3">
                                <div class="avatar-stack">
                                    <div class="avatar"><img src="https://randomuser.me/api/portraits/women/44.jpg"
                                            alt="Family member"></div>
                                    <div class="avatar"><img src="https://randomuser.me/api/portraits/men/32.jpg"
                                            alt="Family member"></div>
                                    <div class="avatar"><img src="https://randomuser.me/api/portraits/women/68.jpg"
                                            alt="Family member"></div>
                                    <div class="avatar"><img src="https://randomuser.me/api/portraits/men/75.jpg"
                                            alt="Family member"></div>
                                    <div class="avatar"><img src="https://randomuser.me/api/portraits/women/12.jpg"
                                            alt="Family member"></div>
                                </div>
                                <p class="mb-0 small fw-semibold text-white-50">Join 50,000+ families</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section
                    class="col-lg-6 auth-panel d-flex align-items-center justify-content-center px-3 px-sm-4 px-md-5 py-5">
                    <div class="auth-card">
                        {{-- <div
                            class="d-flex align-items-center justify-content-center justify-content-sm-start gap-3 mb-4 d-lg-none">
                            <span class="brand-mark bg-gradient-purple text-white"><i class="fa-solid fa-tree"></i></span>
                            <div>
                                <div class="fw-bold" style="font-family:'Playfair Display',serif;font-size:1.35rem;">Family
                                    Legacy</div>
                                <div class="small text-muted">Private family portal</div>
                            </div>
                        </div> --}}

                        <div class="text-center text-sm-start">
                            <div class="auth-badge mb-3">
                                <i class="fa-solid fa-lock"></i>
                                Secure family login
                            </div>
                        </div>

                        <h2 class="auth-title mb-2">Welcome back</h2>
                        <p class="auth-subtitle mb-4">Sign in to your family portal</p>


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

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="form-control auth-input" placeholder="Enter your email address" autocomplete="email"
                                    required>
                                @error('emial')
                                    <div class="text-dange">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label for="password" class="form-label mb-0">Password</label>
                                    <a class="small auth-footer-link" href="{{ route('password.request') }}">Forgot
                                        password?</a>
                                </div>
                                <div class="password-wrap">
                                    <input type="password" id="password" name="password" class="form-control auth-input"
                                        placeholder="Enter your password" autocomplete="current-password" required>
                                    <button type="button" class="password-toggle" id="togglePassword"
                                        aria-label="Toggle password visibility">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit"
                                class="btn btn-auth-primary w-100 d-inline-flex align-items-center justify-content-center text-decoration-none mb-3">
                                Sign In to Family Portal
                            </button>
                        </form>

                        <p class="mt-4 mb-0 text-center text-muted">
                            New to Family Legacy?
                            <a class="auth-footer-link" href="{{ route('register') }}">Create your family
                                portal</a>
                        </p>
                    </div>
                </section>
            </div>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        $('#togglePassword').on('click', function() {
            var input = document.getElementById('password');
            var icon = this.querySelector('i');
            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        });
    </script>
@endpush
