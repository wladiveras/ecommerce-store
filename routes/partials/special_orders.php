<?php

Route::group(['prefix' => 'projetos_especiais'], function () {
    Route::get('/', 'SpecialOrderController@index')->middleware('title:Projetos Especiais')->name("pedidos_especiais.index");
    Route::post('get_items_list', 'SpecialOrderController@get_items_list')->middleware(['auth'])->name("pedidos_especiais.get_items_list");
    Route::post('store', 'SpecialOrderController@store')->name("pedidos_especiais.store");
    Route::group(['prefix' => 'projetos'], function () {
        Route::get('', 'SpecialOrderController@list')->middleware(['title:Projetos Especiais', 'auth'])->name("pedidos_especiais.list");
        Route::get('novo', 'SpecialOrderController@create')->middleware(['title:Projetos Especiais', 'auth'])->name("pedidos_especiais.create");
        Route::get('visualizar/{id}', 'SpecialOrderController@show')->name("pedidos_especiais.show");
        Route::put('editar/{id}', 'SpecialOrderController@put')->name("pedidos_especiais.put");
    });
});
