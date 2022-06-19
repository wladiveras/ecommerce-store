<footer class="footer mt-5">
    <div class="mailing-bar">
        <div class="container d-flex flex-row justify-content-center align-items-center py-3">
            <div class="col-sm-12 col-md-10" style="display:flex">
                <div><img src="{{asset('assets/images/chamada-mailing.png')}}" alt="Padrãocolor" style="max-width:390PX"></div>
                <!-- <div style="
    display: flex;
    flex-direction: column;
    justify-content: center;
">
                    <p style="
    margin: 0;
    font-weight: 900;
">QUER VER MAIS NOVIDADES?</p>
                    <p style="
    margin: 0;
">Cadastre-se e receba no seu e-mail.</p>
                </div>
                
            </div> -->
            <div class="col-sm-12 col-md-6" style="display: flex;align-items: center;">
                <input class="el-input__inner mailing-field" type="text" placeholder="Digite aqui seu email" />
                <a href="#" class="btn-showmore" style="padding: 15px 30px; height: 55px;font-weight: 900; margin-left: 10px; background: transparent; color: #fff !important; padding: 15px 30px; border-radius: 10px; border:1px solid">
                    CADASTRAR
                </a>
            </div>
        </div>
    </div>
    <div class="d-flex flex-row mt-5 container footer-content">
        <div class="col-sm-12 col-md-3">
        <img style="max-width:150px" src="{{asset('assets/images/logo-criafacil.png')}}" alt="Padrãocolor">
            <p class="desc text-left"> Cria Fácil, onde a experiência de personalizar é única! Aqui você realiza o seu pedido personalizado. </p>
        </div>       
        <div class="col-sm-12 col-md-3">
            <h4>Atendimento</h4>
            <p>Telefone:<br>(21)3668-1550</p>
            <p>Email:<br>atendimento@criafacil.com.br</p>
            <p>Horário de Atendimento:<br>Seg a sex - 8h as 18h</p>            
        </div>
        <div class="col-sm-12 col-md-3">
            <h4>Institucional</h4>
            <?php $pages = App\Models\Page::where("enabled",true)->select("pages.title","pages.slug")->orderBy("id","asc")->get(); $count = count($pages); ?>
                @if($count > 0)
                <ul style="list-style: none;padding: 0;">
                    @foreach($pages as $page)
                        
                            <li><a class="mx-0 px-0 f-12 text-center @if($count >= 3) col-md-4 @else col-md-12 @endif col-sm-12" href="{{route('pages.show',['slug'=>$page->slug])}}">{{$page->title}}</a></li> 
                        
                    @endforeach
                </ul>
                        
                @endif
        </div>
        
        <div class="col-sm-12 col-md-3"> 
            <h4>Nossas Redes</h4>           
            <div style="display:flex">
                <a href="https://www.facebook.com/padraocolor" target="_blank">
                    <!-- @svg('/svg/footer/Facebook.svg','svg social') -->
                    <img src="{{asset('assets/images/icon-facebook.png')}}" alt="Padrãocolor" style="max-width:80px" >
                </a>
                <a href="https://www.instagram.com/padraocolor/" target="_blank">
                    <!-- @svg('/svg/footer/Instagram.svg','svg social') -->
                    <img src="{{asset('assets/images/icon-instagram.png')}}" alt="Padrãocolor" style="max-width:80px" >
                </a>
            </div>
        </div>
    </div>
    <div class="d-flex flex-row info">
        <div class="container d-flex justify-content-center">
        <div>
            @svg('/svg/footer/Visa.svg','svg ml-0 mr-2 payment')
            @svg('/svg/footer/Mastercard.svg','svg ml-0 mr-2 payment')
            @svg('/svg/footer/Elo.svg','svg ml-0 mr-2 payment')
            @svg('/svg/footer/Hipercard.svg','svg ml-0 mr-2 payment')
            @svg('/svg/footer/Jcb.svg','svg ml-0 mr-2 payment')
            @svg('/svg/footer/Diners.svg','svg ml-0 mr-2 payment')
            @svg('/svg/footer/Discover.svg','svg ml-0 mr-2 payment')
            @svg('/svg/footer/Boleto.svg','svg ml-0 mr-2 payment')
        </div>
        <!-- <div class="d-flex justify-content-end col-6">
            <a href="https://www.facebook.com/padraocolor" target="_blank"> -->
                <!-- @svg('/svg/footer/Facebook.svg','svg social') -->
                <!-- <img src="{{asset('assets/images/icon-facebook.png')}}" alt="Padrãocolor" style="max-width:30px">
            </a>
            <a href="https://www.instagram.com/padraocolor/" target="_blank"> -->
                <!-- @svg('/svg/footer/Instagram.svg','svg social') -->
                <!-- <img src="{{asset('assets/images/icon-instagram.png')}}" alt="Padrãocolor" style="max-width:30px">
            </a>
        </div> -->
        </div>
    </div>
        
        <div class="copyright">
            <div class="col-12 text-center mt-2 mb-2">
            <!-- <a href="/projetos_especiais">Projetos Especiais</a> -->
            © {{date('Y')}} Cria Fácil. Todos os direitos reservados
            </div>
        </div>

        

</footer>
<!-- End Footer -->
