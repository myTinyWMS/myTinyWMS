@extends('layout.app')

@section('title', 'Inventur Update')

@section('content')
    <form method="post" action="{{ route('article.fix_inventory_save') }}">
        <div class="row">
        <div class="col-lg-8">
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
                                <th width="30%">Lieferant</th>
                                <th width="30%">Einheit</th>
                                <th width="10%" class="text-center">Inventur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $article)
                            <tr>
                                <td>{{ $article->name }}</td>
                                <td>{{ $article->supplier_name }}</td>
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