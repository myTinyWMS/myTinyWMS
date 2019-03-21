@extends('article.form', ['isNewArticle' => false])

@section('title', 'Artikel Details'.((!empty($article->article_number)) ? ' #'.$article->article_number : ''))

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Artikel bearbeiten</strong>
    </li>
@endsection

@section('form_start')
    {!! Form::model($article, ['route' => ['article.update', $article], 'method' => 'PUT']) !!}
@endsection

@section('submit')
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary']) !!}
@endsection

@section('secondCol')
    <div class="w-2/3 flex">
        <div class="w-1/3 ml-4">
            <div class="card">
                <div class="card-header">
                    <div class="flex">
                        <div class="flex-1">Aktueller Lieferant</div>

                        <dot-menu class="ml-2">
                            <a href="javascript:void(0)" class="btn-link" @click="$modal.show('changeSupplierModal')">ändern</a>
                        </dot-menu>
                    </div>
                </div>

                <div class="card-content">
                    <div class="row">
                        <div class="w-1/2">
                            <div class="form-group">
                                <label class="form-label">
                                    Lieferant
                                    <a href="{{ route('article.index', ['supplier' => $article->currentSupplier]) }}" class="m-l-sm" title="alle Artikel des Lieferanten anzeigen" target="_blank"><i class="fa fa-filter"></i></a>
                                </label>
                                <div class="form-control-static"><a href="{{ route('supplier.show', $article->currentSupplier) }}" target="_blank">{{ $article->currentSupplier->name }}</a></div>
                            </div>
                        </div>
                        <div class="w-1/2">
                            <div class="form-group">
                                <label class="form-label">Bestellnummer</label>
                                <div class="form-control-static">{{ $article->currentSupplierArticle->order_number }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2">
                            <div class="form-group">
                                <label class="form-label">Preis <span class="text-red-500">netto</span></label>
                                <div class="form-control-static">{!! formatPrice($article->currentSupplierArticle->price / 100) !!}</div>
                            </div>
                        </div>
                        <div class="w-1/2">
                            <div class="form-group">
                                <label class="form-label">Lieferzeit</label>
                                <div class="form-control-static">{{ $article->currentSupplierArticle->delivery_time }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-1/2">
                            <div class="form-group">
                                <label class="form-label">Bestellmenge</label>
                                <div class="form-control-static">{{ $article->currentSupplierArticle->order_quantity }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Aktionen</h5>
                </div>
                <div class="card-content">
                    <div class="flex flex-col">
                        <div class="py-4">
                            <a href="{{ route('order.create', ['article' => $article]) }}" class="btn btn-secondary">Neue Bestellung</a>
                        </div>

                        <div class="py-4">
                            <a href="{{ route('article.print_single_label', ['article' => $article, 'size' => 'small']) }}" class="btn btn-secondary">kleines Label drucken</a>
                            <a href="{{ route('article.print_single_label', ['article' => $article, 'size' => 'large']) }}" class="btn btn-secondary">großes Label drucken</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <h5>Dateien</h5>
                </div>
                <div class="card-content">
                    <ul class="">
                        @if(is_array($article->files) && count($article->files))
                            @foreach($article->files as $key => $file)
                                <li><a href="{{ route('article.file_download', [$article, $key]) }}">{{ $file['orgName'] }}</a></li>
                            @endforeach
                        @endif
                    </ul>
                    {{ Form::dropzone('attachments', 'Anhänge', route('article.file_upload', $article)) }}
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="flex">
                        <h5 class="flex-1">Notizen</h5>
                        <a href="#" class="btn-link btn-sm" data-toggle="modal" data-target="#newNoteModal">Neue Notiz</a>
                    </div>
                </div>
                <div class="card-content">
                    <div class="feed-activity-list">
                        @foreach($article->articleNotes()->latest()->get() as $note)
                            <div class="feed-element">
                                <div class="flex mb-2">
                                    <div class="font-bold flex-1 text-sm text-gray-800">{{ $note->user->name }}</div>
                                    <div class="flex items-baseline">
                                        <small class="text-gray-600">{{ $note->created_at->format('d.m.Y - H:i') }}</small>

                                        <dot-menu class="ml-2">
                                            <a href="#" class="delete_note" data-id="{{ $note->id }}">löschen</a>
                                        </dot-menu>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-600">{{ $note->content }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="w-2/3 ml-4">
            <collapse title="Logbuch">
                @include('components.audit_list', $audits)
            </collapse>

            <div class="card mt-4">
                <div class="card-header flex">
                    <h5 class="flex-1">
                        Bestands-Verlauf
                    </h5>
                    <a href="{{ route('article.quantity_changelog', $article) }}" class="btn-link btn-xs">mehr</a>
                </div>
                <div class="card-content">
                    <article-quantity-changelog :items="{{ json_encode($article->getShortChangelog()) }}" :article="{{ json_encode($article) }}" :edit-enabled="true"></article-quantity-changelog>
                </div>
            </div>
        </div>
    </div>

    <modal name="changeSupplierModal"height="auto" classes="modal">
        <h4 class="modal-title">Lieferant bearbeiten</h4>

        {!! Form::open(['route' => ['article.change_supplier', $article], 'method' => 'POST']) !!}
            <div class="row">
                <div class="w-full">
                    <div class="form-group">
                        <label for="supplier" class="form-label">Lieferant</label>
                        {!! Form::select('supplier', \Mss\Models\Supplier::orderedByName()->pluck('name', 'id'), $article->currentSupplier->id ?? null, ['class' => 'form-control', 'id' => 'supplier', 'name' => 'supplier', 'style' => 'width: 100%']) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="w-1/2">
                    <div class="form-group">
                        {{ Form::bsText('order_number', $article->currentSupplierArticle->order_number, [], 'Bestellnummer') }}
                    </div>
                </div>
                <div class="w-1/2 ml-4">
                    <div class="form-group">
                        {{ Form::bsText('price', str_replace('.', ',', $article->currentSupplierArticle->price / 100), [], 'Preis netto') }}
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="w-1/2">
                    <div class="form-group">
                        {{ Form::bsText('delivery_time', $article->currentSupplierArticle->delivery_time, [], 'Lieferzeit (Wochentage)') }}
                    </div>
                </div>
                <div class="w-1/2 ml-4">
                    <div class="form-group">
                        {{ Form::bsText('order_quantity', $article->currentSupplierArticle->order_quantity, [], 'Bestellmenge') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" @click="$modal.hide('changeSupplierModal')">Abbrechen</button>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
        {!! Form::close() !!}
    </modal>
    
    {{--

    <!-- Change Supplier Modal -->
    <div class="modal fade" id="changeSupplierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                
            </div>
        </div>
    </div>

    <!-- New Note Modal -->
    <div class="modal fade" id="newNoteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Neue Notiz</h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="new_note">Notiz</label>
                            <textarea id="new_note" class="form-control" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                    <button type="button" class="btn btn-primary" id="save_note">Speichern</button>
                </div>
            </div>
        </div>
    </div>
--}}

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#save_note').click(function () {
            if ($('#new_note').val() === '') {
                alert('Bitte einen Text eingeben!');
                return false;
            }

            $.post("{{ route('article.add_note', $article) }}", { content: $('#new_note').val() })
                .done(function(data) {
                    var newItem = '<div class="feed-element">\n' +
                        '                            <div>\n' +
                        '                                <small class="pull-right text-navy">' + data['createdDiff'] + '</small>\n' +
                        '                                <p><strong>' + data['user'] + '</strong></p>\n' +
                        '                                <p>' + data['content'] + '</p>\n' +
                        '                                <small class="text-muted">' +
                                                         data['createdFormatted'] + ' Uhr' +
                        '                                <button class="btn btn-xs btn-link delete_note" title="Notiz löschen" data-id="' + data['id'] + '">\n' +
                        '                                    <i class="fa fa-trash"></i>\n' +
                        '                                </button>' +
                        '                                </small>\n' +
                        '                            </div>\n' +
                        '                        </div>';

                    $('.feed-activity-list').prepend(newItem);
                    $('#newNoteModal').modal('hide');
                    $('#new_note').val('');
                }
            );
        });

        $('.delete_note').click(function () {
            var note_link = $(this);
            $.post("{{ route('article.delete_note', $article) }}", { note_id: note_link.attr('data-id') })
                .done(function(data) {
                    note_link.parent().parent().parent().remove();
                }
            );
        });

        $("#supplier").select2({
            theme: "bootstrap"
        });
    })
</script>
@endpush