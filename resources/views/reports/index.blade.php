@extends('layout.app')

@section('title', 'Reports')

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-lg-6">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Inventurauswertung</h5>
                    </div>
                    <div class="ibox-content">
                        <form method="post" action="{{ route('reports.inventory_report') }}" id="inventory-report">
                            {{ csrf_field() }}
                            <div class="form-control-static">Monat auswählen um Report zu erstellen:</div>
                            <div id="monthpicker">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                            <input type="hidden" value="" name="month" id="month" />
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Downloads</h5>
                    </div>
                    <div class="ibox-content">
                        <a href="{{ route('reports.inventory_pdf') }}" class="btn btn-primary">Monats-Inventur PDF</a>
                    </div>
                </div>
            </div>
        </div>
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
        });
    </script>
@endpush