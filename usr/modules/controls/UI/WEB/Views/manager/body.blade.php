@extends('layouts.base')

@section('header')
    @php
        $buttons = [];
    @endphp
    @can('control supply')
        @php
            $buttons['group'][trans('controls::lang.controls')] = 'admin.controls.index';
        @endphp
    @endcan
    @can('control admin')
        @php
            $buttons['group'][trans('controls::lang.controls_all')] = 'admin.controls.all.index';
        @endphp
    @endcan
    @can('control storage')
        @php
            $buttons[trans('controls::lang.controls_storage')] = 'admin.controls.storage.index';
        @endphp
    @endcan
    @can('control pay')
        @php
            $buttons[trans('controls::lang.controls_pay')] = 'admin.controls.pay.index';
        @endphp
    @endcan
    @can('control god')
        @php
            $buttons[trans('controls::lang.controls_god')] = 'admin.controls.god';
        @endphp
    @endcan
    @include('elements.header', [
        'buttons' => $buttons,
        // 'show' => ['admin.support.show'],
        // 'add' => [trans('support::general.create'), 'admin.support.create']
    ])
@endsection

@section('vendor-script')
    <script src="{{ asset('usr/modules/controls/Resources/Assets/js/function.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endsection
