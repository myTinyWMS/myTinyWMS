@extends('role.form')

@section('title', __('Rolle bearbeiten'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('role.index') }}">@lang('Rollen Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Rolle bearbeiten')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($role, ['route' => ['role.update', $role], 'method' => 'PUT']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
@endsection