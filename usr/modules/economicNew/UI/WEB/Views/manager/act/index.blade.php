@extends('economicNew::manager.body')

@section('header')
    @include('elements.header', [
        'buttons' => [
            trans('economicNew::lang.act') => 'admin.economic_new.act.index',
            trans('economicNew::lang.balance') => 'admin.controls.god'
        ],
        // 'show' => ['admin.support.show'],
         'add' => [trans('economicNew::lang.create'), 'admin.economic_new.act.create']
    ])
@endsection

@section('content')
        <grid:economicNew::actgrid />
@endsection
