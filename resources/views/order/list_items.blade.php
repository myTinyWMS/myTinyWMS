<div class="dropdown-menu group" style="width: 400px">
    <div class="dropdown-menu-header font-bold">
        {{ $order->items->count() }} Artikel
        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
    </div>
    <div class="dropdown-menu-list">
        <table class="w-full">
            <thead>
                <tr>
                    <th>Artikel</th>
                    <th>Bestellnummer</th>
                    <th>best. Menge</th>
                </tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td class="text-sm" style="white-space: normal!important;">{{ $item->article->name }}</td>
                    <td class="text-sm">{{ $item->article->currentSupplierArticle->order_number }}</td>
                    <td class="text-sm text-center">{{ $item->quantity }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>