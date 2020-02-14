@extends('article.form', ['isNewArticle' => false, 'isCopyOfArticle' => false])

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
    {!! Form::submit('Speichern', ['class' => 'btn btn-primary', 'id' => 'saveArticle']) !!}
@endsection

@section('secondCol')
    <div class="2xl:w-2/3 w-1/2 flex flex-wrap 2xl:flex-no-wrap">
        <div class="2xl:w-2/5 w-full ml-4">
            <div class="card">
                <div class="card-header">
                    <div class="flex">
                        <div class="flex-1">Aktueller Lieferant</div>

                        <dot-menu class="ml-2" id="changeSupplierMenu">
                            <a href="javascript:void(0)" class="btn-link" @click="$modal.show('changeSupplierModal')" id="changeSupplierLink">Lieferoptionen ändern</a>
                        </dot-menu>
                    </div>
                </div>

                <div class="card-content">
                    <div class="row">
                        <div class="w-2/3">
                            <div class="form-group">
                                <label class="form-label">
                                    Lieferant
                                    <a href="{{ route('article.index', ['supplier' => $article->currentSupplier]) }}" class="m-l-sm" title="alle Artikel des Lieferanten anzeigen" target="_blank"><i class="fa fa-filter"></i></a>
                                </label>
                                <div class="form-control-static"><a href="{{ route('supplier.show', $article->currentSupplier) }}" target="_blank">{{ $article->currentSupplier->name }}</a></div>
                            </div>
                        </div>
                        <div class="w-1/3">
                            <div class="form-group">
                                <label class="form-label">Bestellnummer</label>
                                <div class="form-control-static">{{ $article->currentSupplierArticle->order_number }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-2/3">
                            <div class="form-group">
                                <label class="form-label">Preis <span class="text-red-500">netto</span></label>
                                <div class="form-control-static">{!! formatPrice($article->currentSupplierArticle->price / 100) !!}</div>
                            </div>
                        </div>
                        <div class="w-1/3">
                            <div class="form-group">
                                <label class="form-label">Lieferzeit</label>
                                <div class="form-control-static">{{ $article->currentSupplierArticle->delivery_time }} Wochentage</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="w-2/3">
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
                    @if(is_array($article->files) && count($article->files))
                        <ul class="mb-4 pl-4 list-disc" id="fileList">
                            @foreach($article->files as $key => $file)
                                <li class="flex justify-between">
                                    <a href="{{ route('article.file_download', [$article, $key]) }}">{{ $file['orgName'] }}</a>
                                    <a href="{{ route('article.file_delete', [$article, $key]) }}" onclick="return confirm('Sicher?')"><i class="fa fa-trash"></i></a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    {{ Form::dropzone('attachments', 'Anhänge', route('article.file_upload', $article)) }}
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header">
                    <div class="flex">
                        <h5 class="flex-1">Notizen</h5>
                        <a href="javascript:void(0)" class="btn-link btn-xs" @click="$modal.show('newNoteModal')" id="addNote">Neue Notiz</a>
                    </div>
                </div>
                <div class="card-content">
                    <div class="feed-activity-list">
                        @foreach($article->articleNotes()->latest()->get() as $note)
                            <div class="feed-element mb-6">
                                <div class="flex">
                                    <div class="flex-1 text-xs text-gray-600">{{ $note->user->name }}</div>
                                    <div class="flex items-baseline">
                                        <small class="text-gray-600">{{ $note->created_at->format('d.m.Y - H:i') }}</small>

                                        <dot-menu class="ml-2 notes-menu">
                                            <a href="{{ route('article.delete_note', [$article, $note]) }}">löschen</a>
                                        </dot-menu>
                                    </div>
                                </div>
                                <div class="text-gray-800">{{ $note->content }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="2xl:w-3/5 w-full ml-4">
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

    <modal name="changeSupplierModal" height="auto" classes="modal">
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
                        {{ Form::bsNumber('delivery_time', $article->currentSupplierArticle->delivery_time, [], 'Lieferzeit (Wochentage)') }}
                    </div>
                </div>
                <div class="w-1/2 ml-4">
                    <div class="form-group">
                        {{ Form::bsNumber('order_quantity', $article->currentSupplierArticle->order_quantity, [], 'Bestellmenge') }}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" @click="$modal.hide('changeSupplierModal')">Abbrechen</button>
                <button type="submit" class="btn btn-primary" id="saveChangeSupplier">Speichern</button>
            </div>
        {!! Form::close() !!}
    </modal>

    <add-article-note-modal :article="{{ $article }}"></add-article-note-modal>

@endsection

@push('scripts')
<script>
    Dropzone.options.dropzoneForm = {
        parallelUploads: 1
    };

    $(document).ready(function () {
        $("#supplier").select2({
            theme: "bootstrap"
        });
    })
</script>
@endpush