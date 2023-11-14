@extends('price::manager.body')

@section('title', __('price::main.list'))

@section('content')
    <grid:price::indexgrid />
@endsection