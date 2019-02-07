@extends('layout.app')

@section('title', 'Lieferanten')

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('content')
    <div class="toolbar-top-right-content hidden">
        <a href="{{ route('supplier.create') }}" class="btn btn-primary">Neuer Lieferant</a>
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush