@extends('slider::manager.body')

@section('title', __('slider::lang.admin.list'))

{{--@section('header')--}}
{{--@endsection--}}

@section('content')

    @grid('slider::slider')

@endsection
