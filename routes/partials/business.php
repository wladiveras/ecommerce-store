<?php
 //Route::get('{business}', 'BusinessController@show')->middleware(['slugids:business,App\Models\Business', 'bindings'])->name('business.view');
Route::get('/business/{slug}', 'BusinessController@show')->name('business.view');;