<?php $template = App\Http\Controllers\TemplateController::current(); ?>
@if(@$template->detail->promobar->enabled)
<nav id="navbar" class="promotional navbar navbar-expand-md static-top" style="background-color:{{$template->detail->promobar->backgroundColor}};height:{{@$template->detail->promobar->height}};">
    <div class="col d-lg-flex align-items-center {{@$template->detail->promobar->justifyContent}} w-100 py-1 py-lg-0 px-2 px-md-1">
        @if(@$template->detail->promobar->position1->visible)
            <div class="section-1 py-lg-1 text-center w-100">
                {!! @$template->detail->promobar->position1->content !!}
            </div>
        @endif
        @if(@$template->detail->promobar->position2->visible)
            <div class="section-2 py-lg-1 text-center w-100">
                {!! @$template->detail->promobar->position2->content !!}
            </div>
        @endif
        @if(@$template->detail->promobar->timer->visible)
            <div class="section-1 py-1 text-center w-100">
        <span class="right d-flex flex-row justify-content-center">
            <span class="d-none d-lg-block"> {!! @$template->detail->promobar->timer->label !!}</span>
            <b>
                <regressive-timmer class="col-sm-12" style="color:{{@$template->detail->promobar->timer->timer_color}};" to="{{@$template->detail->promobar->timer->target}}"></regressive-timmer>
            </b>
        </span>
            </div>
        @endif
    </div>
</nav>
@endif
@if(Auth::check())
    @if(Auth::user()->wpp_notification===null)
        <component-set-wpp :user="{{json_encode(Auth::user())}}"></component-set-wpp>
    @endif
@endif
<?php
    $user = Auth::user();
    $count = null;
    if($user){
        $code = $user->code;
        $count = Cart::session($code)->getContent()->count();
    }
?>
<header class="header top-header p-1">
    <div class="container">
        <div class="row">
            <div class="logo-container col-md-4 col-sm-12 d-flex">
                <a class="navbar-brand h-100 d-md-flex align-items-center" href="/" data-intro="Clicando na logo você pode voltar a <b>página inicial do site</b>." data-step="5">
                    <img class="logo-header" style="max-width:150px" src="{{asset('assets/images/logo-criafacil.png')}}" alt="Padrãocolor">
                </a>
                <!-- @if($user)
                    <component-menu-institucional-loggedin ref="menu" :user="{{json_encode(Auth::user())}}" ></component-menu-institucional-loggedin>
                    <component-menu-loggedin ref="menu" :highlights="{{json_encode(@$template->highlights)}}"></component-menu-loggedin>
                @else -->
                    <component-menu-institucional ref="menu" :user="{{json_encode(Auth::user())}}" ></component-menu-institucional>
                    <component-menu ref="menu" :highlights="{{json_encode(@$template->highlights)}}"></component-menu>
                <!-- @endif -->

            </div>
            <!-- <div class="header-fixed-menu" style="display:none;">
                <component-menu ref="menu" :highlights="{{json_encode(@$template->highlights)}}"></component-menu>
            </div> -->
            <div class="col-md-5 col-sm-12 search-container">
                <search-product></search-product>
            </div>
            <div class="d-flex flex-row col-md-3 col-sm-12 justify-content-end icons-header">
                <a href="/central-de-ajuda" class="menu-option cart-button hover-item">
                    <div class="d-flex flex-column align-items-center">
                        <img src="{{asset('assets/images/icon-menu-atendimento.jpg')}}" alt="Padrãocolor">
                        <span class="text" style="display:block">Ajuda</span>
                    </div>
                </a>
                @if($user)
                    <div class="dropdown ml-2">
                        <div class="btn btn-default dropdown-toggle menu-option" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="d-flex flex-column align-items-center">
                                <img src="{{asset('assets/images/icon-menu-suaconta.jpg')}}" alt="Padrãocolor">
                                <span class="text">{{Auth::user()->name}}</span>
                            </div>
                        </div>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="{{route('user.data')}}">Meus Dados</a>
                            @if(Auth::user()->ticketit_agent) <a class="dropdown-item" href="{{route('tickets.index')}}">Tickets</a> @endif
                            <a class="dropdown-item" href="{{route('order.view')}}">Minhas Compras</a>
                            <a class="dropdown-item" href="{{route('lista_desejo.view')}}">Minha Lista de Desejos</a>
                            <a class="dropdown-item" href="/logout">Sair</a>
                        </div>
                    </div>
                @else
                    <a href="/login" data-intro="Para ver seus produtos adicionados ao <b>carrinho de compras</b> clique aqui." data-step="2" class="menu-option cart-button hover-item">
                        <div class="d-flex flex-column align-items-center">
                        <img src="{{asset('assets/images/icon-menu-suaconta.jpg')}}" alt="Padrãocolor">
                        <span class="text" style="display:block">Sua conta</span>
                        </div>
                    </a>
                @endif
                <a href="/carrinho" data-intro="Para ver seus produtos adicionados ao <b>carrinho de compras</b> clique aqui." data-step="2" class="menu-option cart-button hover-item">
                    <div class="d-flex flex-column align-items-center">
                        @if($count>0) <img src="{{asset('assets/images/icon-menu-sacola.jpg')}}" alt="Padrãocolor"> @else  <img src="{{asset('assets/images/icon-menu-sacola.jpg')}}" alt="Padrãocolor"> @endif
                        <span class="text" style="display:block">Sacola</span>
                        @if($count>0)
                            <div class="cart-count-wrapper" style="position: relative;">
                                <small id="qty_cart" class="badge badge-primary badge-pill cart-count" style="top: -45px;position: absolute;left: 10px;">{{$count}}</small>
                            </div>
                        @endif
                    </div>
                </a>
            </div>
        </div>
    </div>
