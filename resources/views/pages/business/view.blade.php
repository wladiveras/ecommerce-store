<?php
    $auth = Auth::check() ? Auth::user() : false;
    $category = "todos";
    $showprice = $auth;
    $colors = ["impressao-digital" => "pink","impressao-offset" => "cyan","comunicacao-visual" => "orange" , "todos" => "red"];
 
?>
@extends('template.simple')
@section('content')
@if(count($result) >= 0)

    <banner-business :business="{{json_encode($business)}}" >           
    </banner-business>
    
    <div class='container business-container'>
        <div class="row mt-5">
            <div class="col-sm-12">
                <!-- <h2 class="f-32 f-space text-center mb-3"><span class="font-weight-bold" style="color: #7a7a7a;">{{ $business->name }}</span></h2> -->
                @if(!$business->chamada === null)
                    <p style="text-align: center;font-size: 20px;color: {{ $business->cor }};font-weight: normal; margin-bottom:50px">{{ $business->chamada }}</p>
                @endif
            </div>
        </div>
        
        <div class="mb-4 d-none d-md-block">
            <?php $chunked = array_chunk($result,4);?>
            @for($i=0;$i<count($chunked);$i++)
                @include("pages.products.product_item",["row" => $chunked[$i]])
            @endfor
        </div>
        <div class="mb-4 d-block d-md-none">
            <div class="d-flex flex-row product-list-content product-list-content-scroll">
                @foreach($result as $most)
                    @include("pages.products.product_item_card",["product" => $most["product"]])
                @endforeach
            </div>
        </div>
    </div>
@endif

@endsection
