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
    <order-form inline-template ref="orderForm" :existing-articles="{{ json_encode($preSetArticles) }}" :supplier-col-id="{{ \Mss\DataTables\SelectArticleDataTable::SUPPLIER_COL_ID }}">
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
                            <div class="col-lg-6" id="supplier_select">
                                {{ Form::bsSelect('supplier', $order->supplier_id, \Mss\Models\Supplier::orderedByName()->pluck('name', 'id'),  'Lieferant', ['placeholder' => '', 'v-model' => 'supplier', 'v-bind:disabled' => 'hasArticles']) }}
                            </div>
                            <div class="col-lg-6">
                                {{ Form::bsSelect('payment_status', $order->payment_status, \Mss\Models\Order::PAYMENT_STATUS_TEXT,  'Bezahlmethode') }}
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
                    <div class="ibox-content" id="article-list">
                        <order-article-list ref="articleList" :supplier="supplier" :articles="articles" :all-articles="{{ json_encode($allArticles) }}" :existing-articles="existingArticles"></order-article-list>
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

            <!-- Modal -->
            <div class="modal modal-wide" id="articleSelectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Artikel auswählen:</h4>
                        </div>
                        <div class="modal-body">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="supplier_id" v-model="supplier">
            <input type="hidden" name="article_data" v-model="articleData">
        </div>
    </order-form>
    {!! Form::close() !!}

    <data-tables-filter>
        <data-tables-filter-select label="Kategorie" col-id="{{ \Mss\DataTables\SelectArticleDataTable::CATEGORY_COL_ID }}">
            <option value=""></option>
            @foreach($categories as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </data-tables-filter-select>

        <data-tables-filter-select label="Tags" col-id="{{ \Mss\DataTables\SelectArticleDataTable::TAGS_COL_ID }}">
            <option value=""></option>
            @foreach($tags as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </data-tables-filter-select>

        <data-tables-filter-select label="Status" col-id="{{ \Mss\DataTables\SelectArticleDataTable::STATUS_COL_ID }}" pre-set="1">
            <option value="all">alle</option>
            <option value="1">aktiv</option>
            <option value="0">deaktiviert</option>
            <option value="2">Bestellstopp</option>
        </data-tables-filter-select>
    </data-tables-filter>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        function selectArticle(id) {
            window.app.$refs.orderForm.selectArticle(id);
        }
    </script>
@endpush
