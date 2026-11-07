@php
    $pageTitle = $title ?? data_get($data ?? [], 'title', config('app.name', 'Admin'));
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }} | {{ config('app.name', 'Meivoo') }}</title>
    <link rel="icon" type="image/x-icon"
        href="{{ $settings->favicon ? asset($settings->favicon) : asset('assets/images/fab-icon.ico') }}">

    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="{{ asset('adminlte/admin.css') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/mv-admin.css') }}">
    <style>
        .card-body::after,
        .card-footer::after,
        .card-header::after {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        @include('admin.layouts.partials.navbar')
        @include('admin.layouts.partials.sidebar')

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h1 class="mb-0 fs-3">{{ $pageTitle }}</h1>
                        </div>
                        <div class="col-sm-6">
                            @include('admin.layouts.partials.breadcrumb', ['pageTitle' => $pageTitle])
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

        </main>

        @include('admin.layouts.partials.footer')
    </div>

    @include('admin.components.confirm-delete-modal')
    @include('admin.components.password-toggle-script')

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}


    {{-- <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script> --}}
    {{-- <script>
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script> --}}
    <script>
        document.addEventListener('click', function(event) {
            const trigger = event.target.closest('[data-confirm-url]');
            if (!trigger) {
                return;
            }

            event.preventDefault();
            const modalElement = document.getElementById('confirmDeleteModal');
            const confirmButton = document.getElementById('confirmDeleteButton');
            document.getElementById('confirmDeleteTitle').textContent = trigger.dataset.confirmTitle ||
                'Confirm delete';
            document.getElementById('confirmDeleteMessage').textContent = trigger.dataset.confirmMessage ||
                'This action cannot be undone.';
            confirmButton.setAttribute('href', trigger.dataset.confirmUrl);
            bootstrap.Modal.getOrCreateInstance(modalElement).show();
        });
    </script>
    <script>
        new DataTable('#dataTables', {
            responsive: true,
            pageLength: 10,
            ordering: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search members..."
            }
        });
    </script>
    <script>
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                iziToast.error({
                    title: 'Error',
                    message: @json($error),
                    position: 'topRight',
                    closeOnEscape: true,
                    closeOnClick: true,
                    progressBar: true
                });
            @endforeach
        @endif

        @if (session('success'))
            iziToast.success({
                title: 'Success',
                message: @json(session('success')),
                position: 'topRight'
            });
        @endif

        {{-- Laravel forgot password uses "status" --}}
        @if (session('status'))
            iziToast.success({
                title: 'Success',
                message: @json(session('status')),
                position: 'topRight'
            });
        @endif

        @if (session('error'))
            iziToast.error({
                title: 'Error',
                message: @json(session('error')),
                position: 'topRight'
            });
        @endif

        @if (session('warning'))
            iziToast.warning({
                title: 'Warning',
                message: @json(session('warning')),
                position: 'topRight'
            });
        @endif

        @if (session('info'))
            iziToast.info({
                title: 'Info',
                message: @json(session('info')),
                position: 'topRight'
            });
        @endif
    </script>
    @stack('scripts')
</body>

</html>
