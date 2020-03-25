@extends('user.form')

@section('title', __('Benutzer bearbeiten'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('user.index') }}">@lang('Benutzer Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Benutzer bearbeiten')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($user, ['route' => ['user.update', $user], 'method' => 'PUT']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
@endsection