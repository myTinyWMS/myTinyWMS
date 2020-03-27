@extends('supplier.form')

@section('title', __('Lieferant bearbeiten'))

@section('breadcrumb')
    <li>
        <a href="{{ route('supplier.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Lieferant bearbeiten')</strong>
    </li>
@endsection

@section('form_start')
    @can('supplier.edit')
    {!! Form::model($supplier, ['route' => ['supplier.update', $supplier], 'method' => 'PUT']) !!}
    @endcan
@endsection

@section('submit')
    @can('supplier.edit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
    @endcan
@endsection

@section('secondCol')
    <div class="w-1/3 ml-4">
        <collapse title="@lang('Logbuch')">
            @include('components.audit_list', $audits)
        </collapse>
    </div>
@endsection