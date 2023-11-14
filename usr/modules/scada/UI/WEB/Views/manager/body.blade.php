@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => __('scada::general.title'),
        'buttons' => [
            __('scada::general.dashboard') => 'admin.scada.dashboard',
            __('scada::general.objects') => 'admin.scada.index',
            __('scada::general.tags') => 'admin.scada.tags.index',
            __('scada::general.crash') => 'admin.scada.crash.index',
            __('scada::general.actions') => 'admin.scada.actions.index',
        ],

        'add' => [__('scada::general.create'), 'admin.scada.create'],
        'show' => ['admin.away.show'],
    ])
@endsection
