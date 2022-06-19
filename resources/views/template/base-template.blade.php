<!DOCTYPE html>
<html lang="pt-br">
<head>
  @yield("header")
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-5QGTSFD');</script>
<!-- End Google Tag Manager -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta name="description" content="Compre seus produtos -> Personalize -> Envie para Qualquer lugar do Brasil -> envie também para seus clientes com sua etiqueta!
  Confira o preço especial ACABA HOJE"/>
  @yield('meta')
  <title>
    Cria Fácil
  </title>
  @include('template.head')
  @yield('captcha')
</head>
<body>
  <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5QGTSFD"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
  <div id="app" class="small-template" >
    @include('template.header')
        <div id="view-wrapper">
            <div id="page-content">
                @include('template.alerts')
                    @yield('body')
                    <div class="wrapper mt-4">
                        <div class="container">
                            @yield('content')
                        </div>
                    </div>
                    <div class="wrapper">
                    @yield('footer-widget')
                </div>
            </div>
        </div>
        @include('template.footer')
    </div>
    @include('template.footer-script')
</body>
</html>
