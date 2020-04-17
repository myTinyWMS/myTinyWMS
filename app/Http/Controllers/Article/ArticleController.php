<?php

namespace Mss\Http\Controllers\Article;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Mss\Models\Tag;
use Mss\Models\Article;
use Mss\Models\Supplier;
use Mss\Models\Category;
use Mss\Models\ArticleSupplier;
use Mss\Http\Controllers\Controller;
use Mss\DataTables\ArticleDataTable;
use Mss\Http\Requests\ArticleRequest;
use Mss\Http\Requests\NewArticleRequest;
use Mss\Http\Requests\ChangeArticleQuantityRequest;
use Mss\Http\Requests\FixArticleQuantityChangeRequest;

class ArticleController extends Controller
{
    /**
     * @param ArticleDataTable $articleDataTable
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(ArticleDataTable $articleDataTable) {
        $this->authorize('article.view', Article::class);

        $preSelectedSupplier = request('supplier', null);
        $preSelectedCategory = request('category', null);

        $categories = Category::orderedByName()->get();
        $supplier = Supplier::orderedByName()->get();
        $tags = Tag::orderedByName()->get();

        return $articleDataTable->render('article.list', compact('categories', 'supplier', 'tags', 'preSelectedSupplier', 'preSelectedCategory'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create() {
        $this->authorize('article.create', Article::class);

        $article = new Article();
        $isNewArticle = true;
        $isCopyOfArticle = false;

        return view('article.create', compact('article', 'isNewArticle', 'isCopyOfArticle'));
    }

    /**
     * @param Article $article
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function copy(Article $article) {
        $this->authorize('article.create', Article::class);

        $article = Article::withCurrentSupplier()->withCurrentSupplierArticle()->findOrFail($article->id);
        $isNewArticle = false;
        $isCopyOfArticle = true;

        return view('article.create', compact('article', 'isNewArticle', 'isCopyOfArticle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(NewArticleRequest $request) {
        $this->authorize('article.create', Article::class);

        DB::beginTransaction();

        try {
            $article = Article::create($request->all());

            // tags
            if (!empty($request->get('tags'))) {
                collect(\GuzzleHttp\json_decode($request->get('tags'), true))->each(function ($tagValue) use ($article) {
                    if (!array_key_exists('id', $tagValue)) {
                        $tag = Tag::firstOrCreate(['name' => $tagValue['value']]);
                        $article->tags()->attach($tag);
                    } else {
                        $article->tags()->attach(Tag::where('name', $tagValue['value'])->firstOrFail());
                    }
                });
            }

            // categories
            if (!empty($request->get('category'))) {
                $article->category()->associate($request->get('category'));
                $article->save();
                $article->load('category');

                $article->setNewArticleNumber();
            }

            // supplier
            ArticleSupplier::create([
                'article_id' => $article->id,
                'supplier_id' => $request->get('supplier_id'),
                'order_number' => $request->get('supplier_order_number') ?? 0,
                'delivery_time' => $request->get('supplier_delivery_time'),
                'order_quantity' => $request->get('supplier_order_quantity'),
                'price' => round(floatval(str_replace(',', '.', $request->get('supplier_price'))) * 100, 0)
            ]);
        } catch (QueryException $e) {
            flash(__('Fehler beim Anlegen des Artikels'))->error();
            DB::rollBack();
            return back()->withInput();
        }

        DB::commit();
        flash(__('Artikel angelegt'))->success();

        return redirect()->route('article.show', $article);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show($id) {
        $this->authorize('article.view', Article::class);

        /** @var Article $article */
        $article = Article::withCurrentSupplier()->withCurrentSupplierArticle()->with('articleGroupItems.articleGroup')->findOrFail($id);

        $context = [
            "article" => $article,
            "audits" => $article->getAllAudits()
        ];

        return view('article.show', $context);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ArticleRequest $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(ArticleRequest $request, $id) {
        $this->authorize('article.edit', Article::class);

        /* @var $article Article */
        $article = Article::findOrFail($id);

        if ($article->quantity > 0 && $article->status != Article::STATUS_INACTIVE && $request->get('status') == Article::STATUS_INACTIVE) {
            flash(__('Der Status darf nicht auf deaktiviert gesetzt werden, solange der Artikel noch Bestand hat!'))->error();

            return back()->withInput();
        }

        // save data
        $article->update($request->all());
        $article->inventory = $request->get('inventory', false);
        $article->save();

        // tags
        $article->tags()->detach();
        if (!empty($request->get('tags'))) {
            collect(\GuzzleHttp\json_decode($request->get('tags'), true))->each(function ($tagValue) use ($article) {
                if (!array_key_exists('id', $tagValue)) {
                    $tag = Tag::firstOrCreate(['name' => $tagValue['value']]);
                    $article->tags()->attach($tag);
                } else {
                    $article->tags()->attach(Tag::where('name', $tagValue['value'])->firstOrFail());
                }
            });
        }

        // categories
        if ($request->get('category') != $article->category_id && $request->get('changeCategory') == 1 && !empty($request->get('category'))) {
            $article->category()->associate($request->get('category'));
            $article->save();
            $article->load('category');

            $article->setNewArticleNumber();
        }

        flash(__('Artikel gespeichert'))->success();

        return redirect()->route('article.show', $article);
    }

    /**
     * @param Article $article
     * @param FixArticleQuantityChangeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function fixQuantityChange(Article $article, FixArticleQuantityChangeRequest $request) {
        $this->authorize('article.edit', Article::class);

        $quantity = $request->get('changelogChange');
        if ($request->get('changelogChangeType') === 'sub') {
            $quantity *= -1;
        }

        $article->changeQuantity($quantity, $request->get('changelogType'), $request->get('changelogNote'), null, $request->get('changelogRelatedId'));

        flash(__('Bestand geändert'))->success();

        return redirect()->route('article.show', $article);
    }

    /**
     * @param Article $article
     * @param ChangeArticleQuantityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function changeQuantity(Article $article, ChangeArticleQuantityRequest $request) {
        $this->authorize('article.edit', Article::class);

        $quantity = $request->get('changelogChange');
        if ($request->get('changelogChangeType') === 'sub') {
            $quantity *= -1;
        }

        $article->changeQuantity($quantity, $request->get('changelogType'), $request->get('changelogNote'));

        flash(__('Bestand geändert'))->success();

        return redirect()->route('article.show', $article);
    }
}
