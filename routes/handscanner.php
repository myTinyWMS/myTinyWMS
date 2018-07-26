<?php

Route::namespace('Handscanner')->group(function () {
    Route::get('/login', 'LoginController@login')->name('handscanner.login');
    Route::post('/login', 'LoginController@processLogin')->name('handscanner.processlogin');
    Route::post('/logout', 'LoginController@processLogout')->name('handscanner.logout');

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/', 'HomeController@index')->name('handscanner.index');

        Route::prefix('inventory')->group(function () {
            Route::get('/step1', 'InventoryController@step1')->name('handscanner.inventory.step1');
            Route::get('/step2/{articlenumber}', 'InventoryController@step2')->name('handscanner.inventory.step2');
            Route::post('/step3', 'InventoryController@step3')->name('handscanner.inventory.step3');
        });
    });
});