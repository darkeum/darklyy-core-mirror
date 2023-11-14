@extends('controls::manager.body')

@section('content')
    @include('controls::manager.storage.detail.content')
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/full/DataTables-1.12.1/css/jquery.dataTables.css') }}">
    <link rel="stylesheet" href="{{ asset('usr/modules/controls/Resources/Assets/css/Style.css') }}">
    <link rel="stylesheet" href="{{asset('css/base/pages/app-invoice.css')}}">
@endsection

@section('vendor-script')
    <script src="{{ asset('usr/plugins/js/ajax_admin.js') }}"></script>
    <script src="{{ asset('usr/modules/controls/Resources/Assets/js/controlStorage.js') }}"></script>
    <script src="{{ asset('usr/plugins/js/ajax.js') }}"></script>
    <script src="{{asset('usr/modules/controls/Resources/Assets/js/function.js')}}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/full/DataTables-1.12.1/js/jquery.dataTables.min.js') }}"></script>
@endsection
