@extends('template.simple')
@section('content')
<div class="row user-page">
<div class="col-2 user-page--menu pt-4">
        <p>Olá, {{Auth::user()->name}}</p>
        <div>
            <ul>
                <li class="d-flex align-items-center">
                    <i class="material-icons icon mr-2">person_pin</i>
                    <a href="{{route('user.data')}}">Meus Dados</a>
                </li>
                <li class="d-flex align-items-center">
                    <i class="material-icons icon mr-2">format_list_bulleted</i>
                    <a href="{{route('order.view')}}">Meus Pedidos</a>
                </li>
                <li class="d-flex align-items-center">
                    <i class="material-icons icon mr-2">star</i>
                    <a href="{{route('lista_desejo.view')}}">Minha Lista de Desejos</a>
                </li>
            </ul>
            
        </div>
        <div class="d-flex align-items-center mt-4">
            <i class="material-icons icon mr-2">exit_to_app</i>
            <a href="/logout">Sair</a>    
        </div>
        
    </div>
  <div class="col-10 p-4">
    <div>
      
      <h3 class="f-18 font-weight-bold  f-space">Meus Dados</h3>
      
      <div class="card-body">
        <general-data :user="{{json_encode($user)}}"></general-data>
      </div>
    </div>
    <div>
      
      <h3 class="f-18 font-weight-bold f-space">Meus endereços de entrega</h3>
      
      <div id="addresses-list">

        <div class="card-body">
          <shipping-locations :main="{{json_encode($main)}}" :locations="{{json_encode($addresses)}}" :mode="'edit'"></shipping-locations>
        </div>

      </div>
    </div>

    @if($cards->count()>0)
      <div class="card">
        <div class="card-header" v-b-toggle.card-list>
          <span class="f-18 font-weight-bold text-uppercase f-space">CARTÕES DE CRÉDITO SALVOS</span>
        </div>
        <b-collapse id="card-list">

        <div class="card-body">
          <card-list-edit :cards="{{json_encode($cards)}}" />
        </div>
      </b-collapse>
      </div>
    @endif

  </div>
</div>
@endsection
