@extends('user.form')

@section('title', __('Neuer Benutzer'))

@section('breadcrumb')
    <li>
        <a href="{{ route('user.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Neuer Benutzer')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($user, ['route' => ['user.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
@endsection