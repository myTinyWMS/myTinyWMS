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
                    {!! Form::open(['route' => ['category.print_list'], 'method' => 'POST']) !!}
                    {!! $dataTable->table() !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <div class="toolbar_content hidden">
        <button class="btn btn-xs btn-primary" type="submit">Lagerliste drucken</button>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        $(document).ready(function () {
            $('.toolbar').html($('.toolbar_content').html());
        });
    </script>
@endpush