@extends('layout.handscanner')

@section('content')
    <style>
        dt {
            font-weight: lighter;
            font-size: 10px;
        }
        dd {
            font-size: 16px;
            margin-bottom: 10px;
        }
    </style>
    <div class="row">
        <div class="col-xs-12">
            <h2>Schritt 2:</h2>
            <br/>
            <br/>
            <div class="row text-left">
                <div class="col-xs-12">
                    <small>Name:</small>
                    <br>
                    <h3>{{ $article->name }}</h3>
                </div>

                <div class="col-xs-12">
                    <small>Nummer:</small>
                    <br>
                    <h3>{{ $article->article_number }}</h3>
                </div>

                <div class="col-xs-6">
                    <small>aktueller Bestand:</small>
                    <br>
                    <h3>{{ $article->quantity }}</h3>
                </div>
                <div class="col-xs-6">
                    <small>neuer Bestand:</small>
                    <br>
                    <h3 id="changelogCurrentQuantity">{{ $article->quantity }}</h3>
                </div>
            </div>

            <br>
            <br>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="changelogChange" class="control-label">Veränderung</label>
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="changelog-current-math">-</span> <span class="caret"></span></button>
                                <ul class="dropdown-menu pull-left" id="changelogChangeDropdown">
                                    <li><a href="#" class="changelog-set-sub">-</a></li>
                                    <li><a href="#" class="changelog-set-add">+</a></li>
                                </ul>
                            </div>
                            <input type="hidden" name="changelogChangeType" value="">
                            <input class="form-control" type="text" id="changelogChange" value="" name="changelogChange" placeholder="Menge" required>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="changelogType" class="control-label">Typ der Änderung</label>
                        <select id="changelogType" name="changelogType" class="form-control" required>
                            <option value=""></option>
                            <option value="{{ \Mss\Models\ArticleQuantityChangelog::TYPE_INCOMING }}" data-type="add">Wareneingang</option>
                            <option value="{{ \Mss\Models\ArticleQuantityChangelog::TYPE_OUTGOING }}" data-type="sub">Warenausgang</option>
                            <option value="{{ \Mss\Models\ArticleQuantityChangelog::TYPE_CORRECTION }}" data-type="both">Korrektur</option>
                            <option value="{{ \Mss\Models\ArticleQuantityChangelog::TYPE_INVENTORY }}" data-type="both">Inventur</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="changelogNote" class="control-label">Bemerkung</label>
                <textarea class="form-control" rows="3" id="changelogNote" name="changelogNote"></textarea>
            </div>
        </div>
        <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
        <button type="submit" class="btn btn-primary" id="changelogSubmit">Speichern</button>
    </div>
@endsection

@push('scripts')
<script>
    var changelogMath = 'sub';

    $(document).ready(function () {
        $('#changelogChange').val('');
        updateNewChangelogQuantity();
        updateChangelogType();

        $('#changelogChange').keyup(function () {
            updateNewChangelogQuantity();
        });

        $('#changelogChange').change(function () {
            updateNewChangelogQuantity();
        });

        $('.changelog-set-add').click(function () {
            $('.changelog-current-math').text('+');
            changelogMath = 'add';
            updateNewChangelogQuantity();
            updateChangelogType();
            $('#changelogChangeDropdown').dropdown('toggle');
            return false;
        });

        $('.changelog-set-sub').click(function () {
            $('.changelog-current-math').text('-');
            changelogMath = 'sub';
            updateNewChangelogQuantity();
            updateChangelogType();
            $('#changelogChangeDropdown').dropdown('toggle');
            return false;
        });
    });

    function updateChangelogType() {
        $('#changelogType option').show();
        if (changelogMath === 'add') {
            $('#changelogType option[data-type="sub"]').hide();
        } else {
            $('#changelogType option[data-type="add"]').hide();
        }
        $('#changelogType').val(null);

        $('input[name=changelogChangeType]').val(changelogMath);
    }

    function updateNewChangelogQuantity() {
        var currentQuantity = parseInt($('#changelogCurrentQuantity').attr('data-quantity'));
        var change = parseInt($('#changelogChange').val());

        var newQuantity = currentQuantity;
        if (!isNaN(change) && change !== 0) {
            if (changelogMath === 'add') {
                newQuantity += change;
            } else {
                newQuantity -= change;
            }
        }

        $('#changelogNewQuantity').text(newQuantity);
    }
</script>
@endpush