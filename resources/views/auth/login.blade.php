@extends('layouts.app')

@section('content')

<section class="login-block">
    <div class="container">
        <div class="row">
        
        <div class="col-md-8 banner-sec">
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                        <img class="d-block img-fluid" src="{{ URL::asset('image/IMG1.jpg') }}" alt="First slide">
                        <div class="carousel-caption d-none d-md-block">
                            <div class="banner-text">
                                <h2>Reportes gráficos</h2>
                                <p>Obtenga métricas al cumplimiento de su equipo de trabajo determine su rendimiento.</p>
                            </div>  
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block img-fluid" src="{{ URL::asset('image/IMG2.jpg') }}" alt="First slide">
                        <div class="carousel-caption d-none d-md-block">
                            <div class="banner-text">
                                <h2>Trabajar en equipo</h2>
                                <p>Nunca había sido tan fácil administrar equipos de trabajo todos juntos hacia el mismo lado...</p>
                            </div>  
                        </div>
                    </div>
                    <div class="carousel-item">
                      <img class="d-block img-fluid" src="{{ URL::asset('image/IMG3.jpeg') }}" alt="First slide">
                        <div class="carousel-caption d-none d-md-block">
                            <div class="banner-text">   
                                <h2>Mejore su productividad</h2>
                                <p>Sea más oportuno más diligente obtenga mejores resultados</p>
                            </div>  
                        </div>
                    </div>
                </div>     
            </div>
        </div>

        <div class="col-md-4 login-sec">
            <h2 class="text-center">Iniciar sesión </h2><br>
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="exampleInputEmail1" class="text-uppercase">{{__('Correo electrónico') }}</label>
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" placeholder="Correo electrónico" required autofocus>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
    
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1" class="text-uppercase">{{__('Contraseña') }}</label>
                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"  placeholder="Contraseña" required>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
  
  
                <div class="form-check">
                    <!--<label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </label>-->
                    <div class="form-group row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Iniciar Sesión') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!--<div class="text-center">
                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                </div>-->
  
            </form>
        </div>
    </div>
</section>

@endsection
