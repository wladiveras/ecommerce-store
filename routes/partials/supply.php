<?php

Route::group(['prefix' => 'supply'], function () {
  Route::get('/stores', 'SupplyStoreController@getAll')->name('store.addresses.index');
});
