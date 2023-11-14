@extends('layouts.base')

@section('title',  __('roles::lang.module'))

@section('header')
    @include('elements.header', [
            'title' => trans('roles::lang.module'),
            'buttons' => [
                trans('roles::lang.all') => 'admin.roles.index',
            ],
            //'add' => [trans('roles::lang.admin.add'), 'admin.roles.create']
        ])
@endsection

@section('content')

    <!-- Role cards -->
    <livewire:roles::roles/>
    <!--/ Role cards -->

    <!-- table -->
    <grid:roles::roles/>
    <!-- table -->

    {{-- @include('content/_partials/_modals/modal-add-role') --}}

@endsection

@section('vendor-style')
    <!-- Vendor css files -->
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap5.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/buttons.bootstrap5.min.css')) }}">
@endsection

@section('vendor-script')
    <!-- Vendor js files -->
    <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.bootstrap5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
@endsection

@section('page-style')
    <!-- Page css files -->
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-validation.css')) }}">
@endsection

@push('scripts')

    <script src="{{ asset('/js/modules/roles/role.js') }}"></script>

@endpush
