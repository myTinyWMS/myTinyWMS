@extends('supplier.form')

@section('title', 'Neuer Lieferant')

@section('breadcrumb')
    <li>
        <a href="{{ route('supplier.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Neuer Lieferant</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($supplier, ['route' => ['supplier.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
@endsection