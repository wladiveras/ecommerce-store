@extends('template.simple')
@section('content')
<nav aria-label="breadcrumb" class="mb-4" style="margin-bottom: 0px!important;">
    <ol class="breadcrumb mb-1" >
      <li class="breadcrumb-item"><a href="{{route('home')}}">Início</a></li>
      <li class="breadcrumb-item"><a href="{{route('order.view')}}">Compras</a></li>
      <li class="breadcrumb-item">#{{$order->code}}</li>
      <li class="breadcrumb-item">Detalhes</li>
    </ol>
</nav>

  <order-detail
      :order="{{json_encode($order)}}" 
      :status_list="{{json_encode($status_list)}}"
      :status_payment_list="{{json_encode($status_payment_list)}}"
      :steps="{{json_encode($steps)}}"
      :shipping="{{json_encode($shipping)}}"
      :status_item_list="{{json_encode($status_item_list)}}"
      can_cancel="{{config('dashboard_api.order_cancel.enabled')}}"
      can_resendart="{{config('dashboard_api.resend_art.enabled')}}" 
  />

@endsection
