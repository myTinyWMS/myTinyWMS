<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <style>
            body {
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            }

            .break-before {
                page-break-before: always;
            }

            .break-after {
                page-break-after: always;
            }

            h1 {
                font-size: 24px;
            }

            .table {
                border-collapse: collapse;
                background: #000;
                width: 100%;
            }

            td, th {
                border: 1px solid black;
                background: #fff;
                text-align: left;
                padding: 5px;
            }

            .text-center {
                text-align: center;
            }

            tbody tr:nth-child(odd) td {
                background-color: #f2f2f2;
            }

            table, tr, td, th, tbody, thead, tfoot {
                page-break-inside: avoid !important;
            }

            thead { display: table-row-group; }
        </style>
    </head>

    <body class="white-bg">
        @foreach($groupedArticles as $category => $articles)
            <h1>{{ $category }}</h1>
            <table class="table @if (!$loop->last) break-after @endif" cellpadding="0" cellspacing="1">
                <thead>
                    <tr>
                        <th style="width: 30px">@lang('Art. Nummer')</th>
                        <th style="width: 200px">@lang('Artikel')</th>
                        <th class="text-center" style="width: 40px">@lang('SOLL')</th>
                        <th class="text-center" style="width: 60px">@lang('IST')</th>
                        <th class="text-center" style="width: 30px">@lang('Einheit')</th>
                        <th>@lang('Bemerkung')</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($articles as $article)
                    <tr>
                        <td class="text-center">{{ $article->internal_article_number }}</td>
                        <td>{{ $article->name }}</td>
                        <td class="text-center">{{ $article->quantity }}</td>
                        <td></td>
                        <td>{{ optional($article->unit)->name }}</td>
                        <td>{{ $article->notes }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    </body>
</html>