@extends('template.master')
@section('header')
<?php 
    $description = @$product->custom_extra_info["seo_page_description"] ? $product->custom_extra_info["seo_page_description"] : $product->short_description;
    $title = @$product->custom_extra_info["seo_page_title"] ? $product->custom_extra_info["seo_page_title"] : $product->name;

    $colors = ["impressao-digital" => "pink","impressao-offset" => "cyan","comunicacao-visual" => "orange" , "todos" => "red"];
    $category = "todos";
    //$currentCategory = preg_replace('/( )+/','-',$department);
    $showprice = true;

    $randonCat = rand(1,12);

    $filterProductsByCategory2 = \App\Models\ModelCategory::where('model_type',"=","product");
    $filterProductsByCategory2 = $filterProductsByCategory2->join("products", function($join){
        $join->on('model_categories.model_id', '=', 'products.id');
    })
                            ->where('model_categories.category_id',"=",$randonCat)
                            ->get(['id']);

    $collection2 = collect($filterProductsByCategory2);    
    $plucked2 = $collection2->pluck('id');    
    $plucked2->all();

    $ProductsByCategory2 = \App\Models\Product::where("status",">",0);
    $ProductsByCategory2 = $ProductsByCategory2->whereIn('id', $plucked2)
                            ->limit(3)
                            ->get();
?>
@if($description)
    <meta name="description" content="{{$description}}"/>
@endif
@endsection
@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Início</a></li>
        <li class="breadcrumb-item"><a href="/produtos">Produtos</a></li>
        <!-- <li class="breadcrumb-item">
            <a href="{{route('products.department',['department'=>  str_replace(" ","-",$product->department)   ])}}">
                @switch($product->department)
                    @case("comunicacao visual")
                        Comunicação Visual
                        @break
                    @case("impressao digital")
                        Impressão Digital
                        @break
                    @case("impressao offsset")
                        Impressão Offset
                        @break
                    @default
                        {{$product->department}}
                @endswitch
            </a>
        </li> -->
        <li class="breadcrumb-item active" aria-current="page">{{$product->name}}</li>
    </ol>
</nav>
<div>
<div class="">
    <div itemtype="http://schema.org/Product" itemscope>
        <meta itemprop="name" content="{{$product->name}}" />
        @foreach($product->files as $file)
            <link itemprop="image" href="{{$file->raw_url}}" />
        @endforeach
        <meta itemprop="description" content="{{$product->short_description}}" />
        @if(Auth::check())
        <div itemprop="offers" itemtype="http://schema.org/Offer" itemscope>
            <meta itemprop="availability" content="https://schema.org/InStock" />
            <meta itemprop="priceCurrency" content="R$" />
            <meta itemprop="price" content="{{$product->minPrice->min_price}}" />
            <div itemprop="seller" itemtype="http://schema.org/Organization" itemscope>
                <meta itemprop="name" content="PadrãoColor" />
            </div>
        </div>
        @endif
        <div itemprop="aggregateRating" itemtype="http://schema.org/AggregateRating" itemscope>
            <meta itemprop="reviewCount" content="{{$product->avaliations ? count($product->avaliations) : 0}}" />
            <meta itemprop="ratingValue" content="{{($product->rate ? $product->rate : 0)}}" />
        </div>
        @foreach(($product->avaliations ? $product->avaliations : []) as $avaliation)
            <div itemprop="review" itemtype="http://schema.org/Review" itemscope>
                <div itemprop="author" itemtype="http://schema.org/Person" itemscope>
                    <meta itemprop="name" content="{{$avaliation->author}}" />
                </div>
                <div itemprop="reviewRating" itemtype="http://schema.org/Rating" itemscope>
                    <meta itemprop="ratingValue" content="{{($avaliation->rate ? $avaliation->rate : 0)}}" />
                    <meta itemprop="bestRating" content="5" />
                </div>
            </div>
        @endforeach
      </div>
    </div>
    <product-view :product="{{json_encode($product)}}" :templates="{{json_encode($templates)}}" :user="{{json_encode(Auth::user())}}">
        <h1 class="product_name" slot="title">{!! $product->name !!}</h1>       
        
        <h3 class="categories" slot="categories">{!!
            implode(" / ",array_map(function($x) {
                return $x["name"];
            },$product->categoriesList->toArray()))
        !!}</h3>
        <p slot="short_description" class="short_description">
            @if($product->short_description!='null')
                {!! $product->short_description ? str_replace(`/\r?\n/g`, '<br />',$product->short_description) : "" !!}
            @endif
        </p>
        <p class="description">
            @if($product->description!='null')
                {!! $product->description ? strip_tags ($product->description) : "" !!}
            @endif
        </p>
        
    </product-view>

    <h4 class="mt-4" style="text-transform: uppercase;color: #1597a5;">Leve Também!</h4>

    <div class='container'>
        <div class="row">
            <div class="col-12">
                <div class="d-none d-md-block">
                    <?php $chunked = $ProductsByCategory2->chunk(3);?>
                    @for($i=0;$i<count($chunked);$i++)
                        @include("pages.products.product_item_suggestion",["row" => $chunked[$i]])                                
                    @endfor
                </div>
                <div class="mb-4 d-block d-md-none">
                    <div class="d-flex flex-row d-flex-mobile product-list-content">
                        @foreach($ProductsByCategory2 as $product)
                            @include("pages.products.product_item_suggestion_card",["product" => $product])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection