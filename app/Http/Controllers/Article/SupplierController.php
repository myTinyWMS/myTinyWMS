<?php

namespace Mss\Http\Controllers\Article;

use Mss\Models\Article;
use Illuminate\Http\Request;
use Mss\Models\ArticleSupplier;
use Mss\Http\Controllers\Controller;

class SupplierController extends Controller
{
    public function store(Article $article, Request $request) {
        // reload with current supplier
        $article = Article::withCurrentSupplier()->withCurrentSupplierArticle()->find($article->id);

        if ($article->currentSupplier->id == $request->get('supplier')) {
            $supplierArticle = $article->currentSupplierArticle;
        } else {
            $supplierArticle = new ArticleSupplier();
            $supplierArticle->article_id = $article->id;
            $supplierArticle->supplier_id = $request->get('supplier');
        }

        $supplierArticle->order_number = $request->get('order_number');
        $supplierArticle->price = round(floatval(str_replace(',', '.', $request->get('price'))) * 100, 0);
        $supplierArticle->delivery_time = $request->get('delivery_time');
        $supplierArticle->order_quantity = $request->get('order_quantity');
        $supplierArticle->save();

        flash(__('Lieferantendaten gespeichert'), 'success');
        return redirect()->route('article.show', $article);
    }
}
