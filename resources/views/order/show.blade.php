@extends('layout.app')

@section('title', 'Bestelldetails')

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
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title">
                <h5>
                    Bestelldetails
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

                    <div class="col-xs-3">
                        <small class="stats-label">Gesamtkosten</small>
                        <h2>{!! formatPrice($order->total_cost) !!}</h2>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Versandkosten</small>
                        <h2>{!! formatPrice($order->shipping_cost) !!}</h2>
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
                        <small class="stats-label">Liefertermin</small>
                        <h2>{{ !empty($order->expected_delivery) ? $order->expected_delivery->format('d.m.Y') : '' }}</h2>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Status</small>
                        <h2>@include('order.status', ['status' => $order->status])</h2>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Auftragsbestätigung</small>
                        <h2>
                            @if($order->confirmation_received)
                                <span class="text-success">erhalten</span>
                            @else
                                <span class="text-danger">nicht erhalten</span>
                            @endif
                        </h2>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <small class="stats-label">Bemerkungen</small>
                        <h2>{{ $order->notes ?: '-' }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
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
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Artikel</h5>
            </div>
            <div class="ibox-content">
                @foreach($order->items as $item)
                    <div class="panel panel-primary">
                        <div class="panel-body row">
                            <div class="col-lg-6">
                                <small class="stats-label">Artikel</small>
                                <h4>
                                    <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                    <br/>
                                    <small class="p-t-8"># {{ $item->article->article_number }}</small>
                                </h4>
                            </div>
                            <div class="col-lg-2">
                                <small class="stats-label">Preis je Einheit</small>
                                <h4>{!! formatPrice($item->price)  !!}</h4>
                            </div>
                            <div class="col-lg-2">
                                <small class="stats-label">bestellte Menge</small>
                                <h4>{{ $item->quantity }}</h4>
                            </div>
                            <div class="col-lg-2">
                                @if($item->getQuantityDelivered() == $item->quantity)
                                    <h1 class="pull-right" title="komplett geliefert"><i class="fa fa-check-circle text-success"></i></h1>
                                @elseif($item->getQuantityDelivered() > $item->quantity)
                                    <h1 class="pull-right" title="zu viel geliefert!"><i class="fa fa-exclamation-triangle text-danger"></i></h1>
                                @endif
                                <small class="stats-label">gelieferte Menge</small>
                                <h4 class="@if($item->getQuantityDelivered() < $item->quantity) text-warning @elseif($item->getQuantityDelivered() > $item->quantity) text-danger @else text-success @endif">{{ $item->getQuantityDelivered() }}</h4>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Lieferungen</h5>
            </div>
            <div class="ibox-content">
                @foreach($order->deliveries->sortByDesc('delivery_date') as $delivery)
                <div class="panel panel-primary">
                    <div class="panel-body row">
                        <div class="col-lg-2">
                            <small class="stats-label">Lieferdatum</small>
                            <h3>{{ $delivery->delivery_date ? $delivery->delivery_date->format('d.m.Y') : '-' }}</h3>
                        </div>
                        <div class="col-lg-2">
                            <small class="stats-label">Lieferscheinnummer</small>
                            <h3>{{ $delivery->delivery_note_number }}</h3>
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
            </div>
            <div class="ibox-content order-messages">
                @include('order.communications')
            </div>
        </div>
    </div>
</div>
@endsection