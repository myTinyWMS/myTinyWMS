@extends('layout.app')

@section('title', __('Artikelgruppen'))

@section('breadcrumb')
    <li class="active">
        <strong>@lang('Übersicht')</strong>
    </li>
@endsection

@section('content')

    <div class="table-toolbar-right-content hidden">
        @can('article-group.create')
            <a href="{{ route('article-group.create') }}" class="btn btn-secondary">@lang('Neue Artikelgruppe')</a>
        @endcan
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush