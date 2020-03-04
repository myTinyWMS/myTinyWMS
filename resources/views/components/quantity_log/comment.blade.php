<td class="bg-default text-center" title="@lang('Kommentar')">
    {{ \Mss\Models\ArticleQuantityChangelog::getAbbreviation(\Mss\Models\ArticleQuantityChangelog::TYPE_COMMENT) }}
</td>
<td class="text-info text-center"></td>
<td class="text-center"></td>
@include('components.quantity_log._defaults')