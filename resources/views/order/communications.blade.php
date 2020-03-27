@if($messages->count() == 0 && $order->supplier->email && $order->status == \Mss\Models\Order::STATUS_NEW)
    <a href="{{ route('order.message_create', ['order' => $order, 'sendorder' => 1]) }}" class="btn btn-lg btn-success">@lang('Bestellung per E-Mail an Lieferant schicken')</a>
@endif

<order-messages :messages="{{ $messages }}" :order="{{ $order }}" :edit-enabled="{{ Auth()->user()->hasPermissionTo('ordermessage.edit') ? 'true' : 'false' }}"></order-messages>

<assign-order-message-modal>{!! $dataTable->table() !!}</assign-order-message-modal>

<data-tables-filter>
    <data-tables-filter-select label="@lang('Lieferant')" col-id="1" id="filterSupplier">
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

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush