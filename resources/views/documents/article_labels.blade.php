<style type="text/css" media="print">
    div.page
    {
        page-break-after: always;
        page-break-inside: avoid;
    }
</style>

@foreach($articles as $article)
<div class="page">
    <div style="width: 90px; padding: 0; float: left; text-align: left"><img src="data:image/png;base64,{{ $barcodes[$article->id] }}" /></div>
    <div style="width: 170px;  float: right; text-align: center">
        <span style="font-size: 43px;">{{ $article->article_number }}</span>
    </div>
    <div style="clear: both; font-size: 16px; line-height: 18px; padding-top: 7px">{{ mb_substr(preg_replace('/[[:cntrl:]]/', '', $article->name), 0, 55, 'utf-8') }}</div>
</div>
@endforeach