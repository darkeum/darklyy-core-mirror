@extends('layouts.base')

@section('title')
    @lang('order::general.order')
@endsection

@section('content')
    @include('order::manager.header')
    <grid:order::ordergrid />
@endsection

