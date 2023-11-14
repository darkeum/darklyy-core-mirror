@extends('away::manager.body')

@section('title', __('away::general.editor'))

@section('content')
    <livewire:away::form :away="$away" />
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection
