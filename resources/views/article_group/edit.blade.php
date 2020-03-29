@extends('article_group.form')

@section('title', __('Artikelgruppe bearbeiten'))

@section('breadcrumb')
    <li>
        <a href="{{ route('article-group.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Artikelgruppe bearbeiten')</strong>
    </li>
@endsection

@section('form_start')
    @can('article-group.edit')
    {!! Form::model($articleGroup, ['route' => ['article-group.update', $articleGroup], 'method' => 'PUT']) !!}
    @endcan
@endsection

@section('submit')
    @can('supplier-group.edit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
    @endcan
@endsection

@section('secondCol')
    <div class="w-1/2">
        <collapse title="@lang('Logbuch')">
            @include('components.audit_list', $audits)
        </collapse>
    </div>
@endsection