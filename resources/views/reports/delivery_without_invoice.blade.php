@extends('layout.app')

@section('title', 'Wareneingänge ohne Rechnung')

@section('breadcrumb')
    <li>
        <a href="{{ route('reports.index') }}">Reports</a>
    </li>
    <li class="active">
        <strong>Wareneingänge ohne Rechnung</strong>
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Artikel</th>
                                    <th>Bestellung</th>
                                    <th>Lieferzeitpunkt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($openItems as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('article.show', $item->article) }}" target="_blank">{{ $item->article->name }}</a>
                                    </td>
                                    <td>
                                        <a href="{{ route('order.show', $item->order) }}" target="_blank">{{ $item->order->internal_order_number }}</a>
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
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toolbar_content hidden">
        <button class="btn btn-xs btn-primary" type="submit">Bestellung erstellen</button>
    </div>
@endsection