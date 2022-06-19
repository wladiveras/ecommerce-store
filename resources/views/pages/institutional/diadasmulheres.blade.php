@extends('template.master')
<style>
.jumbotron {
    background-image: url({{asset('dia_da_mulher/img/bannersite.jpg')}});
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
      background-image: url({{asset('dia_da_mulher/img/mobilesite.jpg')}});
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
        <li class="breadcrumb-item">Dia da Mulher</li>
    </ol>
</nav>
<div class="container-fluid">
    <a href="#promos" class="bannerLink">
    <header class="jumbotron my-4">
    </header></a>

    <?php
        $auth_check= Auth::check();
        $cards = [
            ["title" => "10%" , "image" => asset('dia_da_mulher/img/agenda.jpg'),"route" => "/produtos/agenda/offset-75-4-0-15x21-cm-com-espiral-140-folhas-a-partir-de-201/eyJxdHkiOjIwMSwiY2FsY19xdHkiOjIwMSwiYXJ0IjoiVVAifQ=="],
            ["title" => "10%" , "image" => asset('dia_da_mulher/img/almofada.jpg'),"route" => "/produtos/almofada-sublimatica/oxford-40x40-sublimacao/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
            ["title" => "10%" , "image" => asset('dia_da_mulher/img/bolsa.jpg'),"route" => "/produtos/bolsa-branca-com-alca/tecido-cordoba-47x37-cm-sublimacao-1/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
            ["title" => "10%" , "image" => asset('dia_da_mulher/img/caderno.jpg'),"route" => "/produtos/caderno/offset-75-4-0-20x28-cm-1-materia-com-espiral-96-folhas-a-partir-de-101/eyJxdHkiOjEwMSwiY2FsY19xdHkiOjEwMSwiYXJ0IjoiVVAifQ=="],
            ["title" => "10%" , "image" => asset('dia_da_mulher/img/necessaire.jpg'),"route" => "/produtos/necessaire/tecido-cordoba-14x22x10-cm-sublimacao-1/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
            ["title" => "10%" , "image" => asset('dia_da_mulher/img/toalha.jpg'),"route" => "/produtos/toalha-de-rosto-branca/poliester-45x70-cm-sublimacao-1/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
            ["title" => "10%" , "image" => asset('dia_da_mulher/img/toalha-mao.jpg'),"route" => "/produtos/toalha-de-mao/poliester-37x23-cm-sublimacao-1/eyJxdHkiOjEsImNhbGNfcXR5IjoxLCJhcnQiOiJVUCJ9"],
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
                            <a href="{{$card['route']}}?utm_source=LP_DiadaMulher&utm_medium=Produtos&utm_campaign=Dia_da_mulher" class="btn x-btn btn-primary btn-block">@if($auth_check) Comprar @else Entrar @endif</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
  </div>
@endsection
