<div class="col-lg-12 col-md-6 col-12">
    <div class="card earnings-card">
        <div class="card-body">
            <div class="row">
                <div class="col-6">
                    <h4 class="card-title mb-1">@lang('home::earnings.title')</h4>
                    <div class="font-small-2">@lang('home::earnings.description')</div>
                    <h5 class="mb-1 mt-1">{{ $total }}</h5>
                    <p class="card-text text-muted font-small-2 line-height-inherit">
                        @lang('home::earnings.more', ['more' => $more, 'proc' => $proc])
                    </p>
                </div>
                <div class="col-6" wire:ignore wire:key={{ $chart_id }}>
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
</div>
