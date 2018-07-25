@extends('layout.handscanner')

@section('subheader')
    <div class="subheader">Inventur Schritt 1</div>
@endsection

@section('back', route('handscanner.index'))

@section('content')
    <div class="jumbotron text-center">Bitte einen Artikel scannen</div>
    <div id="output" style="border: 1px solid red"></div>

    <qr-reader target-url="{{ route('handscanner.inventory.step2', ['articleNumber' => '']) }}/" style="height: 200px; width: 200px;"></qr-reader>

@endsection