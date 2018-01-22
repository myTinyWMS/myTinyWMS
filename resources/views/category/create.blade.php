@extends('category.form')

@section('title', 'Neuer Kategorie')

@section('breadcrumb')
    <li>
        <a href="{{ route('category.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Neue Kategorie</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($category, ['route' => ['category.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
@endsection