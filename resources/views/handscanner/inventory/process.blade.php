@extends('layout.handscanner')

@section('subheader')
    Inventur - Eingabe
@endsection

@section('back', route('handscanner.inventory.select_article', [$inventory, $article->category]))

@section('content')
    @if (!$article->category->is($category))
        <div class="alert alert-secondary">Achtung, Artikel ist aus anderer Kategorie!</div>
    @endif

    @if(!is_null($item->processed_at))
        <div class="alert alert-warning">Achtung, Artikel wurde bereits bearbeitet!</div>
    @endif
    <div class="row">
        <div class="col">
            <form method="post" action="{{ route('handscanner.inventory.processed', [$inventory, $article]) }}" id="saveinventory">
                @csrf
                <div class="row text-left">
                    <div class="col">
                        <div class="label">Name:</div>
                        <h6>{{ $article->name }}</h6>
                    </div>
                </div>

                <div class="row text-left">
                    <div class="col">
                        <div class="label">Nummer:</div>
                        <h5>{{ $article->article_number }}</h5>
                    </div>

                    <div class="col">
                        <div class="label">aktueller Bestand:</div>
                        <h5>{{ $article->quantity }}</h5>
                    </div>
                </div>

                <div class="row text-left">
                    @if ($article->outsourcing_quantity !== 0)
                        <div class="col">
                            <div class="label text-danger">Außenlagerbestand:</div>
                            <h5 class="text-danger">{{ $article->outsourcing_quantity }}</h5>
                        </div>
                    @endif

                    @if ($article->replacement_delivery_quantity !== 0)
                        <div class="col">
                            <div class="label text-danger">Ersatzlieferung:</div>
                            <h5 class="text-danger">{{ $article->replacement_delivery_quantity }}</h5>
                        </div>
                    @endif
                </div>

                <div class="row text-left">
                    <div class="col">
                        <div class="form-group">
                            <label for="quantity">neuer Bestand:</label>
                            <input type="number" min="0" inputmode="numeric" pattern="[0-9]*" name="quantity" id="quantity" required class="form-control form-control-lg">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-lg btn-success" id="changelogSubmit">Speichern</button>
                <a href="{{ route('handscanner.inventory.select_article', [$inventory, $article->category]) }}" class="btn btn-lg btn-secondary pull-right">Abbrechen</a>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col text-center">
            <form method="post" action="{{ route('handscanner.inventory.processed', [$inventory, $article]) }}">
                @csrf
                <br>
                <br>
                <input type="hidden" name="quantity" value="{{ $article->quantity }}">
                <button type="submit" class="btn btn-lg btn-primary">Bestand stimmt</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#saveinventory').submit(function () {
                var check = window.confirm('Bestand auf ' + $('#quantity').val() + ' ändern?');
                console.log(check);
                return check;
            });
        });
    </script>
@endpush