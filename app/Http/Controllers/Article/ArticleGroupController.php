<?php

namespace Mss\Http\Controllers\Article;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Mss\DataTables\ArticleGroupDataTable;
use Mss\DataTables\SelectArticleDataTable;
use Mss\Http\Controllers\Controller;
use Mss\Http\Requests\ArticleGroupChangeQuantityRequest;
use Mss\Models\Article;
use Mss\Models\ArticleGroup;
use Mss\Models\ArticleGroupItem;

class ArticleGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(ArticleGroupDataTable $articleGroupDataTable) {
        $this->authorize('article-group.view', ArticleGroup::class);

        return $articleGroupDataTable->render('article_group.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(SelectArticleDataTable $selectArticleDataTable) {
        $this->authorize('article-group.create', ArticleGroup::class);

        $articleGroup = new ArticleGroup();
        $allArticles = $this->getArticleList();
        $preSetArticles = collect();
        $selectArticleDataTable->paging = true;

        return $selectArticleDataTable->render('article_group.create', compact('articleGroup', 'allArticles', 'preSetArticles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request) {
        $this->authorize('article-group.create', ArticleGroup::class);

        if ($request->has('id')) {
            $articleGroup = ArticleGroup::findOrFail($request->get('id'));
            $articleGroup->name = $request->get('name');
            $articleGroup->save();
        } else {
            $articleGroup = ArticleGroup::create(['name' => $request->get('name')]);
        }

        // save order items
        $existingItemIds = $articleGroup->items->pluck('id');
        $updatedItemIds = collect();
        collect(\GuzzleHttp\json_decode($request->get('article_data'), true))->each(function ($item) use ($articleGroup, $updatedItemIds) {
            if (!empty($item['article_group_item_id'])) {
                $orderItem = ArticleGroupItem::findOrFail($item['article_group_item_id']);
                $orderItem->quantity = intval($item['quantity']);
                $orderItem->save();
                $updatedItemIds->push($orderItem->id);
            } else {
                $articleGroup->items()->create([
                    'article_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }
        });

        $missingItemIds = $existingItemIds->diff($updatedItemIds);
        ArticleGroupItem::whereIn('id', $missingItemIds)->delete();

        flash(__('Artikelgruppe gespeichert'), 'success');

        return redirect()->route('article-group.show', $articleGroup);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id) {
        $this->authorize('article-group.view', ArticleGroup::class);

        $articleGroup = ArticleGroup::with(['items.article' => function($query) {
            $query->withCurrentSupplier();
        }])->findOrFail($id);
        $audits = $articleGroup->getAudits();

        return view('article_group.show', compact('articleGroup', 'audits'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @param SelectArticleDataTable $selectArticleDataTable
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($id, SelectArticleDataTable $selectArticleDataTable) {
        $this->authorize('article-group.edit', ArticleGroup::class);

        $articleGroup = ArticleGroup::findOrFail($id);
        $audits = $articleGroup->getAudits();
        $allArticles = $this->getArticleList();
        $selectArticleDataTable->paging = true;

        /* @var $articleGroup ArticleGroup */
        $preSetArticles = $articleGroup->items;
        $preSetArticles->transform(function ($item) use ($articleGroup) {
            return [
                'id' => $item->article->id,
                'article_number' => $item->article->article_number,
                'article_group_item_id' => $item->id,
                'name' => $item->article->name,
                'quantity' => $item->quantity
            ];
        });

        return $selectArticleDataTable->render('article_group.edit', compact('articleGroup', 'audits', 'allArticles', 'preSetArticles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, $id) {
        $this->authorize('article-group.edit', ArticleGroup::class);

        $request->merge(['id' => $id]);
        return $this->store($request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($id) {
        $this->authorize('article-group.delete', ArticleGroup::class);

        $articleGroup = ArticleGroup::findOrFail($id);
        $articleGroup->items()->delete();
        $articleGroup->delete();

        flash(__('Artikelgruppe gelöscht'), 'success');

        return redirect()->route('article-group.index');
    }

    /**
     * @param $id
     * @param ArticleGroupChangeQuantityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function changeQuantity($id, ArticleGroupChangeQuantityRequest $request) {
        $this->authorize('article-group.edit', ArticleGroup::class);

        $quantities = collect($request->get('quantity', []));

        $articleGroup = ArticleGroup::with('items.article')->findOrFail($id);
        $articleGroup->items->each(function ($item) use ($quantities, $request) {
            /**@var $item ArticleGroupItem */
            $quantity = $quantities->get($item->id, 0);

            if ($request->get('changelogChangeType') === 'sub') {
                $quantity *= -1;
            }

            $item->article->changeQuantity($quantity, $request->get('changelogType'), $request->get('changelogNote'));
        });

        flash(__('Bestand der Artikel geändert'))->success();

        return redirect()->route('article-group.show', $articleGroup);
    }

    /**
     * @return Collection
     */
    protected function getArticleList() {
        return Article::enabled()->with(['suppliers', 'category'])->withCurrentSupplier()->withCurrentSupplierArticle()->orderBy('name')->get()
            ->filter(function ($article) {
                return !empty($article->currentSupplierArticle);
            })
            ->transform(function ($article) {
                /**@var $article Article */
                $deliveryTime = intval($article->currentSupplierArticle->delivery_time);
                $deliveryDate = Carbon::now()->addWeekdays($deliveryTime);
                return [
                    'id' => $article->id,
                    'article_number' => $article->article_number,
                    'name' => $article->name/*.(!empty($article->unit) ? ' ('.$article->unit->name.')' : '')*/,
                    'supplier_id' => $article->currentSupplier->id,
                    'category' => $article->category->name ?? '',
                    'order_notes' => $article->order_notes ?? '',
                    'delivery_date' =>  $deliveryDate->format('Y-m-d'),
                    'order_quantity' => $article->currentSupplierArticle->order_quantity ?? '',
                    'price' => $article->currentSupplierArticle->price ? formatPriceValue($article->currentSupplierArticle->price / 100) : ''
                ];
            });
    }
}
