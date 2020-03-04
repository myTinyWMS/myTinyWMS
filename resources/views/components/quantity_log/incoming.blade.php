<td class="bg-success text-center" title="@lang('Wareneingang')">
    {{ \Mss\Models\ArticleQuantityChangelog::getAbbreviation(\Mss\Models\ArticleQuantityChangelog::TYPE_INCOMING) }}
</td>
<td class="text-success text-center">+{{ $log->change }}</td>
<td class="text-center">{{ $log->new_quantity }}</td>
@include('components.quantity_log._defaults')