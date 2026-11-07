<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LegacyTree - Dashboard')</title>
    <link rel="icon" type="image/x-icon"
        href="{{ $settings->favicon ? asset($settings->favicon) : asset('assets/images/fab-icon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,400;0,500;0,600;0,700;0,900;1,500&display=swap"
        rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
    <link href="{{ asset('assets/dashboard/style.css') }}" rel="stylesheet">
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('meivoo.theme');
                var theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch (e) { document.documentElement.setAttribute('data-theme', 'light'); }
        })();
    </script>
    <link href="{{ asset('assets/dashboard/meivoo.css') }}" rel="stylesheet">
    <meta name="theme-color" content="#ffffff">
    @stack('styles')
    <style>
        body {
            font-family: 'Poppins', sans-serif !important;
        }

        .form-control {
            font-size: 15px;
            padding: 10px 10px;
        }

        a {
            text-decoration: none !important;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 12px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #843fef;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a354f6;
        }

        .placeholder_card {
            opacity: .4;
        }
    </style>
</head>

<body class="@yield('body_class')">

    @hasSection('page_wrapper_class')
        <div class="@yield('page_wrapper_class')">
    @endif

    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    @include('user.partials.sidebar')

    <div class="main-wrapper" id="mainWrapper">
        @include('user.partials.topbar')
        @yield('content')
    </div>

    @hasSection('page_wrapper_class')
        </div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="{{ asset('assets/dashboard/app.js') }}"></script>
    <script src="{{ asset('assets/dashboard/meivoo.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        lucide.createIcons();
    </script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
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
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
        });
    </script>
    <script>
        function confirmDelete(postId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this post?",
                icon: 'warning',
                width: 400,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#7c3aed',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + postId).submit();
                }
            })
        }
    </script>
    @stack('scripts')
</body>

</html>
