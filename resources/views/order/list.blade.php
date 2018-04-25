@extends('layout.app')

@section('title', 'Bestellungen')

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('subnav')
    <a href="{{ route('order.create') }}" class="btn btn-xs btn-primary">Neue Bestellung</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            @if($unassignedMessages)
            <div class="alert alert-warning">
                <strong>{{ $unassignedMessages }}</strong> nicht zugeordnete neue {{ trans_choice('plural.message', $unassignedMessages) }} - <a class="alert-link" href="{{ route('order.messages_unassigned') }}">mehr &raquo;</a>
            </div>
            @endif

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Übersicht</h5>
                </div>
                <div class="ibox-content">
                    {!! $dataTable->table() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('datatableFilters')
    <label>
        Lieferant:&nbsp;
        <select id="filterSupplier" data-target-col="1" class="form-control input-sm datatableFilter-select">
            <option value=""></option>
            @foreach($supplier as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </label>

    <label>
        Status:&nbsp;
        <select id="filterStatus" data-target-col="2" class="form-control input-sm datatableFilter-select">
            <option value="open">offen (neu, bestellt, teilweise geliefert)</option>
            <option value="{{ \Mss\Models\Order::STATUS_NEW }}">neu</option>
            <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">bestellt</option>
            <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">teilweise geliefert</option>
            <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">geliefert</option>
        </select>
    </label>

    <label>
        Rechnungsstatus:&nbsp;
        <select id="filterInvoiceStatus" data-target-col="4" class="form-control input-sm datatableFilter-select">
            <option value="empty">alle</option>
            <option value="none">offen</option>
            <option value="all">komplett erhalten</option>
            <option value="partial">teilweise erhalten</option>
        </select>
    </label>

    <label>
        AB-Status:&nbsp;
        <select id="filterConfirmationStatus" data-target-col="3" class="form-control input-sm datatableFilter-select">
            <option value="empty">alle</option>
            <option value="none">offen</option>
            <option value="all">komplett erhalten</option>
            <option value="partial">teilweise erhalten</option>
        </select>
    </label>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush