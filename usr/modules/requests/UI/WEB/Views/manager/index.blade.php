@extends('requests::manager.body')

@section('title', __('request::general.index'))

@section('content')
    

    <grid:requests::indexgrid />

@endsection
