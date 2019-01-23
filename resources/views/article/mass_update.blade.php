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
        <div class="col-lg-12">
            @foreach($articles as $category => $items)
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $category }}</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Artikel</th>
                                <th width="15%">Lieferant</th>
                                <th width="25%">Notizen</th>
                                <th width="5%">Kostenstelle</th>
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
                                <td>{{ Form::bsText('cost_center['.$article->id.']', $article->cost_center, [], '') }}</td>
                                <td>
                                    @if(empty($article->unit_id))
                                        {{ Form::bsSelect('unit_id['.$article->id.']', $article->unit_id, $units,  '', ['placeholder' => '']) }}
                                    @else
                                        {{ $article->unit->name }}
                                    @endif
                                </td>
                                <td>
                                    {{ Form::bsSelect('inventory['.$article->id.']', $article->inventory, \Mss\Models\Article::getInventoryTextArray(), '') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
            <div class="ibox">
                <div class="ibox-content">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </div>
        </div>
        </div>
    </form>
@endsection