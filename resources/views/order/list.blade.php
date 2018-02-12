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
        Status:&nbsp;
        <select id="filterStatus" data-target-col="2" class="form-control input-sm datatableFilter-select">
            <option value="open">offen (neu, bestellt, teilweise geliefert)</option>
            <option value="{{ \Mss\Models\Order::STATUS_NEW }}">neu</option>
            <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">bestellt</option>
            <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">teilweise geliefert</option>
            <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">geliefert</option>
            <option value="{{ \Mss\Models\Order::STATUS_PAID }}">bezahlt</option>
            <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">storniert</option>
        </select>
    </label>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush