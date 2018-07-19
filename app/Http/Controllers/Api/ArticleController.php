<?php

namespace Mss\Http\Controllers\Api;

use Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Mss\Http\Controllers\Controller;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\Legacy\Material;
use Illuminate\Http\Request;
use Psy\Util\Json;

class ArticleController extends Controller {
    /**
     * Display the specified resource.
     *
     * @param  Article $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article) {
        return response()->json($article);
    }

    /**
     * @param Article $article
     * @param ChangeArticleQuantityRequest $request
     * @return Response
     */
    public function changeQuantity(Article $article, Request $request) {
        $validator = Validator::make($request->all(), [
            'change' => 'required|integer',
            'type' => [
                'required',
                Rule::in(ArticleQuantityChangelog::getAvailableTypes())
            ]
        ]);

        if($validator->fails()) {
            return Response::create(Json::encode(['result' => 'failure']), 400);
        }

        $note = $request->get('note') ?: 'Api Update';

        $change = intval($request->get('change'));
        if ($request->get('type') == ArticleQuantityChangelog::TYPE_OUTGOING && $change > 0) {
            $change *= -1;
        }

        $article->changeQuantity($change, $request->get('type'), $note);

        return Response::create(Json::encode(['result' => 'success']));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getQuantities(Request $request) {
        $ids = $request->get('ids');
        $articles = Article::find($ids);

        return Response::create(Json::encode($articles->pluck('quantity', 'id')));
    }
}
