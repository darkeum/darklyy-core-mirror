@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => trans('cashbox::general.module'),
        'buttons' => [
            trans('cashbox::general.index') => 'admin.cashbox.index',
            trans('cashbox::general.checks') => 'admin.cashbox.checks.index',
            trans('cashbox::general.cashier') => 'admin.cashbox.cashier.index',
        ],
    ])
@endsection
