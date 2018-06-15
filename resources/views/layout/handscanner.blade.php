<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') </title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{!! asset('css/vendor.css') !!}"/>
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}"/>
    @yield('css_extra')
</head>

<body class="gray-bg">
    <div id="wrapper">
        <div class="middle-box text-center loginscreen">
            <div id="page-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="{!! asset('js/vendor.js') !!}" type="text/javascript"></script>
    <script src="{!! asset('js/app.js') !!}" type="text/javascript"></script>
</body>

</html>
