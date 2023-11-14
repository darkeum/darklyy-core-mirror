@extends('price::manager.body')

@section('title', __('price::receiving.title'))

@section('content')
    <div class="row match-height">
        <livewire:price::receivingprogress />
    </div>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection

@section('page-script')
    {{-- <script src="{{ asset(mix('js/scripts/extensions/ext-component-blockui.js')) }}"></script> --}}
@endsection

@push('scripts')
    <script>
        window.livewire.on('chartUpdate', (chartId, labels, datasets) => {
            let chart = window[chartId];
            chart.updateSeries(datasets);
        });
    </script>
@endpush
