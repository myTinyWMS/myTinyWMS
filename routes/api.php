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

Route::middleware(['auth:sanctum'])->namespace('Api')->group(function () {
    Route::prefix('/v1')->group(function () {
        Route::get('/article', 'ArticleController@index');
        Route::get('/article/{article}', 'ArticleController@show');
        Route::post('/article/getQuantities', 'ArticleController@getQuantities');
        Route::post('/article/{article}/changeQuantity', 'ArticleController@changeQuantity');
    });
});
