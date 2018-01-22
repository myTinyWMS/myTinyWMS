@extends('layout.app')

@section('title', 'Kategorien')

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('subnav')
    <a href="{{ route('category.create') }}" class="btn btn-xs btn-primary">Neue Kategorie</a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Übersicht</h5>
                </div>
                <div class="ibox-content">
                    {!! $dataTable->table() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush