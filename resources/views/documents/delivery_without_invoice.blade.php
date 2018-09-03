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
        <table cellpadding="0" cellspacing="1">
            <thead>
                <tr>
                    <th>Artikel</th>
                    <th>Bestellung</th>
                    <th>Lieferant</th>
                    <th>Lieferzeitpunkt</th>
                    <th>Bestellwert</th>
                </tr>
            </thead>
            <tbody>
                @foreach($openItems as $item)
                <tr>
                    <td>
                        {{ $item->article->name }}
                    </td>
                    <td>
                        {{ $item->order->internal_order_number }}
                    </td>
                    <td>
                        {{ $item->order->supplier->name }}
                    </td>
                    <td>
                        @if($item->deliveryItems->count() > 1)
                            {{ $item->deliveryItems->first()->created_at->format('d.m.Y') }}
                        @else
                            @foreach($item->deliveryItems as $deliveryItem)
                                {{ $deliveryItem->created_at->format('d.m.Y') }}
                                @if(!$loop->last)
                                    <br>
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>
                        {!! formatPrice($item->price * $item->quantity) !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>