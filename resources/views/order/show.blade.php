@extends('layout.app')

@section('title', 'Bestelldetails')

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
                        <h3>{{ $order->internal_order_number }}</h3>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Bestellnummer des Lieferanten</small>
                        <h3>{{ $order->external_order_number }}</h3>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Gesamtkosten</small>
                        <h3>{{ formatPrice($order->total_cost) }}</h3>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Versandkosten</small>
                        <h3>{{ formatPrice($order->shipping_cost) }}</h3>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-xs-3">
                        <small class="stats-label">Bestelldatum</small>
                        <h3>{{ !empty($order->order_date) ? $order->order_date->format('d.m.Y') : '' }}</h3>
                    </div>

                    <div class="col-xs-3">
                        <small class="stats-label">Liefertermin</small>
                        <h3>{{ !empty($order->expected_delivery) ? $order->expected_delivery->format('d.m.Y') : '' }}</h3>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <small class="stats-label">Bemerkungen</small>
                        <h3>{{ $order->notes ?: '-' }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-title">
                <h5>Artikel</h5>
            </div>
            <div class="ibox-content">
                @foreach($order->items as $item)
                    <div class="panel panel-primary">
                        <div class="panel-body row">
                            <div class="col-lg-7">
                                <small class="stats-label">Artikel</small>
                                <h4>{{ $item->article->name }}</h4>
                            </div>
                            <div class="col-lg-2">
                                <small class="stats-label">Menge</small>
                                <h4>{{ $item->quantity }}</h4>
                            </div>
                            <div class="col-lg-3">
                                <small class="stats-label">Preis je Einheit</small>
                                <h4>{{ formatPrice($item->price) }}</h4>
                            </div>
                        </div>
                    </div>
                @endforeach
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
@endsection