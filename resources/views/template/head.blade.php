<meta content="CPLX" name="author" />
<link rel="shortcut icon" href="{{ asset('assets/images/logo-icon.png')}}">
<script src="https://browser.sentry-cdn.com/5.0.7/bundle.min.js" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<link rel="stylesheet" href="{{ asset('/css/introjs.min.css') }}">
<link rel="stylesheet" href="{{ mix('/css/app.css') }}">

<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="root-url" content="{{ url('') }}">
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-109100465-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-109100465-2');
</script>


<script>
   (function(h,o,t,j,a,r){
       h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
       h._hjSettings={hjid:1370696,hjsv:6};
       a=o.getElementsByTagName('head')[0];
       r=o.createElement('script');r.async=1;
       r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
       a.appendChild(r);
   })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
</script>
