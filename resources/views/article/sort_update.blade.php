@extends('layout.app')

@section('title', 'Artikel Sortierung')

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Artikel Sortierung</strong>
    </li>
@endsection

@section('content')
    <form method="post" action="{{ route('article.mass_update_save') }}">
        <div class="row">
            <div class="col-lg-12">
                <div class="tabs-container">
                    <div class="tabs-left">
                        <ul class="nav nav-tabs" role="tablist">
                            @foreach($articles->keys() as $category)
                                <li class="@if($loop->first) active @endif">
                                    <a class="nav-link" data-toggle="tab" href="#{{ str_replace(' ', '_', $category) }}">{{ $category }}</a>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            @foreach($articles as $category => $items)
                                <div role="tabpanel" class="tab-pane @if($loop->first) active @endif" id="{{ str_replace(' ', '_', $category) }}">
                                    <div class="panel-body">
                                        <table class="table table-striped table-hover table-condensed">
                                            <thead>
                                                <tr>
                                                    <th width="5%">Sortierung</th>
                                                    <th>Artikel</th>
                                                    <th width="15%">Lieferant</th>
                                                </tr>
                                            </thead>
                                            <tbody class="js-sortable-table" aria-dropeffect="move">
                                                @foreach($items as $article)
                                                    <tr class="js-sortable-tr" draggable="true" role="option" aria-grabbed="false">
                                                        <td class="sort_id">
                                                            <span class="sort_id_output">{{ $article->sort_id }}</span>
                                                            <input type="hidden" data-id="{{  $article->id }}" name="sort_id[{{ $article->id }}]" value="{{ $article->sort_id }}" />
                                                        </td>
                                                        <td><a href="{{ route('article.show', $article) }}">{{ $article->name }}</a></td>
                                                        <td>{{ $article->supplier_name }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-primary save-sort m-r-md">Speichern</button>
                                        <span class="text-success hidden">Sortierung gespeichert</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        sortable('.js-sortable-table', {
            items: "tr.js-sortable-tr",
            placeholder: "<tr><td colspan=\"3\"><span class=\"center\">Artikel hier einsortieren</span></td></tr>",
            forcePlaceholderSize: false
        });

        $.each(sortable('.js-sortable-table'), function (index, table) {
            table.addEventListener('sortupdate', function(e) {
                setNewOrderIds($(e.detail.item).parent().find('tr'));
            });
        });

        function setNewOrderIds(items) {
            items.each(function (index, item) {
                var sort = index +1;
                $(item).find('.sort_id_output').text(sort);
                $(item).find('.sort_id input').val(sort);
            });
        }

        $(document).ready(function () {
            $('.save-sort').click(function () {
                var updateList = {};
                var button = $(this);

                button.parent().find('input[type="hidden"]').each(function (index, item) {
                    updateList[$(item).attr('data-id')] = $(item).val();
                });

                $.post("{{ route('article.sort_update_form_post') }}", {list: updateList}).done(function( data ) {
                    button.parent().find('.text-success').removeClass('hidden');
                }).fail(function() {
                    alert('Fehler beim Speichern');
                });
            });
        });
    </script>
@endpush