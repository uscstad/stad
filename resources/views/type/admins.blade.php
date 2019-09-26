@extends('layouts.app')

@section('content')

<div class="row">
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
          <i class="fas fa-fw fas fa-user-tag"></i>
        </div>
        <div class="mr-5"><b>Coordinadores: {{ $coordinators }}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('coordinators#') }}">
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
          <i class="fas fa-fw far fa-address-book"></i>
        </div>
        <div class="mr-5"><b>Nivel educativo: {{ $level_educations }}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('level_educations#') }}">
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
          <i class="fas fa-fw far fa-id-card"></i>
        </div>
        <div class="mr-5"><b>Asignatura: {{ $subjects }}</b></div>
      </div>
      <a class="card-footer text-white clearfix small z-1" href="{{ URL('subjects#') }}">
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
          <i class="fas fa-fw fas fa-user-tie"></i>
        </div>
        <div class="mr-5"><b>Docentes: {{ $teachers }}</b></div>
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