<a href="{{ route('article-group.show', $id) }}" target="_blank" class="table-action">@lang('Details')</a>

@can('article-group.delete')
    <form action="{{ route('article-group.destroy', $id) }}" class="list-form" method="POST">
        {{ method_field('DELETE') }}
        {{ csrf_field() }}

        <button class="table-action btn-link" onclick="return confirm('Wirklich löschen?')">@lang('Löschen')</button>
    </form>
@endcan