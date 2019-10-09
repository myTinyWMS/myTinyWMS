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
    public function index(ArticleDataTable $articleDataTable) {
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
     */
    public function create() {
        $article = new Article();

        return view('article.create', compact('article'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewArticleRequest $request) {
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
            flash('Fehler beim Anlegen des Artikels')->error();
            DB::rollBack();
            return back()->withInput();
        }

        DB::commit();
        flash('Artikel angelegt')->success();

        return redirect()->route('article.show', $article);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        /** @var Article $article */
        $article = Article::withCurrentSupplier()->withCurrentSupplierArticle()->findOrFail($id);

        $context = [
            "article" => $article,
            "audits" => $article->getAllAudits()
        ];

        return view('article.show', $context);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ArticleRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ArticleRequest $request, $id) {
        /* @var $article Article */
        $article = Article::findOrFail($id);

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

        flash('Artikel gespeichert')->success();

        return redirect()->route('article.show', $article);
    }

    public function fixQuantityChange(Article $article, FixArticleQuantityChangeRequest $request) {
        $quantity = $request->get('changelogChange');
        if ($request->get('changelogChangeType') === 'sub') {
            $quantity *= -1;
        }

        $article->changeQuantity($quantity, $request->get('changelogType'), $request->get('changelogNote'), null, $request->get('changelogRelatedId'));

        flash('Bestand geändert')->success();

        return redirect()->route('article.show', $article);
    }

    public function changeQuantity(Article $article, ChangeArticleQuantityRequest $request) {
        $quantity = $request->get('changelogChange');
        if ($request->get('changelogChangeType') === 'sub') {
            $quantity *= -1;
        }

        $article->changeQuantity($quantity, $request->get('changelogType'), $request->get('changelogNote'));

        flash('Bestand geändert')->success();

        return redirect()->route('article.show', $article);
    }
}
