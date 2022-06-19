<?php
Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'testimonies'], function () {
        Route::post('get', 'ApiController@get_testimonies')->name('api.get_testimonies');
    });
});