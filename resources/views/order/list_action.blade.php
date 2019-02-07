<a href="{{ route('order.show', $id) }}" class="table-action">Details</a>

<form action="{{ route('order.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    <button class="table-action btn-link mt-2" onclick="return confirm('Wirklich löschen?')">Löschen</button>
</form>