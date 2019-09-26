@extends('layouts.app')

@section('content')

<div class="row">
  <div class="col-xl-3 col-sm-4 mb-3">
    <div class="card text-white bg-primary o-hidden h-100">
      <div class="card-body">
        <div class="card-body-icon">
          <i class="fas fa-fw fa-user-tie"></i>
        </div>
        <div class="mr-5"><b>Docentes: {{$teachers}}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('teachers#') }}">
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
          <i class="fas fa-fw fas fa-clipboard-list"></i>
        </div>
        <div class="mr-5"><b>Actividades: {{$tasks}}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('activities#') }}">
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
          <i class="fas fa-fw fas fa-tags"></i>
        </div>
        <div class="mr-5"><b>Categorias: {{$categories}}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('categories#') }}">
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
          <i class="fas fa-fw fas fa-tasks"></i>
        </div>
        <div class="mr-5"><b>Tareas: {{$tasks_contents}}</b></div>
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
        <div class="mr-5"><b>Mensajes: {{$messages}}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('messages#') }}">
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
        <div class="mr-5"><b>Reportes </b></div>
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