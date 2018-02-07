@extends('order.form')

@section('title', 'Neue Bestellung')

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Neue Bestellung</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($order, ['route' => ['order.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('order.cancel', $order) }}" class="btn btn-danger pull-right">Abbrechen</a>
@endsection