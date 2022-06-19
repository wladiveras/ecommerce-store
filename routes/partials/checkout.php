<?php

Route::group(['prefix' => 'checkout'], function () {
    Route::get('', 'CheckoutController@index')->name('checkout.index')->middleware(['title:Checkout','cart.empty.redirect']);
    Route::post('update_reseller_address', 'CheckoutController@update_reseller_address')->name('checkout.update_reseller_address')->middleware(['title:Checkout','cart.empty.redirect']);
    Route::get('thank-you','CheckoutController@finish');
    Route::post('getcard','CheckoutController@getcard');
});
$this->get('/api/checkout/load-data','CheckoutController@loadCheckoutData');
Route::middleware('auth')->post('api/frete', 'JadlogController@shipping');
Route::middleware('auth')->post('api/fretePickup', 'JadlogController@shippingPickup');
Route::middleware('auth')->get('api/pickup/{zip_code}', 'JadlogController@pickup');   