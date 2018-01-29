<tr>
    <td class="bg-default text-center">KO</td>
    <td class="text-info text-center"></td>
    <td class="text-center"></td>
    <td class="text-nowrap">{{ $log->created_at->format('d.m.Y H:i') }} Uhr</td>
    <td>{{ $log->note }}</td>
    <td class="text-nowrap">{{ $log->user->name }}</td>
</tr>