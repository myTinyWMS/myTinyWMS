<?php

namespace Mss\Http\Controllers\Article;

use Mss\Models\Article;
use Illuminate\Http\Request;
use Mss\Services\PrintLabelService;
use Mss\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;

class LabelController extends Controller
{
    public function printLabel(Request $request) {
        $articles = Article::whereIn('id', $request->get('article'))->orderedByArticleNumber()->get();
        $size = $request->get('label_size', 'small');
        $labelService = new PrintLabelService();
        $quantity = $request->get('label_quantity', 1);

        for($i=1; $i<=$quantity; $i++) {
            $labelService->printArticleLabels($articles, $size);
        }

        flash(__('Label werden gedruckt'), 'success');

        return redirect()->route('article.index');
    }

    /**
     * @param Article $article
     * @param string $size
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function printSingleLabel(Article $article, $size) {
        $labelService = new PrintLabelService();
        $labelService->printArticleLabels(new Collection([$article]), $size);

        flash(__('Label gedruckt'), 'success');

        return redirect()->route('article.show', $article);
    }
}
