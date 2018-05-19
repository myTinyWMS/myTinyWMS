@extends('layout.app')

@section('title', 'Inventur Update')

@section('content')
    <form method="post" action="{{ route('article.fix_inventory_save') }}">
        <div class="row">
        <div class="col-lg-12">
            @foreach($articles as $category => $items)
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $category }}</h5>
                </div>
                <div class="ibox-content">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Artikel</th>
                                <th width="20%">Lieferant</th>
                                <th width="30%">Notizen</th>
                                <th width="10%">Einheit</th>
                                <th width="10%" class="text-center">Inventur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $article)
                            <tr>
                                <td><a href="{{ route('article.show', $article) }}">{{ $article->name }}</a></td>
                                <td>{{ $article->supplier_name }}</td>
                                <td>{{ $article->notes }}</td>
                                <td>
                                    {{ Form::bsSelect('unit_id['.$article->id.']', $article->unit_id, $units,  '', ['placeholder' => '']) }}
                                </td>
                                <td class="text-center">
                                    <div class="i-checks"><label> <input type="checkbox" name="inventory[{{ $article->id }}]" @if($article->inventory) checked @endif value="1"></label></div>
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