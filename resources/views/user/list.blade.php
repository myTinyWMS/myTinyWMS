@extends('layout.app')

@section('title', __('Benutzer'))

@section('breadcrumb')
    <li class="active">
        <strong>@lang('Übersicht')</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('user.create') }}" class="btn btn-primary">@lang('Neuer Benutzer')</a>
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush