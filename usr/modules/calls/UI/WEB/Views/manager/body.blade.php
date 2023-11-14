@extends('layouts.base')

@section('header')
    @include('elements.header', [
        'buttons' => [
            trans('calls::lang.general.list_calls') => 'admin.calls.index',
            trans('calls::lang.general.list_phones') => 'admin.contacts.index',
        ],
        // 'show' => ['route' => 'admin.support.show'],
        // 'add' => ['title' => trans('support::general.create'), 'route' => 'admin.support.create']
    ])
@endsection

@section('vendor-script')
    <script src="{{ asset('usr/tpl/admin/assets/js/script.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection