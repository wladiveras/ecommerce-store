<nav id="navbar" class="mb-4 navbar navbar-expand-md static-top" style="background: #7d1756;">
  <a class="navbar-brand d-block d-md-none" href="/"> <img class="logo-header" style="max-width:150px" src="{{asset('assets/images/logo-criafacil.png')}}" alt="Cria Fácil"> </a>
  <button class="navbar-toggler" type="button">
    <i class="mi mi-menu f-32"></i>
  </button>
  <div class="collapse navbar-collapse" style="display:fixed" id="navbarResponsive">
    <div class="w-100 d-flex align-items-center">
      <div class="menu-md d-flex justify-content-between w-100 h-100 align-items-center">
        <a style="margin-left:15px" class="navbar-brand d-none h-100 d-md-flex align-items-center" href="/"> <img src="{{asset('assets/images/logo.png')}}" alt=""> </a>
        <div class="menu-items h-100 align-self-left align-self-sm-center">
          <div class="options d-flex flex-column flex-md-row flex-wrap flex-md-nowrap align-items-md-center">
       
          </div>
        </div>
      </div>

    </div>

  </div>
</nav>
@if(session('quick.alert'))
  <div style="margin-top:-24px;" class="alert alert-{{ session('quick.alert.type') }} alert-dismissible fade show custom-alert-shadow" role="alert">
      <div class="d-flex">
          <div class="p-2">
              @if((session('quick.alert.type')=="warning")||(session('quick.alert.type')=="danger"))
                  <i class="material-icons">warning</i>
              @elseif((session('quick.alert.type')=="success"))
                  <i class="material-icons">check_circle</i>
              @elseif((session('quick.alert.type')=="info"))
                  <i class="material-icons">info</i>
              @endif
          </div>
          <div class="p-2">{!! session('quick.alert.message') !!}</div>
          <div class="p-2">
              @if(session('quick.alert.closeable'))
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="top: 13px;">
                      <span aria-hidden="true">×</span>
                  </button>
              @endif
          </div>
      </div>
  </div>
  <?php Session(["quick"=>null]); ?>
@endif
