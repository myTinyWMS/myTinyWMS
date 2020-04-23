<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Mss\DataTables\InventoryDataTable;
use Mss\Http\Requests\UnitRequest;
use Illuminate\Http\Request;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Category;
use Mss\Models\Inventory;
use Mss\Services\InventoryService;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(InventoryDataTable $inventoryDataTable) {
        $this->authorize('inventory.view', Inventory::class);

        $closedInventories = Inventory::finished()->with('items.article.category')->get();

        return $inventoryDataTable->render('inventory.list', compact('closedInventories'));
    }

    /**
     * Display the specified resource.
     *
     * @param Inventory $inventory
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Inventory $inventory, Request $request) {
        $this->authorize('inventory.view', Inventory::class);

        $inventory->load('items.article.category', 'items.article.unit', 'items.processor');

        if ($inventory->isFinished()) {
            $items = $inventory->items->groupBy(function ($item) {
                return $item->article->category->name;
            });
        } else {
            $categories = InventoryService::getOpenCategories($inventory);
            $items = $categories->mapWithKeys(function ($category) use ($inventory) {
                return [$category->name => InventoryService::getOpenArticles($inventory, $category)];
            });
        }

        $categoryToPreselect = ($request->has('category_id')) ? Category::find($request->get('category_id')) : null;

        return view('inventory.show', compact('inventory', 'items', 'categoryToPreselect'));
    }

    /**
     * @param Inventory $inventory
     * @param Article $article
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function processed(Inventory $inventory, Article $article, Request $request) {
        $this->authorize('inventory.edit', Inventory::class);

        /* @var $article Article */

        $item = $inventory->items->where('article_id', $article->id)->first();

        if (!$item) {
            return response()->json(false);
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

        return response()->json(true);
    }

    /**
     * @param Inventory $inventory
     * @param Article $article
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function correct(Inventory $inventory, Article $article) {
        $this->authorize('inventory.edit', Inventory::class);

        /* @var $article Article */
        $item = $inventory->items->where('article_id', $article->id)->first();
        if ($item) {
            $item->processed_at = now();
            $item->processed_by = Auth::id();
            $item->save();

            flash(__('Änderung gespeichert'))->success();

            return response()->redirectToRoute('inventory.show', [$inventory, 'category_id' => $article->category_id]);
        }

        flash(__('Fehler beim Speichern'))->error();

        return response()->redirectToRoute('inventory.show', [$inventory, 'category_id' => $article->category_id]);
    }

    /**
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function createMonth() {
        $this->authorize('inventory.create', Inventory::class);

        $inventory = InventoryService::createNewMonthInventory();

        return response()->redirectToRoute('inventory.show', [$inventory]);
    }

    /**
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function createYear() {
        $this->authorize('inventory.create', Inventory::class);

        $inventory = InventoryService::createNewYearInventory();

        return response()->redirectToRoute('inventory.show', [$inventory]);
    }

    /**
     * @param Inventory $inventory
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function categoryDone(Inventory $inventory, Category $category) {
        $this->authorize('inventory.edit', Inventory::class);

        $inventory->load(['items' => function ($query) {
            $query->unprocessed()->with('article.category');
        }]);
        InventoryService::markCategoryAsDone($inventory, $category);

        flash(__('Kategorie abgeschlossen'))->success();

        return response()->redirectToRoute('inventory.show', [$inventory]);
    }

    /**
     * @param Inventory $inventory
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function finish(Inventory $inventory) {
        $this->authorize('inventory.edit', Inventory::class);

        $inventory->load(['items' => function ($query) {
            $query->unprocessed()->with('article.category');
        }]);

        InventoryService::getOpenCategories($inventory)->each(function ($category) use ($inventory) {
            InventoryService::markCategoryAsDone($inventory, $category);
        });

        flash(__('Inventur abgeschlossen'))->success();

        return response()->redirectToRoute('inventory.index');
    }
}
