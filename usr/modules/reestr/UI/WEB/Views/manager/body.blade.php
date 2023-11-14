@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'title' => 'Реестр документов',
            'buttons' => [
                'Список договоров' => 'admin.reestr.index',
                'Типы договоров' => 'admin.reestr.types',
            ],
            'add' => ["Создать договор", 'admin.reestr.create']
        ])
@endsection