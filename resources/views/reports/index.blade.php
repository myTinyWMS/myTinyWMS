@extends('layout.app')

@section('title', 'Reports')

@section('content')
    <div class="row">
        <div class="col-lg-3">
            <div class="contact-box center-version">
                <a href="#">
                    <h3 class="m-b-xs"><strong>Inventurauswertung</strong></h3>
                    <div class="font-bold">Monat und Inventurtyp auswählen um Report zu erstellen:</div>
                </a>
                <div class="contact-box-footer">
                    <form method="post" action="{{ route('reports.inventory_report') }}" id="inventory-report">
                        {{ csrf_field() }}
                        {{ Form::bsSelect('inventorytype', null, \Mss\Models\Article::getInventoryTextArray(),  'Inventur Typ', ['placeholder' => 'egal']) }}
                        <div class="m-t-xs btn-group">
                            <div id="monthpicker"></div>
                        </div>
                        <input type="hidden" value="" name="month" id="month" />
                    </form>
                </div>
            </div>
        </div>

         <div class="col-lg-3">
            <div class="contact-box center-version">
                <a href="#">
                    <h3 class="m-b-xs"><strong>WA Vergleich</strong></h3>
                    <div class="font-bold">Monat auswählen um Report zu erstellen:</div>
                </a>
                <div class="contact-box-footer">
                    <form method="post" action="{{ route('reports.article_usage_report') }}" id="article-usage-report">
                        {{ csrf_field() }}
                        <div class="m-t-xs btn-group">
                            <div id="monthpicker2"></div>
                        </div>
                        <input type="hidden" value="" name="month" id="month2" />
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-2">
            <div class="contact-box center-version">

                <a href="{{ route('reports.inventory_pdf') }}">
                    <h3 class="m-b-xs"><strong>Monats-Inventur-Liste</strong></h3>
                    <div class="font-bold">aktive Verbrauchsmaterialien</div>
                </a>
                <div class="contact-box-footer">
                    <div class="m-t-xs btn-group">
                        <a class="btn btn-white" href="{{ route('reports.inventory_pdf') }}"><i class="fa fa-download"></i> PDF herunterladen </a>
                    </div>
                </div>
            </div>
            <div class="contact-box center-version">

                <a href="{{ route('reports.inventory_pdf') }}">
                    <h3 class="m-b-xs"><strong>Jahres-Inventur-Liste</strong></h3>
                    <div class="font-bold">alle aktiven Artikel</div>
                </a>
                <div class="contact-box-footer">
                    <div class="m-t-xs btn-group">
                        <a class="btn btn-white" href="{{ route('reports.yearly_inventory_pdf') }}"><i class="fa fa-download"></i> PDF herunterladen </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2">
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

        {{--<div class="col-lg-3">
            <div class="contact-box center-version">

                <a href="profile.html">

                    <img alt="image" class="img-circle" src="img/a2.jpg">


                    <h3 class="m-b-xs"><strong>John Smith</strong></h3>

                    <div class="font-bold">Graphics designer</div>
                    <address class="m-t-md">
                        <strong>Twitter, Inc.</strong><br>
                        795 Folsom Ave, Suite 600<br>
                        San Francisco, CA 94107<br>
                        <abbr title="Phone">P:</abbr> (123) 456-7890
                    </address>

                </a>
                <div class="contact-box-footer">
                    <div class="m-t-xs btn-group">
                        <a class="btn btn-xs btn-white"><i class="fa fa-phone"></i> Call </a>
                        <a class="btn btn-xs btn-white"><i class="fa fa-envelope"></i> Email</a>
                        <a class="btn btn-xs btn-white"><i class="fa fa-user-plus"></i> Follow</a>
                    </div>
                </div>

            </div>
        </div>--}}
    </div>
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