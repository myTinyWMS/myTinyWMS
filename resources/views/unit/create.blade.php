@extends('unit.form')

@section('title', __('Neue Einheit'))

@section('breadcrumb')
    <li>
        <a href="{{ route('unit.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Neue Einheit')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($unit, ['route' => ['unit.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
@endsection