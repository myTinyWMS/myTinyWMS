@extends('layout.app')

@section('title', __('Verpackungs-Lizensierungs-Report für den Zeitraum :daterange', ['daterange' => implode(' - ', $dateRange)]))

@section('breadcrumb')
    <li>
        <a href="{{ route('reports.index') }}">@lang('Reports')</a>
    </li>
    <li class="active">
        <strong>@lang('Verpackungs-Lizensierungs-Report')</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-content">
                        @foreach([\Mss\Models\Article::PACKAGING_CATEGORY_PAPER => __('Papier, Pappe, Karton'), \Mss\Models\Article::PACKAGING_CATEGORY_PLASTIC => __('Kunststoffe'), \Mss\Models\Article::PACKAGING_CATEGORY_METAL => __('Blech/Eisen/Metall')] as $key => $headline)
                            <h2>{{ $headline }}</h2>

                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Artikel</th>
                                    <th width="10%" class="text-right">@lang('Verbrauch')</th>
                                    <th width="10%" class="text-right">@lang('Gewicht')</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php ($total = 0)
                                    @if($articles->has($key))
                                        @foreach($articles->get($key) as $article)
                                            @php ($total += (($article->usage * -1) * $article->weight)/1000)
                                            <tr>
                                                <td><a href="{{ route('article.show', $article) }}" target="_blank">{{ $article->name }}</a></td>
                                                <td class="text-right">{{ ($article->usage * -1) }} {{ optional($article->unit)->name }}</td>
                                                <td class="text-right">{{ round((($article->usage * -1) * $article->weight)/1000, 2) }} kg</td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right font-bold">@lang('Gesamt'): {{ round($total, 2) }} kg</td>
                                    </tr>
                                </tfoot>
                            </table>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection