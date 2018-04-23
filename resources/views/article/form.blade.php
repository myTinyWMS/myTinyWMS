@extends('layout.app')

@section('content')
    @if (count($errors) > 0)
        <div class="row">
            <div class="col-lg-12">
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6 col-xxl-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Details</h5>
                </div>
                <div class="ibox-content">
                    @yield('form_start')

                    {{ Form::bsTextarea('name', $article->name, ['rows' => 2] , 'Name') }}
                    {{ Form::bsSelect('status', $article->status, \Mss\Models\Article::getStatusTextArray(),  'Status') }}
                    {{ Form::bsSelect('tags', $article->tags->pluck('id'), \Mss\Models\Tag::orderedByName()->pluck('name', 'id'), 'Tags', ['multiple' => 'multiple', 'name' => 'tags[]']) }}

                    <div class="form-group">
                        {!! Form::label('category', 'Kategorie', ['class' => 'control-label']) !!}

                        @if ($isNewArticle ?? true)
                            {!! Form::select('category', \Mss\Models\Category::orderedByName()->pluck('name', 'id'), null, ['class' => 'form-control', 'name' => 'category']) !!}
                        @else
                            {!! Form::select('category', \Mss\Models\Category::orderedByName()->pluck('name', 'id'), $article->category->id ?? null, ['class' => 'form-control', 'name' => 'category', 'disabled' => 'disabled']) !!}
                            <div class="checkbox checkbox-danger">
                                <input type="checkbox" id="enableChangeCategory" name="changeCategory" value="1" />
                                <label for="enableChangeCategory">
                                    Kategorie ändern
                                </label>
                            </div>
                            <span class="help-block m-b-none text-danger hidden" id="changeCategoryWarning">Beim Ändern der Kategorie wird eine neue Artikel Nummer vergeben!</span>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsSelect('unit_id', $article->unit_id, \Mss\Models\Unit::pluck('name', 'id'),  'Einheit', ['placeholder' => '']) }}
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsText('sort_id', $article->sort_id, [], 'Sortierung') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            @if ($isNewArticle ?? true)
                                {{ Form::bsText('quantity', $article->quantity, [], 'Bestand') }}
                            @else
                                <div class="form-group">
                                    <label class="control-label">Bestand</label>
                                    <div class="form-control-static">
                                        {{ $article->quantity }} <button type="button" class="btn btn-link edit-quantity" data-toggle="modal" data-target="#changeQuantityModal"><i class="fa fa-edit"></i></button>
                                        <a href="{{ route('order.create', ['article' => [$article->id]]) }}" class="btn btn-primary btn-xs pull-right">Neue Bestellung</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-6">

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsText('min_quantity', $article->min_quantity, [], 'Mindestbestand') }}
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsText('issue_quantity', $article->issue_quantity, [], 'Entnahmemenge') }}
                        </div>
                    </div>

                    {{ Form::bsCheckbox('inventory', $article->inventory, 'Inventur', $article->inventory, []) }}
                    {{ Form::bsTextarea('notes', $article->notes, [], 'Bemerkungen') }}


                    <div class="form-group">
                        @yield('submit')
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        @yield('secondCol')
    </div>

    <!-- Modal -->
    <div class="modal fade" id="changeQuantityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['route' => ['article.change_quantity', $article], 'method' => 'POST']) !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Bestand ändern</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="changelogCurrentQuantity" class="control-label">aktueller Bestand</label>
                                    <div class="form-control-static" id="changelogCurrentQuantity" data-quantity="{{ $article->quantity }}">{{ $article->quantity }}</div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="changelogNewQuantity" class="control-label">neuer Bestand</label>
                                    <div class="form-control-static" id="changelogNewQuantity"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="changelogChange" class="control-label">Veränderung</label>
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="changelog-current-math">+</span> <span class="caret"></span></button>
                                            <ul class="dropdown-menu pull-left" id="changelogChangeDropdown">
                                                <li><a href="#" class="changelog-set-add">+</a></li>
                                                <li><a href="#" class="changelog-set-sub">-</a></li>
                                            </ul>
                                        </div>
                                        <input type="hidden" name="changelogChangeType" value="">
                                        <input class="form-control" type="text" id="changelogChange" value="" name="changelogChange" placeholder="Menge">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="changelogType" class="control-label">Typ der Änderung</label>
                                    <select id="changelogType" name="changelogType" class="form-control">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary" id="changelogSubmit">Speichern</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    var changelogMath = 'add';

    $(document).ready(function () {
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

        $('#changeQuantityModal').on('show.bs.modal', function (e) {
            $('#changelogChange').val('');
            updateNewChangelogQuantity();
            updateChangelogType();
        });

        $('#enableChangeCategory').click(function () {
            if($(this).is(':checked')) {
                $('#changeCategoryWarning').removeClass('hidden');
                $("#category").prop('disabled', false);
            } else {
                $('#changeCategoryWarning').addClass('hidden');
                $("#category").prop('disabled', true);
            }
        });

        // changeCategoryWarning
        $("#tags").select2({
            tags: true,
            tokenSeparators: [',', ' '],
            theme: "bootstrap",
            createTag: function (params) {
                var term = $.trim(params.term);

                if (term === '') {
                    return null;
                }

                return {
                    id: 'newTag_'+term,
                    text: term
                }
            }
        });

        $("#category").select2({
            theme: "bootstrap"
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
