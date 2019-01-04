@extends('layout.handscanner')

@section('subheader')
    <div class="subheader">Warenausgang - Bestand ändern</div>
@endsection

@section('back', route('handscanner.index'))

@section('content')
    <div class="row">
        <div class="col">
            <form method="post" action="{{ route('handscanner.outgoing.save', [$article]) }}" id="saveoutgoing">
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
                    <div class="col">
                        <div class="label">Einheit:</div>
                        <h5>{{ optional($article->unit)->name }}</h5>
                    </div>
                </div>

                <div class="row text-left">
                    <div class="col">
                        <div class="form-group">
                            <label for="quantity">Wieviele Artikel sollen ausgebucht werden:</label>
                            <input type="number" min="0" inputmode="numeric" pattern="[0-9]*" name="quantity" id="quantity" required class="form-control form-control-lg">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-lg btn-success" id="changelogSubmit">Speichern</button>
                <a href="{{ route('handscanner.outgoing.start') }}" class="btn btn-lg btn-secondary pull-right">Abbrechen</a>
            </form>
        </div>
    </div>
@endsection