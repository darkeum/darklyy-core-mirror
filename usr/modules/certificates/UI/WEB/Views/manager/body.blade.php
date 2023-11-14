@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'title' => 'Менеджер сертификатов',
            'buttons' => [
                "Таблица сертификатов" => 'admin.certificates.index',
                // trans('request::general.show') => 'admin.requests.show',
            ],

            // 'show' => ['admin.support.show'],
            'add' => ["Добавить сертификат", 'admin.certificates.create']
        ])
@endsection