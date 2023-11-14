@extends('layouts.base')

@section('title', __('catalogv2s::general.pump'))

@section('header')

@endsection

@section('content')

   
    @include('catalogv2s::manager.header')


    {{-- <grid:catalogv2s::pumpgrid /> --}}


    @include('catalogv2s::manager.pump.modalAdd')

@endsection

