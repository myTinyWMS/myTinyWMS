@extends('layout.app')

@section('content')
@if ($isNewArticle ?? true)
    @yield('form_start')
@endif
    <div class="row">
        <div class="col-lg-6 col-xxl-4">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Details</h5>

                    @if (!($isNewArticle ?? true))
                    <a href="{{ route('order.create', ['article' => [$article->id]]) }}" class="btn btn-primary btn-xs pull-right">Neue Bestellung</a>
                    @endif
                </div>
                <div class="ibox-content">
                    @if (!($isNewArticle ?? true))
                        @yield('form_start')
                    @endif

                    <div class="row">
                        <div class="col-lg-4">
                            @if (!($isNewArticle ?? true))
                                <div class="form-group">
                                    <label class="control-label">Bestand</label>
                                    <div class="form-control-static">
                                        {{ $article->quantity }} <button type="button" class="btn btn-danger btn-xs edit-quantity m-l-md" data-toggle="modal" data-target="#changeQuantityModal">ändern</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-8 text-right">
                            @if($article->openOrders()->count())
                            <div class="form-group">
                                <label class="control-label">Offene Bestellungen</label>
                                <div class="form-control-static">
                                    @foreach($article->openOrders() as $openOrder)
                                        <a href="{{ route('order.show', $openOrder) }}" target="_blank">{{ $openOrder->internal_order_number }}</a> ({{ $openOrder->items->where('article_id', $article->id)->first()->quantity }}{{ !empty($article->unit) ? ' '.$article->unit->name : '' }})
                                        <br>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if (!($isNewArticle ?? true) && ($article->outsourcing_quantity !== 0 || $article->replacement_delivery_quantity !== 0))
                        <div class="row">
                            @if ($article->outsourcing_quantity !== 0)
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="control-label">Außenlagerbestand</label>
                                        <div class="form-control-static">
                                            {{ $article->outsourcing_quantity }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($article->replacement_delivery_quantity !== 0)
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="control-label">Ersatzlieferung</label>
                                        <div class="form-control-static">
                                            {{ $article->replacement_delivery_quantity }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{ Form::bsTextarea('name', $article->name, ['rows' => 2] , 'Name') }}
                    {{ Form::bsSelect('status', $article->status, \Mss\Models\Article::getStatusTextArray(),  'Status') }}
                    {{ Form::bsSelect('tags', $article->tags->pluck('id'), \Mss\Models\Tag::orderedByName()->pluck('name', 'id'), 'Tags', ['multiple' => 'multiple', 'name' => 'tags[]']) }}

                    <div class="form-group">
                        {!! Form::label('category', 'Kategorie', ['class' => 'control-label']) !!}
                        <a href="{{ route('article.index', ['category' => $article->category]) }}" class="m-l-sm" title="alle Artikel dieser Kategorie anzeigen" target="_blank"><i class="fa fa-filter"></i></a>

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
                            @if (!empty($article->unit_id))
                                <div class="form-group">
                                    <label class="control-label">Einheit</label>
                                    <div class="form-control-static">{{ $article->unit->name }}</div>
                                </div>
                            @else
                            {{ Form::bsSelect('unit_id', $article->unit_id, \Mss\Models\Unit::pluck('name', 'id'),  'Einheit', ['placeholder' => '']) }}
                            @endif
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsText('sort_id', $article->sort_id ?? 0, [], 'Sortierung') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            @if ($isNewArticle ?? true)
                                {{ Form::bsText('quantity', $article->quantity, [], 'Bestand') }}
                            @endif
                        </div>
                        <div class="col-lg-6">

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsText('min_quantity', $article->min_quantity ?? 0, [], 'Mindestbestand') }}
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsText('issue_quantity', $article->issue_quantity ?? 0, [], 'Entnahmemenge') }}
                        </div>
                        <div class="col-lg-12 m-b-md">
                            <span class="help-block m-b-none">Mindestbestand = -1 &raquo; der Artikel erscheint nicht in der zu Bestellen Liste</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsSelect('inventory', $article->inventory, \Mss\Models\Article::getInventoryTextArray(),  'Inventur Typ') }}
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsText('free_lines_in_printed_list', $article->free_lines_in_printed_list ?? 1, [], 'Leere Zeilen in Lagerliste') }}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsText('cost_center', $article->cost_center ?? '', [], 'Kostenstelle') }}
                        </div>
                        <div class="col-lg-6">

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            {{ Form::bsSelect('packaging_category', $article->packaging_category, [null => '', \Mss\Models\Article::PACKAGING_CATEGORY_PAPER => 'Papier, Pappe, Karton', \Mss\Models\Article::PACKAGING_CATEGORY_PLASTIC => 'Kunststoffe'],  'Verpackungs-Kategorie') }}
                        </div>
                        <div class="col-lg-6">
                            {{ Form::bsText('weight', $article->weight ?? '', [], 'Gewicht in Gramm pro Einheit') }}
                        </div>
                    </div>

                    {{ Form::bsTextarea('notes', $article->notes, ['rows' => 4], 'Bemerkungen') }}
                    {{ Form::bsTextarea('order_notes', $article->order_notes, ['rows' => 2], 'Bestell Hinweise') }}

                    <div class="form-group">
                        @yield('submit')
                    </div>
                    @if (!($isNewArticle ?? true))
                        {!! Form::close() !!}
                    @endif

                </div>
            </div>
        </div>
        @yield('secondCol')
    </div>
