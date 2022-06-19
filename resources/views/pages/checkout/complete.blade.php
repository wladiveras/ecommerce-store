@extends('template.simple')
@section('content')
<div class="container" id="order-complete">
  <div class=" row d-flex justify-content-center flex-column align-items-center">
    <img src="/svg/checkout/Complete.svg" class="svg complete-svg mb-3">

    <p class="f-32 text-primary text-center">COMPRA FINALIZADA COM SUCESSO</p>
    <p class="f-14 g-5">
      {{$message}}
    </p>
    <p class="f-22 g-5 font-weight-bold f-space mb-4">Nº DO PEDIDO - {{$order->code}}</p>
    @if($type == 'bankslip')
    <div class="mb-3">
      <p><a href="{{$order->payments->first()['data']['payment']['bankslip']['html']}}" class="link">Imprimir o boleto bancário</a></p>
    </div>
    @endif
    <div class="mb-4">
      <a href="{{route('order.view')}}" class="btn btn-secondary btn-block f-space mb-3 text-white">Acompanhar Pedidos</a>
      <a href="{{route('products.index')}}" class="btn btn-primary btn-block f-space text-secondary">Continuar Comprando</a>
    </div>
    {{-- <p><a href="#">Politica de compra</a> • <a href="#">Politica de cancelamento</a></p> --}}
  </div>
</div>

@if($type == 'bankslip')
<script>
  var a = document.createElement("a");
  a.href = "{{$order->payments->first()['data']['payment']['bankslip']['pdf']}}";
  a.setAttribute("download", "{{$order->code}}.pdf");
  var b = document.createEvent("MouseEvents");
  b.initEvent("click", false, true);
  a.dispatchEvent(b);
</script>
@endif
@endsection