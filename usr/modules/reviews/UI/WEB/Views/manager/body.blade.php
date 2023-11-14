@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'title' => 'Отзывы клиентов',
            'buttons' => [
                "Таблица отзывов" => 'admin.reviews.index',
                // trans('request::general.show') => 'admin.requests.show',
            ],

            // 'show' => ['admin.support.show'],
            'add' => ["Добавить отзыв", 'admin.reviews.create']
        ])
@endsection