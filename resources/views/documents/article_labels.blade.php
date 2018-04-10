<style type="text/css" media="print">
    div.page
    {
        page-break-after: always;
        page-break-inside: avoid;
    }
</style>

@foreach($articles as $article)
<div class="page">
    <div style="text-align: center"><img src="data:image/png;base64,{{ $barcodes[$article->id] }}" /></div>
    <div style="text-align: center; margin-top: 12px; font-size: 16px; line-height: 18px;">{{ substr($article->name, 0, 65) }}</div>
</div>
@endforeach