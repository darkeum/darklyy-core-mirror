@extends('calls::manager.body')

@section('title', __('calls::lang.admin.contacts'))

@section('header')
    @include('elements.header', [
        'buttons' => [
            trans('calls::lang.general.list_calls') => 'admin.calls.index',
            trans('calls::lang.general.list_phones') => 'admin.contacts.index',
        ],
        // 'show' => ['route' => 'admin.support.show'],
         'add' => [trans('calls::lang.general.contact_create'), 'admin.contacts.create']
    ])
@endsection

@section('content')
    <grid:calls::contacts />
@endsection