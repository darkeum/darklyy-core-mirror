@extends('news::manager.body')

@section('title', __('news::lang.admin.list'))

{{--@section('header')--}}
{{--@endsection--}}

@section('content')

    @grid('news::news')

@endsection
