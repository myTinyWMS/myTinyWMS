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
    <div class="col-lg-6">
        <div class="ibox collapsed">
            <div class="ibox-title">
                <h5>Logbuch</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                @include('components.audit_list', $audits)
            </div>
        </div>
    </div>
@endsection