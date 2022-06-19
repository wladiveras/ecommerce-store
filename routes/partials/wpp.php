<?php
Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'wpp'], function () {
        Route::post('set_wpp_phone', 'ApiController@set_wpp_phone');
    });
});