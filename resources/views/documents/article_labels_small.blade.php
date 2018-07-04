<style type="text/css">
    @import 'https://fonts.googleapis.com/css?family=Roboto+Mono';

    div.page {
        page-break-inside: avoid;
        padding-top: 15px;
    }

    div.page:not(:last-child) {
        page-break-after: always;
    }
</style>

@foreach($articles as $article)
<div class="page">
    <div style="width: 90px; padding: 0; float: left; text-align: left;">
        <img src="data:image/png;base64,{{ $barcodes[$article->id] }}" />
        <br>
        <span style="font-size: 11px">{{ date("d.m.Y") }}</span>
    </div>
    <div style="width: 170px;  float: right; text-align: center;">
        <span style="font-size: 43px;">{{ $article->article_number ?? '#'.$article->id }}</span>
    </div>
    <div style="clear: both; font-size: 16px; line-height: 18px; padding-top: 7px; font-family: 'Roboto Mono';word-break: break-all;">{{ substr(preg_replace('/[\r\n\t]/', '', $article->name), 0, 52) }}</div>
</div>
@endforeach