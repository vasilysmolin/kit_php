<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link href="{{ '/admin/plugins/select2/dist/css/select2.css' }}" rel="stylesheet" type="text/css">
        <link href="{{ '/admin/plugins/select2/dist/css/select2-bootstrap.css' }}" rel="stylesheet" type="text/css">

        <link href="{{ '/admin/css/bootstrap.min.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/css/core.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/css/components.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/css/multi-select.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/css/select2.min.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/css/icons.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/css/menu.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/css/responsive.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/plugins/bootstrap-sweetalert/sweet-alert.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/plugins/nprogress-master/nprogress.css' }}" rel="stylesheet" type="text/css" />
        <link href="{{ '/admin/plugins/uploader/style.css' }}" rel="stylesheet" type="text/css" />

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>

    <script src="{{ '/admin/js/jquery.min.js' }}"></script>
    <script src="{{ '/admin/plugins/jquery-ui/jquery-ui.js' }}"></script>
    <script src="{{ '/admin/js/bootstrap.min.js' }}"></script>
    <script src="{{ '/admin/js/custom/jquery.multi-select.js' }}"></script>
    <script src="{{ '/admin/js/custom/select2.min.js' }}"></script>
    <script src="{{ '/admin/plugins/bootstrap-sweetalert/sweet-alert.min.js' }}"></script>
    <script src="{{ '/admin/plugins/nprogress-master/nprogress.js' }}"></script>
    <script src="{{ '/admin/plugins/uploader/uploader.js' }}"></script>
    <script src="{{ '/admin/custom/default.js' }}" type="text/javascript"></script>
    <script src="{{ '/admin/js/jquery.app.js' }}" type="text/javascript"></script>

    @yield('scripts_bottom')
</html>
