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
    <div class="col-lg-6 col-xxl-3">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Aktueller Lieferant</h5>
                <a href="#" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#changeSupplierModal">ändern</a>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Lieferant</label>
                            <div class="form-control-static"><a href="{{ route('supplier.show', $article->currentSupplier) }}" target="_blank">{{ $article->currentSupplier->name }}</a></div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Bestellnummer</label>
                            <div class="form-control-static">{{ $article->currentSupplierArticle->order_number }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Preis</label>
                            <div class="form-control-static">{!! formatPrice($article->currentSupplierArticle->price / 100) !!}</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Lieferzeit</label>
                            <div class="form-control-static">{{ $article->currentSupplierArticle->delivery_time }}</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label class="control-label">Bestellmenge</label>
                            <div class="form-control-static">{{ $article->currentSupplierArticle->order_quantity }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-title">
                <h5>Dateien</h5>
            </div>
            <div class="ibox-content">
                <ul class="m-b-md">
                @if(is_array($article->files) && count($article->files))
                    @foreach($article->files as $key => $file)
                        <li><a href="{{ route('article.file_download', [$article, $key]) }}">{{ $file['orgName'] }}</a></li>
                    @endforeach
                    @endif
                </ul>
                {{ Form::dropzone('attachments', 'Anhänge', route('article.file_upload', $article)) }}
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-title">
                <h5>Notizen</h5>
                <a href="#" class="btn btn-default btn-xs pull-right" data-toggle="modal" data-target="#newNoteModal">Neue Notiz</a>
            </div>
            <div class="ibox-content">
                <div class="feed-activity-list">
                    @foreach($article->articleNotes()->latest()->get() as $note)
                        <div class="feed-element">
                            <div>
                                <small class="pull-right text-navy">{{ $note->created_at->diffForHumans() }}</small>
                                <p><strong>{{ $note->user->name }}</strong></p>
                                <p>{{ $note->content }}</p>
                                <small class="text-muted">
                                    {{ $note->created_at->format('d.m.Y - H:i') }} Uhr
                                    <button class="btn btn-xs btn-link delete_note" title="löschen" data-id="{{ $note->id }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-xxl-5">
        <div class="ibox collapsed">
            <div class="ibox-title">
                <h5>Logbuch</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                @include('components.audit_list', $audits)
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-title">
                <h5>
                    Bestands-Verlauf
                </h5>
                <div class="pull-right">
                    <a href="{{ route('article.quantity_changelog', $article) }}" class="btn btn-primary btn-xs">mehr</a>
                </div>
            </div>
            <div class="ibox-content">
                <article-quantity-changelog :items="{{ json_encode($article->getShortChangelog()) }}" :article="{{ json_encode($article) }}" :edit-enabled="true"></article-quantity-changelog>
            </div>
        </div>
    </div>

    <!-- Change Supplier Modal -->
    <div class="modal fade" id="changeSupplierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['route' => ['article.change_supplier', $article], 'method' => 'POST']) !!}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Lieferant bearbeiten</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="supplier" class="control-label">Lieferant</label>
                                    {!! Form::select('supplier', \Mss\Models\Supplier::orderedByName()->pluck('name', 'id'), $article->currentSupplier->id ?? null, ['class' => 'form-control', 'id' => 'supplier', 'name' => 'supplier', 'style' => 'width: 100%']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    {{ Form::bsText('order_number', $article->currentSupplierArticle->order_number, [], 'Bestellnummer') }}
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    {{ Form::bsText('price', str_replace('.', ',', $article->currentSupplierArticle->price / 100), [], 'Preis') }}
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    {{ Form::bsText('delivery_time', $article->currentSupplierArticle->delivery_time, [], 'Lieferzeit (Wochentage)') }}
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    {{ Form::bsText('order_quantity', $article->currentSupplierArticle->order_quantity, [], 'Bestellmenge') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                {!! Form::close() !!}
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