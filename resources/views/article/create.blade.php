@extends('article.form')

@section('title', 'Neuer Artikel')

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Neuer Artikel</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($article, ['route' => ['article.store'], 'method' => 'POST']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
@endsection