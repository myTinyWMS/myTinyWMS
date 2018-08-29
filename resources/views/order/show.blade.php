@extends('layout.app')

@section('title', 'Bestellung bei '.optional($order->supplier)->name)

@section('title_extra')
    <a href="{{ route('order.create_delivery', $order) }}" class="btn btn-primary btn-sm pull-right">Wareneingang erfassen</a>
@endsection

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Bestelldetails</strong>
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 col-xxl-8">
        <div class="ibox">
            <div class="ibox-title">
                <h5>
                    Bestellung #{{ $order->internal_order_number }}
                </h5>
                <a href="{{ route('order.edit', $order) }}" class="btn btn-primary btn-xs pull-right">bearbeiten</a>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-xs-3">
                        <small class="stats-label">interne Bestellnummer</small>
                        <h2>{{ $order->internal_order_number }}</h2>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Bestellnummer des Lieferanten</small>
                        <h2>{{ $order->external_order_number }}</h2>
                    </div>

                    <div class="col-xs-6">
                        <small class="stats-label">Lieferant</small>
                        <h2>
                            <a href="{{ route('supplier.show', $order->supplier) }}" target="_blank" title="Lieferant aufrufen">{{ optional($order->supplier)->name }}</a>
                            <a href="{{ route('article.index', ['supplier' => $order->supplier->id]) }}" title="Artikel des Lieferanten aufrufen"><i class="fa fa-filter"></i></a>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-xs-3">
                        <small class="stats-label">Bestelldatum</small>
                        <h2>{{ !empty($order->order_date) ? $order->order_date->format('d.m.Y') : '' }}</h2>
                    </div>

                    <div class="col-xs-3">

                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Status</small>
                        <h2>
                        {!! Form::open(['method' => 'post', 'class' => 'force-inline', 'route' => ['order.change_status', $order]]) !!}
                        <button type="button" class="btn btn-xs btn-link dropdown-toggle no-padding" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @include('order.status', ['status' => $order->status])
                        </button>
                        <ul class="dropdown-menu status-dropdown" aria-labelledby="dLabel">
                            @foreach(\Mss\Models\Order::STATUS_TEXTS as $value => $name)
                            <li><a href="#" data-value="{{ $value }}">{{ $name }}</a></li>
                            @endforeach
                        </ul>
                        <input type="hidden" id="status" name="status" value="" />
                        {!! Form::close() !!}
                        </h2>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Bezahlmethode</small>
                        <h2>
                            @if($order->payment_status > 0)
                                <span class="text-success">{{ \Mss\Models\Order::PAYMENT_STATUS_TEXT[$order->payment_status] }}</span>
                            @else
                                <span class="text-danger">{{ \Mss\Models\Order::PAYMENT_STATUS_TEXT[$order->payment_status] }}</span>
                                {!! Form::open(['method' => 'post', 'class' => 'force-inline', 'route' => ['order.change_payment_status', $order]]) !!}
                                <button type="button" class="btn btn-xs btn-outline btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-check"></i>
                                </button>
                                <ul class="dropdown-menu payment-type-dropdown" aria-labelledby="dLabel">
                                    <li><a href="#" data-value="{{ \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_PAYPAL }}">Paypal</a></li>
                                    <li><a href="#" data-value="{{ \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_CREDIT_CARD }}">Kreditkarte</a></li>
                                    <li><a href="#" data-value="{{ \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_INVOICE }}">Rechnung</a></li>
                                    <li><a href="#" data-value="{{ \Mss\Models\Order::PAYMENT_STATUS_PAID_WITH_AUTOMATIC_DEBIT_TRANSFER }}">Bankeinzug</a></li>
                                </ul>
                                <input type="hidden" id="payment_type" name="type" value="" />
                                {!! Form::close() !!}
                            @endif
                        </h2>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-xs-12">
                        <small class="stats-label">Bemerkungen</small>
                        <h2>{{ $order->notes ?: '-' }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xxl-4">
        <div class="ibox collapsed">
            <div class="ibox-title">
                <h5>Logbuch</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                @include('components.audit_list', $audits)
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 col-xxl-8">
        <div class="ibox">
            <div class="ibox-title">
                <div class="col-lg-5">
                    <h5>Artikel</h5>
                </div>
                <div class="col-lg-7">
                    <div class="col-lg-4">
                        {!! Form::open(['method' => 'post', 'class' => 'force-inline', 'route' => ['order.all_items_confirmation_received', $order]]) !!}
                        <button type="submit" class="btn btn-xs btn-outline btn-success btn-xs" title="alle Auftragsbestätigungen erhalten"><i class="fa fa-check"></i> Auftragsbestätigung</button>
                        {!! Form::close() !!}
                    </div>
                    <div class="col-lg-4">
                        {!! Form::open(['method' => 'post', 'class' => 'force-inline', 'route' => ['order.all_items_invoice_received', $order]]) !!}
                        <button type="submit" class="btn btn-xs btn-outline btn-success btn-xs" title="alle Rechnungen erhalten"><i class="fa fa-check"></i> Rechnung</button>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                @foreach($order->items as $item)
                    <div class="panel panel-primary">
                        <div class="panel-body row">
                            <div class="col-lg-5">
                                <small class="stats-label">Artikel</small>
                                <h3>
                                    <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                    <br/>
                                    <small class="p-t-8"># {{ $item->article->article_number }}</small>
                                    <br>
                                    <br>
                                    @if (($item->article->getCurrentSupplierArticle()->price / 100) != $item->price)
                                    <span class="text-danger font-bold font-14">Achtung, aktueller Artikelpreis weicht von Preis aus dieser Bestellung ab!</span>
                                    @endif
                                </h3>
                            </div>
                            <div class="col-lg-7">
                                <div class="col-lg-4">
                                    <small class="stats-label">Preis netto je Einheit</small>
                                    <h3>{!! formatPrice($item->price)  !!}</h3>
                                </div>

                                <div class="col-lg-4">
                                    <small class="stats-label">bestellte Menge</small>
                                    <h3>{{ $item->quantity }}</h3>
                                </div>
                                <div class="col-lg-4">
                                    @if($item->getQuantityDelivered() == $item->quantity)
                                        <h1 class="pull-right" title="komplett geliefert"><i class="fa fa-check-circle text-success"></i></h1>
                                    @elseif($item->getQuantityDelivered() > $item->quantity)
                                        <h1 class="pull-right" title="zu viel geliefert!"><i class="fa fa-exclamation-triangle text-danger"></i></h1>
                                    @endif
                                    <small class="stats-label">gelieferte Menge</small>
                                    <h3 class="@if($item->getQuantityDelivered() < $item->quantity) text-warning @elseif($item->getQuantityDelivered() > $item->quantity) text-danger @else text-success @endif">{{ $item->getQuantityDelivered() }}</h3>
                                </div>

                                <div class="col-lg-4 m-t-md">
                                    <small class="stats-label">Auftragsbestätigung</small>
                                    <h3>
                                        @if($item->confirmation_received)
                                            <span class="text-success">erhalten</span>
                                        @else
                                            <span class="text-danger">nicht erhalten</span>
                                            {!! Form::open(['method' => 'post', 'class' => 'force-inline', 'route' => ['order.item_confirmation_received', $item]]) !!}
                                            <button type="submit" class="btn btn-xs btn-outline btn-success"><i class="fa fa-check"></i></button>
                                            {!! Form::close() !!}
                                        @endif
                                    </h3>
                                </div>

                                <div class="col-lg-4 m-t-md">
                                    <small class="stats-label">Rechnung</small>
                                    <h3>
                                        @if($item->invoice_received === \Mss\Models\OrderItem::INVOICE_STATUS_RECEIVED)
                                            <span class="text-success">erhalten</span>
                                        @elseif($item->invoice_received === \Mss\Models\OrderItem::INVOICE_STATUS_CHECK)
                                            <span class="text-warning">in Prüfung</span>
                                        @else
                                            <span class="text-danger">nicht erhalten</span>
                                        @endif

                                        {!! Form::open(['method' => 'post', 'class' => 'force-inline', 'route' => ['order.item_invoice_received', $item]]) !!}
                                        <button type="button" class="btn btn-xs btn-outline btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <ul class="dropdown-menu invoice-status-dropdown" aria-labelledby="dLabel">
                                            <li><a href="#" data-value="{{ \Mss\Models\OrderItem::INVOICE_STATUS_RECEIVED }}">{{ \Mss\Models\OrderItem::INVOICE_RECEIVED_TEXT[\Mss\Models\OrderItem::INVOICE_STATUS_RECEIVED] }}</a></li>
                                            <li><a href="#" data-value="{{ \Mss\Models\OrderItem::INVOICE_STATUS_CHECK }}">{{ \Mss\Models\OrderItem::INVOICE_RECEIVED_TEXT[\Mss\Models\OrderItem::INVOICE_STATUS_CHECK] }}</a></li>
                                            <li><a href="#" data-value="{{ \Mss\Models\OrderItem::INVOICE_STATUS_OPEN }}">{{ \Mss\Models\OrderItem::INVOICE_RECEIVED_TEXT[\Mss\Models\OrderItem::INVOICE_STATUS_OPEN] }}</a></li>
                                        </ul>
                                        <input type="hidden" name="invoice_status" value="" />
                                        <input type="hidden" name="mail_note" value="" />
                                        <input type="hidden" name="mail_attachments" value="" />
                                        {!! Form::close() !!}

                                    </h3>
                                </div>

                                <div class="col-lg-4 m-t-md">
                                    <small class="stats-label">Liefertermin</small>
                                    <h3>{{ !empty($item->expected_delivery) ? $item->expected_delivery->format('d.m.Y') : '' }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xxl-4">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Lieferungen</h5>
            </div>
            <div class="ibox-content">
                @foreach($order->deliveries->sortByDesc('delivery_date') as $delivery)
                <div class="panel panel-primary">
                    <div class="panel-body row">
                        <div class="col-lg-4">
                            <small class="stats-label">Lieferdatum</small>
                            <h3>{{ $delivery->delivery_date ? $delivery->delivery_date->format('d.m.Y') : '-' }}</h3>
                        </div>
                        <div class="col-lg-8">
                            <small class="stats-label">Bemerkung</small>
                            <h3>{{ $delivery->notes }}</h3>
                        </div>
                        <div class="col-lg-12">
                            <table class="table table-condensed table-border">
                                <thead>
                                    <tr>
                                        <th style="width: 40px">#</th>
                                        <th>Artikel</th>
                                        <th>Menge</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($delivery->items as $item)
                                    <tr>
                                        <td>{{ $item->article->article_number }}</td>
                                        <td>
                                            <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Kommunikation</h5>
                <a href="{{ route('order.message_new', $order) }}" class="btn btn-primary btn-xs pull-right">Neue Nachricht</a>
            </div>
            <div class="ibox-content order-messages">
                @include('order.communications')
            </div>
        </div>
    </div>
</div>

<!-- New Note Modal -->
<div class="modal fade" id="invoiceCheckModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Rechnungsprüfung - Mail an Einkaufsteam</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="invoice_check_note">Bemerkungen zur Rechnung</label>
                        <textarea id="invoice_check_note" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="dropzone"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Ohne Mail weiter</button>
                <button type="button" class="btn btn-primary" id="send_invoice_check_mail">Mail verschicken</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var attachments = [];

    Dropzone.autoDiscover = false;
    var dropzoneOptions = {
        url: "{{ route('order.invoice_check_upload', $order) }}",
        clickable: true,
        dictDefaultMessage: 'Dateien hier ablegen',
        init: function() {
            this.on("complete", function(event) {
                var file = {
                    'tempFile': JSON.parse(event.xhr.response),
                    'orgName': event.name,
                    'type': event.type
                };

                attachments.push(file);
            });
        }
    };

    var currentForm = null;

    $(document).ready(function () {
        $('.payment-type-dropdown a').click(function (e) {
            e.preventDefault();
            $('#payment_type').val($(this).data('value'));
            $(this).closest('form').submit();
        });

        $('.status-dropdown a').click(function (e) {
            e.preventDefault();
            $('#status').val($(this).data('value'));
            $(this).closest('form').submit();
        });

        $('.invoice-status-dropdown a').click(function (e) {
            e.preventDefault();
            $(this).parent().parent().parent().find('input[name="invoice_status"]').val($(this).data('value'));

            if ($(this).data('value') === 2) {
                currentForm = $(this).closest('form');
                $('#invoiceCheckModal').modal('show');

                return false;
            }

            $(this).closest('form').submit();
        });

        $('#send_invoice_check_mail').click(function () {
            $(currentForm).find('input[name="mail_note"]').val($('#invoice_check_note').val());
            $(currentForm).find('input[name="mail_attachments"]').val(JSON.stringify(attachments));
            $('#invoiceCheckModal').modal('hide');
        });

        $('#invoiceCheckModal').on('hide.bs.modal', function (e) {
            $(currentForm).submit();
        });
        $('#invoiceCheckModal').on('shown.bs.modal', function (e) {
            $('.dropzone').dropzone(dropzoneOptions);
        });
    })
</script>
@endpush