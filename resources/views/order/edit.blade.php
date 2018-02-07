@extends('order.form')

@section('title', 'Bestellung bearbeiten')

@section('breadcrumb')
    <li>
        <a href="{{ route('order.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Bestellung bearbeiten</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($order, ['route' => ['order.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
@endsection