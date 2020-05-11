@extends('layout.handscanner')

@section('subheader')
    <div class="subheader">Inventur - Artikel auswählen</div>
@endsection

@section('back', route('handscanner.inventory.select_category', $inventory))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h6 class="text-center mb-4 mt-2">Bitte einen Artikel scannen oder auswählen</h6>

            {{--<qr-reader target-url="{{ route('handscanner.inventory.process', [$inventory, '']) }}/" --}}{{--style="height: 200px; width: 200px;"--}}{{--></qr-reader>--}}

            @if($items->count())
                @foreach($items as $item)
                    <a href="{{ route('handscanner.inventory.process', [$inventory, $category, $item->article->internal_article_number]) }}" class="btn btn-md btn-block btn-primary m-b-lg" style="white-space: normal">
                        <table>
                            <tr>
                                <td class="text-left" width="60">{{ $item->article->internal_article_number }}</td>
                                <td class="text-left">{{ $item->article->name }}</td>
                            </tr>
                        </table>

                    </a>
                @endforeach
            @else
                <div class="jumbotron text-success text-center">Keine Artikel mehr übrig</div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col text-center">
            <form method="post" action="{{ route('handscanner.inventory.category_processed', [$inventory, $category]) }}">
                @csrf
                <br>
                <br>
                <button type="submit" class="btn btn-lg btn-danger">Kategorie abschließen</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        var scanned = '';
        $(document).ready(function () {
            window.addEventListener('keypress', function(event) {
                if (event.keyCode == 13) {
                    window.location.href = '{{ route('handscanner.inventory.process', [$inventory, $category, '']) }}/' + scanned;
                } else {
                    scanned += String.fromCharCode(event.charCode);
                }
            });
        });
    </script>
@endpush