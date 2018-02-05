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
        'order' => 'OrderController',
    ]);

    Route::get('order/article_list/{supplier}', 'OrderController@articleList')->name('order.article_list');

    Route::post('article/reorder', 'ArticleController@reorder')->name('article.reorder');
    Route::post('article/{article}/addnote', 'ArticleController@addNote')->name('article.add_note');
    Route::post('article/{article}/deletenote', 'ArticleController@deleteNote')->name('article.delete_note');
});
