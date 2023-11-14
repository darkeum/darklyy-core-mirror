@extends('layouts.base')

@section('header')
    @include('elements.header', [
            'title' => trans('price::general.prices'),
            'buttons' => [
                trans('price::general.price') => 'admin.price.index',
                trans('price::general.search') => 'admin.price.search',
                trans('price::general.types') => 'admin.price.types.index',

                trans('price::general.settings') => [
                    trans('price::general.settings.list') => 'admin.price.settings.list',
                    trans('price::general.settings.account') => 'admin.price.settings.account',
                    trans('price::general.settings.load') => 'admin.price.settings.load'
                ],

                trans('price::general.rival') => [
                    trans('price::general.rival.client') => 'admin.price.rival.client',
                    trans('price::general.rival.group') => 'admin.price.rival.group',
                    trans('price::general.rival.discount') => 'admin.price.rival.discount'
                ],

                trans('price::general.receiving') => 'admin.price.receiving',
            ],
            'add' => [trans('price::general.add'), route('admin.price.add')],
    ])
@endsection