@if ($isNewArticle ?? true)
    {!! Form::close() !!}
@endif

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
                                    <div class="form-control-static">
                                        <span id="changelogCurrentQuantity" data-quantity="{{ $article->quantity }}">{{ $article->quantity }}</span>
                                        {{ optional($article->unit)->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-lg-offset-2">
                                <div class="form-group">
                                    <label for="changelogNewQuantity" class="control-label">Entnahmemenge</label>
                                    <div class="form-control-static">
                                        {{ $article->issue_quantity }}
                                        {{ optional($article->unit)->name }}
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                        <option value="{{ \Mss\Models\ArticleQuantityChangelog::TYPE_INVENTORY }}" data-type="both">Inventur</option>
                                        <option value="{{ \Mss\Models\ArticleQuantityChangelog::TYPE_REPLACEMENT_DELIVERY }}" data-type="both">Ersatzlieferung</option>
                                        <option value="{{ \Mss\Models\ArticleQuantityChangelog::TYPE_OUTSOURCING }}" data-type="both">Ein-/Auslagerung Aussenlager</option>
                                        <option value="{{ \Mss\Models\ArticleQuantityChangelog::TYPE_SALE_TO_THIRD_PARTIES }}" data-type="sub">Verkauf an Fremdfirmen</option>
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
    var changelogMath = 'sub';

    $(document).ready(function () {
        @if (!($isNewArticle ?? true))
        $('#unit_id').change(function () {
            alert('Achtung. Änderung der Einheit nur in Absprache mit der Buchhaltung bzw. Geschäftsleitung!')
        });
        @endif

        $('#changelogSubmit').click(function () {
            if (changelogMath === 'sub' && parseInt($('#changelogCurrentQuantity').attr('data-quantity')) < parseInt($('#changeQuantityModal #changelogChange').val())) {
                alert('Es ist nicht möglich mehr auszubuchen als Bestand vorhanden ist!');
                return false;
            }
            var message = 'Du willst den Bestand um ';
            message += (changelogMath === 'sub') ? 'MINUS ' : 'PLUS ';
            message += $('#changeQuantityModal #changelogChange').val() + ' ändern - als ';
            message += '"' + $('#changelogType option:selected').text() + '". SICHER?';

            return confirm(message);
        });

        $('.changelog-set-add').click(function () {
            $('.changelog-current-math').text('+');
            changelogMath = 'add';
            updateChangelogType();
            $('#changelogChangeDropdown').dropdown('toggle');
            return false;
        });

        $('.changelog-set-sub').click(function () {
            $('.changelog-current-math').text('-');
            changelogMath = 'sub';
            updateChangelogType();
            $('#changelogChangeDropdown').dropdown('toggle');
            return false;
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

    $('#changeQuantityModal').on('show.bs.modal', function (event) {
        changelogMath = 'sub';
        $('#changeQuantityModal #changelogChange').val('');
        updateChangelogType();
    });

    function updateChangelogType() {
        $('#changelogType option').hide();
        if (changelogMath === 'add') {
            $('#changelogType option[data-type="add"]').show();
            $('#changelogType option[data-type="both"]').show();
        }
        if (changelogMath === 'sub') {
            $('#changelogType option[data-type="sub"]').show();
            $('#changelogType option[data-type="both"]').show();
        }
        $('#changelogType').val(null);

        $('input[name=changelogChangeType]').val(changelogMath);
    }
</script>
@endpush
