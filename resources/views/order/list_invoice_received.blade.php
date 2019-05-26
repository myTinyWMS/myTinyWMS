<?php $received = collect($items)->where('invoice_received', \Mss\Models\OrderItem::INVOICE_STATUS_RECEIVED)->count(); ?>
<?php $check = collect($items)->where('invoice_received', \Mss\Models\OrderItem::INVOICE_STATUS_CHECK)->count(); ?>
@if($received == 0)
    <span class="text-gray-600 font-semibold">offen</span>
@else
    @if($received < collect($items)->count())
        <span class="w-3 h-3 inline-block align-middle rounded-full bg-orange-500 mr-1"></span>
        <span class="text-orange-500 font-semibold align-middle">{{ $received }} / {{ collect($items)->count() }}</span>
    @else
        <span class="w-3 h-3 inline-block align-middle rounded-full bg-green-500 mr-1"></span>
        <span class="text-green-600 font-semibold align-top">erhalten</span>
    @endif
@endif
@if($check > 0)
    <small class="block text-danger font-semibold mt-2">in Prüfung</small>
@endif