@extends('article.form')

@section('title', 'Artikel Details')

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Artikel bearbeiten</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($article, ['route' => ['article.update', $article], 'method' => 'PUT']) !!}
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

        <div class="ibox">
            <div class="ibox-title">
                <h5>Bestands-Verlauf</h5>
            </div>
            <div class="ibox-content">

            </div>
        </div>
    </div>
@endsection