@extends('unit.form')

@section('title', 'Neue Einheit')

@section('breadcrumb')
    <li>
        <a href="{{ route('unit.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Neuer Einheit</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($unit, ['route' => ['unit.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
@endsection