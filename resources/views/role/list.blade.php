@extends('layout.app')

@section('title', __('Rollen Übersicht'))


@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li class="active">
        <strong>@lang('Rollen Übersicht')</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('role.create') }}" class="btn btn-secondary">@lang('Neue Rolle')</a>
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush