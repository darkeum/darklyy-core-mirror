@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'title' => 'Слайдер',
            'buttons' => [
                trans('slider::lang.admin.list') => 'admin.slider.index',
            ],
            // 'show' => ['admin.support.show'],
            'add' => [trans('slider::lang.admin.add'), 'admin.slider.create']
        ])
@endsection