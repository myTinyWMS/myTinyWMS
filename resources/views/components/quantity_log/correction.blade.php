<td class="bg-info text-center" title="@lang('Korrektur')">
    {{ \Mss\Models\ArticleQuantityChangelog::getAbbreviation(\Mss\Models\ArticleQuantityChangelog::TYPE_CORRECTION) }}
</td>
<td class="text-info text-center">{{ $log->change >= 0 ? '+'.$log->change : $log->change }}</td>
<td class="text-center">{{ $log->new_quantity }}</td>
@include('components.quantity_log._defaults')