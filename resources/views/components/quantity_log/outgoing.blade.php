<tr>
    <td class="bg-danger text-center">WA</td>
    <td class="text-danger text-center">{{ $log->change }}</td>
    <td class="text-center">{{ $log->new_quantity }}</td>
    <td class="text-nowrap">{{ $log->created_at->format('d.m.Y H:i') }} Uhr</td>
    <td>{{ $log->note }}</td>
    <td class="text-nowrap">{{ $log->user->name }}</td>
</tr>