<!DOCTYPE html>
<html class="h-full font-sans-nunito antialiased">

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>@yield('title') </title>

        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i" rel="stylesheet">
        <link href="/css/material-icons.css" rel="stylesheet">
        <link href="/css/vendor.css" rel="stylesheet">

        <link rel="stylesheet" href="{!! mix('css/app.css') !!}" />

        @yield('css_extra')
    </head>

    <body class="min-w-site bg-gray-200 text-black min-h-full">
        <div class="flex min-h-screen items-center flex-col">
            <div class="my-8">
                <img src="{{ config('app.logo') }}" alt="MSS" width="280" />
            </div>

            @yield('content')
        </div>

        <script src="{!! asset('js/vendor.js') !!}" type="text/javascript"></script>
        <script src="{!! asset('js/app.js') !!}" type="text/javascript"></script>
    </body>

</html>
