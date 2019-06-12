@if($messages->count() == 0 && $order->supplier->email && $order->status == \Mss\Models\Order::STATUS_NEW)
    <a href="{{ route('order.message_create', ['order' => $order, 'sendorder' => 1]) }}" class="btn btn-lg btn-success">Bestellung per E-Mail an Lieferant schicken</a>
@endif

<order-messages :messages="{{ $messages }}" :order="{{ $order }}"></order-messages>

<assign-order-message-modal>{!! $dataTable->table() !!}</assign-order-message-modal>

<data-tables-filter>
    <data-tables-filter-select label="Status" col-id="2">
        <option value="open">offen (neu, bestellt, teilweise geliefert)</option>
        <option value="{{ \Mss\Models\Order::STATUS_NEW }}">neu</option>
        <option value="{{ \Mss\Models\Order::STATUS_ORDERED }}">bestellt</option>
        <option value="{{ \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED }}">teilweise geliefert</option>
        <option value="{{ \Mss\Models\Order::STATUS_DELIVERED }}">geliefert</option>
        <option value="{{ \Mss\Models\Order::STATUS_PAID }}">bezahlt</option>
        <option value="{{ \Mss\Models\Order::STATUS_CANCELLED }}">storniert</option>
    </data-tables-filter-select>
</data-tables-filter>

@push('scripts')
    {!! $dataTable->scripts() !!}

    <script>
        $(document).ready(function () {
            $('#assignMessageModal').on('shown.bs.modal', function (e) {
                $('#message').val($(e.relatedTarget).attr('data-message-id'));
            })
        });
    </script>
@endpush