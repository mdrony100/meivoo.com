<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | {{ config('app.name', 'Meivoo') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte/admin.css') }}">
</head>
<body class="login-page auth-page">
<div class="auth-shell">
    <div class="auth-panel">
        <section class="auth-hero">
            <div>
                <div class="auth-brand-mark">{{ strtoupper(substr(config('app.name', 'M'), 0, 1)) }}</div>
                <div class="auth-hero-copy mt-4">
                    <h1>Password recovery</h1>
                    <p>Request a new password for your admin account. Keep the flow short and predictable.</p>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge text-bg-light border text-secondary">Email reset</span>
                <span class="badge text-bg-light border text-secondary">Quick recovery</span>
            </div>
        </section>

        <section class="auth-card-shell">
            <div class="card auth-card">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="text-uppercase small fw-semibold text-secondary mb-2">Admin access</div>
                        <h2 class="h4 mb-1">Reset password</h2>
                        <p class="text-secondary mb-0">Enter your email and we will send a new password.</p>
                    </div>

                    @include('admin.components.flash-messages')
                    @include('admin.components.validation-errors')

                    <form method="POST" action="#">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label required" for="email">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control" placeholder="admin@example.com" required>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 btn-icon justify-content-center" type="submit">
                            <i class="bi bi-send"></i>
                            Send password
                        </button>
                    </form>

                    <div class="mt-3 text-center">
                        <a class="link-primary fw-semibold" href="{{ route('login.admin') }}">Back to login</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@include('admin.components.password-toggle-script')
</body>
</html>
