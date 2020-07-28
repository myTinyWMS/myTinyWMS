<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr>
        <td align="left" valign="middle" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 14px; color: #353535; padding:3%; padding-top:40px; padding-bottom:40px;">
            <p>@lang('Für folgende Artikel ist soeben eine Lieferung eingegangen.')</p>

            <p>@lang('Bitte prüfen:')</p>
        </td>
    </tr>
    <tr>
        <td align="center">
            <table width="94%" border="0" cellpadding="0" cellspacing="0" style="border: 1px solid #EEEEEE">
                <tr>
                    <td width="10%" align="left" bgcolor="#252525" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-right:0;">
                        <b>@lang('Nr')</b>
                    </td>
                    <td width="50%" align="left" bgcolor="#252525" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-right:0;">
                        <b>@lang('Artikel')</b>
                    </td>
                    <td width="10%" align="center" bgcolor="#252525" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>@lang('Menge')</b>
                    </td>
                    <td width="30%" align="center" bgcolor="#252525" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #EEEEEE; padding:10px; padding-left:0;">
                        <b>@lang('Kategorie')</b>
                    </td>
                </tr>
                @foreach($articles as $article)
                    <tr>
                        <td width="10%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-right:0;">
                            {{ $article->internal_article_number }}
                        </td>
                        <td width="50%" align="left" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-right:0;">
                            {{ $article->name }}
                        </td>
                        <td width="10%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                            {{ $delivery->items->where('article_id', $article->id)->first()->quantity }}
                        </td>
                        <td width="30%" align="center" bgcolor="{{ $loop->index % 2 ? '#FFFFFF' : '#EEEEEE' }}" style="font-family: Verdana, Geneva, Helvetica, Arial, sans-serif; font-size: 12px; color: #252525; padding:10px; padding-left:0;">
                            {{ $article->category->name }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>
</table>