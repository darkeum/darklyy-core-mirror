@extends('notifications::manager.body')

@section('title', __('notifications::admin.manager'))

@section('content')
    <grid:notifications::index />
@endsection

@section('vendor-script')
  <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection
