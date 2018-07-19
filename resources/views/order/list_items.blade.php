<div class="panel-group" id="accordion">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $order->id }}">anzeigen ({{ $order->items->count() }})</a>
            </h5>
        </div>
        <div id="collapse{{ $order->id }}" class="panel-collapse collapse">
            <div class="panel-body">
                <table class="table table-condensed">
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
                            <td>{{ $item->article->name }}</td>
                            <td>{{ $item->article->currentSupplierArticle->order_number }}</td>
                            <td>{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>