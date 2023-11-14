@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/pages/dashboard-ecommerce.css')) }}" />
@endsection

@section('title')
    @lang('home::general.main')
@endsection

{{-- Dashboard Ecommerce Starts --}}
<section id="dashboard-ecommerce">
    <div class="row match-height">

        {{-- Rating Card --}}
        <livewire:home::rating />
        {{-- / Rating Card --}}

        {{-- Statistics Card --}}
        <livewire:home::statistics />
        {{-- / Statistics Card --}}

    </div>

    <div class="row match-height">
        <div class="col-lg-4 col-12">
            <div class="row match-height">

                {{-- Bar Chart - Orders --}}
                <livewire:home::orders />
                {{-- / Bar Chart - Orders --}}

                {{-- Line Chart - Profit --}}
                <livewire:home::profit />
                {{-- / Line Chart - Profit --}}

                {{-- Earnings Card --}}
                <livewire:home::earnings />
                {{-- / Earnings Card --}}

            </div>
        </div>

        {{-- Revenue Report Card --}}
        <livewire:home::revenue />
        {{-- / Revenue Report Card --}}

    </div>

    <div class="row match-height">

        {{-- Sales Table Card --}}
        <livewire:home::sales />
        {{-- / Sales Table Card --}}

        {{-- Calendar Card --}}
        <livewire:home::calendar />
        {{-- / Calendar Meetup Card --}}

        {{-- Annual statistics Card --}}
        <livewire:home::annual />
        {{-- / Annual statistics Card --}}

        {{-- Goal Overview Card --}}
        <livewire:home::goal />
        {{-- / Goal Overview Card --}}

        {{-- Transaction Card --}}
        <livewire:home::transaction />
        {{-- / Transaction Card --}}

    </div>
</section>

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/charts/chart.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
@endsection

@section('page-script')
    {{--<script src="{{ asset(mix('js/scripts/extensions/ext-component-blockui.js')) }}"></script>--}}
@endsection

@push('scripts')
    <script>
        window.livewire.on('chartUpdate', (chartId, labels, datasets) => {
            let chart = window[chartId];
            chart.updateSeries(datasets);
        });
    </script>
@endpush
