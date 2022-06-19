@extends('template.simple')
@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{route('home')}}">Início</a></li>
      <li class="breadcrumb-item"><a href="{{route('order.view')}}">Compras</a></li>
      <li class="breadcrumb-item">#{{$order->code}}</li>
      <li class="breadcrumb-item">Timeline</li>
    </ol>
</nav>

<order-timeline  :order="{{json_encode($order)}}"/>

@endsection
