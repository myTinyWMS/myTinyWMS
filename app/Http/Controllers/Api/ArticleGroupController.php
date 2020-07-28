<?php

namespace Mss\Http\Controllers\Api;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Mss\Models\ArticleGroup;
use Mss\Models\ArticleGroupItem;
use Mss\Models\ArticleQuantityChangelog;
use Mss\Models\User;
use Psy\Util\Json;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleGroupController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $this->authorize(User::API_ABILITY_ARTICLE_GROUP_GET);

        $groups = QueryBuilder::for(ArticleGroup::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('external_article_number'),
                'name'
            ])
            ->get();

        return response()->json($groups);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $this->authorize(User::API_ABILITY_ARTICLE_GROUP_GET);

        $group = ArticleGroup::with('items')->findOrFail($id);

        return response()->json($group);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function changeQuantity($id, Request $request) {
        $this->authorize(User::API_ABILITY_ARTICLE_GROUP_EDIT);

        $articleGroup = ArticleGroup::with('items.article')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'change' => 'required|integer',
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

        $articleGroup->items->each(function ($item) use ($request) {
            /**@var $item ArticleGroupItem */
            $quantity = $item->quantity * $request->get('change');
            $note = $request->get('note') ?: 'Api Update';

            if ($request->get('type') === ArticleQuantityChangelog::TYPE_OUTGOING) {
                $quantity *= -1;
            }

            $item->article->changeQuantity($quantity, $request->get('type'), $note);
        });


        return Response::create(Json::encode(['result' => 'success']));
    }
}
