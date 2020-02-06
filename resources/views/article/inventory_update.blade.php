@extends('layout.app')

@section('title', __('Artikel-Inventur-Update'))

@section('breadcrumb')
    <li>
        <a href="{{ route('article.index') }}">@lang('Übersicht')</a>
    </li>
    <li class="active">
        <strong>@lang('Artikel-Inventur-Update')</strong>
    </li>
@endsection

@section('content')
    <form method="post" action="{{ route('article.inventory_update_save') }}">
        <div class="row">
            <div class="w-full">
                <div class="alert alert-danger">@lang('Achtung, Änderungen am Bestand werden hier als Inventurbuchung gespeichert!')</div>
                @php $tabindex = 0; @endphp
                @foreach($articles as $category => $items)
                <div class="card mt-6">
                    <div class="card-header">
                        <h5>{{ $category }}</h5>
                    </div>
                    <div class="card-content">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('Artikel')</th>
                                    <th width="15%">@lang('Lieferant')</th>
                                    <th width="25%">@lang('Notizen')</th>
                                    <th width="10%">@lang('Inventurtyp')</th>
                                    <th width="10%">@lang('Einheit')</th>
                                    <th width="10%">@lang('aktueller Bestand')</th>
                                    <th width="10%">@lang('neuer Bestand')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $article)
                                    @php $tabindex++; @endphp
                                <tr>
                                    <td>{{ $article->article_number }}</td>
                                    <td><a href="{{ route('article.show', $article) }}">{{ $article->name }}</a></td>
                                    <td>{{ $article->supplier_name }}</td>
                                    <td>{{ $article->notes }}</td>
                                    <td>{{ \Mss\Models\Article::getInventoryTextArray()[$article->inventory] }}</td>
                                    <td>{{ optional($article->unit)->name }}</td>
                                    <td>{{ $article->quantity }}</td>
                                    <td data-org-quantity="{{ $article->quantity }}">
                                        {{ Form::bsText('quantity['.$article->id.']', $article->quantity, ['class' => 'form-input newquantity', 'tabindex' => $tabindex, 'id' => 'quantity_'.$article->id], '') }}
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
                        <button type="submit" class="btn btn-primary" id="submit">@lang('Speichern')</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.newquantity').change(function () {
            if (parseInt($(this).attr('data-org-quantity')) !== parseInt($(this).val())) {
                $(this).addClass('bg-red-500').addClass('text-white');
            } else {
                $(this).removeClass('bg-red-500').removeClass('text-white');
            }
        });
    });
</script>
@endpush