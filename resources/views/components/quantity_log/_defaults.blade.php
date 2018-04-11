<td class="text-nowrap">{{ $log->created_at->format('d.m.Y H:i') }} Uhr</td>
<td>
    @if ($log->deliveryItem)
        <a href="{{ route('order.show', $log->deliveryItem->delivery->order) }}" target="_blank">{{ $log->note }}</a>
    @else
        {{ $log->note }}
    @endif
</td>
<td class="text-nowrap">{{ $log->user->name }}</td>