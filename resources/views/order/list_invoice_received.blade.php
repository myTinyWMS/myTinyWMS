<?php $received = collect($items)->where('invoice_received', \Mss\Models\OrderItem::INVOICE_STATUS_RECEIVED)->count(); ?>
<?php $check = collect($items)->where('invoice_received', \Mss\Models\OrderItem::INVOICE_STATUS_CHECK)->count(); ?>
@if($received == 0)
    <span class="label">offen</span>
@else
    @if($received < collect($items)->count())
        <span class="label label-warning">{{ $received }} / {{ collect($items)->count() }}</span>
    @else
        <span class="label label-success">erhalten</span>
    @endif
@endif
@if($check > 0)
    <br/>
    <small class="block m-t-sm text-danger font-bold">in Prüfung</small>
@endif