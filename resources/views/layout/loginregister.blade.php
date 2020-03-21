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
            <div class="my-8 text-center">
                @if (file_exists(public_path(config('app.logo'))))
                    <img src="{{ config('app.logo') }}" alt="{{ env('APP_NAME') }}" width="280" />
                @endif
            </div>

            @yield('content')
        </div>

        <script src="{!! asset('js/vendor.js') !!}" type="text/javascript"></script>
        <script src="{!! asset('js/app.js') !!}" type="text/javascript"></script>

        @if(env('APP_DEMO') && !empty(config('app.google_analytics_id')))
            <!-- Global site tag (gtag.js) - Google Analytics -->
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('app.google_analytics_id') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());

                gtag('config', '{{ config('app.google_analytics_id') }}', {'anonymize_ip': true});
            </script>
        @endif
    </body>

</html>
