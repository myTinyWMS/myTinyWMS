<a href="{{ route('unit.show', $id) }}" class="table-action">@lang('Details')</a>
<form action="{{ route('unit.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    @if ($model->articles->count() > 0)
        <button class="table-action btn-link" title="@lang('Dieser Einheit sind noch Artikel zugeordnet. Sie kann nicht gelöscht werden!')" disabled="disabled">@lang('Löschen')</button>
    @else
        <button class="table-action btn-link" onclick="return confirm('@lang('Wirklich löschen?')')">@lang('Löschen')</button>
    @endif
</form>