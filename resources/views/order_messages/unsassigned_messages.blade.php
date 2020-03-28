@extends('layout.app')

@section('title', __('Neue Nachrichten'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Bestellungen')</a>
    </li>
    <li class="active">
        <strong>@lang('Neue Nachrichten')</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="card w-full">
            <div class="card-content">
                <order-messages :messages="{{ $unassignedMessages }}" :edit-enabled="{{ Auth()->user()->can('ordermessage.edit') ? 'true' : 'false' }}"></order-messages>
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
            <option value="open">@lang('offen (neu, bestellt, teilweise geliefert)')</option>
            <option value="{{ \Mss\Models\Order::STATUS_NEW }}">@lang('neu')</option>
            <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">@lang('bestellt')</option>
            <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">@lang('teilweise geliefert')</option>
            <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">@lang('geliefert')</option>
            <option value="{{ \Mss\Models\Order::STATUS_PAID }}">@lang('bezahlt')</option>
            <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">@lang('storniert')</option>
        </data-tables-filter-select>
    </data-tables-filter>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush