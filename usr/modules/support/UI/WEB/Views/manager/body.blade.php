@extends('layouts.base')

@section('content-header')
{{--    @include('elements.header', [--}}
{{--            'title' => 'Заявки на расчет',--}}
{{--            'buttons' => [--}}
{{--                trans('requests::general.table') => 'admin.requests.index',--}}
{{--                // trans('request::general.show') => 'admin.requests.show',--}}
{{--            ],--}}
{{--            --}}
{{--            // 'show' => ['admin.support.show'],--}}
{{--            'add' => [trans('requests::general.add'), 'admin.requests.create']--}}
{{--        ])--}}

@include('support::manager.layouts.header')

@endsection

@section('header')
    @include('support::manager.layouts.header')
@endsection