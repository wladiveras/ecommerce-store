<?php


Route::get('/', 'HomeController@index')->middleware('title:Home')->name('home');

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'menu'], function () {
        Route::post('get_menu_options', 'MenuController@get_menu_options');
    });
});
Route::group(['middleware' => 'auth'], function () {
    require 'partials/api.php';
    require 'partials/checkout.php';
    require 'partials/user.php';
    require 'partials/cart.php';
    require 'partials/order.php';
});
require 'partials/tickets.php';
require 'partials/wpp.php';
require 'partials/supply.php';
require 'partials/institucional.php';
require 'partials/debug.php';
require 'partials/auth.php';
require 'partials/product.php';
require 'partials/general.php';
require 'partials/testimonies.php';
require 'partials/padraocolor/join-us.php';
require 'partials/special_orders.php';
require 'partials/template.php';
require 'partials/helpcenter.php';
require 'partials/business.php';
require 'partials/lista_desejo.php';
require 'partials/comprar_novamente.php';
Route::get("/dia-da-mulher","HomeController@dia_das_mulheres_view");
Route::get("/novos-produtos-desconto","HomeController@novos_produtos_desconto_view");

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
