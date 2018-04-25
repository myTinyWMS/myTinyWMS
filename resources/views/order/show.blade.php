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
                        <h2><a href="{{ route('supplier.show', $order->supplier) }}" target="_blank">{{ optional($order->supplier)->name }}</a></h2>
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
                        <h2>@include('order.status', ['status' => $order->status])</h2>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Bezahlstatus</small>
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
                <div class="col-lg-6">
                    <h5>Artikel</h5>
                </div>
                <div class="col-lg-6">
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
                                </h3>
                            </div>
                            <div class="col-lg-7">
                                <div class="col-lg-4">
                                    <small class="stats-label">Preis je Einheit</small>
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
                                        @if($item->invoice_received)
                                            <span class="text-success">erhalten</span>
                                        @else
                                            <span class="text-danger">nicht erhalten</span>
                                            {!! Form::open(['method' => 'post', 'class' => 'force-inline', 'route' => ['order.item_invoice_received', $item]]) !!}
                                            <button type="submit" class="btn btn-xs btn-outline btn-success"><i class="fa fa-check"></i></button>
                                            {!! Form::close() !!}
                                        @endif
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
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.payment-type-dropdown a').click(function (e) {
            e.preventDefault();
            $('#payment_type').val($(this).data('value'));
            $(this).closest('form').submit();
        });
    })
</script>
@endpush