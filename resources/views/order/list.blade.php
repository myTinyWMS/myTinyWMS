@extends('layout.app')

@section('title', __('Bestellungen'))

@section('title_extra')
    @can('order.manage')
    <a href="{{ route('order.create') }}" class="btn btn-secondary">@lang('Neue Bestellung')</a>
    @endcan
@endsection

@section('breadcrumb')
    <li class="active">
        <strong>@lang('Übersicht')</strong>
    </li>
@endsection

@section('content')

    @can('ordermessage.manage')
        @if($unassignedMessages)
        <div class="alert alert-warning mb-6">
            <strong>{{ $unassignedMessages }}</strong> @lang('nicht zugeordnete neue') {{ trans_choice('plural.message', $unassignedMessages) }} - <a class="alert-link" href="{{ route('order.messages_unassigned') }}">@lang('mehr') &raquo;</a>
        </div>
        @endif

        @if($assignedMessages->count())
        <div class="alert alert-success mb-6">
            <strong>@lang('Neue Nachrichten zu folgenden Bestellungen'):</strong>
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
    @endcan

    {!! $dataTable->table() !!}

    <data-tables-filter>
        <data-tables-filter-select label="@lang('Lieferant')" col-id="1" id="filterSupplier">
            <option value=""></option>
            @foreach($supplier as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </data-tables-filter-select>

        <data-tables-filter-select label="@lang('Status')" col-id="3" id="filterStatus">
            <option value="open">@lang('offen (neu, bestellt, teilweise geliefert)')</option>
            <option value="{{ \Mss\Models\Order::STATUS_NEW }}">@lang('neu')</option>
            <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">@lang('bestellt')</option>
            <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">@lang('teilweise geliefert')</option>
            <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">@lang('geliefert')</option>
            <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">@lang('storniert')</option>
            <option value="{{ \Mss\Models\Order::STATUS_PAID }}">@lang('bezahlt')</option>
        </data-tables-filter-select>

        <data-tables-filter-select label="@lang('Rechnungsstatus')" col-id="5" id="filterInvoiceStatus">
            <option value="empty">@lang('alle')</option>
            <option value="none">@lang('offen')</option>
            <option value="all">@lang('komplett erhalten')</option>
            <option value="partial">@lang('teilweise erhalten')</option>
            <option value="check">@lang('in Prüfung')</option>
        </data-tables-filter-select>

        <data-tables-filter-select label="@lang('AB-Status')" col-id="4" id="filterABStatus">
            <option value="empty">@lang('alle')</option>
            <option value="none">@lang('offen')</option>
            <option value="all">@lang('komplett erhalten')</option>
            <option value="partial">@lang('teilweise erhalten')</option>
        </data-tables-filter-select>
    </data-tables-filter>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush