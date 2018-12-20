@extends('unit.form')

@section('title', 'Inventur bearbeiten - gestartet am '.$inventory->created_at->format('d.m.Y H:i'))

@section('breadcrumb')
    <li>
        <a href="{{ route('inventory.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Inventur bearbeiten</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="tabs-container">
                <div class="tabs-left">
                    <ul class="nav nav-tabs" role="tablist">
                        @foreach($items->keys() as $category)
                            <li class="@if((empty($categoryToPreselect) && $loop->first) || optional($categoryToPreselect)->name === $category) active @endif">
                                <a class="nav-link" data-toggle="tab" href="#{{ md5($category) }}">
                                    {{ $category }}
                                    <span class="badge">{{ count($items[$category]) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        @foreach($items as $category => $articles)
                            <div role="tabpanel" class="tab-pane @if((empty($categoryToPreselect) && $loop->first) || optional($categoryToPreselect)->name === $category) active @endif" id="{{ md5($category) }}">
                                <div class="panel-body">
                                    <table class="table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Artikel</th>
                                                <th width="15%">Lieferant</th>
                                                <th width="25%">Notizen</th>
                                                <th width="5%" class="text-nowrap">Einheit</th>
                                                <th width="5%" class="text-nowrap">Bestand alt</th>
                                                <th width="10%" class="text-nowrap text-center">Bestand neu</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($articles as $item)
                                                <tr>
                                                    <td>{{ $item->article->article_number }}</td>
                                                    <td><a href="{{ route('article.show', $item->article) }}">{{ $item->article->name }}</a></td>
                                                    <td>{{ $item->article->supplier_name }}</td>
                                                    <td>{{ $item->article->notes }}</td>
                                                    <td class="text-nowrap">{{ optional($item->article->unit)->name }}</td>

                                                    @if ($inventory->isFinished())
                                                        <td class="text-center">{{ $item->old_quantity }}</td>
                                                        <td class="text-center @if($item->old_quantity != $item->new_quantity) danger @endif">
                                                            {{ $item->new_quantity }}
                                                            @if (!empty($item->processed_at))
                                                                <i class="fa fa-question-circle m-l-sm" data-toggle="tooltip" data-placement="right" title="{{ $item->processor->name }} - {{ $item->processed_at->format("d.m.Y H:i") }}"></i>
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td class="text-center p-t-15">{{ $item->article->quantity }}</td>
                                                        <td class="text-center text-nowrap" data-org-quantity="{{ $item->article->quantity }}">
                                                            <form method="post" action="{{ route('inventory.processed', [$inventory, $item->article]) }}">
                                                                @csrf
                                                                <div class="input-group">
                                                                    {{ Form::bsText('quantity', null, ['class' => 'form-control newquantity'], '') }}
                                                                    <span class="input-group-btn">
                                                                        <button type="submit" class="btn btn-warning btn-sm" title="Bestand ändern">
                                                                            <i class="fa fa-save"></i>
                                                                        </button>
                                                                    </span>
                                                                    <span class="input-group-btn">
                                                                        <a href="{{ route('inventory.correct', [$inventory, $item->article]) }}" class="bnt btn-success btn-sm m-l-md" title="Bestand stimmmt">
                                                                            <i class="fa fa-check"></i>
                                                                        </a>
                                                                    </span>
                                                                </div>
                                                            </form>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <a href="{{ route('inventory.category.done', [$inventory, $articles->first()->article->category]) }}" class="btn btn-danger">Kategorie abschließen</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection