<?php

namespace Mss\Http\Controllers\Api;

use Mss\Models\User;
use Spatie\QueryBuilder\AllowedFilter;
use Validator;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Mss\Models\Article;
use Mss\Models\ArticleQuantityChangelog;
use Illuminate\Http\Request;
use Psy\Util\Json;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends ApiController {

    /**
     * @queryParam filter[name] Filter by internal article name
     * @queryParam filter[internal_article_number] Filter by internal article number
     * @queryParam filter[external_article_number] Filter by external article number
     * @queryParam filter[category_id] Filter by category id
     * @queryParam filter[inventory] Filter by inventory. Possible values: 0 = spare parts, 1 = consumables
     * @queryParam filter[cost_center] Filter by cost center
     * @queryParam filter[packaging_category] Filter by packaging category. Possible values: paper or plastics
     * @queryParam fields[article] Set fields to get only some columns back
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $this->authorize(User::API_ABILITY_ARTICLE_GET);

        $allowedFields = (new \ReflectionClass(Article::class))->getDefaultProperties()['fillable'];
        $allowedFields[] = 'id';

        $articles = QueryBuilder::for(Article::class)
            ->active()
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('internal_article_number'),
                AllowedFilter::exact('external_article_number'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('inventory'),
                AllowedFilter::exact('packaging_category'),
                AllowedFilter::exact('cost_center'),
                'name'
            ])
            ->allowedFields($allowedFields)
            ->get();

        return response()->json($articles);
    }

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
    public function changeQuantity(Request $request, Article $article) {
        $this->authorize(User::API_ABILITY_ARTICLE_EDIT);

        $validator = Validator::make($request->all(), [
            'change' => 'required|integer|gt:0',
            'note' => 'string|nullable',
            'type' => [
                'required',
                Rule::in(ArticleQuantityChangelog::getAvailableTypes())
            ]
        ]);

        if($validator->fails()) {
            return Response::create(Json::encode([
                'result' => 'failure',
                'error' => $validator->errors()
            ]), 400);
        }

        $note = $request->get('note') ?: 'Api Update';

        $change = intval($request->get('change'));
        if ($request->get('type') == ArticleQuantityChangelog::TYPE_OUTGOING && $change > 0) {
            $change *= -1;
        }

        $article->changeQuantity($change, $request->get('type'), $note);

        return Response::create(Json::encode(['result' => 'success']));
    }
}
