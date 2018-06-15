<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Request;
use Mss\Models\Article;

class HandscannerController extends Controller
{
    public function index() {
        return view('handscanner.index');
    }

    public function showArticle($articlenumber) {
        $article = Article::where('article_number', $articlenumber)->firstOrFail();

        return view('handscanner.show', compact('article'));
    }
}
