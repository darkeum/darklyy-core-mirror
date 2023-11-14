@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'title' => trans('users::lang.module'),
            'buttons' => [
                trans('users::lang.admin.list') => 'admin.users.index',
            ],
            // 'show' => ['admin.support.show'],
            'add' => [trans('users::lang.admin.add'), 'admin.users.create']
        ])
@endsection