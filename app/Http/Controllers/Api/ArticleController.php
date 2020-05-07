<?php

namespace Mss\Http\Controllers\Api;

use Mss\Models\User;
use Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Illuminate\Http\Request;
use Psy\Util\Json;

class ArticleController extends ApiController {
    /**
     * Display the specified resource.
     *
     * @param  Article $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article) {
        $this->authorize(User::API_ABILITY_ARTICLE_GET);

        return response()->json($article);
    }

    /**
     * @param Article $article
     * @param Request $request
     * @return Response
     */
    public function changeQuantity(Article $article, Request $request) {
        $this->authorize(User::API_ABILITY_ARTICLE_EDIT);

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
        $this->authorize(User::API_ABILITY_ARTICLE_GET);

        $ids = is_array($request->get('ids')) ? $request->get('ids') : explode(',', $request->get('ids'));
        $quantities = Article::whereIn('id', $ids)->pluck('quantity', 'id');

        return Response::create(Json::encode($quantities));
    }
}
