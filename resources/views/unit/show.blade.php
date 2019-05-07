@extends('unit.form')

@section('title', 'Einheit bearbeiten')

@section('breadcrumb')
    <li>
        <a href="{{ route('unit.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Einheit bearbeiten</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($unit, ['route' => ['unit.update', $unit], 'method' => 'PUT']) !!}
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