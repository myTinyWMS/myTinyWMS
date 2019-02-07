<?php $received = collect($items)->where('confirmation_received', true)->count(); ?>
@if($received == 0)
    <span class="badge badge-plain"> offen </span>
@else
    @if($received < collect($items)->count())
        <span class="badge badge-warning"> {{ $received }} / {{ collect($items)->count() }} </span>
    @else
        <span class="badge badge-success"> erhalten </span>
    @endif
@endif