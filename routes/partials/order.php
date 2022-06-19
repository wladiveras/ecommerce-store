<?php

Route::group(['prefix' => 'order'], function () {
    Route::post('store', 'OrderController@Store')->name('order.store')->middleware('cart.empty.redirect');
    Route::post('search', 'OrderController@search')->middleware('title:Busca da Compra')->name('order.view.search');
});

Route::group(['prefix' => 'compras'], function () {
    if(config('dashboard_api.order_cancel.enabled'))
    {
        Route::post('cancelamento', 'OrderController@OrderItemCancel')->middleware('title:Cancelamento de Pedido')->name('order.cancel.item');
    }
    Route::get('{hashid}/detail', 'OrderController@OrdersDetails')->middleware('title:Detalhes da Compra')->name('order.view.detail');
    Route::get('{hashid}/timeline', 'OrderController@OrderTimeline')->middleware('title:Timeline da Compra')->name('order.view.timeline');
    if(config('dashboard_api.resend_art.enabled'))
    {
        Route::get('{hashid}/pedido/{pedido_id}/resendart', 'OrderController@resendArt')->middleware('title:Reenvio da Arte')->name('order.view.resend');
        Route::post('{hashid}/pedido/{pedido_id}/resendart/submit', 'OrderController@resendArtSubmit')->middleware('title:Reenvio da Arte Upload Submit')->name('order.view.resend.submit');
        Route::post('{hashid}/pedido/{pedido_id}/resendArtUpload', 'OrderController@resendArtUpload')->middleware('title:Reenvio da Arte Upload')->name('order.view.resend.upload');
    }
    Route::get('{status?}', 'OrderController@OrdersView')->middleware('title:Minhas Compras')->name('order.view');
});
