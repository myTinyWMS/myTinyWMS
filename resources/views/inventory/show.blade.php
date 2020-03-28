@extends('unit.form')

@section('title', __('Inventur bearbeiten - gestartet am ').$inventory->created_at->format('d.m.Y H:i'))

@section('breadcrumb')
    <li>
        <a href="{{ route('inventory.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Inventur bearbeiten')</strong>
    </li>
@endsection

@section('content')
    <inventory-articles :items="{{ json_encode($items) }}" :inventory="{{ json_encode($inventory) }}" :inventory-is-finished="{{ json_encode($inventory->isFinished()) }}" :edit-enabled="{{ Auth()->user()->can('inventory.edit') ? 'true' : 'false' }}"></inventory-articles>
@endsection