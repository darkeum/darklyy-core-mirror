<div class="col-lg-6 col-md-3 col-6">
    <div class="card">
        <div class="card-header pb-0">
            <div>
                <h4 class="card-title">@lang('home::orders.title')</h4>
                <p class="card-text font-small-2">@lang('home::orders.now')</p>
            </div>
        </div>
        <div class="card-body pb-50">
            <h4 class="fw-bolder">@lang('home::orders.total', ['total' => $total])</h4>
            <div wire:ignore wire:key={{ $chart_id }}>
                @if ($chart)
                    {!! $chart->container() !!}
                @endif
            </div>
            @if ($chart)
                @push('scripts')
                    {!! $chart->script() !!}                    
                @endpush
            @endif

        </div>
    </div>
</div>
