<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>
        <link rel="icon" type="image/jpeg" href="{{ asset('cslogo.jpg') }}">
        <link rel="shortcut icon" href="{{ asset('cslogo.jpg') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <script>
            (function () {
                function shouldForceReloadFromHistory(event) {
                    const navEntry = (performance.getEntriesByType && performance.getEntriesByType('navigation')[0]) || null;
                    const isBackForward = !!(navEntry && navEntry.type === 'back_forward');
                    return !!(event && event.persisted) || isBackForward;
                }

                window.addEventListener('pageshow', function (event) {
                    if (shouldForceReloadFromHistory(event)) {
                        window.location.reload();
                    }
                });
            })();
        </script>
        @php($hideNavigation = trim($__env->yieldContent('hide_navigation')) === '1')
        <div class="min-h-screen bg-gray-100">
            @if (! $hideNavigation)
                @include('layouts.navigation')
            @endif

            <!-- Page Heading -->
            @php($pageHeader = trim($__env->yieldContent('page_header')))
            @if (isset($header) || $pageHeader !== '')
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @isset($header)
                            {{ $header }}
                        @else
                            @yield('page_header')
                        @endisset
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </body>
</html>
