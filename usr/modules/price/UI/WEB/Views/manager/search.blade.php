@extends('price::manager.body')

@section('title', __('price::search.title'))

@section('content')
    <livewire:price::search/>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endpush

@push('scripts')
    <script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
@endpush
