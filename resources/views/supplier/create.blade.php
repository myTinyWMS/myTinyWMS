@extends('supplier.form')

@section('title', __('Neuer Lieferant'))

@section('breadcrumb')
    <li>
        <a href="{{ route('supplier.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Neuer Lieferant')</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($supplier, ['route' => ['supplier.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
@endsection