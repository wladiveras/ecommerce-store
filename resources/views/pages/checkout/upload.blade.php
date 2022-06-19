@extends('template.simple')
@section('content')
<nav aria-label="breadcrumb" class="mb-4" id="products-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Início</a></li>
        <li class="breadcrumb-item"><a href="{{route('products.department',['department'=>'todos'])}}">Produtos</a></li>
        <li class="breadcrumb-item">
            <a href="{{route('products.department',['department'=>  str_replace(" ","-",$data['product']->department)   ])}}">
                @switch($data['product']->department)
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
                        {{$data['product']->department}}
                @endswitch
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <a href="{{route('product.view',['product'=>$data['product']->slug])}}">{{$data["product"]->name}}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Configuração de Produto</li>
    </ol>
</nav>
    <config-product
        show_card_attributes="{{config('product_config.options.show_config_attribute_card')}}"
        route_upload="{{$route_upload}}"
        w2p_token="{{$w2p_token}}"
        route_web2print="{{$route_w2p}}"
        :data="{{json_encode($data)}}"
        
    />
@endsection
