@extends('layout.app')

@section('title', 'Neue Nachrichten')

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">Bestellungen</a>
    </li>
    <li class="active">
        <strong>Neue Nachrichten</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="card w-full">
            <div class="card-content">
                <order-messages :messages="{{ $unassignedMessages }}"></order-messages>
            </div>
        </div>
    </div>


    <assign-order-message-modal>{!! $dataTable->table() !!}</assign-order-message-modal>

    <data-tables-filter>
        <data-tables-filter-select label="Lieferant" col-id="1" id="filterSupplier">
            <option value=""></option>
            @foreach(\Mss\Models\Supplier::orderedByName()->get() as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </data-tables-filter-select>

        <data-tables-filter-select label="Status" col-id="3">
            <option value="open">offen (neu, bestellt, teilweise geliefert)</option>
            <option value="{{ \Mss\Models\Order::STATUS_NEW }}">neu</option>
            <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">bestellt</option>
            <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">teilweise geliefert</option>
            <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">geliefert</option>
            <option value="{{ \Mss\Models\Order::STATUS_PAID }}">bezahlt</option>
            <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">storniert</option>
        </data-tables-filter-select>
    </data-tables-filter>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush