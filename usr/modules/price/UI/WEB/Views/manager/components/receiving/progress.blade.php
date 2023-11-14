<div class="col-lg-3 col-md-6 col-12" wire:poll>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">@lang('price::receiving.progress.title')</h4>        
            <ul class="list-inline mb-0">
                <li>
                    <a wire:click="reload"><i class="fa-light fa-rotate-right"></i></a>
                </li>
            </ul>
        </div>
        <div class="card-body p-0">
            <div class="my-2" wire:ignore wire:key={{ $chart_id }}>
                @if ($chart)
                    {!! $chart->container() !!}
                @endif
            </div>
            @if ($chart)
                @push('scripts')
                    {!! $chart->script() !!}
                @endpush
            @endif
            <div class="row border-top text-center mx-0">
                <div class="col-6 border-end py-1">
                    <p class="card-text text-muted mb-0">@lang('price::receiving.progress.all')</p>
                    <h3 class="fw-bolder mb-0">{{ $all_count }}</h3>
                </div>
                <div class="col-6 py-1">
                    <p class="card-text text-muted mb-0">@lang('price::receiving.progress.complete')</p>
                    <h3 class="fw-bolder mb-0">{{ $complete_count }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
