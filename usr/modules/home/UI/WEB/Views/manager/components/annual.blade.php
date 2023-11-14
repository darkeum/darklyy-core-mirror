<div class="col-lg-5 col-md-6 col-12">
    <div class="card card-browser-states">
        <div class="card-header">
            <div>
                <h4 class="card-title">@lang('home::annual.title')</h4>
                <p class="card-text font-small-2">@lang('home::annual.description')</p>
            </div>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li>
                        <a wire:click="reload"><i class="fa-light fa-rotate-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- preloader --}}
        @include('elements.preloader', ['wire' => true])
        {{-- preloader --}}

        <div class="card-body">
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
