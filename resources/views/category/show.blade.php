@extends('category.form')

@section('title', __('Kategorie bearbeiten'))

@section('breadcrumb')
    <li>
        <a href="{{ route('admin.index') }}">@lang('Administrator')</a>
    </li>
    <li>
        <a href="{{ route('category.index') }}">@lang('Kategorien Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Kategorie bearbeiten')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($category, ['route' => ['category.update', $category], 'method' => 'PUT']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
@endsection

@section('secondCol')
    <div class="w-1/3 ml-4">
        <collapse title="@lang('Logbuch')">
            @include('components.audit_list', $audits)
        </collapse>
    </div>
@endsection