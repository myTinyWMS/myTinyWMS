<?php

namespace Mss\Http\Controllers;

use Carbon\Carbon;
use Mss\Http\Requests\ChangeArticleQuantityRequest;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mss\DataTables\ArticleDataTable;
use Mss\Http\Requests\ArticleRequest;
use Mss\Models\Tag;

class ArticleController extends Controller
{
    public function index(ArticleDataTable $articleDataTable) {
        $categories = Category::orderedByName()->get();
        $supplier = Supplier::orderedByName()->get();
        $tags = Tag::orderedByName()->get();

        return $articleDataTable->render('article.list', compact('categories', 'supplier', 'tags'));
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
    public function store(ArticleRequest $request) {
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

        flash('Artikel angelegt')->success();

        return redirect()->route('article.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $article = Article::findOrFail($id);

        $context = [
            "article" => $article,
            "audits" => $article->getAudits()
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

        return redirect()->route('article.show', $id);
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
        $dateStart = $request->has('start') ? Carbon::parse($request->get('start')) : Carbon::now()->subMonth(3);
        $dateEnd = $request->has('end') ? Carbon::parse($request->get('end'))->addDay() : Carbon::now();
        $changelog = $article->quantityChangelogs()->with('user')->latest()->whereBetween('created_at', [$dateStart, $dateEnd])->paginate(10);

        $all = $article->quantityChangelogs()->oldest()->whereBetween('created_at', [$dateStart, $dateEnd])->get();

        $chartLabels = $all->groupBy(function ($item) {
            return $item->created_at->formatLocalized('%b %Y');
        })->keys();

        $chartValues = $all->groupBy(function ($item) {
            return $item->type;
        })->transform(function ($group, $type) use ($chartLabels) {
            $data = $group->groupBy(function ($item) {
                return $item->created_at->formatLocalized('%b %Y');
            })->transform(function ($items) {
                return $items->sum('change');
            });
            /*
             * @todo remove me!
             */
            if ($type == 1) {
                unset($data['Jan 2018']);
            }

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

        /*$quantities = $all->groupBy(function ($item) {
            return $item->created_at->formatLocalized('%b %Y');
        })->transform(function ($group) {
            return $group->sum('new_')
        });*/

        return view('article.quantity_changelog', compact('article', 'changelog', 'dateStart', 'dateEnd', 'chartLabels', 'chartValues'));
    }
}
