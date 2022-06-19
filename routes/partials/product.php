<?php
Route::group(['prefix' => 'produtos'], function () {
    Route::get('/', 'ProductsController@index')->middleware('title:Produtos')->name("products.index");
    Route::get('/nossosprodutos', 'ProductsController@index2')->middleware('title:Nossos Produtos')->name("products.products_all");
    Route::get('/lancamentos', 'ProductsController@index3')->middleware('title:Lançamentos')->name("products.products_lancamento");
    Route::get('/por-categoria', 'ProductsController@index4')->middleware('title:Nossas Categorias')->name("products.products_by_category");
    Route::get('/queridinhos', 'ProductsController@index5')->middleware('title:Queridinhos do Site')->name("products.products_category1");
    Route::get('{product}', 'ProductsController@view')->middleware(['slugids:product,App\Models\Product', 'bindings'])->name('product.view');
    Route::get('categorias/{department}', 'ProductsController@department')->middleware('title:Produtos')->name("products.department");
    Route::post('{product}/create_avaliation', 'ProductsController@create_avaliation')->middleware(['slugids:product,App\Models\Product', 'bindings'])->name('product.create_avaliation');
    Route::post('{product}/create_faq', 'ProductsController@create_faq')->middleware(['slugids:product,App\Models\Product', 'bindings'])->name('product.create_faq');
    //Route::get('{product}/configuracao_produto', 'CheckoutController@upload')->middleware(['title:Configuração de Produto', 'slugids:product,App\Models\Product', 'bindings'])->name('category.product.configuracao_produto');
    Route::post('get_options', 'ConfigProductController@getConfigOptions')->name('checkout.config.get_options');
    Route::post('get_qty', 'ConfigProductController@getConfigQty')->name('checkout.config.get_options_qty');
    Route::post('check_has_options', 'ConfigProductController@check_has_options')->name('checkout.config.check_has_options');
    Route::post('calc_qty', 'ConfigProductController@calc_qty')->name('checkout.config.calc_qty');
    Route::group(['middleware' => 'auth'], function () {

        Route::group(['prefix' => '{product}'], function () {

            Route::group(['prefix' => 'criar_minha_arte'], function () {
                //Route::get("{product_id}/{sku_id}", "Web2PrintController@init")->name("products.web2print");
            });

            Route::group(['prefix' => 'compra-rapida'], function () {
                Route::get('', 'PricingTableController@fastbuy_show')->name("products.fastbuy_show")->middleware(['slugids:product,App\Models\Product', 'bindings']);
                Route::post('/{item}', 'PricingTableController@getData')->name("tabela_precos.get_data");
                Route::get('/comprar', 'PricingTableController@fastbuy')->middleware(['slugids:product,App\Models\Product', 'bindings'])->name("products.fastbuy");
            });
            Route::get('{sku}/{settings}', 'PricingTableController@loadSetup')->middleware(['slugids:product,App\Models\Product', 'slugids:sku,App\Models\Sku', 'bindings'])->name('product.load-sku');
        });
        //Route::get('{product}/configuracao_produto', 'CheckoutController@upload')->middleware(['title:Configuração de Produto', 'slugids:product,App\Models\Product', 'bindings'])->name('category.product.configuracao_produto');
        Route::post('{product}/configuracao_produto/upload', 'CheckoutController@uploadFile')->name('checkout.upload.file');
        Route::post('{product}/configuracao_produto/upload/remove', 'CheckoutController@removeUploadedFile')->name('checkout.upload.file.remove');
    });
    Route::get('{product}/configuracao_produto', 'CheckoutController@upload')->middleware(['title:Configuração de Produto', 'slugids:product,App\Models\Product', 'bindings'])->name('category.product.configuracao_produto');
    // Route::get('{category}/{product}', 'ProductsController@ShowDetail');
});
Route::get('api/products/list/{amount?}', 'ProductsController@list');
Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'product'], function () {
        Route::post('department', 'ApiController@product_department')->name('api.product.department');
        Route::post('config', 'ApiController@product_config')->name('api.product.config');
    });
});
