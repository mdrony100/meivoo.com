<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Legacy - @yield('title')</title>
    @if (View::hasSection('meta'))
        @yield('meta')
    @else
        <meta property="og:title" content="{{ $settings->seo_meta_title }}">
        <meta property="og:description" content="{{ $settings->seo_meta_description }}">
        <meta property="og:image" content="{{ asset($settings->seo_image) }}">
        <meta name="description" content="{{ $settings->seo_meta_description }}">
        <meta name="keywords" content="{{ $settings->seo_keywords }}">
    @endif

    <link rel="icon" type="image/x-icon"
        href="{{ $settings->favicon ? asset($settings->favicon) : asset('assets/images/fab-icon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/css/iziToast.min.css">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:500,600,700,800" rel="stylesheet">
    <link href="{{ asset('assets/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/mv-site.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/mv-auth.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body>
    @unless (View::hasSection('hide_navbar'))
        @include('frontend.partials.navbar')
    @endunless

    @yield('content')

    @unless (View::hasSection('hide_footer'))
        @include('frontend.partials.footer')
    @endunless

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('assets/plugin/jquery.event.move.js') }}"></script>
    <script src="{{ asset('assets/plugin/jquery.twentytwenty.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('assets/app.js') }}"></script>

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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const swiper = new Swiper('.testimonial-swiper', {
                slice: true,
                loop: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                spaceBetween: 24,

                breakpoints: {
                    0: {
                        slidesPerView: 1,
                    },
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    }
                }
            });
        });
    </script>
    <script src="{{ asset('assets/explore.js') }}"></script>
    @stack('scripts')
    <script src="{{ asset('assets/dashboard/meivoo.js') }}"></script>
</body>

</html>
