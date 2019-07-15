@extends('layout.app')

@section('title', 'Änderungsverlauf Artikel '.((!empty($article->article_number)) ? ' #'.$article->article_number : ''))

@section('title_extra')
    <small>{{ $article->name }}</small>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">Übersicht</a>
    </li>
    <li>
        <a href="{{ route('article.show', $article) }}">Artikel Details</a>
    </li>
    <li class="active">
        <strong>Änderungsverlauf</strong>
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
                    <h5 class="flex-1">Änderungsverlauf</h5>
                    <div>
                        <div id="daterange" class="pull-right bg-white cursor-pointer px-4 py-2 border border-gray-300 w-full">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="card-content">
                    <table class="table table-condensed table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Typ</th>
                                <th class="text-center">Änderung</th>
                                <th class="text-center">Bestand</th>
                                <th class="text-center">Einheit</th>
                                <th>Zeitpunkt</th>
                                <th>Kommentar</th>
                                <th>Benutzer</th>
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
                    {{ $changelog->appends(['start' => $dateStart->format('Y-m-d'), 'end' => $dateEnd->format('Y-m-d')])->links() }}
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
                    'label': 'Wareneingang (Ø {{ $diffMonths ? round(abs($chartValues[1]->sum() / $diffMonths), 0) : $chartValues[1]->sum() }} / Monat)',
                    data: {!! $chartValues[1]->toJson() !!}
                },
                @endif
                @if(isset($chartValues[2]))
                {
                    type: 'bar',
                    backgroundColor: '#ED5565',
                    'label': 'Warenausgang (Ø {{ $diffMonths ? round(abs($chartValues[2]->sum() / $diffMonths), 0) : $chartValues[2]->sum() }} / Monat)',
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
                    'Letzte 30 Tage': [moment().subtract(29, 'days'), moment()],
                    'Dieser Monat': [moment().startOf('month'), moment().endOf('month')],
                    'Letzter Monat': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'aktuelles Jahr': [moment().startOf('year'), moment()],
                    '12 Monate': [moment().subtract(12, 'month').startOf('month'), moment()],
                    '24 Monate': [moment().subtract(24, 'month').startOf('month'), moment()],
                    '36 Monate': [moment().subtract(36, 'month').startOf('month'), moment()]
                },
                "locale": {
                    "format": "DD.MM.YYYY",
                    "separator": " - ",
                    "applyLabel": "Übernehmen",
                    "cancelLabel": "Abbrechen",
                    "fromLabel": "Von",
                    "toLabel": "Bis",
                    "customRangeLabel": "Individuell",
                    "weekLabel": "W",
                    "daysOfWeek": [
                        "So",
                        "Mo",
                        "Di",
                        "Mi",
                        "Do",
                        "Fr",
                        "Sa"
                    ],
                    "monthNames": [
                        "Januar",
                        "Februar",
                        "März",
                        "April",
                        "Mai",
                        "Juni",
                        "Juli",
                        "August",
                        "September",
                        "Oktober",
                        "November",
                        "Dezember"
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