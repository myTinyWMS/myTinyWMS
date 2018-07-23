@extends('layout.handscanner')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h4 class="text-center">Schritt 2:</h4>
            <br/>
            <br/>
            <div class="row text-left">
                <div class="col-lg-12">
                    <div class="label">Name:</div>
                    <h5>{{ $article->name }}</h5>
                </div>

                <div class="col-lg-12">
                    <div class="label">Nummer:</div>
                    <h5>{{ $article->article_number }}</h5>
                </div>

                <div class="col-lg-6">
                    <div class="label">aktueller Bestand:</div>
                    <h5>{{ $article->quantity }}</h5>
                </div>

                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="quantity">neuer Bestand:</label>
                        <input type="number" name="quantity" id="quantity" class="form-control form-control-lg">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="changelogNote" class="control-label">Bemerkung</label>
                <textarea class="form-control" rows="3" id="changelogNote" name="changelogNote"></textarea>
            </div>

            <button type="button" class="btn btn-lg btn-secondary pull-right" data-dismiss="modal">Abbrechen</button>
            <button type="submit" class="btn btn-lg btn-success" id="changelogSubmit">Speichern</button>
        </div>

    </div>
@endsection