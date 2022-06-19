<?php
    $auth = Auth::check() ? Auth::user() : false;
    $category = "todos";
    $currentCategory = preg_replace('/( )+/','-',$department);
    $showprice = $auth;
    $colors = ["impressao-digital" => "pink","impressao-offset" => "cyan","comunicacao-visual" => "orange" , "todos" => "red"];
    if(!@$products) {
        $products = \App\Models\Product::where("id",">",0);
        if($showmore) $products = $products->limit(16);
        if($currentCategory!='todos') $products = $products->where('department',$department);
        $products = $products->get();
    }

    $productsH = \App\Models\Product::where("lancamento","=",1)->orderBy("ordem_lancamento","asc");
    $productsH = $productsH->get();

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
?>
@if(@!$_GET['procurando'])    
    
    <div class="content-gray">
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
<div>
    <div class="container">
        <div class="row mt-3">
            <div class="col-sm-12 mb-3">
                @if(@!$_GET['procurando'])
                    <!-- <div>
                        <img style="max-width:100%" src="{{asset('assets/images/lancamentos.png')}}" alt="Padrãocolor">
                    </div> -->
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
        @if(count($productsH) >= 0)
            <div class='container'>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3 d-none d-md-block testando">
                            <?php $chunked = $productsH->chunk(4);?>
                            @for($i=0;$i<count($chunked);$i++)
                                @include("pages.products.product_item",["row" => $chunked[$i]])                                
                            @endfor
                        </div>
                        <div class="mb-4 d-block d-md-none">
                            <div class="d-flex flex-row product-list-content overflow-auto">
                                @foreach($productsH as $product)
                                    @include("pages.products.product_item_card",["product" => $product])
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif                    
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
        @if($showmore)
            <div class="d-flex mt-5 justify-content-center">
                <a href="produtos/nossosprodutos" class="btn-showmore">                    
                    Ver todos os produtos                    
                </a>
            </div>
        @endif
    @endif
</div>
