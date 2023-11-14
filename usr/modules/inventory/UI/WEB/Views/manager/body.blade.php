@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => __('inventory::general.title'),
        'buttons' => [
            __('inventory::general.index') => 'admin.inventory.index'
        ],
        'add' => [__('inventory::general.add'), 'admin.inventory.create'],
    ])
@endsection
