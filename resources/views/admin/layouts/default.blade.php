<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link rel="shortcut icon" href="{{ '/favicon.ico' }}">

        <title>Admin Panel</title>

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

        @yield('styles')

    </head>
    <body>
        @yield('header')

        @yield('content')
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

        <script>
            var token = '{{ Session::token() }}'
        </script>
    </body>
</html>
