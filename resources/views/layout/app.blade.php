@inject('globalPageService', 'Mss\Services\GlobalPageService')
<!DOCTYPE html>
<html class="h-full tracking-normal antialiased" lang="{{ Config::get('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link href="/css/material-icons.css" rel="stylesheet">
    <link href="/css/vendor.css" rel="stylesheet">

    {{--<link rel="stylesheet" href="{!! mix('css/vendor.css') !!}" />--}}
    <link rel="stylesheet" href="{!! mix('css/app.css') !!}" />

    @yield('extra_head')
    @routes
</head>

<body class="min-w-site bg-gray-200 text-black min-h-full">
    <div class="flex min-h-screen" id="app">
        <div class="content" id="wrapper">
            @include('layout.topnavbar')

            <div class="px-12 print:px-4 py-12 mx-auto pt-40">
                <div>
                    <ul class="breadcrumb print:hidden">
                        @yield('breadcrumb')
                    </ul>
                </div>

                <div class="flex items-center">
                    <h1 class="flex-1">@yield('title')</h1>
                    @yield('title_extra')
                </div>

                @include('flash::message')
                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>

            <p class="footer">
                © 2019 - {{ date('Y') }} Alexander Reichardt
                <span class="px-1">·</span>
                @lang('Version') {{ config('version.app_version') }} - @lang('build') {{ config('version.build_version') }} ({{ config('version.branch') }})
                <span class="px-1 print:hidden">·</span>
                <span class="print:hidden">@lang('Eingeloggt als'): {{ \Illuminate\Support\Facades\Auth::user()->username ?? \Illuminate\Support\Facades\Auth::user()->name }}</span>
            </p>
        </div>
    </div>

    @routes
    <script src="{!! mix('js/vendor.js') !!}" type="text/javascript"></script>
    <script src="{!! mix('js/app.js') !!}" type="text/javascript"></script>

    <script>
        function addFixedTableHeader() {
            var tableOffset = $("#dataTableBuilder").offset().top;
            var header = $("#dataTableBuilder > thead").clone();

            $("#header-fixed").html('');
            var fixedHeader = $("#header-fixed").append(header);

            $("#dataTableBuilder th").each(function (index) {
                $("#header-fixed th:eq(" + index + ")").css('width', $(this).css('width'));
            });
            $("#header-fixed th:eq(0)").find('label').remove();

            $(window).bind("scroll", function() {
                var offset = $(this).scrollTop();

                if (offset >= tableOffset && fixedHeader.is(":hidden")) {
                    fixedHeader.show();
                } else if (offset < tableOffset) {
                    fixedHeader.hide();
                }
            });
        }

        $('#dataTableBuilder').on( 'draw.dt', function () {
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
            });

            if (!$("#header-fixed").length) {
                $('<table id="header-fixed"></table>').insertAfter('#dataTableBuilder');
            }

            addFixedTableHeader();
            adjustDisabledButtons();

            $("body").trigger('dt.draw');
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function () {
            // $('[data-toggle="tooltip"]').tooltip();

            if ($('#select_all').length) {
                $('#select_all').change(function () {
                    if ($(this).is(':checked')) {
                        $('#dataTableBuilder tbody input[type=checkbox]').attr('checked', 'checked');
                    } else {
                        $('#dataTableBuilder tbody input[type=checkbox]').attr('checked', null);
                    }
                });

                $('body').on('click', '#dataTableBuilder tbody tr td', function () {
                    var checkbox = $(this).parent().find('input[type=checkbox]');
                     if (checkbox.is(':checked')) {
                        checkbox.attr('checked', null);
                     } else {
                        checkbox.attr('checked', 'checked');
                     }
                })
            }

            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
            });

            if ($('.table-footer-actions').length && $('.footer_actions').length) {
                $('.table-footer-actions').html($('.footer_actions').html());
            }

            if ($('.table-toolbar-right').length && $('.table-toolbar-right-content').length) {
                $('.table-toolbar-right').html($('.table-toolbar-right-content').html());
            }

            $('.btn:disabled, .btn-link:disabled').addClass('btn-disabled');
            $('input:disabled, textarea:disabled, select:disabled').addClass('form-disabled');

            adjustDisabledButtons();
        });

        function adjustDisabledButtons() {
            $('.btn:disabled, .btn-link:disabled, a[disabled="disabled"]').addClass('btn-disabled').each(function () {
                $(this).on('click', function (event) {
                    event.stopImmediatePropagation();
                    return false;
                })
            });
        }
    </script>
    @stack('scripts')

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