<?php

namespace Mss\Http\Controllers;

use Illuminate\Http\Request;
use Mss\Models\Article;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \Mss\ArticleController  $articleController
     * @return \Illuminate\Http\Response
     */
    public function show(ArticleController $articleController)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Mss\ArticleController  $articleController
     * @return \Illuminate\Http\Response
     */
    public function edit(ArticleController $articleController)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Mss\ArticleController  $articleController
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ArticleController $articleController)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Mss\ArticleController  $articleController
     * @return \Illuminate\Http\Response
     */
    public function destroy(ArticleController $articleController)
    {
        //
    }

    public function reorder(Request $request) {
        $count = 0;

        if (count($request->json()->all())) {
            $ids = $request->json()->all();
            foreach($ids as $i => $key) {
                $id = $key['id'];
                $position = $key['position'];
                $mymodel = Article::find($id);
                $mymodel->sort_id = $position;
                if($mymodel->save()) {
                    $count++;
                }
            }
            $response = 'send response records updated goes here';
            return response()->json( $response );
        } else {
            $response = 'send nothing to sort response goes here';
            return response()->json( $response );
        }
    }
}
