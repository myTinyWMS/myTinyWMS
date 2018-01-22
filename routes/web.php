<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::resources([
        'article' => 'ArticleController',
        'supplier' => 'SupplierController',
        'category' => 'CategoryController',
    ]);

    Route::post('article/reorder', 'ArticleController@reorder')->name('article.reorder');
});
