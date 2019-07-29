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

Route::get('/', 'DashboardController@index');

Route::group(['middleware' => ['auth']], function () {
    Route::namespace('Article')->group(function () {
        Route::get('article/sort-update', 'SortController@index')->name('article.sort_update_form');
        Route::post('article/sort-update', 'SortController@store')->name('article.sort_update_form_post');

        Route::get('article/mass-update', 'MassUpdateController@index')->name('article.mass_update_form');
        Route::post('article/mass-update', 'MassUpdateController@store')->name('article.mass_update_save');

        Route::get('article/inventory-update', 'InventoryUpdateController@index')->name('article.inventory_update_form');
        Route::post('article/inventory-update', 'InventoryUpdateController@store')->name('article.inventory_update_save');

        Route::post('article/{article}/file_upload', 'AttachmentController@upload')->name('article.file_upload');
        Route::get('article/{article}/file-download/{file}', 'AttachmentController@download')->name('article.file_download');

        Route::get('article/{article}/print-label/{size}', 'LabelController@printSingleLabel')->name('article.print_single_label');
        Route::post('article/print-label', 'LabelController@printLabel')->name('article.print_label');

        Route::post('article/{article}/addnote', 'NoteController@store')->name('article.add_note');
        Route::get('article/{article}/deletenote/{note}', 'NoteController@delete')->name('article.delete_note');

        Route::post('article/{article}/change-changelog-note', 'QuantityChangelogController@changeChangelogNote')->name('article.change_changelog_note');
        Route::get('article/{article}/quantity-changelog', 'QuantityChangelogController@index')->name('article.quantity_changelog');
        Route::get('article/{article}/quantity-changelog/{changelog}/delete', 'QuantityChangelogController@delete')->name('article.quantity_changelog.delete');

        Route::post('article/{article}/change-quantity', 'ArticleController@changeQuantity')->name('article.change_quantity');
        Route::post('article/{article}/fix-quantity-change', 'ArticleController@fixQuantityChange')->name('article.fix_quantity_change');

        Route::post('article/{article}/change-supplier', 'SupplierController@store')->name('article.change_supplier');
    });

    Route::post('inventory/{inventory}/article/{article}/processed', 'InventoryController@processed')->name('inventory.processed');
    Route::get('inventory/{inventory}/article/{article}/correct', 'InventoryController@correct')->name('inventory.correct');
    Route::get('inventory/{inventory}/category/{category}/done', 'InventoryController@categoryDone')->name('inventory.category.done');
    Route::get('inventory/{inventory}/finish', 'InventoryController@finish')->name('inventory.finish');

    Route::get('inventory/create_month', 'InventoryController@createMonth')->name('inventory.create_month');
    Route::get('inventory/create_year', 'InventoryController@createYear')->name('inventory.create_year');

    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::resources([
        'article' => 'Article\ArticleController',
        'supplier' => 'SupplierController',
        'category' => 'CategoryController',
        'order' => 'Order\OrderController',
        'unit' => 'UnitController',
        'inventory' => 'InventoryController',
    ]);

    Route::get('reports', 'ReportsController@index')->name('reports.index');
    Route::get('reports/deliveries-without-invoice', 'ReportsController@deliveriesWithoutInvoice')->name('reports.deliveries_without_invoice');
    Route::get('reports/invoices-without-delivery', 'ReportsController@invoicesWithoutDelivery')->name('reports.invoices_without_delivery');
    Route::get('reports/inventory-pdf', 'ReportsController@generateInventoryPdf')->name('reports.inventory_pdf');
    Route::get('reports/yearly-inventory-pdf', 'ReportsController@generateYearlyInventoryPdf')->name('reports.yearly_inventory_pdf');
    Route::post('reports/inventory-report', 'ReportsController@generateInventoryReport')->name('reports.inventory_report');
    Route::post('reports/article-usage-report', 'ReportsController@generateArticleUsageReport')->name('reports.article_usage_report');
    Route::post('reports/article-weight-report', 'ReportsController@generateArticleWeightReport')->name('reports.article_weight_report');

    Route::get('notification/{id}/delete', 'NotificationController@delete');

    Route::get('settings', 'SettingsController@show')->name('settings.show');
    Route::post('settings', 'SettingsController@save')->name('settings.save');
    Route::get('change_pw', 'SettingsController@changePwForm')->name('settings.change_pw');
    Route::post('change_pw', 'SettingsController@changePw')->name('settings.change_pw_post');

    Route::post('category/print-list', 'CategoryController@printList')->name('category.print_list');

    Route::post('global-search', 'GlobalSearchController@process')->name('global_search');

    Route::namespace('Order')->group(function () {
        Route::post('order/{orderitem}/item-invoice-received', 'OrderItemsController@invoiceReceived')->name('order.item_invoice_received');
        Route::get('order/{orderitem}/item-confirmation-status/{status}', 'OrderItemsController@confirmationReceived')->name('order.item_confirmation_received');
        Route::post('order/{order}/all-items-invoice-received', 'OrderItemsController@allItemsInvoiceReceived')->name('order.all_items_invoice_received');
        Route::post('order/{order}/all-items-confirmation-received', 'OrderItemsController@allItemsConfirmationReceived')->name('order.all_items_confirmation_received');

        Route::post('order/create', 'OrderController@create')->name('order.create_post');
        Route::get('order/{order}/cancel', 'OrderController@cancel')->name('order.cancel');
        Route::get('order/{order}/payment-status/{payment_status}', 'OrderController@changePaymentStatus')->name('order.change_payment_status');
        Route::get('order/{order}/status/{status}', 'OrderController@changeStatus')->name('order.change_status');
        Route::post('order/{order}/invoicecheck/upload', 'OrderController@uploadInvoiceCheckAttachments')->name('order.invoice_check_upload');

        Route::get('order/{order}/create-delivery', 'DeliveryController@create')->name('order.create_delivery');
        Route::post('order/{order}/store-delivery', 'DeliveryController@store')->name('order.store_delivery');
    });

    Route::get('order/message/{message}/attachment-download/{attachment}', 'OrderMessageController@messageAttachmentDownload')->name('order.message_attachment_download');
    Route::get('order/{order}/message/new', 'OrderMessageController@create')->name('order.message_new');
    Route::post('order/{order}/message/new', 'OrderMessageController@store')->name('order.message_create');
    Route::get('order/message/{message}/delete/{order?}', 'OrderMessageController@delete')->name('order.message_delete');
    Route::get('order/{order}/message/{message}/read', 'OrderMessageController@markRead')->name('order.message_read');
    Route::get('order/{order}/message/{message}/unread', 'OrderMessageController@markUnread')->name('order.message_unread');
    Route::get('order/message/unassigned', 'OrderMessageController@unassignedMessages')->name('order.messages_unassigned');
    Route::post('order/{order}/message/upload', 'OrderMessageController@uploadNewAttachments')->name('order.message_upload');
    Route::post('order/message/assign', 'OrderMessageController@assignToOrder')->name('order.message_assign');
    Route::get('order/message/{message}/forward', 'OrderMessageController@forwardForm')->name('order.message_forward_form');
    Route::post('order/message/{message}/forward', 'OrderMessageController@forward')->name('order.message_forward');
});
