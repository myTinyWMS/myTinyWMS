<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{!! asset('css/vendor.css') !!}" />
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}" />

    @yield('extra_head')

</head>

<body>
    <!-- Wrapper-->
    <div id="wrapper">
        @include('layout.sidebar')

        <!-- Page wraper -->
        <div id="page-wrapper" class="gray-bg">

            <!-- Page wrapper -->
            @include('layout.topnavbar')

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

            <div class="wrapper wrapper-content animated fadeIn">
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

        @if (!empty($__env->yieldContent('datatableFilters')))
            <div id="datatableFilter" class="hidden">
                <div class="pull-left m-b-md">
                    <h4 class="text-left">Filter:</h4>
                    @yield('datatableFilters')
                </div>
            </div>
        @endif
    </div>

    <script src="{!! asset('js/vendor.js') !!}" type="text/javascript"></script>
    <script src="{!! asset('js/app.js') !!}" type="text/javascript"></script>

    @if (!empty($__env->yieldContent('datatableFilters')))
        <script>
            $('#dataTableBuilder').on( 'init.dt', function () {
                if ($('#datatableFilter').html().length) {
                    $('#dataTableBuilder_filter').append($('#datatableFilter').html());
                    $('#datatableFilter').remove();

                    $('.datatableFilter-select').each(function () {
                        $(this).change(function () {
                            console.log('changed');
                            console.log($(this).val(), $(this).attr('data-target-col'));
                            window.LaravelDataTables.dataTableBuilder.columns($(this).attr('data-target-col')).search($(this).val()).draw();
                            saveFilterState($(this).attr('id'), $(this).attr('data-target-col'), $(this).val());
                        });

                        if ($(this).attr('data-pre-select')) {
                            $(this).val($(this).attr('data-pre-select'));
                        }
                    });

                    loadFilterState();
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
        $('#dataTableBuilder').on( 'draw.dt', function () {
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green'
            });
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
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
    </script>
    @stack('scripts')
</body>
</html>