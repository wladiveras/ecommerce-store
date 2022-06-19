<?php
Route::group(['prefix' => "central-de-ajuda"], function () {
    Route::get('{category?}/{topic?}', 'HelpcenterController@index')->name('help_center.index');
    Route::post('search', 'HelpcenterController@search')->name('help_center.search');
});
