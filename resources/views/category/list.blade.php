@extends('layout.app')

@section('title', 'Kategorien')

@section('breadcrumb')
    <li class="active">
        <strong>Übersicht</strong>
    </li>
@endsection

@section('content')
    <div class="toolbar-top-right-content hidden">
        <a href="{{ route('category.create') }}" class="btn btn-primary">Neue Kategorie</a>
    </div>

    {!! Form::open(['route' => ['category.print_list'], 'method' => 'POST']) !!}
    {!! $dataTable->table() !!}
    {!! Form::close() !!}

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