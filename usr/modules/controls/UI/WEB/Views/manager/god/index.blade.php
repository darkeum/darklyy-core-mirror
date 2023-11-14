@extends('controls::manager.body')

@section('content')
    @include('controls::manager.god.content')
@endsection

@push('scripts')
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{asset(mix('vendors/js/forms/select/select2.full.min.js'))}}"></script>
@endpush
@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/full/DataTables-1.12.1/css/dataTables.bootstrap5.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('usr/modules/controls/Resources/Assets/js/function.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/full/DataTables-1.12.1/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendors/js/tables/datatable/full/DataTables-1.12.1/js/dataTables.bootstrap5.js') }}"></script>
@endsection