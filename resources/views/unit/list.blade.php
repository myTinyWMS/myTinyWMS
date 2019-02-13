@extends('layout.app')

@section('title', 'Einheiten')

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('unit.create') }}" class="btn btn-xs btn-primary">Neue Einheit</a>
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush