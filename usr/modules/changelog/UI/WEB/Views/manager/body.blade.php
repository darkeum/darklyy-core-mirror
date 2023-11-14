@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'buttons' => [
            __('changelog::lang.module') => 'admin.changelog.index',
        ],

        'add' => [__('changelog::lang.create'), 'admin.changelog.create'],
        'show' => ['admin.changelog.show'],
        //'edit' => ['admin.changelog.edit'],
    ])
@endsection