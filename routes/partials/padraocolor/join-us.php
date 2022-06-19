<?php

Route::group(["namespace" => "PadraoColor"],function(){
    Route::group(["prefix" => "seja-um-revendedor"],function(){
        Route::get("/","JoinUsFormController@index")->name("seja-um-revendedor");
        Route::post("progress","JoinUsFormController@saveProgress");
        Route::post("/","JoinUsFormController@store");
        Route::get("editar/{id}","JoinUsFormController@edit")->name("join-us.edit");
        Route::get("finalizar/{id}","JoinUsFormController@finishView")->name("join-us.finish");
        Route::post("finalizar/{id}","JoinUsFormController@finishRequest")->name("join-us.finish.request");
        //Route::get("{id}","JoinUsFormController@loadRecovery");
    });
});