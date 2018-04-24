<?php $received = collect($items)->where('confirmation_received', true)->count(); ?>
@if($received == 0)
    <span class="label"> offen </span>
@else
    @if($received < collect($items)->count())
        <span class="label label-warning"> {{ $received }} / {{ collect($items)->count() }} </span>
    @else
        <span class="label label-success"> erhalten </span>
    @endif
@endif