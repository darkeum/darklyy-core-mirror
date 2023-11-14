@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => __('bank::main.module'),
        'buttons' => [
            __('bank::main.operations') => 'admin.bank.index',
            __('bank::main.statements') => 'admin.bank.statements.index'
        ],
        'add' => [__('bank::main.create'), 'admin.bank.create'],
    ])
@endsection
