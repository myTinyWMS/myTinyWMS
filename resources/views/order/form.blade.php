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
        <div class="col-lg-12 col-xl-6">
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
                        </div>
                    </div>

                    <hr class="hr-line-solid">

                    <div class="row">
                        <div class="col-lg-12">
                            {{ Form::bsSelect('supplier', $order->supplier_id, \Mss\Models\Supplier::orderedByName()->pluck('name', 'id'),  'Lieferant', ['placeholder' => '']) }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsText('external_order_number', null, [], 'Bestellnummer des Lieferanten') }}
                        </div>

                        <div class="col-lg-6">
                            {{ Form::bsText('order_date', (!empty($order->order_date) ? $order->order_date->format('d.m.Y') : ''), ['class' => 'form-control datepicker'], 'Bestelldatum') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-xl-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Bemerkungen</h5>
                </div>
                <div class="ibox-content">
                    {{ Form::bsTextarea('notes', null, [], '') }}
                    {!! Form::hidden('order_id', $order->id) !!}
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-xl-12">
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

        <div class="col-lg-12 col-xl-12">
            <div class="ibox">
                <div class="ibox-content">
                    <div class="form-group">
                        @yield('submit')
                    </div>
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
                <div class="col-lg-6">
                    {{ Form::bsSelect('article[]', null, [],  'Artikel', ['class' => 'form-control article-select']) }}
                    <button type="button" class="btn btn-warning btn-xs m-t-sm m-r-md article-order-notes" data-toggle="tooltip" data-placement="left" title=""><i class="fa fa-exclamation-triangle"></i></button>
                </div>
                <div class="col-lg-2 text-right">
                    {{ Form::bsText('quantity[]', null, ['class' => 'form-control text-right quantity-select', 'required' => 'required'], 'Menge') }}
                </div>
                <div class="col-lg-2 text-right">
                    {{ Form::bsText('price[]', null, ['class' => 'form-control text-right price-select', 'required' => 'required'], 'Preis je Einheit') }}
                </div>
                <div class="col-lg-2 text-right">
                    {{ Form::bsText('expected_delivery[]', null, ['class' => 'form-control datepicker delivery-input'], 'Liefertermin') }}
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
            $('.article-order-notes').hide();
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
                var tooltip = $(this).parent().parent().parent().find('.article-order-notes');
                var quantity = $(this).parent().parent().parent().find('.quantity-select');
                var price = $(this).parent().parent().parent().find('.price-select');
                var delivery_date = $(this).parent().parent().parent().find('.delivery-input');

                console.log(quantity, price, delivery_date);

                tooltip.hide();

                $.each(allArticles, function (key, value) {
                    if (value.id == e.params.data.id) {
                        quantity.val(value.order_quantity);
                        price.val(formatPrice(value.price));
                        delivery_date.val(moment(value.delivery_date).format('DD.MM.YYYY'));
                        if (value.order_notes != '') {
                            tooltip.show().attr('title', value.order_notes).tooltip('fixTitle');
                        }
                    }
                });
            });

            $('.datepicker').datepicker({
                format: 'dd.mm.yyyy',
                language: 'de',
                todayHighlight: true,
                daysOfWeekDisabled: [0,6],
                autoclose: true,
                calendarWeeks: true
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

                console.log(value);

                $("#article-list .article-order-notes:eq(" + key + ")").attr('title', value.order_notes).tooltip('fixTitle');
                $("#article-list .article-select:eq(" + key + ")").val(value.article_id).trigger("change");
                $("#article-list .quantity-select:eq(" + key + ")").val(value.quantity).trigger("change");
                $("#article-list .price-select:eq(" + key + ")").val(formatPrice(value.price)).trigger("change");
                $("#article-list .delivery-input:eq(" + key + ")").val((moment(value.delivery_date).isValid() ? moment(value.delivery_date).format('DD.MM.YYYY') : ''));
            });

            $('.datepicker').datepicker({
                format: 'dd.mm.yyyy',
                language: 'de',
                todayHighlight: true,
                daysOfWeekDisabled: [0,6],
                autoclose: true,
                calendarWeeks: true
            });
        }

        function formatPrice(value) {
            return value.toString().replace('.', ',');
        }

        function filterArticlesAndSetSelects(supplier_id) {
            currentArticles = [];
            $.each(allArticles, function (key, value) {
                if (value.supplier_id == supplier_id) {
                    currentArticles.push({
                        id: value.id,
                        text: value.name,
                        category: value.category,
                        order_notes: value.order_notes,
                        delivery_date: value.delivery_date
                    });
                }
            });

            groupedCurrentArticles = [];
            $.each(groupBy(currentArticles, 'category'), function (key, value) {
                groupedCurrentArticles.push({
                    text: key,
                    children: value
                })
            });

            $("#article-list .article-select").select2({
                theme: "bootstrap",
                placeholder: "Bitte Artikel wählen",
                allowClear: true,
                data: groupedCurrentArticles
            });
            $("#article-list .article-select").val(null).trigger("change");
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

        var groupBy = function(xs, key) {
            return xs.reduce(function(rv, x) {
                (rv[x[key]] = rv[x[key]] || []).push(x);
                return rv;
            }, {});
        };
    </script>
@endpush
