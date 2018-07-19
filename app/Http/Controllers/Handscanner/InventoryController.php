<?php

namespace Mss\Http\Controllers\Handscanner;

use Mss\Http\Controllers\Controller;
use Mss\Models\Article;

class InventoryController extends Controller
{
    public function step1() {
        return view('handscanner.inventory.step1');
    }
    public function step2($articlenumber) {
        $article = Article::where('article_number', $articlenumber)->firstOrFail();

        return view('handscanner.inventory.step2', compact('article'));
    }
}
