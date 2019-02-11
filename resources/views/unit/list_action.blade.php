<a href="{{ route('unit.show', $id) }}" class="table-action">Details</a>
<form action="{{ route('unit.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    @if ($model->articles->count() > 0)
        <button class="table-action btn-link mt-2" title="Dieser Einheit sind noch Artikel zugeordnet. Sie kann nicht gelöscht werden!" disabled="disabled">Löschen</button>
    @else
        <button class="table-action btn-link mt-2" onclick="return confirm('Wirklich löschen?')">Löschen</button>
    @endif
</form>