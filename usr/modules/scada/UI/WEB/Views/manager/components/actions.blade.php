<div class="col-lg-6 col-12">
    <div class="card  @if ($actions->count()) {{ 'card-company-table' }}@else{{ 'text-center' }} @endif">
        <div class="card-header">
            <h4 class="card-title">@lang('scada::actions.list.last')</h4>
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

        @if ($actions->count())
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>@lang('scada::actions.grid.tag')</th>
                                <th>@lang('scada::actions.grid.value')</th>
                                <th>@lang('scada::actions.grid.status')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($actions as $action)
                                <tr>
                                    <td class="text-truncate">
                                        <div>
                                            <div class="fw-bolder">{{ $action->tags->title }}</div>
                                            <div class="font-small-2 text-muted">{{ $action->tags->scada->title }}</div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">
                                        <div>
                                            <div class="fw-bolder" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="{{ $action->value }}">
                                                {{ \Boot\Support\Str::limit($action->value, 45) }}</div>
                                            <div class="font-small-2 text-muted">{{ \Boot\Support\Carbon::parse($action->created_at)->format('d.m.Y H:i:s') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($action->status == 0)
                                                <span class='badge rounded-pill badge-light-danger d-block' data-toggle='tooltip'>@lang('scada::actions.grid.status.off')</span>
                                            @else
                                                <span class='badge rounded-pill badge-light-success d-block' data-toggle='tooltip'>@lang('scada::actions.grid.status.on')</span>
                                            @endif
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
                <h4 class="card-title">@lang('scada::actions.empty')</h4>
                <p class="card-text">
                    @lang('scada::actions.empty.description')
                </p>
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
