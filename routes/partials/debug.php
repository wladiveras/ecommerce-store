<?php


Route::group(['prefix' => 'debug1'], function () {
        
        Route::get('jadlog', 'DebugController@createJadlog')->name('debug.create.jadlog');
        Route::get('cancelCreditcard', 'DebugController@cancelCreditcard')->name('debug.cancelCreditcard');
        Route::get('saveCreditcard', 'DebugController@saveCreditcard')->name('debug.saveCreditcard');
        Route::get('safeBox', 'DebugController@safeBox')->name('debug.safeBox');
        Route::get('safeBoxListAll', 'DebugController@safeBoxListAll')->name('debug.safeBoxListAll');
        Route::get('dashboardApi', 'DebugController@dashboardApi')->name('debug.dashboardApi');
        Route::get("businessRule",'DebugController@businessRule')->name('debug.businessRule');
        Route::get("email",'DebugController@emailTemplate')->name('emailTemplate');
});



