<?php

namespace Mss\Http\Controllers;

use Mss\Models\Order;
use Mss\Models\Article;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    protected $results = [];

    public function process(Request $request) {
        $phrase = $request->get('query');

        if (preg_match('/^[0-9]{8}$/', $phrase)) {
            $order = Order::where('internal_order_number', $phrase)->first();
            if ($order) {
                $this->addResult('Bestellung: '.$phrase, route('order.show', $order));
            }
        } else {
            $articles = Article::where('name', 'like', '%'.$phrase.'%')->get();
            if ($articles) {
                $articles->each(function ($article) {
                    $this->addResult($article->name, route('article.show', $article));
                });
            }
        }

        return response()->json($this->results);
    }

    protected function addResult($name, $link) {
        $this->results[] = ['name' => $name, 'link' => $link];
    }
}
