@extends('article_group.form')

@section('title', __('Artikelgruppe bearbeiten'))

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Artikel')</a>
    </li>
    <li>
        <a href="{{ route('article-group.index') }}">@lang('Artikelgruppen verwalten')</a>
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
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary', 'id' => 'submit']) !!}
    @endcan
@endsection

@section('secondCol')
    <collapse title="@lang('Logbuch')">
        @include('components.audit_list', $audits)
    </collapse>
@endsection