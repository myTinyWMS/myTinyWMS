<a href="{{ route('category.show', $id) }}" class="btn btn-primary btn-xs">Details</a>
<form action="{{ route('category.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    @if ($model->articles->count() > 0)
        <button class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="left" title="Dieser Kategorie sind noch Artikel zugeordnet. Sie kann nicht gelöscht werden!" disabled="disabled">Löschen</button>
    @else
        <button class="btn btn-danger btn-xs" onclick="return confirm('Wirklich löschen?')">Löschen</button>
    @endif
</form>