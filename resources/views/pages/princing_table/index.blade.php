@extends('template.simple')
@section('content')
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('home')}}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{route('products.department',['department'=>'todos'])}}">Produtos</a></li>
            <li class="breadcrumb-item">
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
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <a href="{{route('product.view',['product'=>$product->slug])}}">{{$product->name}}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Compra Rápida</li>
        </ol>
    </nav>
    <h3>Compra rápida de {{$product->name}}</h3>
    <princing-table   
        @if(Auth::check()) :user="{{json_encode(Auth::user())}}" @endif :productid="{{$product->id}}"  >
    </princing-table>
@endsection