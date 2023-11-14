@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'buttons' => [
                trans('objects::lang.list') => 'admin.objects.index',
                trans('objects::lang.map') => 'admin.objects.map',
            ],

            // 'show' => ['admin.objects.show'],
            'add' => [trans('objects::lang.add'), 'admin.objects.create']
        ])
@endsection