<?php

namespace Mss\Http\Controllers;

use Mss\Models\Article;
use Mss\Models\Category;
use Mss\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mss\DataTables\ArticleDataTable;
use Mss\Http\Requests\ArticleRequest;

class ArticleController extends Controller
{
    public function index(ArticleDataTable $articleDataTable) {
        $categories = Category::all();
        $supplier = Supplier::all();

        return $articleDataTable->render('article.list', compact('categories', 'supplier'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleRequest $request) {
        Article::create($request->all());

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
        $article = Article::findOrFail($id);

        // save data
        $article->update($request->all());

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
}
