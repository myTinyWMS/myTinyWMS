<div class="dropdown-list group" style="width: 800px">
    <div class="flex items-center cursor-pointer text-sm rounded-t-lg py-1 px-2 font-bold text-gray-600">
        {{ $articleGroup->items->count() }} @lang('Artikel')
        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
    </div>
    <div class="dropdown-list-items">
        <table class="w-full">
            <thead>
                <tr>
                    <th>@lang('Artikel')</th>
                    <th>@lang('Menge')</th>
                </tr>
            </thead>
            <tbody>
            @foreach($articleGroup->items as $item)
                <tr>
                    <td class="text-sm" style="white-space: normal!important;">{{ $item->article->name }}</td>
                    <td class="text-sm text-center">{{ $item->quantity }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>