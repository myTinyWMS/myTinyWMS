@extends('unit.form')

@section('title', 'Inventur bearbeiten - gestartet am '.$inventory->created_at->format('d.m.Y H:i'))

@section('breadcrumb')
    <li>
        <a href="{{ route('inventory.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Inventur bearbeiten</strong>
    </li>
@endsection

@section('content')
    <inventory-articles :items="{{ json_encode($items) }}" :inventory="{{ json_encode($inventory) }}" :inventory-is-finished="{{ json_encode($inventory->isFinished()) }}"></inventory-articles>
@endsection