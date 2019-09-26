@extends('layouts.app')

@section('content')

<div class="row">
  <div class="col-xl-3 col-sm-4 mb-3">
    <div class="card text-white bg-primary o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-users"></i>
        </div>
        <div class="mr-5"><b>Usuarios: {{ $users }}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('users#') }}">
        <span class="float-left">Más información</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>
  <div class="col-xl-3 col-sm-4 mb-3">
    <div class="card text-white bg-primary o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-calendar-alt"></i>
        </div>
        <div class="mr-5"><b>Años lectivos: {{ $school_years }}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('school_years#') }}">
        <span class="float-left">Más información</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>
  <div class="col-xl-3 col-sm-4 mb-3">
    <div class="card text-white bg-primary o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-calendar"></i>
        </div>
        <div class="mr-5"><b>Periodos: {{ $teaching_periods }}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('teaching_periods#') }}">
        <span class="float-left">Más información</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>
  <div class="col-xl-3 col-sm-4 mb-3">
    <div class="card text-white bg-primary o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-tag"></i>
        </div>
        <div class="mr-5"><b>Tareas: {{ $tasks }}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('tasks#') }}">
        <span class="float-left">Más información</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>
  <div class="col-xl-3 col-sm-4 mb-3">
    <div class="card text-white bg-primary o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-comments"></i>
        </div>
        <div class="mr-5"><b>Mensajes: </b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="#">
        <span class="float-left">Más información</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>
  <div class="col-xl-3 col-sm-4 mb-3">
    <div class="card text-white bg-primary o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-chart-bar"></i>
        </div>
        <div class="mr-5"><b>Reportes: </b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="#">
        <span class="float-left">Más información</span>
        <span class="float-right">
          <i class="fas fa-angle-right"></i>
        </span>
      </a>
    </div>
  </div>
</div>

@endsection