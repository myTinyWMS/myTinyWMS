<?php

Route::namespace('Handscanner')->group(function () {
    Route::get('/login', 'LoginController@login')->name('handscanner.login');
    Route::post('/login', 'LoginController@processLogin')->name('handscanner.processlogin');
    Route::post('/logout', 'LoginController@processLogout')->name('handscanner.logout');

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/', 'HomeController@index')->name('handscanner.index');

        Route::prefix('outgoing')->group(function () {
            Route::get('/start', 'OutgoingController@start')->name('handscanner.outgoing.start');
            Route::get('/process/{internal_article_number}', 'OutgoingController@process')->name('handscanner.outgoing.process');
            Route::post('/save/{article}', 'OutgoingController@save')->name('handscanner.outgoing.save');
        });

        Route::prefix('inventory')->group(function () {
            Route::get('/start', 'InventoryController@start')->name('handscanner.inventory.start');
            Route::post('/continue', 'InventoryController@continue')->name('handscanner.inventory.continue');
            Route::get('/new', 'InventoryController@new')->name('handscanner.inventory.new');
            Route::get('/{inventory}/select-category', 'InventoryController@selectCategory')->name('handscanner.inventory.select_category');
            Route::get('/{inventory}/category/{category}/select-article', 'InventoryController@selectArticle')->name('handscanner.inventory.select_article');
            Route::post('/{inventory}/category/{category}/processed', 'InventoryController@categoryProcessed')->name('handscanner.inventory.category_processed');
            Route::get('/{inventory}/category/{category}/article/process/{internal_article_number}', 'InventoryController@process')->name('handscanner.inventory.process');
            Route::post('/{inventory}/article/{article}/processed', 'InventoryController@processed')->name('handscanner.inventory.processed');

            Route::get('/step1', 'InventoryController@step1')->name('handscanner.inventory.step1');
        });
    });
});