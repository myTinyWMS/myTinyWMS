<a href="{{ route('supplier.show', $id) }}" class="table-action">@lang('Details')</a>
<form action="{{ route('supplier.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    @if ($model->articles->count() > 0)
        <button class="btn-link table-action mt-2" data-toggle="tooltip" data-placement="left" title="Diesem Lieferanten sind noch Artikel zugeordnet. Er kann nicht gelöscht werden!" disabled="disabled">@lang('Löschen')</button>
    @else
        <button class="btn-link table-action mt-2" onclick="return confirm('@lang('Wirklich löschen?')')">@lang('Löschen')</button>
    @endif
</form>