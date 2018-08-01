@extends('layout.app')

@section('title', 'Inventur')

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Übersicht - offene Inventuren</h5>

                    <div class="pull-right">
                        <a href="{{ route('inventory.create') }}" class="btn btn-xs btn-primary">Neue Inventur starten</a>
                    </div>
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