@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'title' => 'Заявки на расчет',
            'buttons' => [
                trans('requests::general.table') => 'admin.requests.index',
                // trans('request::general.show') => 'admin.requests.show',
            ],

            // 'show' => ['admin.support.show'],
            'add' => [trans('requests::general.add'), 'admin.requests.create']
        ])
@endsection