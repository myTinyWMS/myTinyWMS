@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>zu Bestellen - Artikel die den Mindestbestand erreicht oder unterschritten haben</h5>
                </div>
                <div class="ibox-content">
                    {!! Form::open(['route' => ['category.print_list'], 'method' => 'POST']) !!}
                    {!! $dataTable->table() !!}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toolbar_content hidden">
    <button class="btn btn-xs btn-primary" type="submit">Bestellung erstellen</button>
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