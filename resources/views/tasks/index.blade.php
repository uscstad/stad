@extends('layouts.app')

@section('content')

<tasks
	:data="{{ $data }}"
></tasks>

@endsection