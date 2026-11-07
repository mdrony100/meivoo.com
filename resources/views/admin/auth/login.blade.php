<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | {{ config('app.name', 'Meivoo') }}</title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="{{ asset('adminlte/admin.css') }}">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/mv-admin.css') }}">
    <style>
        body.admin-login-page {
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, .10), transparent 30%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, .10), transparent 28%),
                linear-gradient(180deg, #fbfcfe 0%, #f4f7fb 100%);
        }

        .admin-login-shell {
            align-items: center;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .admin-login-card {
            background: #fff;
            border: 1px solid #dbe4ef;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
            overflow: hidden;
            width: 28rem !important;
        }

        .admin-login-panel {
            padding: 3rem 3rem 2rem;
        }

        .admin-login-kicker {
            color: #7b8794;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .01em;
            text-transform: uppercase;
        }

        .admin-login-title {
            color: #1f2937;
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 1.5;
            margin: .35rem 0 2.45rem;
        }

        .admin-login-subtitle {
            color: #6b7280;
            font-size: 1.06rem;
            margin-bottom: 0;
        }

        .admin-login-label {
            color: #334155;
            font-size: .98rem;
            font-weight: 600;
            margin-bottom: .55rem;
        }

        .admin-login-input,
        .password-field .form-control {
            border-color: #d8e0ea;
            border-radius: .5rem;
            box-shadow: none;
            font-size: 1.02rem;
            min-height: 3rem;
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .admin-login-input::placeholder,
        .password-field .form-control::placeholder {
            color: #94a3b8;
        }

        .admin-login-input:focus,
        .password-field .form-control:focus {
            border-color: rgba(37, 99, 235, .58);
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .10);
        }

        .admin-login-card .input-group-text {
            background: #fff;
            border-color: #d8e0ea;
            color: #64748b;
            justify-content: center;
            min-width: 3rem;
        }

        .password-field .form-control {
            border-end-end-radius: 0;
            border-start-end-radius: 0;
        }

        .password-toggle {
            align-items: center;
            background: #fff;
            border-color: #d8e0ea;
            color: #64748b;
            display: inline-flex;
            justify-content: center;
            min-width: 3.2rem;
        }

        .password-toggle:hover,
        .password-toggle:focus {
            background: #f8fafc;
            color: #1f2937;
        }

        .password-toggle.is-active {
            color: #2563eb;
        }

        .input-group>.password-toggle {
            border-start-end-radius: .5rem;
            border-end-end-radius: .5rem;
        }

        .admin-login-submit {
            background: #1670e8;
            border: 0;
            border-radius: .5rem;
            box-shadow: 0 12px 24px rgba(22, 112, 232, .18);
            font-size: 1.02rem;
            font-weight: 700;
            min-height: 3.4rem;
        }

        .admin-login-submit:hover,
        .admin-login-submit:focus {
            background: #0f63d1;
            box-shadow: 0 14px 26px rgba(22, 112, 232, .24);
        }

        .admin-login-footer {
            align-items: center;
            border-top: 1px solid #e5eaf1;
            background: #fafbfd;
            display: flex;
            justify-content: center;
            min-height: 4.4rem;
            padding: .75rem 1rem;
        }

        .admin-login-footer a {
            color: #6b7280;
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
        }

        .admin-login-footer a:hover {
            color: #111827;
        }

        @media (max-width: 575.98px) {
            .admin-login-shell {
                padding: 1rem;
            }

            .admin-login-panel {
                padding: 1.5rem 1.25rem 1.25rem;
            }

            .admin-login-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>

<body class="login-page auth-page admin-login-page">
    <div class="admin-login-shell">
        <div class="admin-login-card">
            <div class="admin-login-panel">
                <div class="text-center mb-4">
                    <h1 class="admin-login-title">Admin Login</h1> 
                </div>

                <form method="POST" action="{{ route('admin.login') }}" novalidate>
                    @csrf

                    <div class="mb-4">
                        <label class="form-label admin-login-label required" for="email">Admin Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="form-control admin-login-input @error('email') is-invalid @enderror"
                                placeholder="Enter admin email" required autofocus>
                        </div>
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label admin-login-label required" for="password">Password</label>
                        <input type="password" name="password" id="password"
                            class="form-control admin-login-input @error('password') is-invalid @enderror"
                            placeholder="Enter password" required>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit"
                        class="btn btn-primary admin-login-submit w-100 btn-icon justify-content-center">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Sign In
                    </button>
                </form>
            </div>

            <div class="admin-login-footer">
                <a href="{{ url('/') }}">
                    <i class="bi bi-arrow-left"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    @include('admin.components.password-toggle-script')
</body>

</html>
