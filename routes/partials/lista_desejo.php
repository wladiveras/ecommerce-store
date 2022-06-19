<?php
Route::get('/lista_desejo', 'ListaDeDesejosController@show')->middleware('title:Lista de Desejo')->name('lista_desejo.view');;

Route::get('/lista_desejo/remove/{id}', 'ListaDeDesejosController@remove')->name('lista_desejo.remove');

Route::get('/lista_desejo/addCart/{id}', 'ListaDeDesejosController@addCart')->name('lista_desejo.addCart');