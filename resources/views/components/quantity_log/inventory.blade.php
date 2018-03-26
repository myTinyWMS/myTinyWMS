<tr>
    <td class="bg-primary text-center">INV</td>
    <td class="text-{{  $log->change >= 0 ? 'success' : 'danger' }} text-center">{{ $log->change >= 0 ? '+'.$log->change : $log->change }}</td>
    <td class="text-center">{{ $log->new_quantity }}</td>
    <td class="text-nowrap">{{ $log->created_at->format('d.m.Y H:i') }} Uhr</td>
    <td>{{ $log->note }}</td>
    <td class="text-nowrap">{{ $log->user->name }}</td>
</tr>