@extends('layout.app')

@section('title', 'Bestellungen')

@section('title_extra')
    <a href="{{ route('order.create') }}" class="btn btn-secondary">Neue Bestellung</a>
@endsection

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('content')

    @if($unassignedMessages)
    <div class="alert alert-warning mb-6">
        <strong>{{ $unassignedMessages }}</strong> nicht zugeordnete neue {{ trans_choice('plural.message', $unassignedMessages) }} - <a class="alert-link" href="{{ route('order.messages_unassigned') }}">mehr &raquo;</a>
    </div>
    @endif

    @if($assignedMessages->count())
    <div class="alert alert-success mb-6">
        <strong>Neue Nachrichten zu folgenden Bestellungen:</strong>
        <br>
        <br>
        <ul>
        @foreach($assignedMessages as $message)
            <li>
                @if ($message->order)
                <a href="{{ route('order.show', $message->order) }}" target="_blank">{{ $message->order->internal_order_number }} bei {{ $message->order->supplier->name }}</a>
                @else
                {{ $message->id }}
                @endif
            </li>
        @endforeach
        </ul>
    </div>
    @endif

    {!! $dataTable->table() !!}

    <data-tables-filter>
        <data-tables-filter-select label="Lieferant" col-id="1" id="filterSupplier">
            <option value=""></option>
            @foreach($supplier as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </data-tables-filter-select>

        <data-tables-filter-select label="Status" col-id="3" id="filterStatus">
            <option value="open">offen (neu, bestellt, teilweise geliefert)</option>
            <option value="{{ \Mss\Models\Order::STATUS_NEW }}">neu</option>
            <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">bestellt</option>
            <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">teilweise geliefert</option>
            <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">geliefert</option>
            <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">storniert</option>
            <option value="{{ \Mss\Models\Order::STATUS_PAID }}">bezahlt</option>
        </data-tables-filter-select>

        <data-tables-filter-select label="Rechnungsstatus" col-id="5" id="filterInvoiceStatus">
            <option value="empty">alle</option>
            <option value="none">offen</option>
            <option value="all">komplett erhalten</option>
            <option value="partial">teilweise erhalten</option>
            <option value="check">in Prüfung</option>
        </data-tables-filter-select>

        <data-tables-filter-select label="AB-Status" col-id="4" id="filterABStatus">
            <option value="empty">alle</option>
            <option value="none">offen</option>
            <option value="all">komplett erhalten</option>
            <option value="partial">teilweise erhalten</option>
        </data-tables-filter-select>
    </data-tables-filter>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush