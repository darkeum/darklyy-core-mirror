@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => __('warranties::general.index'),
        'buttons' => [],
    ])
@endsection
