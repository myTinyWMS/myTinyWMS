<?php

namespace Mss\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Mss\Models\Article;

class InventoryController extends Controller
{
    public function generate() {
        $articles = Article::where('inventory', true)->active()->orderedByName()->with(['unit', 'category'])->get();
        $groupedArticles = $articles->groupBy(function ($article) {
            return $article->category->name;
        })->ksort();

        $pdf = App::make('snappy.pdf.wrapper');
        return $pdf->loadView('documents.inventory', compact('groupedArticles'))->setPaper('a4')->setOrientation('landscape')->download('inventur_'.Carbon::now()->format('Y-m-d').'.pdf');
    }
}
