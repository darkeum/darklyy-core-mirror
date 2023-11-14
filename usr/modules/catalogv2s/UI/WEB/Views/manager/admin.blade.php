@extends('layouts.base')

@section('title', __('support::lang.list'))

@section('header')
    @include('elements.header', [
             'buttons' => [
                 trans('support::lang.my') => 'admin.support.index',
                 trans('support::lang.list') => 'admin.support.admin',
             ],
             // 'show' => ['admin.support.show'],
             'add' => [trans('support::lang.create'), 'admin.support.create']
         ])
@endsection

@section('content')

    <grid:support::supportindex />

@endsection
