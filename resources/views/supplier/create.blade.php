@extends('provider.form')

@section('title', __('provider.create.headline_create'))

@section('breadcrumb')
    <li>
        <a href="{{ route('provider.index') }}">@lang('provider.headline_list')</a>
    </li>
    <li class="active">
        <strong>@lang('provider.create.headline_create')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($provider, ['route' => ['provider.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('buttons.create'), ['class' => 'btn btn-primary']) !!}
@endsection