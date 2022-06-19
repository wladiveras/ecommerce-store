<?php $department = @$department ? $department : "todos"; ?>
@extends('template.simple')
@section('content')
<nav aria-label="breadcrumb" id="products-breadcrumb" class="mb-4">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="">Início</a></li>
    <li class="breadcrumb-item"><a href="/produtos">Produtos</a></li>
      @if($department!="todos")
        <li class="breadcrumb-item"><a href="{{route('products.department',['department'=>preg_replace('/( )+/','-',$department)])}}">
            @switch($department)
                @case("impressao offset")
                    Impressão Offset
                    @break
                @case("comunicacao visual")
                    Comunicação Visual
                    @break
                @case("impressao digital")
                    Impressão Digital
                    @break
                @default
                    {{$department}}
            @endswitch
        </a></li>
    @endif
  </ol>
</nav>
<?php $showmore = false;?>
@include("pages.products.list",compact('department','showmore','products'))

@endsection
