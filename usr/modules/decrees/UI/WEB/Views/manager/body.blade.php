@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => trans('decrees::admin.reestr'),
        'buttons' => [
            trans('decrees::admin.list') => 'admin.decrees.index',
        ],
        'add' => [trans('decrees::admin.new'), 'admin.decrees.create'],
    ])
@endsection
