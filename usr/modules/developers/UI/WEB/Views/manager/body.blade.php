@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => trans('developers::admin.developers'),
        'buttons' => array(
            __('developers::admin.index') => 'admin.developers.index',
            __('developers::admin.git') => 'admin.developers.git',
            __('developers::admin.composer') => 'admin.developers.composer',
            __('developers::admin.daemons') => 'admin.developers.daemon.index',
            __('developers::admin.routes') => 'admin.developers.routes',
            __('developers::admin.wilo') => 'admin.developers.wilo',
        ),

    ])
@endsection