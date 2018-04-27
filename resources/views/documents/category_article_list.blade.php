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

            table, tr, td, th, tbody, thead, tfoot {
                page-break-inside: avoid !important;
            }
        </style>
    </head>

    <body class="white-bg">
        @foreach($categories as $category)
        <h1>{{ $category->name }}</h1>
        <table class="table @if (!$loop->last) break-after @endif" cellpadding="0" cellspacing="1">
            <thead>
                <tr>
                    <th style="width: 25px">Nummer</th>
                    <th style="width: 200px">Name</th>
                    <th style="width: 100px">Entnahme</th>
                    <th>Ausgabe</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category->articles as $article)
                <tr>
                    <td>{{ $article->article_number }}</td>
                    <td>{{ $article->name }}</td>
                    <td>{{ $article->issue_quantity }} {{ optional($article->unit)->name }}</td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endforeach
    </body>
</html>