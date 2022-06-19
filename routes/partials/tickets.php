<?php
Route::group(['middleware' => 'is_ticket_agent'], function () {
    Route::group(['prefix' => 'tickets'], function () {
        Route::get('', 'TicketsController@index')->name('tickets.index');
        Route::get('{id}/detalhes', 'TicketsController@show')->name('tickets.show');
        Route::post('search', 'TicketsController@search')->name('tickets.search');
        Route::get('novo', 'TicketsController@create')->name('tickets.create');
        Route::post('store', 'TicketsController@store')->name('tickets.store');
        Route::post('{id}/new_comment', 'TicketsController@new_comment')->name('tickets.new_comment');
    });
});