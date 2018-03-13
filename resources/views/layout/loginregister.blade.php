<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') </title>

    <link rel="stylesheet" href="{!! asset('css/vendor.css') !!}"/>
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}"/>
    @yield('css_extra')
</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen   animated fadeInDown">
    <div>
        <div>
            <img src="{{ asset('img/logo.jpg') }}" alt="MSS" width="280" />
        </div>
        <br/>

        @yield('content')
    </div>
</div>

<script src="{!! asset('js/vendor.js') !!}" type="text/javascript"></script>
<script src="{!! asset('js/app.js') !!}" type="text/javascript"></script>
</body>

</html>
