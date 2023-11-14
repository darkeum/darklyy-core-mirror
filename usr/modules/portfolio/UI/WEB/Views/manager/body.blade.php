@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'title' => 'Менеджер портфолио',
            'buttons' => [
                "Список портфолио" => 'admin.portfolio.index',
                // trans('request::general.show') => 'admin.requests.show',
            ],

            // 'show' => ['admin.support.show'],
            'add' => ["Добавить портфолио", 'admin.portfolio.create']
        ])
@endsection