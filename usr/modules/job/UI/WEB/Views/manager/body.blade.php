@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'title' => __('job::general.title'),
        'buttons' => [
            __('job::general.list') => 'admin.job.index',
            __('job::general.subscribes') => 'admin.job.subscribes',
        ],

        'add' => [__('job::general.create'), 'admin.job.create']
    ])
@endsection
