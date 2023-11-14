@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'buttons' => [
            trans('economicNew::lang.act') => 'admin.economic_new.act.index',
            trans('economicNew::lang.balance') => 'admin.controls.god'
        ],
        // 'show' => ['admin.support.show'],
        // 'add' => [trans('support::general.create'), 'admin.support.create']
    ])
@endsection