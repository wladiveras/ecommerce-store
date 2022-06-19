@extends('template.simple')

@section('content')


    <div class="card d-flex flex-column" style="border-radius: 8px;background: linear-gradient(180deg, rgb(247, 55, 1) 0%, rgb(248, 203, 33) 100%);max-width: 500px;margin: 0 auto;padding: 25px 10px;">
      <div>
        <p style="font-size: 20px; margin-left: 5px;text-transform: uppercase; color:#fff; text-align:center">Faça o Login</p>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('login') }}">
          @csrf
          <!-- <span class="f-12">Para acessar sua conta, por favor, preencha os campos abaixo:</span> -->
          <div class="mt-1 mb-0">
            <div class="form-group">
                <div>
                  <label style="color:#fff">Email</label>
                  <input style="display: none" value="{{@$_GET['redirect'] ? $_GET['redirect'] : ''}}" name="redirect" />
                  <input id="user" style="background: transparent;border-color: #fff;color: #fff;" class="form-control{{ $errors->has('user') ? ' is-invalid' : '' }}" name="user" value="{{ old('user') }}" required autofocus>
                  @if ($errors->has('user'))
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('user') }}</strong>
                  </span>
                  @endif
                </div>
            </div>

            <div class="form-group">
              <div>
                <label for="" style="color:#fff">Senha:</label>
                <input id="password" style="background: transparent;border-color: #fff;color: #fff;" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
              </div>
              <div class="d-flex justify-content-end">
                <a class="link" href="{{ route('password.request') }}" style="
                    text-transform: uppercase;
                    font-size: 10px;
                    margin-top: 5px;
                    color: #fff !important;
                ">
                  Esqueci minha senha
                </a>
              </div>
            </div>
          </div>
          <div class="form-group mb-0 d-flex flex-column justify-content-start">


            <button type="submit" style="background: #fff;border: 0;color: #f74b05;" class="mt-3 btn btn-primary btn-block w-100">Entrar</button>

            <!-- <p style="text-align:center; margin: 20px 0; color:#9b9b9b">OU</p>

            <div class="m-2">
              <a href="{{route('login_social',["provider"=>"google"])}}" type="button" class="btn btn-block btn-social google" @click="$loading()">
                <span>Entrar com <img src="/assets/images/google_completo.png"></span>
              </a>
            </div>
            <div class="m-2">
              <a href="{{route('login_social',["provider"=>"facebook"])}}" type="button" class="btn btn-block btn-social facebook" @click="$loading()">
                <span>Entrar com <img src="/assets/images/facebook_completo.png"></span>
              </a>
            </div>               -->

          </div>
        </form>
      </div>
    </div>
    <div>
      <p style="text-align:center; margin: 20px 0 0 0; color:rgb(83, 83, 83); margin-top: 0; font-size:25px; margin-top:20px;">Ainda não é cliente?</p>
      <a class="mt-3 btn btn-primary btn-block" href="{{ route('seja-um-revendedor') }}"
         style=" display: block; text-align: center; text-transform: uppercase;  color: rgb(255, 255, 255); font-size: 18px; max-width: 450px; margin: 0px auto;">
        Cadastre-se aqui
      </a>
    </div>



@endsection
