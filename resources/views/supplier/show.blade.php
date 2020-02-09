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
    {!! Form::model($supplier, ['route' => ['supplier.update', $supplier], 'method' => 'PUT']) !!}
@endsection

@section('submit')
    {!! Form::submit(__('Speichern'), ['class' => 'btn btn-primary']) !!}
@endsection

@section('secondCol')
    <div class="w-1/3 ml-4">
        <collapse title="@lang('Logbuch')">
            @include('components.audit_list', $audits)
        </collapse>
    </div>
@endsection