<?php

namespace Mss\Http\Controllers;

use Illuminate\Support\Str;
use Mss\Models\Order;
use Mss\Models\Article;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    protected $results = [];

    public function process(Request $request) {
        $phrase = $request->get('query');

        if (preg_match('/^[0-9]{7,8}$/', $phrase)) {
            $order = Order::where('internal_order_number', $phrase)->first();
            if ($order) {
                $this->addResult(__('Bestellung'), $phrase, route('order.show', $order));
            }
        } elseif (preg_match('/^[0-9]{5}$/', $phrase)) {
            $article = Article::where('article_number', $phrase)->first();
            if ($article) {
                $this->addResult(__('Artikel'), $article->name.' ('.$phrase.')', route('article.show', $article), $article->status);
            }
        } else {
            $articles = Article::where('name', 'like', '%'.$phrase.'%')->get();
            if ($articles) {
                $articles->each(function ($article) {
                    $this->addResult(__('Artikel'), $article->name, route('article.show', $article), $article->status);
                });
            }
        }

        return response()->json(collect($this->results)->values());
    }

    protected function addResult($group, $name, $link, $status = null) {
        if (!array_key_exists($group, $this->results)) {
            $this->results[$group] = [
                'name' => $group,
                'items' => []
            ];
        }
        $this->results[$group]['items'][] = ['name' => Str::limit($name, 50), 'link' => $link, 'title' => $name, 'status' => $status];
    }
}
