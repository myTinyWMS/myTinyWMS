@inject('globalPageService', 'Mss\Services\GlobalPageService')
<!DOCTYPE html>
<html class="h-full font-sans-nunito antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i" rel="stylesheet">
    <link href="/css/material-icons.css" rel="stylesheet">
    <link href="/css/vendor.css" rel="stylesheet">

    {{--<link rel="stylesheet" href="{!! mix('css/vendor.css') !!}" />--}}
    <link rel="stylesheet" href="{!! mix('css/app.css') !!}" />

    @yield('extra_head')
    @routes
</head>

<body class="min-w-site bg-grey-lighter text-black min-h-full">
    <div class="flex min-h-screen">
        @include('layout.sidebar')

        <div class="content" id="wrapper">
            <div class="flex items-center relative shadow h-header bg-white z-20 px-6">
                <div class="relative z-50 w-full max-w-xs">
                    <div class="relative">
                        <div class="relative">
                            <label class="search"><input type="search" placeholder="Suche" class="form-control form-input form-input-bordered w-full shadow"></label>
                        </div>
                    </div>
                </div>
                <div class="dropdown relative ml-auto h-9 flex items-center dropdown-right">
                    <a class="dropdown-trigger h-dropdown-trigger flex items-center cursor-pointer select-none h-9 flex items-center">
                        <img src="https://secure.gravatar.com/avatar/e64c7d89f26bd1972efa854d13d7dd61?size=512" class="rounded-full w-8 h-8 mr-3"> <span class="text-90">Diamond Wilkinson</span>
                        <svg width="10px" height="6px" viewBox="0 0 10 6" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="ml-2"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><g id="04-user" transform="translate(-385.000000, -573.000000)" fill="var(--90)" fill-rule="nonzero"><path d="M393.292893,573.292893 C393.683418,572.902369 394.316582,572.902369 394.707107,573.292893 C395.097631,573.683418 395.097631,574.316582 394.707107,574.707107 L390.707107,578.707107 C390.316582,579.097631 389.683418,579.097631 389.292893,578.707107 L385.292893,574.707107 C384.902369,574.316582 384.902369,573.683418 385.292893,573.292893 C385.683418,572.902369 386.316582,572.902369 386.707107,573.292893 L390,576.585786 L393.292893,573.292893 Z" id="Path-2-Copy"></path></g></g></svg>
                    </a>
                </div>
            </div>

            <div class="px-view py-view mx-auto">
                <h1>@yield('title')</h1>

                @include('flash::message')
                @if ($errors->any())
                    <div class="alert alert-danger">
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
                Icons made by <a href="https://www.flaticon.com/authors/vectors-market" title="Vectors Market">Vectors Market</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0" target="_blank">CC 3.0 BY</a>
                <span class="px-1">·</span>
                © 2019 Alexander Reichardt
                <span class="px-1">·</span>
                v1.1.0
            </p>
        </div>
    </div>

    @if (!empty($__env->yieldContent('datatableFilters')))
        <div id="datatableFilter" class="hidden">
            <div class="flex">
                @yield('datatableFilters')
            </div>
        </div>
    @endif

    {{--@include('layout.topnavbar')--}}

    {{--<div id="wrapper">
        @include('layout.sidebar')

        <!-- Page wraper -->
        <div id="page-wrapper" class="gray-bg">

            <!-- Page wrapper -->


            <!-- Main view  -->
            <div class="row wrapper page-heading">
                <div class="col-lg-12">
                    <h2>
                        @yield('title')
                        @yield('title_extra')
                    </h2>
                    <ol class="breadcrumb pull-left">
                        @yield('breadcrumb')
                    </ol>
                    <div class="btn-toolbar pull-right">
                        @yield('subnav')
                    </div>
                </div>
            </div>

            <div class="wrapper wrapper-content">
                @include('flash::message')
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')

                <!-- Footer -->
                @include('layout.footer')
            </div>
        </div>


    </div>--}}

    @routes
    <script src="{!! mix('js/vendor.js') !!}" type="text/javascript"></script>
    <script src="{!! mix('js/app.js') !!}" type="text/javascript"></script>

    @if (!empty($__env->yieldContent('datatableFilters')))
        <script>
            $('#dataTableBuilder').on( 'init.dt', function () {
                if ($('#datatableFilter').length && $('#datatableFilter').html().length) {
                    $('.table-filter').append($('#datatableFilter').html());
                    $('#datatableFilter').remove();

                    $('.datatableFilter-select').each(function () {
                        $(this).change(function () {
                            window.LaravelDataTables.dataTableBuilder.columns($(this).attr('data-target-col')).search($(this).val()).draw();
                            $("body").trigger('dt.filter.' + $(this).attr('id'));
                            // saveFilterState($(this).attr('id'), $(this).attr('data-target-col'), $(this).val());
                        });

                        if ($(this).attr('data-pre-select')) {
                            $(this).val($(this).attr('data-pre-select'));
                        }
                    });

                    $("body").trigger('dt.init');

                    //loadFilterState();
                }
            });

            function saveFilterState(elementId, col, value) {
                var currentFilterState = JSON.parse(localStorage.getItem('datatables-filterState'));
                if (currentFilterState === null) {
                    currentFilterState = {};
                }

                currentFilterState[elementId] = {value: value, col: col};
                localStorage.setItem('datatables-filterState', JSON.stringify(currentFilterState));
            }

            function loadFilterState() {
                var currentFilterState = JSON.parse(localStorage.getItem('datatables-filterState'));
                if (currentFilterState !== null) {
                    $.each(currentFilterState, function (elementId, item) {
                        $('#'+elementId).val(item.value);
                    });
                }
            }
        </script>
    @endif

    <script>
        function addFixedTableHeader() {
            var tableOffset = $("#dataTableBuilder").offset().top;
            var header = $("#dataTableBuilder > thead").clone();
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

            $('#dataTableBuilder_filter input[type="search"]').attr('placeholder', 'Suche').parent().addClass('search');

            $('<table id="header-fixed"></table>').insertAfter('#dataTableBuilder');

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
            $('[data-toggle="tooltip"]').tooltip();

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
</body>
</html>