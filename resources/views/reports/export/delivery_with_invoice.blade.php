"Bestellung","Artikel","Kategorie","Lieferant","Lieferzeitpunkt","Bestellwert"
@foreach($items as $deliveriesGroup)
@foreach($deliveriesGroup as $delivery)
@foreach($delivery->items as $deliveryItem)
"{{ $delivery->order->internal_order_number }}","{{ $deliveryItem->article->name }}","{{ $deliveryItem->article->category->name }}","{{ $delivery->order->supplier->name }}","{{ $delivery->created_at->format('d.m.Y') }}",{!! round($deliveryItem->orderItem->price * $deliveryItem->orderItem->quantity, 2) !!}
@endforeach
@endforeach
@endforeach