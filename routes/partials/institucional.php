<?php

Route::group(['prefix' => 'institucional'], function () {
    Route::get('{slug}', 'InstitutionalController@showPage')->name('pages.show');
});
