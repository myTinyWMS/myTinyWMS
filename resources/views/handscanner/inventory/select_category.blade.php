@extends('layout.handscanner')

@section('subheader')
    <div class="subheader">Inventur - Kategorie auswählen</div>
@endsection

@section('back', route('handscanner.inventory.start'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h5 class="text-center mb-4 mt-2">Bitte auswählen</h5>

            @foreach($categories as $category)
                <a href="{{ route('handscanner.inventory.select_article', [$inventory, $category]) }}" class="btn btn-md btn-block btn-primary m-b-lg">{{ $category->name }}</a>
            @endforeach
        </div>
    </div>
@endsection