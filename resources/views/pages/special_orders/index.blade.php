@extends('template.simple')
@section('body')
<div class="container-fluid pt-5" style="background-color:#FCFCFC;">
    <div class="wrapper">
        <div class="container-fluid">
            <div class="special_orders">
                <div class="row d-flex">
                    <div class="col-md-6 col-sm-12 d-flex flex-column">
                        <h1 class="title"> Projetos Especiais</h1>
                        @if($data["special_orders"]["show_list_link"])
                            <div class="mb-4"><a href="{{$data["routes"]["list"]}}" class="btn btn-secondary">Ver @if(!$data["is_admin"]) Meus @endif Projetos Especiais</a></div>
                        @endif
                        <div class="sub-title">
                            <p class="mb-0">Se você tem alguma demanda de produto especial,</p>
                            <p class="mb-0">customização especifica nós ajudamos você na produção</p>
                            <p class="mb-0">desse material.</p>
                        </div>
                        <div class="topics mt-4">
                            <p class="mb-0 d-flex align-items-center"><i class="icon material-icons mr-3">check</i> Atendimento Exclusivo</p>
                            <p class="mb-0 d-flex align-items-center"><i class="icon material-icons mr-3">check</i> Melhor Preço Para o Seu Projeto</p>
                            <p class="mb-0 d-flex align-items-center"><i class="icon material-icons mr-3">check</i> Qualidade de Produto Final</p>
                        </div>
                        @if(!$data["is_admin"])
                            <a class="btn btn-primary btn-block px-5 py-3 mt-3" href="{{$data['routes']['create']}}">{{$data["text_btn"]}}</a>
                        @endif
                    </div>
                    <div class="col-md-6 col-sm-12 p-5 d-flex justify-content-center align-items-center">
                        <img class="w-100" src="/assets/images/handshake.png" /> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('content')
    <div class="special_orders">
        <div class="row mt-5">
            <div class="col-12">
                <h1 class="title_2 text-center">Como Funciona ?</h1>
                <div class="sub-title_2 text-center">Acompanhe seus projetos de forma simplificada através do nosso site.</div>
            </div>
        </div>
        <div class="row d-flex align-items-center mt-4">
            <div class="col-md-6 col-sm-12 px-3 d-flex align-items-center justify-content-center">
                <img class="w-100 image_2" src="/assets/images/example_2.png" />
            </div>
            <div class="col-md-6 col-sm-12 d-flex justify-content-center flex-column align-items-start">
                <div class="topics_2 d-flex align-items-center"><i class="icon material-icons mr-3">message</i>Descreva seu projeto em detalhes e a data que você precisa dele pronto.</div>
                <div class="topics_2 d-flex align-items-center"><i class="icon material-icons mr-3">contact_phone</i>Receba um atendimento exclusivo de um gerente de contas que vai auxiliar no seu projeto.</div>
                <div class="topics_2 d-flex align-items-center"><i class="icon material-icons mr-3">check</i>Após sua aprovação, seu pedido começa a ser produzido e entregue aonde você precisar.</div>
                @if(!$data["is_admin"])
                    <a class="btn btn-primary btn-block px-5 py-3 mt-3" href="{{$data['routes']['create']}}">{{$data["text_btn"]}}</a>
                @endif
            </div>
        </div>
    </div>
@endsection