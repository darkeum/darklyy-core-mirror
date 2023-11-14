@extends('price::manager.body')

@section('title', __('price::types.list'))

@section('content')
    <grid:price::typesgrid/>
@endsection
