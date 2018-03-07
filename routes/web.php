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

    Route::post('category/print-list', 'CategoryController@printList')->name('category.print_list');

    Route::post('global-search', 'GlobalSearchController@process')->name('global_search');

    Route::get('order/article_list/{supplier}', 'OrderController@articleList')->name('order.article_list');
    Route::get('order/{order}/cancel', 'OrderController@cancel')->name('order.cancel');
    Route::get('order/{order}/create-delivery', 'OrderController@createDelivery')->name('order.create_delivery');
    Route::post('order/{order}/store-delivery', 'OrderController@storeDelivery')->name('order.store_delivery');
    Route::get('order/message/{message}/attachment-download/{attachment}', 'OrderController@messageAttachmentDownload')->name('order.message_attachment_download');

    Route::post('article/reorder', 'ArticleController@reorder')->name('article.reorder');
    Route::post('article/{article}/addnote', 'ArticleController@addNote')->name('article.add_note');
    Route::post('article/{article}/deletenote', 'ArticleController@deleteNote')->name('article.delete_note');
    Route::get('article/{article}/quantity-changelog', 'ArticleController@quantityChangelog')->name('article.quantity_changelog');
    Route::post('article/{article}/change-quantity', 'ArticleController@changeQuantity')->name('article.change_quantity');
});
