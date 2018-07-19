@extends('layout.handscanner')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <h5 class="text-center mb-4 mt-2">Modus auswählen</h5>
            {{--<a href="" class="btn btn-lg btn-block btn-default m-b-lg">Warenausgang</a>--}}
            <a href="{{ route('handscanner.inventory.step1') }}" class="btn btn-lg btn-block btn-primary m-b-lg">Inventur</a>
            {{--<a href="#" class="btn btn-lg btn-block btn-default m-b-lg">Check</a>--}}
        </div>
    </div>

@endsection