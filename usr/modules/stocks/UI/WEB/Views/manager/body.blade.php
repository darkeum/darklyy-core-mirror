@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => __('stocks::general.index'),
        'buttons' => [
            __('stocks::general.table') => 'admin.stocks.index'
        ],

        'add' => [__('stocks::general.create'), 'admin.stocks.create'],
    ])
@endsection
