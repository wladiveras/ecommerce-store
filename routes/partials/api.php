<?php

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'reseller'], function () {
        Route::get('get_logo_url', 'UserAddressesController@get_logo_url')->name('user.get_logo');
        Route::post('post_logo_url', 'UserAddressesController@logo_upload')->name('user.post_logo');
        Route::post('upload_image', 'ApiController@upload_image')->name('api.upload_image');
    });
    Route::group(['prefix' => 'produto_config'], function () {
        //Route::post('get_options', 'ConfigProductController@getConfigOptions')->name('checkout.config.get_options');
        //Route::post('get_qty', 'ConfigProductController@getConfigQty')->name('checkout.config.get_options_qty');
        Route::post('get_final_sku', 'ConfigProductController@getConfigSkuFinal')->name('checkout.config.get_final_sku');
        //Route::post('check_has_options', 'ConfigProductController@check_has_options')->name('checkout.config.check_has_options');
        //Route::post('calc_qty', 'ConfigProductController@calc_qty')->name('checkout.config.calc_qty');
        Route::post('get_calc_qty_tshirts', 'ConfigProductController@get_calc_qty_tshirts')->name('checkout.config.get_calc_qty_tshirts');
        Route::post('calc_qty_tshirts', 'ConfigProductController@calc_qty_tshirts')->name('checkout.config.calc_qty_tshirts');
        Route::post('get_calc_finishes', 'ConfigProductController@get_calc_finishes')->name('checkout.config.get_calc_finishes');
        Route::post('submit_order', 'CheckoutController@confirmArt')->name('category.product.confirmArt');
        Route::post('get_company_categories', 'ApiController@get_company_categories')->name('category.product.get_company_categories');
    });
});
