@extends('role.form')

@section('title', __('Neue Rolle'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('role.index') }}">@lang('Rollen Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Neue Rolle')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($role, ['route' => ['role.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
@endsection