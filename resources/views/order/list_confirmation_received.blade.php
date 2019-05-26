<?php $received = collect($items)->where('confirmation_received', true)->count(); ?>
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