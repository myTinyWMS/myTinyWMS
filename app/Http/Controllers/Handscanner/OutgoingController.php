<?php

namespace Mss\Http\Controllers\Handscanner;

use Mss\Models\Article;
use Mss\Http\Controllers\Controller;
use Mss\Models\ArticleQuantityChangelog;

class OutgoingController extends Controller
{
    public function start() {
        return view('handscanner.outgoing.start');
    }

    public function process($internal_article_number) {
        $article = Article::where('internal_article_number', $internal_article_number)->firstOrFail();

        return view('handscanner.outgoing.enter_quantity', compact('article'));
    }

    public function save(Article $article) {
        $change = intval(request('quantity'));

        if ($change > 0) {
            $article->changeQuantity(($change * -1), ArticleQuantityChangelog::TYPE_OUTGOING);
            flash('Warenausgang gespeichert')->success();

            return response()->redirectToRoute('handscanner.outgoing.start');
        } else {
            flash('Menge ungültig')->danger();

            return response()->redirectToRoute('handscanner.outgoing.process', ['internal_article_number' => $article->internal_article_number]);
        }
    }
}
