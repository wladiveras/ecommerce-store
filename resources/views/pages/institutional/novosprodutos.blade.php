@extends('template.master')
<style>
.jumbotron {
    background-image: url({{asset('novos-produtos/img/bannersite.jpg')}});
    background-size: cover;
    background-repeat: no-repeat;
    background-position: top center;
    padding: 0;
    margin: 0;
    height: 60vh;
}
.x-card {
    max-height: 412px;
}
.x-card .card-title {
    font-family: 'Pacifico', cursive;
    font-size: 2em;
    position: absolute;
    top: -20px;
    right: -20px;
    background: linear-gradient(90deg, rgba(255,121,23,1) 0%, rgba(255,110,169,1) 100%);
    color: #fff;
    padding: 10px 6px;
    border-radius: 50px;
}
.x-card .card-footer {
    background-color: #fff;
    border-top: none;
}
.x-btn {
  background-color: #D6EF63 !important;
  border-color: unset;      
}
.x-btn:hover{
  background-color: #4caf50 !important;
  border-color: unset;
}
@media (max-width: 600px) {
  .jumbotron {
      background-image: url({{asset('novos-produtos/img/mobilesite.jpg')}});
  }
  .x-card {
      max-height: 286px;
  }
}
</style>
@section('content')
<nav aria-label="breadcrumb" >
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('home')}}">Início</a></li>
        <li class="breadcrumb-item">Novos Produtos com Desconto</li>
    </ol>
</nav>
<div class="container-fluid">
    <a href="#promos" class="bannerLink">
    <header class="jumbotron my-4">
    </header></a>
    <?php
        $auth_check= Auth::check();
        $cards = [
            ["title" => "10%" , "image" => asset('novos-produtos/img/agenda.jpg'),"route" => "/produtos/agenda/offset-75-4-0-15x21-cm-com-espiral-140-folhas-a-partir-de-201/eyJxdHkiOjIwMSwiY2FsY19xdHkiOjIwMSwiYXJ0IjoiVVAifQ=="],
            ["title" => "10%" , "image" => asset('novos-produtos/img/almofada.jpg'),"route" => "/produtos/almofada-sublimatica/oxford-40x40-sublimacao/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
            ["title" => "10%" , "image" => asset('novos-produtos/img/bolsa.jpg'),"route" => "/produtos/bolsa-branca-com-alca/tecido-cordoba-47x37-cm-sublimacao-1/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
            ["title" => "10%" , "image" => asset('novos-produtos/img/caderno.jpg'),"route" => "/produtos/caderno/offset-75-4-0-20x28-cm-1-materia-com-espiral-96-folhas-a-partir-de-101/eyJxdHkiOjEwMSwiY2FsY19xdHkiOjEwMSwiYXJ0IjoiVVAifQ=="],
            ["title" => "10%" , "image" => asset('novos-produtos/img/necessaire.jpg'),"route" => "/produtos/necessaire/tecido-cordoba-14x22x10-cm-sublimacao-1/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
            ["title" => "10%" , "image" => asset('novos-produtos/img/toalha.jpg'),"route" => "/produtos/toalha-de-rosto-branca/poliester-45x70-cm-sublimacao-1/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
            ["title" => "10%" , "image" => asset('novos-produtos/img/toalha-mao.jpg'),"route" => "/produtos/toalha-de-mao/poliester-37x23-cm-sublimacao-1/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
//novos itens bandeirola
["title" => "10%" , "image" => asset('novos-produtos/img/caixa-combinado-japones.jpg'),"route" => "/produtos/bandeirola"],
//novos itens caixa-combinado-japones
["title" => "10%" , "image" => asset('novos-produtos/img/caixa-combinado-japones.jpg'),"route" => "/produtos/caixa-combinado-japones"],
//novos itens caixa-de-presente
["title" => "10%" , "image" => asset('novos-produtos/img/caixa-de-presente.jpg'),"route" => "/produtos/caixa-de-presente"],
//novos itens caixa-de-presente-com-alca
["title" => "10%" , "image" => asset('novos-produtos/img/caixa-de-presente-com-alca.png'),"route" => "/produtos/caixa-de-presente-com-alca"],
//novos caixa bolo fatiado
["title" => "10%" , "image" => asset('novos-produtos/img/caixa-fatia-de-bolo.png'),"route" => "/produtos/caixa-fatia-de-bolo"],
//novos caixa travesseiro
["title" => "10%" , "image" => asset('novos-produtos/img/caixa-travesseiro.png'),"route" => "/produtos/caixa-travesseiro"],
//novos convite
["title" => "10%" , "image" => asset('novos-produtos/img/convite.png'),"route" => "/produtos/convite"],
//novos cubo de mesa
["title" => "10%" , "image" => asset('novos-produtos/img/cubo-de-mesa.png'),"route" => "/produtos/cubo-em-papel"],
//novos embalagem sushi
["title" => "10%" , "image" => asset('novos-produtos/img/embalage-de-sushi.png'),"route" => "/produtos/embalage-para-sushi"],
//novos embalagem rolinho primavera
["title" => "10%" , "image" => asset('novos-produtos/img/embalagem-de-rolinho-primavera.png'),"route" => "/produtos/embalagem-para-rolinho-primavera"],
//novos embalagem temaki
["title" => "10%" , "image" => asset('novos-produtos/img/embalagem-para-temaki.jpg'),"route" => "/produtos/embalagem-para-temaki"],
//novos estojo
["title" => "10%" , "image" => asset('novos-produtos/img/estojo.jpg'),"route" => "/produtos/estojo"],
//novos jogo americano
["title" => "10%" , "image" => asset('novos-produtos/img/jogo-americano.jpg'),"route" => "/produtos/jogo-americano1"],
//novos luva de cozinha
["title" => "10%" , "image" => asset('novos-produtos/img/luva-de-cozinha.jpg'),"route" => "/produtos/luva-de-cozinha"],
//novos mascara de dormir
["title" => "10%" , "image" => asset('novos-produtos/img/mascara-de-dormir.jpg'),"route" => "/produtos/mascara-de-dormir"],
//novos quebra cabeca
["title" => "10%" , "image" => asset('novos-produtos/img/quebra-cabeca.jpg'),"route" => "/produtos/quebra-cabeca"],
//novos saco de presente
["title" => "10%" , "image" => asset('novos-produtos/img/sacola-de-presente.png'),"route" => "/produtos/sacola-de-presente"],
//novos tags
["title" => "10%" , "image" => asset('novos-produtos/img/tags.png'),"route" => "/produtos/tags"],
//novos tags
["title" => "10%" , "image" => asset('novos-produtos/img/viseira.png'),"route" => "/produtos/viseira"],
        ];
    ?>
    <div class="container">
        <div class="row d-flex flex-wrap justify-content-center mt-3">
            @foreach($cards as $card)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card x-card">
                    <img class="card-img-top" src="{{$card['image']}}" alt="">
                        <span class="card-title">{{$card['title']}}</span>
                        <div class="card-footer">
                            <a href="{{$card['route']}}?utm_source=lp-novos-produtos" class="btn x-btn btn-primary btn-block">@if($auth_check) Comprar @else Entrar @endif</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
  </div>
@endsection