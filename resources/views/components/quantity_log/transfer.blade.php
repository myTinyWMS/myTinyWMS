<td class="bg-primary text-center" title="@lang('Umbuchung')">
    {{ \Mss\Models\ArticleQuantityChangelog::getAbbreviation(\Mss\Models\ArticleQuantityChangelog::TYPE_TRANSFER) }}
</td>
<td class="text-{{  $log->change >= 0 ? 'success' : 'danger' }} text-center">{{ $log->change >= 0 ? '+'.$log->change : $log->change }}</td>
<td class="text-center">{{ $log->new_quantity }}</td>
@include('components.quantity_log._defaults')