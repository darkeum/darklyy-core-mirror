@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'title' => trans('news::lang.module'),
            'buttons' => [
                trans('news::lang.admin.list') => 'admin.news.index',
            ],
            // 'show' => ['admin.support.show'],
            'add' => [trans('news::lang.admin.add'), 'admin.news.create']
        ])
@endsection