@extends('layout.app')

@section('title', 'Einheiten')

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Übersicht</h5>

                    <div class="pull-right">
                        <a href="{{ route('unit.create') }}" class="btn btn-xs btn-primary">Neue Einheit</a>
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