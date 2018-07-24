<?php

namespace Mss\Http\Controllers\Handscanner;

use Illuminate\Http\Request;
use Mss\Http\Controllers\Controller;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;

class InventoryController extends Controller
{
    public function step1() {
        return view('handscanner.inventory.step1');
    }
    public function step2($articlenumber) {
        $article = Article::where('article_number', $articlenumber)->firstOrFail();

        return view('handscanner.inventory.step2', compact('article'));
    }

    public function step3(Request $request) {
        /* @var $article Article */
        $article = Article::findOrFail($request->get('article'));
        $article->changeQuantity(($request->get('quantity') - $article->quantity), ArticleQuantityChangelog::TYPE_INVENTORY, 'Inventurupdate '.date("d.m.Y"));
        flash('Änderung gespeichert')->success();

        return response()->redirectToRoute('handscanner.inventory.step1');
    }
}
