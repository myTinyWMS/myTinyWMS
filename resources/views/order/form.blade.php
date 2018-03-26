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
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Bestelldetails</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label">interne Bestellnummer</label>
                                <h2 class="form-control-static no-margins">
                                    {{ $order->internal_order_number }}
                                </h2>
                                <small class="text-danger">Bitte bei der Bestellung angeben</small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsSelect('status', $order->status, \Mss\Models\Order::STATUS_TEXTS,  'Status') }}
                            {{ Form::bsCheckbox('confirmation_received', 1, 'Auftragsbetätigung erhalten', $order->confirmation_received, []) }}
                            {{ Form::bsCheckbox('invoice_received', 1, 'Rechnung erhalten', $order->invoice_received, []) }}
                        </div>
                    </div>

                    <hr class="hr-line-solid">

                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsSelect('supplier', $order->supplier_id, \Mss\Models\Supplier::orderedByName()->pluck('name', 'id'),  'Lieferant', ['placeholder' => '']) }}
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsText('external_order_number', null, [], 'Bestellnummer des Lieferanten') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsText('total_cost', str_replace('.', ',', $order->total_cost), [], 'Gesamtkosten') }}
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsText('shipping_cost', str_replace('.', ',', $order->shipping_cost), [], 'Versandkosten') }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsText('order_date', (!empty($order->order_date) ? $order->order_date->format('d.m.Y') : ''), ['class' => 'form-control datepicker'], 'Bestelldatum') }}
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsText('expected_delivery', (!empty($order->expected_delivery) ? $order->expected_delivery->format('d.m.Y') : ''), ['class' => 'form-control datepicker'], 'Liefertermin') }}
                        </div>
                    </div>

                    {{ Form::bsTextarea('notes', null, [], 'Bemerkungen') }}
                    {!! Form::hidden('order_id', $order->id) !!}

                    <div class="form-group">
                        @yield('submit')
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Bestellte Artikel</h5>
                </div>
                <div class="ibox-content">
                    <div id="article-list"></div>
                    <button class="btn btn-primary btn-sm" id="add-article">weiterer Artikel</button>
                </div>
            </div>
        </div>
        @yield('secondCol')
    </div>
    {!! Form::close() !!}

    <style>
        #article-list .row {
            position: relative;
        }

        #article-list .row .btn {
            position: absolute;
            right: 0;
            top: -15px;
        }
    </style>

    <div class="article-template hidden">
        <div class="panel panel-primary">
            <div class="panel-body row">
                <div class="col-lg-7">
                    {{ Form::bsSelect('article[]', null, [],  'Artikel', ['class' => 'form-control article-select']) }}
                </div>
                <div class="col-lg-2 text-right">
                    {{ Form::bsText('quantity[]', null, ['class' => 'form-control text-right quantity-select', 'required' => 'required'], 'Menge') }}
                </div>
                <div class="col-lg-3 text-right">
                    {{ Form::bsText('price[]', null, ['class' => 'form-control text-right price-select', 'required' => 'required'], 'Preis je Einheit') }}
                </div>
                <a href="#" class="btn btn-xs btn-default btn-circle remove-article"><i class="glyphicon glyphicon-remove"></i></a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        addArticle();

        var existingArticles = {!! $order->items->toJson() !!};
        var currentArticles = [];
        var allArticles = {!! $articles->toJson() !!};

        $(document).ready(function () {
            $('#add-article').click(function () {
                addArticle();
                return false;
            });

            $("#supplier").select2({
                theme: "bootstrap",
                placeholder: "Bitte Lieferant wählen",
                allowClear: true
            });

            $("#supplier").on('select2:select', function (e) {
                filterArticlesAndSetSelects(e.params.data.id);
            });

            $("#article-list .article-select").on('select2:select', function (e) {
                var quantity = $(this).parent().parent().parent().find('.quantity-select');
                var price = $(this).parent().parent().parent().find('.price-select');

                $.each(allArticles, function (key, value) {
                    if (value.id == e.params.data.id) {
                        quantity.val(value.order_quantity);
                        price.val(formatPrice(value.price / 100));
                    }
                });
            });

            $('.datepicker').datepicker({
                format: 'dd.mm.yyyy',
                language: 'de',
                todayHighlight: true,
                daysOfWeekDisabled: [0,6],
                autoclose: true
            });

            @if(!empty($order->supplier_id))
            $('#supplier').val('{{ $order->supplier_id }}'); // Select the option with a value of '1'
            $('#supplier').trigger('change'); // Notify any JS components that the value changed
            filterArticlesAndSetSelects({{ $order->supplier_id }});
            @endif

            if (existingArticles.length) {
                preSetArticles();
            }
        });

        function preSetArticles() {
            $.each(existingArticles, function (key, value) {
                if (key !== 0) {
                    addArticle();
                }

                $("#article-list .article-select:eq(" + key + ")").val(value.article_id).trigger("change");
                $("#article-list .quantity-select:eq(" + key + ")").val(value.quantity).trigger("change");
                $("#article-list .price-select:eq(" + key + ")").val(formatPrice(value.price / 100)).trigger("change");
            });
        }

        function formatPrice(value) {
            return value.toString().replace('.', ',');
        }

        function filterArticlesAndSetSelects(supplier_id) {
            currentArticles = [];
            $.each(allArticles, function (key, value) {
                if (value.supplier_id === supplier_id) {
                    currentArticles.push({
                        id: value.id,
                        text: value.name
                    });
                }
            });
            $("#article-list .article-select").val(null).trigger("change");
            $("#article-list .article-select").select2({
                theme: "bootstrap",
                placeholder: "Bitte Artikel wählen",
                allowClear: true,
                data: currentArticles
            });
        }

        function addArticle() {
            $('#article-list').append($('.article-template').html());

            var newId = generateId();
            $("#article-list .article-select:not(.select2-hidden-accessible)").attr('id', newId);
            $("#"+newId).select2({
                theme: "bootstrap",
                placeholder: "Bitte Artikel wählen",
                allowClear: true,
                data: currentArticles
            });

            $('.remove-article').click(function () {
                $(this).parent().parent().remove();
                return false;
            });
        }
    </script>
@endpush