</header>
<nav class="navbar navbar-expand-lg navbar-light navbar-mobile d-none py-1">
    <component-responsive-menu ref="menu" :user="{{json_encode(Auth::user())}}" logo="{{asset('assets/images/logo-white.png')}}"></component-responsive-menu>
</nav>
@if(request()->route()->getName() == 'home' || request()->route()->getName() == 'products.products_lancamento')
<custom-home-view
        :slider_mobile="{{json_encode(@$template->detail->slider_mobile ? @$template->detail->slider_mobile : [])}}"
        :banners="{{json_encode(@$template->detail->banners ? @$template->detail->banners : [])}}"
        :trio="{{json_encode(@$template->detail->banners_trio ? @$template->detail->banners_trio : false)}}"
        :mobile="{{json_encode(@$template->detail->banners_mobile ? @$template->detail->banners_mobile : false)}}"
        :user="{{json_encode(Auth::user())}}"

    >
    </custom-home-view>


@endif

<nav class="navbar navbar-expand-lg navbar-light navbar-mobile d-none py-1 search-mobile">

    <search-product></search-product>
</nav>
<!-- <div class="d-block d-md-none d-flex flex-row navbar navbar-expand-md responsive-navbar static-top px-0">
    <component-responsive-menu ref="menu" :user="{{json_encode(Auth::user())}}" logo="{{asset('assets/images/logo-white.png')}}"></component-responsive-menu>
    <div class="mt-3 mb-1  el-input">
        <search-product></search-product>
    </div>
    @if($user)
        <div class="mt-3 mb-1 w-100">
            <div class="col">
                <div class="d-flex justify-content-between w-100 px-1">
                    <a class="text-white" href="{{route('user.data')}}">Meus Dados</a>
                    @if(Auth::user()->ticketit_agent) <a class="text-white" href="{{route('tickets.index')}}">Tickets</a> @endif
                    <a class="text-white" href="{{route('order.view')}}">Minhas Compras</a>
                </div>
            </div>
        </div>
    @endif
</div> -->
