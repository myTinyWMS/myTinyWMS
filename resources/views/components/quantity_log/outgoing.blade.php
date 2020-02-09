<td class="bg-danger text-center" title="@lang('Warenausgang')">
    {{ \Mss\Models\ArticleQuantityChangelog::getAbbreviation(\Mss\Models\ArticleQuantityChangelog::TYPE_OUTGOING) }}
</td>
<td class="text-danger text-center">{{ $log->change }}</td>
<td class="text-center">{{ $log->new_quantity }}</td>
@include('components.quantity_log._defaults')