@extends('template.pdf')
@section('content')
<style>
  body { font-family: DejaVu Sans; }
  .greeting{
    margin:25px 0;
  }
  .uppercase{
    text-transform: uppercase;
  }
  .sku-item{
    border-bottom: #f1f1f1 5px solid;
  }
  .sku-item p:nth-child(n+1){
    margin-bottom:8px;
    margin-top:8px;
  }
</style>
@if($user->reseller->logo_url)
  <img src="{{$user->reseller->logoCdnUrl."?height=120&width=120"}}"alt="">
@endif
<p style="float:right">Gerado em: {{\Carbon::now()->format('d/m/Y')}}</p>

<div class="greeting uppercase">
  <p>Olá {{$user->name}},</p>
  <p>Você está recebendo, informações do produto ao qual solicitou orçamento.</p>
</div>
<div class="sku-list">
  <?php
    $subTotal = 0;
  ?>
  @foreach($cart as $key => $item)
  <?php
    $data = $item->attributes['data'];
    $skus = $item->attributes['items'];
    $product = $skus[0]['product'];
    $subTotal += $item->price;
    $finishes_time = 0;
    $prepared_in = max((Array)array_pluck($skus,'sku.prepared_in'));
  ?>
  <div class="sku-item">
    <h3 style="font-weight:bold;">{{$data['ref_name']}}</h3>
    <p>Produto: {{$product['name']}}</p>
    <p>Tipo de Serviço:
      @switch($item->attributes['data']['chosen'])
      @case('CA')
        Criação de Arte
        @break
      @case('UP')
        Upload de Arte
        @break
      @endswitch
    </p>
    <p>
      {{$product['name']}}: {{implode(', ',$data['options'])}}
      @if(@$item->attributes['data']['extra']['cover'])
        Capa {{$item->attributes['data']['extra']['cover']}} m/g²
      @endif
    </p>
    @if($item->attributes['data']['finishes'])
    <p>Acabamento:
      @foreach($data['finishes']['finishes'] as $test => $finish)
      <?php
        $finishes_time += @$finish['prepared_in'];
      ?>
      {{$finish['name']}} x {{$finish['qty']}}@if(!$loop->last),@endif
      @endforeach
    </p>
    @endif
    @if($item->attributes['data']['additional'] != 0)
    <p>
      Opções Adicionais:
      {{implode(', ',array_pluck($item->attributes['data']['additional']['additional_attributes'],'name'))}}
    </p>
    @endif
    <?php
      if($item->quantity > 1){
        $amount = $item->quantity;
      }else{
        $amount = 1;
      }
    ?>
    <p>Quantidade: {{$amount}} {{\Illuminate\Support\Str::plural('unidade',$amount)}}</p>
    <p>Preço: {{formatMoney($item->price)}}
      @if($item->attributes['data']['finishes'])
        (acabamento {{formatMoney($item->attributes['data']['finishes']['total'])}})
      @endif
    </p>
    <p>Tempo de Produção: {{($prepared_in > 1)? getNextBusinessDay($prepared_in) . " (".$prepared_in." dias úteis)" : getNextBusinessDay(1). " (1 dia útil)"}}</p>
  </div>
  @endforeach
  <p style="float:right; text-align:right; font-weight:bold">Total da Compra: {{formatMoney($subTotal)}}
    <br>
    <span style="font-size:14px; font-weight:100">Obs: preço do frete não incluso</span>
  </p>
  <div class="">
    <p style="float:right; position:absolute; right:0"></p>
  </div>
</div>
@endsection
