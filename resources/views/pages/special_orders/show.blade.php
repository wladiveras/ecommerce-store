@extends('template.simple')
@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('pedidos_especiais.index')}}">Projetos Especiais</a></li>
        <li class="breadcrumb-item"><a href="{{route('pedidos_especiais.list')}}">@if(!$data["is_admin"]) Meus @endif Projetos</a></li>
        <li class="breadcrumb-item active">Detalhes</li>
    </ol>
</nav>
<div class="special_orders">
    <h4 style="color:#8e8e8e;">{{$data["formated_id"]}} - {{$data["title"]}}</h4>

    <div class="row">
        <div class="col-md-7 col-sm-12">
            <div class="card card-item status {{$data["status_value"]}} no-hover">
                <div class="card-header d-flex justify-content-between">
                    <strong>Detalhes do Projeto</strong>
                    <div class="ml-2 d-flex flex-row align-items-center mb-0"><span class="status-ball {{$data["status_value"]}} mx-2"></span>{{$data["status"]}}</div>
                </div>
                <div class="card-body">
                    <div class="row d-flex">
                        @if($data["is_admin"])
                            <div class="col-12 d-flex flex-row justify-content-between">
                                <div class="d-flex flex-row">
                                    <div><strong>Autor : </strong></div>
                                    <div class="ml-2">#{{$data["user"]["id"]}} - {{$data["user"]["username"]}}</div>
                                </div>
                                <div class="d-flex flex-row">
                                    <div><strong>Telefone : </strong></div>
                                    <div class="ml-2">{{$data["user"]["phone"]}}</div>
                                </div>
                                <div class="d-flex flex-row">
                                    <div><strong>Email : </strong></div>
                                    <div class="ml-2">{{$data["user"]["email"]}}</div>
                                </div>
                            </div>
                        @endif
                        <div class="col-12 d-flex flex-row justify-content-between">
                            <div class="d-flex flex-row">
                                <div><strong>Título do Projeto : </strong></div>
                                <div class="ml-2">{{$data["title"]}}</div>
                            </div>
                            <div class="d-flex flex-row">
                                <div><strong>Data de Solicitação : </strong></div>
                                <div class="ml-2">{{$data["formated_created_at"]}}</div>
                            </div>
                        </div>
                        <div class="col-12 d-flex flex-row justify-content-between">
                                <div class="d-flex flex-row">
                                    <div><strong>Data de Entrega : </strong></div>
                                    <div class="ml-2">{{$data["target_date"]}}</div>
                                </div>
                            </div>
                        <hr>
                        <div class="col-12 d-flex flex-column mt-4">
                            <div><strong>Descrição do Projeto : </strong></div>
                            <div>{!! $data["description"] !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <strong>Timeline</strong>
                </div>
                <div class="card-body">
                    <el-timeline class="p-0">
                        @if($data["is_admin"])
                            <update-special-order :data="{{json_encode($data)}}"></update-special-order>
                        @endif
                        @foreach(array_reverse($data["hystory"]) as $item)
                            <el-timeline-item timestamp="{{date_format(date_create($item['created_at']),'d/m/Y - H:i:s')}}" placement="top">
                                <div class="card card-item status {{$item["status"]["value"]}} no-hover">
                                    <div class="card-body p-1"> 
                                        <p class="ml-2 d-flex flex-row align-items-center mb-0">Status Definido para <span class="status-ball {{$item["status"]["value"]}} mx-2"></span>{{$item["status"]["name"]}}</p>
                                        @if(@$item["description"]) <div class="ml-2 mt-2">{!! @$item["description"] !!}</div> @endif
                                    </div>
                                </div>
                            </el-timeline-item>
                        @endforeach
                        <el-timeline-item timestamp="{{$data['formated_created_at']}}" placement="top">
                            <div class="card card-item status opened no-hover">
                                <div class="card-body p-1"> 
                                    <p class="ml-2 d-flex flex-row align-items-center mb-0">Status Definido para <span class="status-ball opened mx-2"></span>Aberto</p>
                                </div>
                            </div>
                        </el-timeline-item>
                    </el-timeline>
                </div>
                <div class="card-foter"></div>
            </div>
        </div>
    </div>
</div>
@endsection