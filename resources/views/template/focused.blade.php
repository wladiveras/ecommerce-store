<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5QGTSFD');</script>
<!-- End Google Tag Manager -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <title>Checkout Padrão Color</title>
  @include('template.head')

  <script 
    type="text/javascript" 
    src="https://h.online-metrix.net/fp/tags.js?org_id={AMBIENTE}&session_id={NUMERO DE SESSÃO}">
  </script>
</head>

<body>
  <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5QGTSFD"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
  <div id="app" class="small-template">
    @include('template.focused-header')
    <div class="wrapper">
      <div class="container-fluid">
        @yield('content')
      </div>
    </div>
    {{-- @if(Auth::check())
        <help-float-button :user="{{Auth::user()}}"></help-float-button>
    @endif --}}
  </div>
  @include('template.footer-script')
</body>

</html>