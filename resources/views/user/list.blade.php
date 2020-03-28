@extends('layout.app')

@section('title', __('Benutzer Übersicht'))


@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li class="active">
        <strong>@lang('Benutzer Übersicht')</strong>
    </li>
@endsection

@section('content')
    <div class="table-toolbar-right-content hidden">
        <a href="{{ route('role.index') }}" class="btn btn-secondary mr-4">@lang('Rollen verwalten')</a>
        <a href="{{ route('user.create') }}" class="btn btn-secondary">@lang('Neuer Benutzer')</a>
    </div>

    {!! $dataTable->table() !!}
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush