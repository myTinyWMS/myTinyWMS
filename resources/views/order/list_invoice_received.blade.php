<?php $received = collect($items)->where('invoice_received', \Mss\Models\OrderItem::INVOICE_STATUS_RECEIVED)->count(); ?>
<?php $check = collect($items)->where('invoice_received', \Mss\Models\OrderItem::INVOICE_STATUS_CHECK)->count(); ?>
@if($received == 0)
    <span class="badge badge-plain">offen</span>
@else
    @if($received < collect($items)->count())
        <span class="badge badge-warning">{{ $received }} / {{ collect($items)->count() }}</span>
    @else
        <span class="badge badge-success">erhalten</span>
    @endif
@endif
@if($check > 0)
    <small class="block text-danger font-bold mt-2">in Prüfung</small>
@endif