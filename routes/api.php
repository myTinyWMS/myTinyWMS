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

Route::middleware(['auth.basic.stateless'])->namespace('Api')->group(function () {
    /*Route::get('/user', function (Request $request) {
        return $request->user();
    });*/

    Route::get('/article/{article}', 'ArticleController@show');
    Route::post('/article/getQuantities', 'ArticleController@getQuantities');
    Route::post('/article/{article}/changeQuantity', 'ArticleController@changeQuantity');
});
