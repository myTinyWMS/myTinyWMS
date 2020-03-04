@extends('order.form')

@section('title', __('Neue Bestellung'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Neue Bestellung')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($order, ['route' => ['order.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary', 'id' => 'save-order']) !!}
    <a href="{{ route('order.cancel', $order) }}" class="btn btn-danger pull-right">@lang('Abbrechen')</a>
@endsection