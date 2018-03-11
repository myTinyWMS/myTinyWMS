@component('mail::message')

Sehr geehrte/geehrter {{ $order->supplier->contact_person ?: 'Damen und Herren' }},

hiermit bestellen wir folgende Artikel:

@component('mail::table')
    | Artikel       | Bestellnummer         | Menge   | Einzel-Preis   |
    |:------------- |:--------------------- |:-------:|:------- |
    @foreach($order->items as $item)
        | {{ $item->article->name }}      | {{ $item->article->currentSupplierArticle->order_number }}      | {{ $item->quantity }}      | {!! formatPrice($item->price) !!}      |
    @endforeach
@endcomponent

Bitte bestätigen Sie uns die Bestellung unter Angabe unserer Auftragsnummer "{{ $order->internal_order_number }}".

Mit freundlichen Grüßen

{{ \Illuminate\Support\Facades\Auth::user()->name }}<br><br>
Test GmbH<br>
Abt. Einkauf<br>
Teststraße 1<br>
01234 Test<br>

@endcomponent