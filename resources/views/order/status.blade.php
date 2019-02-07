@if ($status == \Mss\Models\Order::STATUS_NEW)
    <span class="badge badge-plain">neu</span>
@elseif ($status == \Mss\Models\Order::STATUS_ORDERED)
    <span class="badge badge-info">bestellt</span>
@elseif($status == \Mss\Models\Order::STATUS_PARTIALLY_DELIVERED)
    <span class="badge badge-warning">teilweise geliefert</span>
@elseif($status == \Mss\Models\Order::STATUS_DELIVERED)
    <span class="badge badge-success">geliefert</span>
@elseif($status == \Mss\Models\Order::STATUS_CANCELLED)
    <span class="badge badge-default">storniert</span>
@elseif($status == \Mss\Models\Order::STATUS_PAID)
    <span class="badge badge-success">bezahlt</span>
@endif