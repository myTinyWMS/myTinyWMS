@extends('layout.app')

@section('title', 'Änderungsverlauf Artikel '.((!empty($article->article_number)) ? ' #'.$article->article_number : ''))

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
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title" style="min-height: 55px">
                    <h5>Änderungsverlauf</h5>
                    <div class="pull-right">
                        <div id="daterange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                            <span></span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <table class="table table-condensed table-bordered">
                        <thead>
                        <tr>
                            <th>Typ</th>
                            <th class="text-center">Änderung</th>
                            <th class="text-center">Bestand</th>
                            <th>Zeitpunkt</th>
                            <th>Kommentar</th>
                            <th>Benutzer</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($changelog as $log)
                            @if ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_INCOMING)
                                @include('components.quantity_log.incoming')
                            @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_OUTGOING)
                                @include('components.quantity_log.outgoing')
                            @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_CORRECTION)
                                @include('components.quantity_log.correction')
                            @elseif ($log->type == \Mss\Models\ArticleQuantityChangelog::TYPE_COMMENT)
                                @include('components.quantity_log.comment')
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                    {{ $changelog->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        $(function() {

            var start = moment('{{ $dateStart->format('Y-m-d') }}');
            var end = moment('{{ $dateEnd->format('Y-m-d') }}');

            function cb(start, end) {
                $('#daterange span').html(start.format('DD.MM.YYYY') + ' - ' + end.format('DD.MM.YYYY'));
            }

            $('#daterange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Heute': [moment(), moment()],
                    'Gestern': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Letzte 7 Tage': [moment().subtract(6, 'days'), moment()],
                    'Letzte 30 Tage': [moment().subtract(29, 'days'), moment()],
                    'Dieser Monat': [moment().startOf('month'), moment().endOf('month')],
                    'Letzter Monat': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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