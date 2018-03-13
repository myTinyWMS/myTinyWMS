{{ link_to_route('order.show', $order->internal_order_number, ['order' => $order]) }}
@if($order->messages->where('read', false)->count())
<div class="inline-count-info pull-right">
    <a class="count-info" href="{{ route('order.show', $order) }}" title="{{ $order->messages->where('read', false)->count() }} ungelesen Nachrichten">
        <i class="fa fa-envelope"></i>  <span class="label label-primary">{{ $order->messages->where('read', false)->count() }}</span>
    </a>
</div>
@endif
