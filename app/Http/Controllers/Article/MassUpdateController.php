<?php

namespace Mss\Http\Controllers\Article;

use Mss\Models\Unit;
use Mss\Models\Article;
use Illuminate\Http\Request;
use Mss\Http\Controllers\Controller;

class MassUpdateController extends Controller
{
    public function index() {
        $articles = Article::enabled()->with('category')->withCurrentSupplier()->withCurrentSupplierName()->get()->groupBy(function ($article) {
            return optional($article->category)->name;
        })->ksort();
        $units = Unit::orderedByName()->pluck('name', 'id');

        return view('article.mass_update', compact('articles', 'units'));
    }

    public function store(Request $request) {
        Article::whereIn('id', array_keys($request->get('inventory')))->get()->each(function ($article) use ($request) {
            $article->inventory = $request->get('inventory')[$article->id];

            if ($request->has('unit_id')) {
                $newUnitId = array_key_exists($article->id, $request->get('unit_id')) ? intval($request->get('unit_id')[$article->id]) : null;
                if (!empty($newUnitId) && $article->unit_id !== $newUnitId) {
                    $article->unit_id = $newUnitId;
                }
            }

            $article->save();
        });

        flash('Änderungen gespeichert');

        return response()->redirectToRoute('article.mass_update_form', 'success');
    }
}
