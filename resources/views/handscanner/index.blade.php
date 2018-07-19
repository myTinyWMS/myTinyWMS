@extends('layout.handscanner')

@section('content')

    <h2 class=" m-b-lg">Modus auswählen</h2>
    {{--<a href="" class="btn btn-lg btn-block btn-default m-b-lg">Warenausgang</a>--}}
    <a href="{{ route('handscanner.inventory.step1') }}" class="btn btn-lg btn-block btn-default m-b-lg">Inventur</a>
    {{--<a href="#" class="btn btn-lg btn-block btn-default m-b-lg">Check</a>--}}



@endsection