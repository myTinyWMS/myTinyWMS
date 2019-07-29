<?php

namespace Mss\Http\Controllers\Article;

use Mss\Models\Unit;
use Mss\Models\Article;
use Illuminate\Http\Request;
use Mss\Http\Controllers\Controller;

class SortController extends Controller
{
    public function index() {
        $articles = Article::enabled()->with('category')->withCurrentSupplier()->withCurrentSupplierName()->orderBy('sort_id')->get()->groupBy(function ($article) {
            return optional($article->category)->name;
        })->ksort();
        $units = Unit::orderedByName()->pluck('name', 'id');

        return view('article.sort_update', compact('articles', 'units'));
    }

    public function store(Request $request) {
        Article::find(array_keys($request->get('list')))->each(function ($article) use ($request) {
            $article->sort_id = intval($request->get('list')[$article->id]);
            $article->save();
        });

        return response()->json(['status' => 'ok']);
    }
}
