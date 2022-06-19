<?php
    $auth = Auth::check() ? Auth::user() : false;
    $category = "todos";
    $currentCategory = preg_replace('/( )+/','-',$department);
    //$showprice = $auth;
    $showprice = true;
    $colors = ["impressao-digital" => "pink","impressao-offset" => "cyan","comunicacao-visual" => "orange" , "todos" => "red"];
    if(!@$products) {
        $products = \App\Models\Product::where("id",">",0);
        if($showmore) $products = $products->limit(16);
        if($currentCategory!='todos') $products = $products->where('department',$department);
        $products = $products->get();
    }

    function promo_price($product) {
        $pricing_data = $product->viewPrice;
        $price = floatval($pricing_data["price"]);
		$value = floatval($pricing_data["promo_value"]);
		if ($pricing_data["promo_type"] == "pp") {
			$percentage = $price * $value / 100;
			return $price + $percentage;
		}
		if ($pricing_data["promo_type"] == "rl") {
			return $price + $value;
		}
		if ($pricing_data["promo_type"] == "ex") return $pricing_data["promo_value"];
	}

    //$productsH = \App\Models\Product::where("is_highlight","=",1);
    //$productsH = $productsH->limit(8)->get();

    // $productsK = \App\Models\ModelCategory::where('model_type',"=","product");
    // $productsK = $productsK->join('products', 'model_categories.model_id', '=', 'products.id')
    //                         ->where('model_categories.category_id',"=",6)
    //                         ->get();

    $filterProductsByCategory1 = \App\Models\ModelCategory::where('model_type',"=","product");
    $filterProductsByCategory1 = $filterProductsByCategory1->join("products", function($join){
        $join->on('model_categories.model_id', '=', 'products.id');
    })
                            ->where('model_categories.category_id',"=",10)
                            ->get(['id']);

    $collection1 = collect($filterProductsByCategory1);
    $plucked1 = $collection1->pluck('id');
    $plucked1->all();

    $ProductsByCategory1 = \App\Models\Product::where("status",">",0);
    $ProductsByCategory1 = $ProductsByCategory1->whereIn('id', $plucked1)
                            ->limit(6)
                            ->get();


    // $filterProductsByCategory2 = \App\Models\ModelCategory::where('model_type',"=","product");
    // $filterProductsByCategory2 = $filterProductsByCategory2->join("products", function($join){
    //     $join->on('model_categories.model_id', '=', 'products.id');
    // })
    //                         ->where('model_categories.category_id',"=",1)
    //                         ->get(['id']);

    // $collection2 = collect($filterProductsByCategory2);
    // $plucked2 = $collection2->pluck('id');
    // $plucked2->all();

    // $ProductsByCategory2 = \App\Models\Product::where("status",">",0);
    // $ProductsByCategory2 = $ProductsByCategory2->whereIn('id', $plucked2)
    //                         ->limit(6)
    //                         ->get();


    // $filterProductsByCategory3 = \App\Models\ModelCategory::where('model_type',"=","product");
    // $filterProductsByCategory3 = $filterProductsByCategory3->join("products", function($join){
    //     $join->on('model_categories.model_id', '=', 'products.id');
    // })
    //                         ->where('model_categories.category_id',"=",5)
    //                         ->get(['id']);

    // $collection3 = collect($filterProductsByCategory3);
    // $plucked3 = $collection3->pluck('id');
    // $plucked3->all();

    // $ProductsByCategory3 = \App\Models\Product::where("status",">",0);
    // $ProductsByCategory3 = $ProductsByCategory3->whereIn('id', $plucked3)
    //                         ->limit(6)
    //                         ->get();


    // $ProductsByCategory4 = \App\Models\Product::where("is_highlight","=",1);
    // $ProductsByCategory4 = $ProductsByCategory4->limit(6)->get();


    // $filterProductsByCategory5 = \App\Models\ModelCategory::where('model_type',"=","product");
    // $filterProductsByCategory5 = $filterProductsByCategory5->join("products", function($join){
    //     $join->on('model_categories.model_id', '=', 'products.id');
    // })
    //                         ->where('model_categories.category_id',"=",11)
    //                         ->get(['id']);

    // $collection5 = collect($filterProductsByCategory5);
    // $plucked5 = $collection5->pluck('id');
    // $plucked5->all();

    // $ProductsByCategory5 = \App\Models\Product::where("status",">",0);
    // $ProductsByCategory5 = $ProductsByCategory5->whereIn('id', $plucked5)
    //                         ->limit(6)
    //                         ->get();



    //$productsCarousel = \App\Models\Product::where("maisvendido","=",1)->orderBy("ordem_maisvendido","asc");
    //$productsCarousel = $productsCarousel->limit(24)->get();


?>
@if(@!$_GET['procurando'])

    <div>
        @if(@$template->detail->banner_wide)
            <div class="container py-3">
                @foreach($template->detail->banner_wide as $banner)
                    <div class="row mt-2 d-flex align-items-center justify-content-center">
                        <div class="col-12 mb-1 mx-0 p-0">
                            <img src="{{$banner->url}}" alt="banner home" class="w-100"/>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endif
