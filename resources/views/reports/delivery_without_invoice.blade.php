@extends('layout.app')

@section('title', __('Wareneingänge ohne Rechnung'))

@section('breadcrumb')
    <li>
        <a href="{{ route('reports.index') }}">@lang('Reports')</a>
    </li>
    <li class="active">
        <strong>@lang('Wareneingänge ohne Rechnung')</strong>
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
                                    <th>@lang('Artikel')</th>
                                    <th>@lang('Bestellung')</th>
                                    <th>@lang('Lieferant')</th>
                                    <th>@lang('Lieferzeitpunkt')</th>
                                    <th>@lang('Bestellwert')</th>
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
                                    <td>{{ $item->order->supplier->name }}</td>
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
                                    <td>{!! formatPrice($item->price * $item->quantity) !!}</td>
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