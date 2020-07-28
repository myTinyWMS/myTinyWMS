@extends('layout.app')

@section('title', __('Änderungsverlauf Artikel ').((!empty($article->internal_article_number)) ? ' #'.$article->internal_article_number : ''))

@section('title_extra')
    <small>{{ $article->name }}</small>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Übersicht')</a>
    </li>
    <li>
        <a href="{{ route('article.show', $article) }}">@lang('Artikel Details')</a>
    </li>
    <li class="active">
        <strong>@lang('Änderungsverlauf')</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="w-1/2 mr-4">
            <canvas id="chart"></canvas>
        </div>
        <div class="w-1/2">
            <div class="card">
                <div class="card-header flex" style="min-height: 55px">
                    <h5 class="flex-1">@lang('Änderungsverlauf')</h5>
                    <div>
                        <div id="daterange" class="pull-right bg-white cursor-pointer px-4 py-2 border border-gray-300 w-full">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <table class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('Typ')</th>
                                <th class="text-center">@lang('Änderung')</th>
                                <th class="text-center">@lang('Bestand')</th>
                                <th class="text-center">@lang('Einheit')</th>
                                <th>@lang('Zeitpunkt')</th>
                                <th>@lang('Kommentar')</th>
                                <th>@lang('Benutzer')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($changelog as $log)
                                <tr>
                                    @if ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_INCOMING)
                                        @include('components.quantity_log.incoming')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_OUTGOING)
                                        @include('components.quantity_log.outgoing')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_CORRECTION)
                                        @include('components.quantity_log.correction')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_COMMENT)
                                        @include('components.quantity_log.comment')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_INVENTORY)
                                        @include('components.quantity_log.inventory')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY)
                                        @include('components.quantity_log.replacement_delivery')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_OUTSOURCING)
                                        @include('components.quantity_log.outsourcing')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_SALE_TO_THIRD_PARTIES)
                                        @include('components.quantity_log.sale_to_third_parties')
                                    @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_TRANSFER)
                                        @include('components.quantity_log.transfer')
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-6 flex justify-end">
                        {{ $changelog->appends(['start' => $dateStart->format('Y-m-d'), 'end' => $dateEnd->format('Y-m-d')])->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        window.chartColors = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

        var chartData = {
            labels: {!! $chartLabels->toJson() !!},
            datasets: [
                @if(isset($chartValues[1]))
                {
                    type: 'bar',
                    backgroundColor: '#449D44',
                    'label': '@lang('Wareneingang') (Ø {{ $dataDiffInMonths ? round(abs($chartValues[1]->sum() / $dataDiffInMonths), 0) : $chartValues[1]->sum() }} / @lang('Monat'))',
                    data: {!! $chartValues[1]->toJson() !!}
                },
                @endif
                @if(isset($chartValues[2]))
                {
                    type: 'bar',
                    backgroundColor: '#ED5565',
                    'label': '@lang('Warenausgang') (Ø {{ $dataDiffInMonths ? round(abs($chartValues[2]->sum() / $dataDiffInMonths), 0) : $chartValues[2]->sum() }} / @lang('Monat'))',
                    data: {!! $chartValues[2]->toJson() !!}
                }
                @endif
            ]
        };

        $(function() {
            var ctx = document.getElementById("chart").getContext("2d");
            new Chart(ctx, {
                type: 'bar',
                data: chartData,
                options: {
                    responsive: true,
                    title: {
                        display: false,
                    },
                    tooltips: {
                        mode: 'point',
                        // intersect: true
                    }
                }
            });

            var start = moment('{{ $dateStart->format('Y-m-d') }}');
            var end = moment('{{ $dateEnd->format('Y-m-d') }}');

            function cb(start, end) {
                $('#daterange span').html(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));
            }

            $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
                opens: 'left',
                ranges: {
                    '@lang('Letzte 30 Tage')': [moment().subtract(29, 'days'), moment()],
                    '@lang('Dieser Monat')': [moment().startOf('month'), moment().endOf('month')],
                    '@lang('Letzter Monat')': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    '@lang('aktuelles Jahr')': [moment().startOf('year'), moment()],
                    '@lang('12 Monate')': [moment().subtract(12, 'month').startOf('month'), moment()],
                    '@lang('24 Monate')': [moment().subtract(24, 'month').startOf('month'), moment()],
                    '@lang('36 Monate')': [moment().subtract(36, 'month').startOf('month'), moment()]
                },
                "locale": {
                    "format": "DD.MM.YYYY",
                    "separator": " - ",
                    "applyLabel": "@lang('Übernehmen')",
                    "cancelLabel": "@lang('Abbrechen')",
                    "fromLabel": "@lang('Von')",
                    "toLabel": "@lang('Bis')",
                    "customRangeLabel": "@lang('Individuell')",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "@lang('So')",
                        "@lang('Mo')",
                        "@lang('Di')",
                        "@lang('Mi')",
                        "@lang('Do')",
                        "@lang('Fr')",
                        "@lang('Sa')"
                    ],
                    "monthNames": [
                        "@lang('Januar')",
                        "@lang('Februar')",
                        "@lang('März')",
                        "@lang('April')",
                        "@lang('Mai')",
                        "@lang('Juni')",
                        "@lang('Juli')",
                        "@lang('August')",
                        "@lang('September')",
                        "@lang('Oktober')",
                        "@lang('November')",
                        "@lang('Dezember')"
                    ],
                    "firstDay": 1
                }
            }, cb);

            cb(start, end);

            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                window.location.href = '{{ route('article.quantity_changelog', $article) }}?start=' + picker.startDate.format('YYYY-MM-DD') + '&end=' + picker.endDate.format('YYYY-MM-DD');
            });

        });
    </script>
@endpush