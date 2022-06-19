<?php

Route::group(['prefix' => 'carrinho'], function () {
    Route::get('', 'CartController@index')->middleware('title:Carrinho')->name('cart.index');
    Route::get('limpar', 'CartController@clear')->name('cart.clear');
    Route::get('remove/{id}', 'CartController@remove')->name('cart.remove');
    Route::get('edit/{id}', 'CartController@edit')->name('cart.edit');
    Route::post('edit/{id}', 'CartController@edit')->name('cart.edit');
    Route::post('edit/configuracao_produto/upload', 'CheckoutController@uploadFile')->name('checkout.upload.file');
});
$this->get('orcamento', 'CartController@generateBudget')->middleware('cart.empty.redirect');
