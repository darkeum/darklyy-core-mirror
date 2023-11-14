@extends('layouts.base')

@include('org::manager.header')

@section('title')
    @lang('org::general.manager')
@endsection

@section('content')
    {!! $grid !!}
@endsection
