<a href="{{ route('inventory.show', $id) }}" class="table-action">@lang('fortsetzen')</a>
@can('inventory.edit')
<a href="{{ route('inventory.finish', $id) }}" class="table-action" onclick="return confirm('@lang('Sicher?')')">@lang('abschließen')</a>
@endcan