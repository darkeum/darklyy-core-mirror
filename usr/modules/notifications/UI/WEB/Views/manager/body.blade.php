@extends('layouts.base')

{{--@section('header')--}}
{{--    @include('elements.header', [--}}
{{--            'title' => 'Заявки на расчёт',--}}
{{--            'buttons' => [--}}
{{--                trans('requests::general.table') => 'admin.request.index',--}}
{{--                // trans('request::general.show') => 'admin.request.show',--}}
{{--            ],--}}
{{--            // 'show' => ['admin.support.show'],--}}
{{--            'add' => [trans('requests::general.add'), 'admin.request.create']--}}
{{--        ])--}}
{{--@endsection--}}

@section('header')
    @include('notifications::manager.layouts.header')
@endsection