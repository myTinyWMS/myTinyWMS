@extends('category.form')

@section('title', 'Kategorie bearbeiten')

@section('breadcrumb')
    <li>
        <a href="{{ route('category.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Kategorie bearbeiten</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($category, ['route' => ['category.update', $category], 'method' => 'PUT']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
@endsection

@section('secondCol')
    <div class="w-1/3 ml-4">
        <collapse title="Logbuch">
            @include('components.audit_list', $audits)
        </collapse>
    </div>
@endsection