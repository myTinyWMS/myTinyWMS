@if ($status == \Mss\Models\Order::STATUS_NEW)
    <span class="label label-plain">neu</span>
@elseif ($status == \Mss\Models\Order::STATUS_ORDERED)
    <span class="label label-info">bestellt</span>
@elseif($status == \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED)
    <span class="label label-warning">teilweise geliefert</span>
@elseif($status == \Mss\Models\Order::STATUS_DELIVERED)
    <span class="label label-success">geliefert</span>
@elseif($status == \Mss\Models\Order::STATUS_CANCELLED)
    <span class="label label-default">storniert</span>
@elseif($status == \Mss\Models\Order::STATUS_PAID)
    <span class="label label-success">bezahlt</span>
@endif