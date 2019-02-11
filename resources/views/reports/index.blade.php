@extends('layout.app')

@section('title', 'Reports')

@section('content')
<div class="px-2 -ml-2">
    <div class="flex -mx-2">
        <div class="md:w-1/2 lg:w-1/4 px-2">
            <div class="box">
                <h3>Inventurauswertung</h3>
                <small>Monat und Inventurtyp auswählen um Report zu erstellen:</small>

                <form method="post" action="{{ route('reports.inventory_report') }}" id="inventory-report" class="mt-4">
                    {{ csrf_field() }}
                    {{ Form::bsSelect('inventorytype', null, \Mss\Models\Article::getInventoryTextArray(),  'Inventur Typ', ['placeholder' => 'egal']) }}
                    <div class="mt-4">
                        <div id="monthpicker"></div>
                    </div>
                    <input type="hidden" value="" name="month" id="month" />
                </form>
            </div>
        </div>

        <div class="md:w-1/2 lg:w-1/4 px-2">
            <div class="box">
                <h3>WA Vergleich</h3>
                <small>Monat auswählen um Report zu erstellen</small>

                <form method="post" action="{{ route('reports.article_usage_report') }}" id="article-usage-report" class="mt-4">
                    {{ csrf_field() }}
                    <div class="mt-4">
                        <div id="monthpicker2"></div>
                    </div>
                    <input type="hidden" value="" name="month" id="month2" />
                </form>
            </div>
        </div>

        <div class="md:w-1/2 lg:w-1/4 px-2">
            <div class="flex-column">
                <div class="box mb-4 h-32">
                    <h3>Monats-Inventur-Liste</h3>
                    <small>nur aktive Verbrauchsmaterialien</small>

                    <a class="btn-link block pb-2 absolute pin-b" href="{{ route('reports.inventory_pdf') }}"><i class="fa fa-download"></i> PDF herunterladen </a>
                </div>

                <div class="box h-32">
                    <h3>Jahres-Inventur-Liste</h3>
                    <small>alle aktiven Artikel</small>

                    <a class="btn-link block pb-2 absolute pin-b" href="{{ route('reports.yearly_inventory_pdf') }}"><i class="fa fa-download"></i> PDF herunterladen </a>
                </div>
            </div>
        </div>

        <div class="md:w-1/2 lg:w-1/4 px-2">
            <div class="flex-column">
                <div class="box mb-4 h-32">
                    <h3>Wareneingänge ohne Rechnung</h3>

                    <a class="btn-link block pb-2 absolute pin-b" href="{{ route('reports.deliveries_without_invoice') }}"><i class="fa fa-arrow-right"></i> Liste anzeigen </a>
                </div>

                <div class="box h-32">
                    <h3>Rechnungen ohne Wareneingang</h3>

                    <a class="btn-link block pb-2 absolute pin-b" href="{{ route('reports.invoices_without_delivery') }}"><i class="fa fa-arrow-right"></i> Liste anzeigen </a>
                </div>
            </div>
        </div>
    </div>
</div>

        {{--<div class="col-lg-2">
            <div class="contact-box center-version">
                <a href="{{ route('reports.deliveries_without_invoice') }}">
                    <h3 class="m-b-xs"><strong>Wareneingänge ohne Rechnung</strong></h3>
                    <div class="font-bold">&nbsp;</div>
                </a>
                <div class="contact-box-footer">
                    <div class="m-t-xs btn-group">
                        <a class="btn btn-white" href="{{ route('reports.deliveries_without_invoice') }}"><i class="fa fa-arrow-right"></i> Liste anzeigen </a>
                    </div>
                </div>
            </div>

            <div class="contact-box center-version">
                <a href="{{ route('reports.invoices_without_delivery') }}">
                    <h3 class="m-b-xs"><strong>Rechnungen ohne Wareneingang</strong></h3>
                    <div class="font-bold">&nbsp;</div>
                </a>
                <div class="contact-box-footer">
                    <div class="m-t-xs btn-group">
                        <a class="btn btn-white" href="{{ route('reports.invoices_without_delivery') }}"><i class="fa fa-arrow-right"></i> Liste anzeigen </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="contact-box center-version">
                <a href="#">
                    <h3 class="m-b-xs"><strong>Verpackungs-Lizensierungs-Report</strong></h3>
                    <div class="font-bold">Zeitraum auswählen um Report zu erstellen:</div>
                </a>
                <div class="contact-box-footer">
                    <form method="post" action="{{ route('reports.article_weight_report') }}" id="article-weight-report" class="form-inline">
                        {{ csrf_field() }}
                        <input type="text" id="daterangepicker1" name="daterange" class="form-control">
                        <button type="submit" class="btn btn-white">
                            <i class="fa fa-arrow-right"></i> Liste anzeigen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>--}}
@endsection

@push('scripts')
    <script>
        $(function() {
            $('#monthpicker').datepicker({
                autoclose: true,
                minViewMode: 1,
                format: 'yyyy-mm'
            }).on('changeDate', function(selected){
                $('#month').val(moment(selected.date).format('YYYY-MM'));
                $('#inventory-report').submit();
            });

            $('#monthpicker2').datepicker({
                autoclose: true,
                minViewMode: 1,
                format: 'yyyy-mm'
            }).on('changeDate', function(selected){
                $('#month2').val(moment(selected.date).format('YYYY-MM'));
                $('#article-usage-report').submit();
            });

            $('#daterangepicker1').daterangepicker({
                startDate: "01.01." + moment().year(),
                endDate: "31.12." + moment().year(),
                ranges: {
                    'aktuelles Jahr': ["01.01." + moment().year(), "31.12." + moment().year()],
                    'letztes Jahr': ["01.01." + moment().subtract(1, 'year').year(), "31.12." + moment().subtract(1, 'year').year()]
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
            });
        });
    </script>
@endpush