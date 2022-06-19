@extends('template.simple')
@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('home')}}">Início</a></li>
      <li class="breadcrumb-item"><a href="{{route('order.view')}}">Compras</a></li>
      <li class="breadcrumb-item">#{{$order->code}}</li>
      <li class="breadcrumb-item"><a href="{{route('order.view.detail',['hashid'=>$order->hashid])}}">Detalhes</a></li>
      <li class="breadcrumb-item">Reenvio de Arte</li>
    </ol>
</nav>
<order-resend :order="{{json_encode($order)}}" :sku="{{json_encode($pedido)}}"/>

@endsection
