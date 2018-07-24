@extends('layout.handscanner')

@section('subheader')
    <div class="subheader">Inventur Schritt 1</div>
@endsection

@section('back', route('handscanner.index'))

@section('content')
    <div class="jumbotron text-center">Bitte einen Artikel scannen</div>

    <div id="scanner-component">
        <component v-bind:is="currentScannerComponent"></component>
    </div>

@endsection

@push('scripts')
    <script>

    </script>
@endpush