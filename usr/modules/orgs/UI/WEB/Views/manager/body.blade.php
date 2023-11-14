@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => __('orgs::general.manager'),
        'buttons' => [
            __('orgs::general.list') => 'admin.orgs.index',
            __('orgs::general.list.sign') => 'admin.orgs.sign.index',
        ],
    
        'add' => [
            [__('orgs::general.create') ,'admin.orgs.create'],
            [__('orgs::general.create.sign') ,'admin.orgs.sign.create'],
        ],
    ])
@endsection
