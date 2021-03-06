@extends('layout.app')

@section('title', __('Lieferanten'))

@section('breadcrumb')
    <li class="active">
        <strong>@lang('Übersicht')</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        @can('supplier.create')
        <a href="{{ route('supplier.create') }}" class="btn btn-secondary">@lang('Neuer Lieferant')</a>
        @endcan
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush