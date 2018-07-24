<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') </title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{!! asset('css/handscanner.css') !!}"/>
    @yield('css_extra')
</head>

<body>
    <div id="wrapper">
        <div class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
            <div class="container">
                <a href="#" onclick="window.history.back();" class="btn btn-secondary pull-left @if(in_array(\Illuminate\Support\Facades\Route::current()->getName(), ['handscanner.login', 'handscanner.index'])) invisible @endif"><i class="fa fa-arrow-left"></i></a>

                <a href="../" class="navbar-brand">MSS</a>

                <a href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-secondary pull-right @if (\Illuminate\Support\Facades\Auth::guest()) invisible @endif">
                    <i class="fa fa-sign-out"></i>
                </a>

                <form id="logout-form" action="{{ route('handscanner.logout') }}" method="POST" style="display: none;">{{ csrf_field() }}</form>
            </div>
        </div>

        <div class="container" style="padding-top: 65px">
            @include('flash::message')
            @yield('content')
        </div>
    </div>

    <script src="{!! asset('js/vendor.js') !!}" type="text/javascript"></script>
    <script src="{!! asset('js/app.js') !!}" type="text/javascript"></script>
    @stack('scripts')
</body>

</html>
