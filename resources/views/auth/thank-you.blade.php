@extends('template.simple')
@section('content')
<div class="container" id="order-complete">
  <div class=" row d-flex justify-content-center flex-column align-items-center">
    <img src="/svg/checkout/Complete.svg" class="svg complete-svg mb-3">

    <p class="f-32 text-primary">FORMULÁRIO ENVIADO COM SUCESSO</p>
    <p class="f-14 g-5">Muito obrigado pelo interesse, se aprovado lhe contataremos em breve.</p>
    <div class="mb-4">
        <a href="https://padraocolor.com.br/blog/grafica-de-revenda-entenda-como-funciona/" 
        class="btn btn-secondary f-space mr-2 text-white">Como a Revenda Gráfica Funciona</a>
      <a href="https://padraocolor.com.br/Blog/" class="btn btn-primary f-space ml-2 text-secondary">Veja Nosso Blog</a>
    </div>
    @if(false)
    <p class="f-22 g-5 font-weight-bold f-space mb-4">Nº DO PEDIDO - {{$order->code}}</p>
    <div class="mb-4">
      <a href="{{route('order.view')}}" class="btn btn-secondary f-space mr-2 text-white">Acompanhar Pedidos</a>
      <a href="{{route('products.index')}}" class="btn btn-primary f-space ml-2 text-secondary">Continuar Comprando</a>
    </div>
    <p><a href="#">Politica de compra</a> • <a href="#">Politica de cancelamento</a></p>
    @endif
  </div>
</div>
@endsection