<?php

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'promotional'], function () {
        Route::post('get_products', 'TemplateController@get_products');
    });
});
