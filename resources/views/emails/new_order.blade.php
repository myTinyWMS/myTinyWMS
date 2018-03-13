<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td align="left" valign="middle" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 14px; color: #353535; padding:3%; padding-top:40px; padding-bottom:40px;">
            <p>Sehr geehrte/geehrter {{ $order->supplier->contact_person ?: 'Damen und Herren' }},</p>

            <p>hiermit bestellen wir folgende Artikel:</p>
        </td>
    </tr>
    <tr>
        <td align="center">
            <table width="94%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #EEEEEE">
                <tr>
                    <td width="50%" align="left" bgcolor="#252525" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-right:0;">
                        <b>Artikel</b>
                    </td>
                    <td width="30%" align="center" bgcolor="#252525" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>Bestellnummer</b>
                    </td>
                    <td width="10%" align="center" bgcolor="#252525" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>Menge</b>
                    </td>
                    <td width="10%" align="center" bgcolor="#252525" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>Einzel-Preis</b>
                    </td>
                </tr>
                @foreach($order->items as $item)
                <tr>
                    <td width="50%" align="left" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-right:0;">
                        {{ $item->article->name }}
                    </td>
                    <td width="30%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                        {{ $item->article->currentSupplierArticle->order_number }}
                    </td>
                    <td width="10%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                        {{ $item->quantity }}
                    </td>
                    <td width="10%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                        {!! formatPrice($item->price) !!}
                    </td>
                </tr>
                @endforeach
            </table>
        </td>
    </tr>
    <tr>
        <td align="left" valign="middle" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 14px; color: #353535; padding:3%; padding-top:40px; padding-bottom:40px;">
            Bitte bestätigen Sie uns die Bestellung unter Angabe unserer Auftragsnummer "{{ $order->internal_order_number }}".
            <br/><br/><br/>
            Mit freundlichen Grüßen<br/>
            <br/>
            {{ \Illuminate\Support\Facades\Auth::user()->name }}<br><br>
            Test GmbH<br>
            Abt. Einkauf<br>
            Teststraße 1<br>
            01234 Test<br>
        </td>
    </tr>
</table>