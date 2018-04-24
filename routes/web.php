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

    Route::get('inventory', 'InventoryController@generate')->name('inventory');

    Route::post('category/print-list', 'CategoryController@printList')->name('category.print_list');

    Route::post('global-search', 'GlobalSearchController@process')->name('global_search');

    Route::post('order/create', 'OrderController@create')->name('order.create_post');
    Route::get('order/article_list/{supplier}', 'OrderController@articleList')->name('order.article_list');
    Route::get('order/{order}/cancel', 'OrderController@cancel')->name('order.cancel');
    Route::get('order/{order}/create-delivery', 'OrderController@createDelivery')->name('order.create_delivery');
    Route::post('order/{order}/store-delivery', 'OrderController@storeDelivery')->name('order.store_delivery');
    Route::post('order/{order}/change-payment-status', 'OrderController@changePaymentStatus')->name('order.change_payment_status');

    Route::post('order/{orderitem}/item-invoice-received', 'OrderController@itemInvoiceReceived')->name('order.item_invoice_received');
    Route::post('order/{orderitem}/item-confirmation-received', 'OrderController@itemConfirmationReceived')->name('order.item_confirmation_received');
    Route::post('order/{order}/all-items-invoice-received', 'OrderController@allItemsInvoiceReceived')->name('order.all_items_invoice_received');
    Route::post('order/{order}/all-items-confirmation-received', 'OrderController@allItemsConfirmationReceived')->name('order.all_items_confirmation_received');

    Route::get('order/message/{message}/attachment-download/{attachment}', 'OrderMessageController@messageAttachmentDownload')->name('order.message_attachment_download');
    Route::get('order/{order}/message/new', 'OrderMessageController@create')->name('order.message_new');
    Route::post('order/{order}/message/new', 'OrderMessageController@store')->name('order.message_create');
    Route::post('order/message/{message}/delete/{order?}', 'OrderMessageController@delete')->name('order.message_delete');
    Route::get('order/{order}/message/{message}/read', 'OrderMessageController@markRead')->name('order.message_read');
    Route::get('order/{order}/message/{message}/unread', 'OrderMessageController@markUnread')->name('order.message_unread');
    Route::get('order/message/unassigned', 'OrderMessageController@unassignedMessages')->name('order.messages_unassigned');
    Route::post('order/{order}/message/upload', 'OrderMessageController@uploadNewAttachments')->name('order.message_upload');
    Route::post('order/message/assign', 'OrderMessageController@assignToOrder')->name('order.message_assign');

    Route::post('article/reorder', 'ArticleController@reorder')->name('article.reorder');
    Route::post('article/print-label', 'ArticleController@printLabel')->name('article.print_label');
    Route::post('article/{article}/addnote', 'ArticleController@addNote')->name('article.add_note');
    Route::post('article/{article}/deletenote', 'ArticleController@deleteNote')->name('article.delete_note');
    Route::get('article/{article}/quantity-changelog', 'ArticleController@quantityChangelog')->name('article.quantity_changelog');
    Route::get('article/{article}/quantity-changelog/{changelog}/delete', 'ArticleController@deleteQuantityChangelog')->name('article.quantity_changelog.delete');
    Route::post('article/{article}/change-quantity', 'ArticleController@changeQuantity')->name('article.change_quantity');
    Route::post('article/{article}/change-supplier', 'ArticleController@changeSupplier')->name('article.change_supplier');
});
