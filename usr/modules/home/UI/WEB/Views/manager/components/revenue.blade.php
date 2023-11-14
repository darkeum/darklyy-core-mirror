<div class="col-lg-8 col-12">
    <div class="card card-revenue-budget">
        <div class="row mx-0">
            <div class="col-md-8 col-12 revenue-report-wrapper">
                <div class="d-sm-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-50 mb-sm-0">@lang('home::revenue.title')</h4>
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center me-2">
                            <span class="bullet font-small-3 me-50 cursor-pointer" style="background-color: rgba(115, 103, 240, 0.85);"></span>
                            <span>@lang('home::revenue.income')</span>
                        </div>
                        <div class="d-flex align-items-center ms-75">
                            <span class="bullet bullet-warning font-small-3 me-50 cursor-pointer"></span>
                            <span>@lang('home::revenue.consumption')</span>
                        </div>
                    </div>
                </div>
                <div wire:ignore wire:key={{ $chart_id }}>

                    {{-- preloader --}}
                    @include('elements.preloader', ['wire' => true])
                    {{-- preloader --}}

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
            <div class="col-md-4 col-12 budget-wrapper">
                <div class="btn-group">
                    {{-- Компания --}}
                    <div class="{{--btn-group--}}">
                        <button type="button"
                                class="btn btn-outline-primary btn-sm dropdown-toggle budget-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                        >
                            {{ $org_choose_title }}
                        </button>
                        <div class="dropdown-menu">
                            @foreach ($org as $value)
                                <a class="dropdown-item" href="#" wire:click="changeRevenue({{ $value->id }})">
                                    {{ $value->organization->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    {{-- Компания --}}

                    {{-- Год --}}
                    {{--<div class="btn-group">--}}
                    {{--    <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle budget-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
                    {{--        2022--}}
                    {{--    </button>--}}
                    {{--    <div class="dropdown-menu">--}}
                    {{--        <a class="dropdown-item" href="#">2022</a>--}}
                    {{--        <a class="dropdown-item" href="#">2021</a>--}}
                    {{--        <a class="dropdown-item" href="#">2020</a>--}}
                    {{--    </div>--}}
                    {{--</div>--}}
                    {{-- Год --}}
                </div>

                {{-- Баланс в текущем месяце --}}
                <div class="mb-25">
                    @lang('home::revenue.balance.now')
                </div>
                <h2 class="mb-25 @if($balance_now < 0){{ 'text-danger' }}@elseif($balance_now > 0){{ 'text-success' }}@else{{ 'text-secondary' }}@endif">
                    {{ $balance_now }}
                </h2>
                {{-- Баланс в текущем месяце --}}

                {{-- Баланс в прошлом месяце --}}
                {{--<div class="d-flex justify-content-center">--}}
                {{--    <span class="fw-bolder me-25">Прошлый месяц:</span>--}}
                {{--    <span>999,999</span>--}}
                {{--</div>--}}
                {{-- Баланс в прошлом месяце --}}

                 <div wire:ignore wire:key={{ $chart_second_id }}>
                    @if ($chart_second)
                        {!! $chart_second->container() !!}
                    @endif
                </div>
                @if ($chart_second)
                    @push('scripts')
                        {!! $chart_second->script() !!}                       
                    @endpush
                @endif
                <button type="button" disabled class="btn btn-primary">@lang('home::revenue.balance.go')</button>
            </div>
        </div>
    </div>
</div>
