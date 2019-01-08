<td class="text-nowrap text-center">{{ optional($log->unit)->name }}</td>
<td class="text-nowrap">{{ $log->created_at->format('d.m.Y H:i') }} Uhr</td>
<td>
    @if ($log->deliveryItem)
        <a href="{{ route('order.show', $log->deliveryItem->delivery->order) }}" target="_blank">{{ $log->note ?? 'Bestellung '.$log->deliveryItem->delivery->order->internal_order_number }}</a>
    @else
        {{ $log->note }}
    @endif

    @if ($edit ?? false)
    <button type="button" class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#changeChangelogNoteModal" data-id="{{ $log->id }}" data-note="{{ $log->note }}"><i class="fa fa-edit"></i></button>
    @endif
</td>
<td class="text-nowrap">{{ optional($log->user)->name }}</td>