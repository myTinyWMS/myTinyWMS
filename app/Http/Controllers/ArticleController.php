<?php

namespace Mss\Http\Controllers;

use Carbon\Carbon;
use Mss\Http\Requests\ChangeArticleQuantityRequest;
use Mss\Http\Requests\NewArticleRequest;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\ArticleSupplier;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mss\DataTables\ArticleDataTable;
use Mss\Http\Requests\ArticleRequest;
use Mss\Models\Tag;
use Mss\Models\Unit;
use Mss\Services\PrintLabelService;
use Webpatser\Uuid\Uuid;

class ArticleController extends Controller
{
    public function index(ArticleDataTable $articleDataTable) {
        $preSelectedSupplier = request('supplier', null);
        $categories = Category::orderedByName()->get();
        $supplier = Supplier::orderedByName()->get();
        $tags = Tag::orderedByName()->get();

        return $articleDataTable->render('article.list', compact('categories', 'supplier', 'tags', 'preSelectedSupplier'));
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
        $article = Article::create($request->all());

        // tags
        collect($request->get('tags'))->each(function ($tagValue) use ($article) {
            if (preg_match('/^newTag_(.+)/', $tagValue, $matches)) {
                $tag = Tag::firstOrCreate(['name' => $matches[1]]);
                $article->tags()->attach($tag);
            } else {
                $article->tags()->attach($tagValue);
            }
        });

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
        collect($request->get('tags'))->each(function ($tagValue) use ($article) {
            if (preg_match('/^newTag_(.+)/', $tagValue, $matches)) {
                $tag = Tag::firstOrCreate(['name' => $matches[1]]);
                $article->tags()->attach($tag);
            } else {
                $article->tags()->attach($tagValue);
            }
        });

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    public function reorder(Request $request) {
        $count = 0;

        if (count($request->json()->all())) {
            $ids = $request->json()->all();
            foreach($ids as $i => $key) {
                $id = $key['id'];
                $position = $key['position'];
                $mymodel = Article::find($id);
                $mymodel->sort_id = $position;
                if($mymodel->save()) {
                    $count++;
                }
            }
            $response = 'send response records updated goes here';
            return response()->json( $response );
        } else {
            $response = 'send nothing to sort response goes here';
            return response()->json( $response );
        }
    }

    public function addNote(Article $article, Request $request) {
        $note = $article->articleNotes()->create([
            'content' => $request->get('content'),
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'createdDiff' => 'gerade eben',
            'user' => $note->user->name,
            'content' => $note->content,
            'createdFormatted' => $note->created_at->format('d.m.Y - H:i'),
            'id' => $note->id
        ]);
    }

    public function deleteNote(Article $article, Request $request) {
        return $article->articleNotes()->where('id', $request->get('note_id'))->delete();
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

    public function quantityChangelog(Article $article, Request $request) {
        $dateStart = $request->has('start') ? Carbon::parse($request->get('start')) : Carbon::now()->subMonth(12);
        $dateEnd = $request->has('end') ? Carbon::parse($request->get('end'))->addDay() : Carbon::now();
        $changelog = $article->quantityChangelogs()->with(['user', 'unit', 'deliveryItem.delivery.order'])->latest()->whereBetween('created_at', [$dateStart, $dateEnd])->paginate(100);

        $all = $article->quantityChangelogs()->oldest()->whereBetween('created_at', [$dateStart, $dateEnd])->get();

        $chartLabels = $all->groupBy(function ($item) {
            return $item->created_at->formatLocalized('%b %Y');
        })->keys();

        $chartValues = $all->groupBy(function ($item) {
            if ($item->type == ArticleQuantityChangelog::TYPE_INVENTORY) {
                return ($item->change < 0) ? ArticleQuantityChangelog::TYPE_OUTGOING : ArticleQuantityChangelog::TYPE_INCOMING;
            }

            return $item->type;
        })->transform(function ($group, $type) use ($chartLabels) {
            $data = $group->groupBy(function ($item) {
                return $item->created_at->formatLocalized('%b %Y');
            })->transform(function ($items) {
                return $items->sum('change');
            });

            $chartLabels->each(function ($label) use ($data) {
                if (!$key = $data->has($label)) {

                    $data[$label] = 0;
                }
            });

            $data = $data->mapWithKeys(function ($item, $key) use ($chartLabels) {
                return [$chartLabels->search($key) => $item];
            });

            $data = $data->toArray();
            ksort($data);
            return collect($data);
        });

        return view('article.quantity_changelog', compact('article', 'changelog', 'dateStart', 'dateEnd', 'chartLabels', 'chartValues'));
    }

    public function deleteQuantityChangelog(Article $article, ArticleQuantityChangelog $changelog) {
        if ($changelog->deliveryItem) {
            $delivery = $changelog->deliveryItem->delivery;
            if ($delivery && $delivery->items()->count() == 0) {
                $order = $delivery->order;
                $delivery->delete();
                flash('Lieferung zur Bestellung '.link_to_route('order.show', $order->internal_order_number, $order).' gelöscht, da keine Artikel mehr vorhanden', 'warning');
            }

            $changelog->deliveryItem->delete();
        }

        $change = $changelog->change * -1;
        $article->quantity += $change;
        $article->save();

        $changelog->delete();

        flash('Bestandsänderung gelöscht');
        return redirect()->route('article.show', $article);
    }

    public function changeSupplier(Article $article, Request $request) {
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

        flash('Lieferantendaten gespeichert', 'success');
        return redirect()->route('article.show', $article);
    }

    public function printLabel(Request $request) {
        $articles = Article::whereIn('id', $request->get('article'))->orderedByArticleNumber()->get();
        $size = $request->get('label_size', 'small');
        $labelService = new PrintLabelService();
        $quantity = $request->get('label_quantity', 1);

        for($i=1; $i<=$quantity; $i++) {
            $labelService->printArticleLabels($articles, $size);
        }

        flash('Label werden gedruckt', 'success');

        return redirect()->route('article.index');
    }

    public function fixInventoryForm() {
        $articles = Article::active()->with('category')->withCurrentSupplier()->withCurrentSupplierName()->get()->groupBy(function ($article) {
            return $article->category->name;
        })->ksort();
        $units = Unit::orderedByName()->pluck('name', 'id');

        return view('article.fix_inventory', compact('articles', 'units'));
    }

    public function fixInventorySave(Request $request) {
        Article::whereIn('id', array_keys($request->get('inventory')))->get()->each(function ($article) use ($request) {
            $article->inventory = $request->get('inventory')[$article->id];
            $article->save();
        });

        Article::whereIn('id', array_keys($request->get('unit_id')))->get()->each(function ($article) use ($request) {
            $newUnitId = intval($request->get('unit_id')[$article->id]);
            if (!empty($newUnitId) && $article->unit_id !== $newUnitId) {
                $article->unit_id = $newUnitId;
                $article->save();
            }
        });

        flash('Änderungen gespeichert');

        return response()->redirectToRoute('article.fix_inventory_form', 'success');
    }

    public function changeChangelogNote(Request $request) {
        $changelog = ArticleQuantityChangelog::findOrFail($request->get('id'));
        $changelog->note = $request->get('content');
        $changelog->save();

        return response('', 200);
    }

    public function fileUpload(Article $article, Request $request) {
        $file = $request->file('file');
        $filename = $article->id.'_'.Uuid::generate(4)->string;
        $upload_success = $file->storeAs('article_files', $filename);
        if ($upload_success) {
            $files = $article->files;
            $files[] = [
                'orgName' => $file->getClientOriginalName(),
                'mimeType' => $file->getClientMimeType(),
                'storageName' => $filename
            ];
            $article->files = $files;
            $article->save();
            return response()->json($upload_success, 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function fileDownload(Article $article, $file) {
        $attachment = $article->files[$file];
        return response()->download(storage_path('app/article_files/'.$attachment['storageName']), $attachment['orgName'], ['Content-Type' => $attachment['mimeType']]);
    }
}
