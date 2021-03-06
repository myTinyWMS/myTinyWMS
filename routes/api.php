<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/v1')->group(function () {
    Route::get('/article', 'ArticleController@index');
    Route::post('/article/getQuantities', 'ArticleController@getQuantities');
    Route::get('/article/{article}', 'ArticleController@show');
    Route::post('/article/{article}/changeQuantity', 'ArticleController@changeQuantity');

    Route::get('/article-group', 'ArticleGroupController@index');
    Route::get('/article-group/{articleGroup}', 'ArticleGroupController@show');
    Route::post('/article-group/{articleGroup}/changeQuantity', 'ArticleGroupController@changeQuantity');
});
