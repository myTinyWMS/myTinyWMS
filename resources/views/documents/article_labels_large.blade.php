<style type="text/css">
    html, body {
        margin: 0;
        padding: 0;
    }
    div.page {
        transform: rotate(-90deg);
        transform-origin:49% 78%;
        -webkit-transform: rotate(-90deg);
        -webkit-transform-origin: 53% 78%;
        page-break-inside: avoid;
        height: 440px;
        width: 670px;
        overflow: hidden;
        position: relative;
    }
</style>

@foreach($articles as $article)
<div class="page">
    <div style="width: 250px; padding: 0; position:absolute; top: 0; left: 0;"><img src="data:image/png;base64,{{ $barcodes[$article->id] }}" /></div>
    <div style="width: 400px;  position:absolute; left: 290px; top: 50px;font-size: 120px;">{{ $article->internal_article_number ?? '#'.$article->id }}</div>
    <div style="width: 400px;  position:absolute; left: 0px; top: 255px;font-size: 14px;">{{ date("d.m.Y") }}</div>
    <div style="position: absolute; top: 270px; left: 0; font-size: 40px; line-height: 40px; height: 150px; width: 680px; overflow: hidden; padding-top: 15px;">{{ mb_substr(preg_replace('/[[:cntrl:]]/', '', $article->name), 0, 55, 'utf-8') }}</div>
</div>
@endforeach