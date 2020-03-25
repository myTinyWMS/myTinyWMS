@extends('category.form')

@section('title', __('Neue Kategorie'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('category.index') }}">@lang('Kategorien Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Neue Kategorie')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($category, ['route' => ['category.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
@endsection