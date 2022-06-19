<?php $product = is_array($product) ? $product["product"] : $product;?>
<a href="{{route('product.view',['slug' => $product->slug])}}" class="col-sm-12 col-md-4 mb-4">
    <div class="product-item suggestion aux h-100">
        <div class="product-item-overlay">
            <div class="product-item-overlay-button">Compre já</div>
        </div>
        @if($product->status ==  2)
            <div style="background:#ccc;padding: 5px;text-align: center;text-transform: uppercase;font-weight: bold;">
                Temporariamente Indisponível
            </div>        
        @endif        
        <div class="d-flex justify-content-center mt-2" style="width:130px">
            <div class="product-img">
                @if(count($product->files)<=0)
                    <img-loader url="/assets/images/placeholder-thumbnail.png" :thumb="{{json_encode(true)}}"/>
                @else
                    <img-loader url="{{$product->files[0]->raw_url}}" alt="{{$product->name}}" :thumb="{{json_encode(true)}}"/>
                @endif
            </div>
        </div>
        <div class="content mt-2 {{ $product->status ==  2 ? 'indisponivel' : ''  }}" >
            <p class="f-16 f-space font-weight-bold">{{$product->name}}</p>
            @if($showprice)
                @if(@$product->viewPrice["promo_value"]>0 && @$product->viewPrice["promo_type"]!='hide')
                    <p class="from_for mb-0">De {{number_format(promo_price($product),2,",",".") }} por</p>
                @endif
              
                <p class="beggining mb-0">A partir de </p>
                <p class="price mb-0">
                    R$ {{@$product->viewPrice["price"]  ? number_format($product->viewPrice["price"],2,",",".") : "2"}}
                    <small class="rule">{{@$product->viewPrice["rule"]}}</small>
                </p>
               
            @else
                <p class="font-weight-bold"><span class="f-12 g-5">Entre para ver os preços</span></p>
            @endif
            <div style="background: #1799a8;color: #fff;padding: 3px 10px;border-radius: 5px; text-align: center;">
                Ver Produto
            </div>
        </div>
    </div>
</a>
