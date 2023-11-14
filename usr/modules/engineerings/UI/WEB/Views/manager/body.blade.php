@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => trans('engineerings::general.register'),
        'buttons' => [
            // trans('engineerings::general.index') => 'admin.engineerings',
            trans('engineerings::general.register') => 'admin.engineerings.register',
        ],
        'add' => [trans('engineerings::general.register.add'), 'admin.engineerings.register.create'],
    ])
@endsection
