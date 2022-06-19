<?php
    $auth = Auth::check() ? Auth::user() : false;
    $category = "todos";
    $currentCategory = preg_replace('/( )+/','-',$department);
    $showprice = true;
    $colors = ["impressao-digital" => "pink","impressao-offset" => "cyan","comunicacao-visual" => "orange" , "todos" => "red"];
    if(!@$products) {
        $products = \App\Models\Product::where("id",">",0);
        if($showmore) $products = $products->limit(32);
        if($currentCategory!='todos') $products = $products->where('department',$department);
        $products = $products->get();
    }

    //$getCategoryId = \App\Models\ModelCategory::where('model_type',"=","product")->get(['category_id']);
    $getCategoryId = \App\Models\Category::where('id',"=",14)->get(['id','name']);
    $getCategoryName = \App\Models\Category::where('id',"=",14)->get(['name']);
    $getCategoryIdCollection = collect($getCategoryId);    
    $pluckedCategoryId = $getCategoryIdCollection->pluck('id');
    $pluckedCategoryName = $getCategoryIdCollection->pluck('name');    
    //$pluckedCategoryId = $pluckedCategoryId->unique();
    $pluckedCategoryId->all();
    $pluckedCategoryName->all();  

        
        
        $filterProductsByCategory = \App\Models\ModelCategory::where('model_type',"=","product");
        $filterProductsByCategory = $filterProductsByCategory->join("products", function($join){
            $join->on('model_categories.model_id', '=', 'products.id');
        })
                                ->where('model_categories.category_id',"=",14)
                                ->get(['id']);
        $ByCategory = \App\Models\ModelCategory::where('model_type',"=","product");
        $ByCategory = $ByCategory->join("categories", function($join){
            $join->on('model_categories.model_id', '=', 'categories.id');
        })
                                //->where('model_categories.category_id',"=",$value)
                                ->get(['name']);

        // ${"collection$k"} = collect(${"filterProductsByCategory$k"});    
        // ${"plucked$k"} = ${"collection$k"}->pluck('id');    
        // ${"plucked$k"}->all();

        $ProductsByCategory = \App\Models\Product::where("status",">",0);
        $ProductsByCategory = $ProductsByCategory->limit(8)->get();

        
                                
        



    

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

    <div class="container">
        <div class="row mt-3">
            <div class="col-sm-12 mb-3">
                @if(@!$_GET['procurando'])
                    <h1 class="f-32 f-space text-center">
                        <span class="font-weight-light">QUERIDINHOS DO</span>
                        <span class="font-weight-bold">SITE</span>
                    </h1>
                @else
                    <p class="f-32 f-space text-center"><span class="font-weight-light">Você procurou por </span><span class="font-weight-bold">{{@$_GET['procurando']}}</span> </p>
                @endif
            </div>
            
        </div>
    </div>
    
    
    @if(@!$_GET['procurando'])       

        
        @if(count(${"ProductsByCategory"}) >= 0)
        <div class='container'>
            <div class="row">
                <div class="col-12">
                    <div class="mt-3 d-none d-md-block">            
                        <!-- <h4>{{ $getCategoryName }}</h4>                 -->
                        <?php $chunked = $ProductsByCategory->chunk(4);?>
                        @for($i=0;$i<count($chunked);$i++)
                            @include("pages.products.product_item_by_category",["row" => $chunked[$i]])                                
                        @endfor
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
                <a href="/produtos" class="btn-showmore">                    
                    Ver todos os produtos                    
                </a>
            </div>
        @endif
    @endif

