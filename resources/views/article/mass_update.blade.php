@extends('layout.app')

@section('title', 'Artikel-Massenupdate')

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">Übersicht</a>
    </li>
    <li class="active">
        <strong>Artikel-Massenupdate</strong>
    </li>
@endsection

@section('content')
    <form method="post" action="{{ route('article.mass_update_save') }}">
        <div class="row">
        <div class="w-full">
            @foreach($articles as $category => $items)
            <div class="card">
                <div class="card-header">
                    <h5>{{ $category }}</h5>
                </div>
                <div class="card-content">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Artikel</th>
                                <th width="15%">Lieferant</th>
                                <th width="25%">Notizen</th>
                                <th width="10%">Einheit</th>
                                <th width="10%">Inventurtyp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $article)
                            <tr>
                                <td>{{ $article->article_number }}</td>
                                <td><a href="{{ route('article.show', $article) }}">{{ $article->name }}</a></td>
                                <td>{{ $article->supplier_name }}</td>
                                <td>{{ $article->notes }}</td>
                                <td>
                                    @if(empty($article->unit_id))
                                        {{ Form::bsSelect('unit_id['.$article->id.']', $article->unit_id, $units,  '', ['placeholder' => '', 'id' => 'unit_'.$article->id]) }}
                                    @else
                                        {{ $article->unit->name }}
                                    @endif
                                </td>
                                <td>
                                    {{ Form::bsSelect('inventory['.$article->id.']', $article->inventory, \Mss\Models\Article::getInventoryTextArray(), '', ['id' => 'inventory_'.$article->id]) }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
            <div class="card mt-4">
                <div class="card-content">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary" id="submit">Speichern</button>
                </div>
            </div>
        </div>
        </div>
    </form>
@endsection