<?php

namespace Mss\Http\Controllers\Handscanner;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mss\Http\Controllers\Controller;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Category;
use Mss\Models\Inventory;

class InventoryController extends Controller
{
    public function start() {
        $inventories = Inventory::unfinished()->orderBy('created_at', 'DESC')->get();

        return view('handscanner.inventory.start', compact('inventories'));
    }

    public function new() {
        $inventory = Inventory::create([
            'started_by' => Auth::id()
        ]);

        $articles = Article::active()->where('inventory', Article::INVENTORY_TYPE_CONSUMABLES)->get();
        $articles->each(function ($article) use ($inventory) {
            $inventory->items()->create([
                'article_id' => $article->id
            ]);
        });

        return response()->redirectToRoute('handscanner.inventory.select_category', $inventory);
    }

    public function continue(Request $request) {
        $inventory = Inventory::findOrFail($request->get('inventory'));

        return response()->redirectToRoute('handscanner.inventory.select_category', $inventory);
    }

    public function selectCategory(Inventory $inventory) {
        $inventory->load(['items' => function ($query) {
            $query->unprocessed()->with('article.category');
        }]);

        $categories = $inventory->items->map(function ($item) {
            return $item->article->category;
        })->unique()->sortBy('name');

        return view('handscanner.inventory.select_category', compact('categories', 'inventory'));
    }

    public function selectArticle(Inventory $inventory, Category $category) {
        $inventory->load(['items' => function ($query) {
            $query->unprocessed()->with('article.category');
        }]);

        $items = $inventory->items->filter(function ($item) use ($category) {
            return ($item->article->category->is($category));
        });

        return view('handscanner.inventory.select_article', compact('items', 'category', 'inventory'));
    }

    public function process(Inventory $inventory, Category $category, $article_number) {
        $article = Article::where('article_number', $article_number)->firstOrFail();
        $item = $inventory->items->where('article_id', $article->id)->first();

        return view('handscanner.inventory.process', compact('article', 'inventory', 'category', 'item'));
    }

    public function processed(Inventory $inventory, Article $article, Request $request) {
        /* @var $article Article */
        $article->changeQuantity(($request->get('quantity') - $article->quantity), ArticleQuantityChangelog::TYPE_INVENTORY, 'Inventurupdate '.date("d.m.Y"));

        $item = $inventory->items->where('article', $article)->first();


        if ($item) {
            $item->processed_at = now();
            $item->processed_by = Auth::id();
            $item->save();

            flash('Änderung gespeichert')->success();

            return response()->redirectToRoute('handscanner.inventory.select_article', [$inventory, $article->category]);
        }

        flash('Fehler beim Speichern')->error();

        return response()->redirectToRoute('handscanner.inventory.select_article', [$inventory, $article->category]);
    }
}
