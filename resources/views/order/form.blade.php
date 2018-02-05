@extends('layout.app')

@section('content')
    @if (count($errors) > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @yield('form_start')
    <div class="row">
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Bestelldetails</h5>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label class="control-label">interne Bestellnummer</label>
                        <p class="form-control-static">{{ $order->internal_order_number }}</p>
                    </div>
                    {{ Form::bsText('external_order_number', null, [], 'Bestellnummer des Lieferanten') }}
                    {{ Form::bsText('total_cost', null, [], 'Gesamtkosten') }}
                    {{ Form::bsText('shipping_cost', null, [], 'Versandkosten') }}
                    {{ Form::bsText('expected_delivery', null, ['class' => 'form-control datepicker'], 'Liefertermin') }}
                    {{ Form::bsText('order_date', null, ['class' => 'form-control datepicker'], 'Bestelldatum') }}
                    {{ Form::bsSelect('supplier', $order->unit_id, \Mss\Models\Supplier::orderBy('name')->pluck('name', 'id'),  'Lieferant') }}

                    {{ Form::bsTextarea('notes', null, [], 'Bemerkungen') }}

                    <div class="form-group">
                        @yield('submit')
                        {!! Form::button('Abbrechen', ['class' => 'btn btn-danger pull-right']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Artikel</h5>
                </div>
                <div class="ibox-content">
                    {{ Form::bsSelect('article[]', null, [],  'Artikel', ['class' => 'form-control article']) }}
                </div>
            </div>
        </div>
        @yield('secondCol')
    </div>
    {!! Form::close() !!}
@endsection

@push('scripts')
    <script>
        var currentArticles = [];
        var allArticles = {!! $articles->toJson() !!};
        $(document).ready(function () {
            $("#supplier").select2({
                theme: "bootstrap"
            });

            $("#supplier").on('select2:select', function (e) {
                currentArticles = [];
                $.each(allArticles, function (key, value) {
                    if (value.supplier_id == e.params.data.id) {
                        currentArticles.push({
                            id: value.id,
                            text: value.name
                        });
                    }
                });
                console.log(currentArticles);
                $(".article").val(null).trigger("change");
                $(".article").select2('destroy').select2({
                    theme: "bootstrap",
                    data: currentArticles
                });
            });

            $(".article").select2({
                theme: "bootstrap",
                data: currentArticles
            });

            $('.datepicker').datepicker({
                format: 'dd.mm.yyyy',
                language: 'de',
                todayHighlight: true,
                daysOfWeekDisabled: [0,6]
            });
        });
    </script>
@endpush
