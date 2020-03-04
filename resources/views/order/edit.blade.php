@extends('order.form')

@section('title', __('Bestellung bearbeiten'))

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">@lang('Übersicht')</a>
    </li>
    <li>
        <a href="{{ route('order.show', $order) }}">@lang('Bestellung') #{{ $order->internal_order_number }}</a>
    </li>
    <li class="active">
        <strong>@lang('Bestellung bearbeiten')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($order, ['route' => ['order.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
@endsection