@extends('layouts.base')

@section('title', __('request::general.index'))

@section('content')
    @include('request::manager.header')

    <grid:request::indexgrid />

@endsection
