@extends('layout.app')

@section('title', 'Verpackungs-Lizensierungs-Report für den Zeitraum '.implode(' - ', $dateRange))

@section('breadcrumb')
    <li>
        <a href="{{ route('reports.index') }}">Reports</a>
    </li>
    <li class="active">
        <strong>Verpackungs-Lizensierungs-Report</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-content">
                        @foreach([\Mss\Models\Article::PACKAGING_CATEGORY_PAPER => 'Papier, Pappe, Karton', \Mss\Models\Article::PACKAGING_CATEGORY_PLASTIC => 'Kunststoffe'] as $key => $headline)
                            <h2>{{ $headline }}</h2>

                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Artikel</th>
                                    <th width="10%" class="text-right">Verbrauch</th>
                                    <th width="10%" class="text-right">Gewicht</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php ($total = 0)
                                    @foreach($articles[$key] as $article)
                                        @php ($total += (($article->usage * -1) * $article->weight)/1000)
                                        <tr>
                                            <td><a href="{{ route('article.show', $article) }}" target="_blank">{{ $article->name }}</a></td>
                                            <td class="text-right">{{ ($article->usage * -1) }} {{ optional($article->unit)->name }}</td>
                                            <td class="text-right">{{ round((($article->usage * -1) * $article->weight)/1000, 2) }} kg</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right font-bold">Gesamt: {{ round($total, 2) }} kg</td>
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