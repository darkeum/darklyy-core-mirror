<div class="col-lg-8 col-12">
    <div class="card  @if($sales->count()){{ 'card-company-table' }}@else{{ 'text-center' }}@endif">
        <div class="card-header">
            <h4 class="card-title">@lang('home::sales.tasks')</h4>
            <div class="heading-elements">
                <ul class="list-inline mb-0">
                    <li>
                        <a wire:click="reload" {{--data-action="reload"--}}><i class="fa-light fa-rotate-right"></i></a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- preloader --}}
        @include('elements.preloader', ['wire' => true])
        {{-- preloader --}}

        @if ($sales->count())
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>@lang('home::sales.numb')</th>
                            <th>@lang('home::sales.organization')</th>
                            <th>@lang('home::sales.task')</th>
                            <th>@lang('home::sales.total')</th>
                            <th>@lang('home::sales.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($sales as $sale)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar rounded">
                                            <div class="avatar-content">
                                                @switch($sale->sales_type)
                                                    @case(0)
                                                    <img src="{{ asset('images/icons/book.svg') }}"
                                                         data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                         data-bs-placement="bottom"
                                                         title="{{ $sale->getSalesTypeViewAttribute() }}"
                                                         alt="{{ $sale->getSalesTypeViewAttribute() }}"/>
                                                    @break

                                                    @case(1)
                                                    <img src="{{ asset('images/icons/toolbox.svg') }}"
                                                         data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                         data-bs-placement="bottom"
                                                         title="{{ $sale->getSalesTypeViewAttribute() }}"
                                                         alt="{{ $sale->getSalesTypeViewAttribute() }}"/>
                                                    @break

                                                    @case(2)
                                                    <img src="{{ asset('images/icons/brush.svg') }}"
                                                         data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                         data-bs-placement="bottom"
                                                         title="{{ $sale->getSalesTypeViewAttribute() }}"
                                                         alt="{{ $sale->getSalesTypeViewAttribute() }}"/>
                                                    @break

                                                    @case(3)
                                                    <img src="{{ asset('images/icons/speaker.svg') }}"
                                                         data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                         data-bs-placement="bottom"
                                                         title="{{ $sale->getSalesTypeViewAttribute() }}"
                                                         alt="{{ $sale->getSalesTypeViewAttribute() }}"/>
                                                    @break

                                                    @case(4)
                                                    <img src="{{ asset('images/icons/rocket.svg') }}"
                                                         data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                         data-bs-placement="bottom"
                                                         title="{{ $sale->getSalesTypeViewAttribute() }}"
                                                         alt="{{ $sale->getSalesTypeViewAttribute() }}"/>
                                                    @break

                                                    @case(5)
                                                    <img src="{{ asset('images/icons/star.svg') }}"
                                                         data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                         data-bs-placement="bottom"
                                                         title="{{ $sale->getSalesTypeViewAttribute() }}"
                                                         alt="{{ $sale->getSalesTypeViewAttribute() }}"/>
                                                    @break
                                                @endswitch

                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bolder">{{ $sale->numb }}</div>
                                            <div class="font-small-2 text-muted">{{ \Boot\Support\Carbon::parse($sale->date)->format('d.m.Y') }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-truncate" style="max-width: 250px">
                                    <div class="d-flex align-items-center">

                                        <div class="avatar bg-light-{{ $sale->getStatusClientColorAttribute() }} me-1">
                                            <div class="avatar-content">
                                                {!! $sale->getStatusClientIconAttribute() !!}
                                            </div>
                                        </div>

                                        <div>
                                            <div class="fw-bolder" data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                                 data-bs-placement="bottom" title="{{ $sale->organization->name }}"
                                            >
                                                {{ $sale->organization->name }}
                                            </div>

                                            <div class="font-small-2 text-muted" data-bs-toggle="tooltip"
                                                 data-popup="tooltip-custom" data-bs-placement="bottom"
                                                 title="{{ $sale->object_->name }}"
                                            >
                                                {{ $sale->object_->name }}
                                            </div>

                                        </div>
                                    </div>
                                </td>

                                <td class="text-truncate @if (\Boot\Support\Carbon::now()->format('Y-m-d') > $sale->task_date_end) text-danger @endif"
                                    style="max-width: 250px">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bolder mb-25" data-bs-toggle="tooltip"
                                              data-popup="tooltip-custom" data-bs-placement="bottom"
                                              title="{{ $sale->task_title }}">{{ $sale->task_title }}</span>
                                        <span class="font-small-2 text-muted">{{ \Boot\Support\Carbon::parse($sale->task_date_end)->format('d.m.Y') }}</span>
                                    </div>
                                </td>
                                <td>{{ \Boot\Support\Numb::formatMoney($sale->total, '₽') }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a target="_blank" href="{{ route('admin.sales.show', $sale->id) }}"
                                           class="btn btn-sm btn-outline-success waves-effect">@lang('home::sales.open')</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="card-body d-flex flex-column align-items-center justify-content-center">
                <h4 class="card-title">@lang('home::sales.empty')</h4>
                <p class="card-text">
                    @lang('home::sales.empty.description')
                </p>
                <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-primary">
                    @lang('home::sales.go')
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('tooltip', () => {
            $('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
        });
    </script>
@endpush