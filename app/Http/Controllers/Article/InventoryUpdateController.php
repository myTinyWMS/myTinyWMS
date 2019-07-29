<?php

namespace Mss\Http\Controllers\Article;

use Mss\Models\Article;
use Illuminate\Http\Request;
use Mss\Http\Controllers\Controller;
use Mss\Models\ArticleQuantityChangelog;

class InventoryUpdateController extends Controller
{

    public function index() {
        $articles = Article::enabled()->with(['category', 'unit'])->withCurrentSupplier()->withCurrentSupplierName()->get()->groupBy(function ($article) {
            return optional($article->category)->name;
        })->ksort();

        return view('article.inventory_update', compact('articles'));
    }

    public function store(Request $request) {
        Article::whereIn('id', array_keys($request->get('quantity')))->get()->each(function ($article) use ($request) {
            /* @var Article $article */
            $newQuantity = $request->get('quantity')[$article->id];
            if ($newQuantity != $article->quantity) {
                $article->changeQuantity(($newQuantity - $article->quantity), ArticleQuantityChangelog::TYPE_INVENTORY, 'Inventurupdate '.date("d.m.Y"));
            }
        });

        flash('Änderungen gespeichert', 'success');

        return response()->redirectToRoute('article.inventory_update_form');
    }
}
