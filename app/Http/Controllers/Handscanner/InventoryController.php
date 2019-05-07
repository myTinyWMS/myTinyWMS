<?php

namespace Mss\Http\Controllers\Handscanner;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mss\Http\Controllers\Controller;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Category;
use Mss\Models\Inventory;
use Mss\Services\InventoryService;

class InventoryController extends Controller
{
    public function start() {
        $inventories = Inventory::unfinished()->orderBy('created_at', 'DESC')->get();

        return view('handscanner.inventory.start', compact('inventories'));
    }

    public function new() {
        $inventory = InventoryService::createNewMonthInventory();

        return response()->redirectToRoute('handscanner.inventory.select_category', $inventory);
    }

    public function continue(Request $request) {
        $inventory = Inventory::findOrFail($request->get('inventory'));

        return response()->redirectToRoute('handscanner.inventory.select_category', $inventory);
    }

    public function selectCategory(Inventory $inventory) {
        $categories = InventoryService::getOpenCategories($inventory);

        return view('handscanner.inventory.select_category', compact('categories', 'inventory'));
    }

    public function selectArticle(Inventory $inventory, Category $category) {
        $items = InventoryService::getOpenArticles($inventory, $category);

        return view('handscanner.inventory.select_article', compact('items', 'category', 'inventory'));
    }

    public function process(Inventory $inventory, Category $category, $article_number) {
        $article = Article::where('article_number', $article_number)->firstOrFail();
        $item = $inventory->items->where('article_id', $article->id)->first();

        return view('handscanner.inventory.process', compact('article', 'inventory', 'category', 'item'));
    }

    public function processed(Inventory $inventory, Article $article, Request $request) {
        /* @var $article Article */

        $item = $inventory->items->where('article_id', $article->id)->first();

        if (!$item) {
            flash('Fehler beim Speichern')->error();

            return response()->redirectToRoute('handscanner.inventory.select_article', [$inventory, $article->category]);
        }

        $old = $article->quantity;
        $new = $request->get('quantity');
        $diff = ($new - $old);
        if ($diff !== 0) {
            $article->changeQuantity($diff, ArticleQuantityChangelog::TYPE_INVENTORY, 'Inventurupdate '.date("d.m.Y"));
        }

        $item->old_quantity = $old;
        $item->new_quantity = $new;
        $item->processed_at = now();
        $item->processed_by = Auth::id();
        $item->save();

        flash('Änderung gespeichert')->success();

        return response()->redirectToRoute('handscanner.inventory.select_article', [$inventory, $article->category]);
    }

    public function categoryProcessed(Inventory $inventory, Category $category) {
        $inventory->load(['items' => function ($query) {
            $query->unprocessed()->with('article.category');
        }]);
        InventoryService::markCategoryAsDone($inventory, $category);
        flash('Kategorie abgeschlossen')->success();

        return response()->redirectToRoute('handscanner.inventory.select_category', [$inventory]);
    }
}
