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
        <h3 class="f-18 font-weight-bold  f-space">Meus Pedidos</h3>
        <order-filter :request="{{json_encode($filter)}}" :counter="{{json_encode($counter)}}" user="{{Auth::user()->name}}" ></order-filter>
        <resource-table :filter="{{json_encode($filter)}}" route="{{route('order.view.search')}}" perpage="20">
            <template slot="table">
                <div class="detail-card">
                        
                    <div class="card-header" style="border: 1px solid #80808040;"></div>
                    <table id="table" class="table table-striped items" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:1%;">Código</th>
                                <th class="no-sort" style="width:5%;">Nome(s) da(s) arte</th>
                                <th style="width:1%;">Data</th>
                                <th style="width:1%;">Hora</th>
                                <th style="width:1%;">Status</th>
                                <th style="width:5%;" class="no-sort">Tipo Pgto</th>
                                <th style="width:1%;" class="no-sort">Frete</th>
                                <th style="width:1%;" class="no-sort">Total</th>
                                <th style="width:1%;" class="no-sort"></th>
                                <th style="width:1%;" class="no-sort"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </template>
        </resource-table>
    </div>
</div>
@endsection
