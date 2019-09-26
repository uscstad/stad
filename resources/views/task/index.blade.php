@extends('layouts.app')

@section('content')

<task
	:data="{{ json_encode($data) }}"
></task>

@endsection