@extends('reviews::manager.body')

@section('title', "Отзывы клиентов")

@section('content')
    
    <grid:reviews::indexgrid />

@endsection
