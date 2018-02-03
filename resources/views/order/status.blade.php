@if ($status == \Mss\Models\Order::STATUS_ORDERED)
    <span class="label label-plain">bestellt</span>
@elseif($status == \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED)
    <span class="label label-warning">teilweise geliefert</span>
@elseif($status == \Mss\Models\Order::STATUS_DELIVERED)
    <span class="label label-success">geliefert</span>
@endif