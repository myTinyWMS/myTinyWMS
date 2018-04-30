<style type="text/css">
    div.page {
        page-break-inside: avoid;
        height: 440px;
        width: 685px;
        overflow: hidden;
        position: relative;
    }
</style>

@foreach($articles as $article)
<div class="page">
    <div style="width: 250px; padding: 0; position:absolute; top: 0; left: 0;"><img src="data:image/png;base64,{{ $barcodes[$article->id] }}" /></div>
    <div style="width: 130px;  position:absolute; left: 290px; top: 50px;font-size: 120px;">{{ $article->article_number }}</div>
    <div style="position: absolute; top: 280px; left: 0; font-size: 50px; line-height: 18px; padding-top: 7px; height: 150px; width: 680px; overflow: hidden">{{ mb_substr(preg_replace('/[[:cntrl:]]/', '', $article->name), 0, 55, 'utf-8') }}</div>
</div>
@endforeach