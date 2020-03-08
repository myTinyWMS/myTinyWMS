<a href="{{ route('user.show', $id) }}" class="table-action">@lang('Details')</a>
<form action="{{ route('user.destroy', $id) }}" class="list-form" method="POST">
    {{ method_field('DELETE') }}
    {{ csrf_field() }}

    <button class="btn-link table-action mt-2" onclick="return confirm('@lang('Wirklich löschen?')')">@lang('Löschen')</button>
</form>