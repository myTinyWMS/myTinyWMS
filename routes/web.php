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
    Route::post('order/{order}/invoice-received', 'OrderController@invoiceReceived')->name('order.invoice_received');
    Route::post('order/{order}/confirmation-received', 'OrderController@confirmationReceived')->name('order.confirmation_received');
    Route::get('order/{order}/create-delivery', 'OrderController@createDelivery')->name('order.create_delivery');
    Route::post('order/{order}/store-delivery', 'OrderController@storeDelivery')->name('order.store_delivery');
    Route::get('order/message/{message}/attachment-download/{attachment}', 'OrderController@messageAttachmentDownload')->name('order.message_attachment_download');
    Route::get('order/{order}/message/new', 'OrderController@newMessage')->name('order.message_new');
    Route::post('order/{order}/message/new', 'OrderController@createNewMessage')->name('order.message_create');
    Route::post('order/{order}/message/{message}/delete', 'OrderController@deleteMessage')->name('order.message_delete');
    Route::get('order/{order}/message/{message}/read', 'OrderController@markRead')->name('order.message_read');
    Route::get('order/{order}/message/{message}/unread', 'OrderController@markUnread')->name('order.message_unread');
    Route::get('order/message/unassigned', 'OrderController@unassignedMessages')->name('order.messages_unassigned');

    Route::post('order/{order}/message/upload', 'OrderController@uploadNewAttachments')->name('order.message_upload');

    Route::post('article/reorder', 'ArticleController@reorder')->name('article.reorder');
    Route::post('article/{article}/addnote', 'ArticleController@addNote')->name('article.add_note');
    Route::post('article/{article}/deletenote', 'ArticleController@deleteNote')->name('article.delete_note');
    Route::get('article/{article}/quantity-changelog', 'ArticleController@quantityChangelog')->name('article.quantity_changelog');
    Route::post('article/{article}/change-quantity', 'ArticleController@changeQuantity')->name('article.change_quantity');
});
