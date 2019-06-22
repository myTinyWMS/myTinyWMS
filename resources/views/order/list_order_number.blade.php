{{ link_to_route('order.show', $order->internal_order_number, ['order' => $order], ['target' => '_blank']) }}
@if($order->messages->where('read', false)->count())
<div class="inline-count-info pull-right">
    <a class="count-info relative" href="{{ route('order.show', $order) }}" title="{{ $order->messages->where('read', false)->count() }} ungelesen {{ trans_choice('plural.message', $order->messages->where('read', false)->count()) }}">
        <i class="fa fa-envelope"></i>  <span class="inline-block text-white rounded-full w-5 h-5 absolute bg-blue-700 notification-label">{{ $order->messages->where('read', false)->count() }}</span>
    </a>
</div>
@endif
