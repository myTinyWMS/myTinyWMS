@extends('layout.app')

@section('title', 'Wareneingänge mit Rechnung - vom '.$start->monthName.' '.$start->year)

@section('breadcrumb')
    <li>
        <a href="{{ route('reports.index') }}">Reports</a>
    </li>
    <li class="active">
        <strong>Wareneingänge mit Rechnung</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-content">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Bestellung</th>
                                    <th>Artikel</th>
                                    <th>Lieferant</th>
                                    <th>Lieferzeitpunkt</th>
                                    <th>Bestellwert</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $orderItemGroup)
                                <tr>
                                    <td>
                                        <a href="{{ route('order.show', $orderItemGroup->first()->order) }}" target="_blank">{{ $orderItemGroup->first()->order->internal_order_number }}</a>
                                    </td>
                                    <td>
                                        @foreach($orderItemGroup as $orderItem)
                                        <a href="{{ route('article.show', $orderItem->article) }}" target="_blank">{{ $orderItem->article->name }}</a><br>
                                        @endforeach
                                    </td>
                                    <td>{{ $orderItemGroup->first()->order->supplier->name }}</td>
                                    <td>
                                        @foreach($orderItemGroup as $orderItem)
                                            {{ $orderItem->deliveryItems->sortBy('delivery_date')->last()->created_at->format('d.m.Y') }}<br>
                                        @endforeach
                                    </td>
                                    <td class="text-right">
                                        @php($total = 0)
                                        @foreach($orderItemGroup as $orderItem)
                                            {!! formatPrice($orderItem->price * $orderItem->quantity) !!}<br>
                                            @php($total += $orderItem->price * $orderItem->quantity)
                                        @endforeach
                                        @if($orderItemGroup->count() > 1)
                                        <span class="font-bold border-black border-t-2">&sum; {!! formatPrice($total) !!}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection