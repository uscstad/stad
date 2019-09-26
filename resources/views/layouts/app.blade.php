<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Stad') }}</title>
    <link rel="shortcut icon" href="{{ URL::asset('image/stad.png') }}">

    <!-- Scripts -->
    
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/vuejs-paginator.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin.min.js') }}"></script>
    <script src="{{ asset('js/toastr.js') }}"></script>

    <!-- Fonts -->
    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin.css') }}" rel="stylesheet">
    <style>
      .card {
        margin: 0 auto; /* Added */
        float: none; /* Added */
        margin-bottom: 10px; /* Added */
      }
    </style>
    <link href="{{ asset('css/toastr.css') }}" rel="stylesheet">
</head>
<body>
  <div id="app">
    @guest
      <br><br><br><br>  
    @else
      <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ URL::asset('image/stad.png') }}" width="70px" height="40px">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
          <ul class="navbar-nav mr-auto">

          </ul>

            <!-- Right Side Of Navbar -->
          <ul class="navbar-nav ml-auto">
            <!-- Authentication Links -->
              
            <li class="nav-item dropdown">
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-getpopup="true" aria-expanded="false" v-pre>
                    <b>{{ Auth::user()->name }} {{ Auth::user()->lastname }}</b> <span class="caret"></span>
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item">Tipo: {{ ucwords(Auth::user()->type) }}</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                     document.getElementById('logout-form').submit();">
                        Cerrar Sesión
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
          </ul>
        </div>
      </nav>
    @endguest  
        @guest
          @yield('content')
        @else
          <div id="wrapper">
            <!-- Sidebar -->
            <ul class="sidebar navbar-nav">
              @if (Auth::user()->type == 'admins')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('school_years#') }}">
                    <i class="fas fa-fw fas fa-calendar-alt"></i>
                    <span>Años lectivos</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'admins')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('teaching_periods#') }}">
                    <i class="fas fa-fw fas fa-calendar"></i>
                    <span>Periodos</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'admins')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('coordinators#') }}">
                    <i class="fas fa-fw fas fa-user-tag"></i>
                    <span>Coordinadores</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'admins')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('level_educations#') }}">
                    <i class="fas fa-fw far fa-address-book"></i>
                    <span>Nivel educativo</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'admins')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('subjects#') }}">
                    <i class="fas fa-fw far fa-id-card"></i>
                    <span>Asignatura</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'admins' || Auth::user()->type == 'coordinators')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('teachers#') }}">
                    <i class="fas fa-fw fas fa-user-tie"></i>
                    <span>Docentes</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'coordinators')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('activities#') }}">
                    <i class="fas fa-fw fas fa-clipboard-list"></i>
                    <span>Actividades</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'coordinators')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('categories#') }}">
                    <i class="fas fa-fw fas fa-tags"></i>
                    <span>Categorias</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'coordinators')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('tasks#') }}">
                    <i class="fas fa-fw fas fa-tasks"></i>
                    <span>Tareas</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'teachers')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('task#') }}">
                    <i class="fas fa-fw fas fa-tasks"></i>
                    <span>Tareas</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'coordinators' || Auth::user()->type == 'teachers')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('messages#') }}">
                    <i class="fas fa-fw far fa-comments"></i>
                    <span>Mensajes</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'admins' || Auth::user()->type == 'coordinators' || Auth::user()->type == 'teachers')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('reports#') }}">
                    <i class="fas fa-fw fas fa-chart-bar"></i>
                    <span>Reportes</span>
                  </a>
                </li>
              @endif
              @if (Auth::user()->type == 'admins')
                <li class="nav-item">
                  <a class="nav-link" href="{{ URL('settings#') }}">
                    <i class="fas fa-fw fas fa-cogs"></i>
                    <span>Configuracion</span>
                  </a>
                </li>
              @endif
            </ul>

            <div id="content-wrapper">

              <div class="container-fluid">
                @yield('content')
              </div>
              <!-- /.container-fluid -->

              <!-- Sticky Footer -->
              <footer class="sticky-footer">
                <div class="container my-auto">
                  <div class="copyright text-center my-auto">
                    <span>Copyright © Your Website 2019</span>
                  </div>
                </div>
              </footer>

            </div>
            <!-- /.content-wrapper -->

          </div>
          <!-- /#wrapper -->
        @endguest

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded ir-arriba">
            <i class="fas fa-angle-up"></i>
        </a>


    </div>
    <script>
        $(document).ready(function(){
 
            $('.ir-arriba').click(function(){
                $('body, html').animate({
                    scrollTop: '0px'
                }, 300);
            });
         
            $(window).scroll(function(){
                if( $(this).scrollTop() > 0 ){
                    $('.ir-arriba').slideDown(300);
                } else {
                    $('.ir-arriba').slideUp(300);
                }
            });
         
        });
    </script>
</body>
</html>
