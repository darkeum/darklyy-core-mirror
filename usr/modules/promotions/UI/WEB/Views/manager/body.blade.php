@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => __('promotions::general.manager'),
        'buttons' => [
            __('promotions::general.promotions') => 'admin.promotions.index'
        ],

        'add' => [__('promotions::general.add'), 'admin.promotions.create'],
    ])
@endsection