<div class="main-content">
    <div class="container">
        <div class="row mt-3">
            <div class="col-sm-12 mb-3 text-center">
                @if(@!$_GET['procurando'])

                @else
                    <p class="f-32 f-space text-center"><span class="font-weight-light">Você procurou por </span><span class="font-weight-bold">{{@$_GET['procurando']}}</span> </p>
                @endif
            </div>
            <!-- <div class="col-12">
                <div class="mb-3" data-intro="Se quiser você pode filtrar seus produtos por <b>departamento</b>." data-step="7">
                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-md-3 mb-2">
                            <a href="/produtos" class="btn mb-0 btn-category w-100 {{$currentCategory=='todos' ? 'actual' : ''}} btn-{{$colors['todos']}}">Todos</a>
                        </div>
                        <div class="col-md-3 col-sm-12 mb-2">
                            <a href="/produtos/categorias/impressao-digital" class="btn btn-category  w-100 {{$currentCategory=='impressao-digital' ? 'actual' : ''}} btn-{{$colors['impressao-digital']}}">Impressão Digital</a>
                        </div>
                        <div class="col-md-3 col-sm-12 mb-2">
                            <a href="/produtos/categorias/impressao-offset" class="btn btn-category w-100 {{$currentCategory=='impressao-offset' ? 'actual' : ''}} btn-{{$colors['impressao-offset']}}">Impressão Offset</a>
                        </div>
                        <div class="col-md-3 col-sm-12 mb-2">
                            <a href="/produtos/categorias/comunicacao-visual" class="btn w-100 btn-category {{$currentCategory=='comunicacao-visual' ? 'actual' : ''}} btn-{{$colors['comunicacao-visual']}}">Comunicação Visual</a>
                        </div>

                    </div>
                </div>
            </div> -->
        </div>
    </div>
    @if(@!$_GET['procurando'])

        <section class="container d-flex justify-content-center">
            <div class="chamadas-home">
                <div>
                    <div class="chamada">
                        <a href="produtos/queridinhos">
                            <img id="chamada01" src="{{asset('assets/images/home-queridinhos.png')}}" alt="Padrãocolor">
                        </a>
                    </div>
                    <div class="chamada">
                        <a href="produtos/nossosprodutos">
                            <img id="chamada02" src="{{asset('assets/images/home-promocoes.png')}}" alt="Padrãocolor">
                        </a>
                    </div>
                </div>
                <div>
                    <div class="chamada">
                        <a href="produtos/nossosprodutos">
                            <img id="chamada05" src="{{asset('assets/images/home-presentes-criativos.png')}}" alt="Padrãocolor">
                        </a>
                    </div>
                </div>
                <div>
                    <div class="chamada">
                        <a href="produtos/nossosprodutos">
                            <img id="chamada03" src="{{asset('assets/images/home-maisvendidos.png')}}" alt="Padrãocolor">
                        </a>
                    </div>
                    <div class="chamada">
                        <a href="produtos/nossosprodutos">
                            <img id="chamada04" src="{{asset('assets/images/home-novidadess.png')}}" alt="Padrãocolor">
                        </a>
                    </div>
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-12">
                    <img src="{{asset('assets/images/chamada-home.png')}}" alt="Padrãocolor">
                </div>
            </div> -->
        </section>
        <div class="container container--tabs">
            <section class="row">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab1">Brindes</a></li>
                    <li class=""><a href="#tab2">Festas</a></li>
                    <li class=""><a href="#tab3">Editora</a></li>
                    <li class=""><a href="#tab4">Eventos</a></li>
                </ul>
                <div class="tab-content">
                    <div id="tab1" class="tab-pane active">
                        <span class="col-md-10">
                        @if(count($ProductsByCategory1) >= 0)
                            <div class='container'>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-none d-md-block">
                                            <?php $chunked = $ProductsByCategory1->chunk(3);?>
                                            @for($i=0;$i<count($chunked);$i++)
                                                @include("pages.products.product_item",["row" => $chunked[$i]])
                                            @endfor
                                        </div>
                                        <div class="mb-4 d-block d-md-none">
                                            <div class="d-flex flex-row d-flex-mobile product-list-content">
                                                @foreach($ProductsByCategory1 as $product)
                                                    @include("pages.products.product_item_card",["product" => $product])
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        </span>
                    </div>
                    <div id="tab2" class="tab-pane">
                        
                    </div>
                    <div id="tab3" class="tab-pane">
                        
                    </div>
                    <div id="tab4" class="tab-pane">
                       
                    </div>
                </div>
            </section>
        </div>

    @else
        @if(count($products) >= 0)
            <div class='container'>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3 d-none d-md-block">
                            <?php $chunked = $products->chunk(4);?>
                            @for($i=0;$i<count($chunked);$i++)
                                @include("pages.products.product_item",["row" => $chunked[$i]])
                            @endfor
                        </div>
                        <div class="mb-4 d-block d-md-none">
                            <div class="d-flex flex-row product-list-content overflow-auto">
                                @foreach($products as $product)
                                    @include("pages.products.product_item_card",["product" => $product])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif


    @if(count($products) == 0)
        <div class="col-sm-12 mb-5">
            <h3 class="text-center">Nenhum produto encontrado :'(</h3>
        </div>
    @else

    @endif

    <div class="d-flex mt-5 justify-content-center">
        <a href="produtos/por-categoria" class="btn-showmore">                    
            Conheça todas nossas categorias e produtos
        </a>
    </div>

    <section class="container row banner-presentear">
        <div class="col-sm-12 col-md-6">
            <h4>Por que personalizar o presente?</h4>
            <p>O ato de presentear uma pessoa especial é capaz de nos trasmitir um sentimento de felicidade. Afinal, você pensou nas caracteristicas e personalidade dela e transformou tudo isso em um presente.</p>
            <p><b>Presentear alguém é uma demonstração de amor, carinho e gentileza.</b></p>
        </div>
        <div class="col-sm-12 col-md-6"><img src="{{asset('assets/images/personalizar-presente.png')}}" alt=""></div>
    </section>

    <section class="container">
        <img src="{{asset('assets/images/tit-criafacil-home.png')}}" alt="">
        <p style="font-size: 1.5rem;color: #f74f06;">Aqui na CriaFácil, você vai poder customizar seus presentes da forma que você prefere! Agora seus presentes vão ter sempre o seu toque especial.</p>
    </section>

</div>
