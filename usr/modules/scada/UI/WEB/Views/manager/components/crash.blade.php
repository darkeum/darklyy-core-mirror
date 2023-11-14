<div class="col-lg-6 col-12">
    <div class="card  @if ($crashes->count()) {{ 'card-company-table' }}@else{{ 'text-center' }} @endif">
        <div class="card-header">
            <h4 class="card-title">@lang('scada::crash.last')</h4>
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

        @if ($crashes->count())
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>@lang('scada::crash.grid.type')</th>
                                <th>@lang('scada::crash.grid.tag')</th>
                                <th>@lang('scada::crash.grid.description')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($crashes as $crash)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar rounded">
                                                <div class="avatar-content">
                                                    @switch($crash->type)
                                                        @case('crash')
                                                            <img src="{{ asset('images/icons/speaker.svg') }}" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="@lang('scada::crash.grid.type.crash')" alt="@lang('scada::crash.grid.type.crash')" />
                                                        @break

                                                        @case('connection')
                                                            <img src="{{ asset('images/icons/toolbox.svg') }}" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="@lang('scada::crash.grid.type.connection')" alt="@lang('scada::crash.grid.type.connection')" />
                                                        @break
                                                    @endswitch

                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">
                                        <div>
                                            @if (!empty($crash->tags_id))
                                                <div class="fw-bolder">{{ $crash->tags->title }}</div>
                                            @else
                                                Общая авария
                                            @endif
                                            <div class="font-small-2 text-muted">{{ $crash->scada->title }}</div>
                                        </div>
                                    </td>
                                    <td class="text-truncate">
                                        <div>
                                            <div class="fw-bolder" data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="bottom" title="{{ $crash->description }}">
                                                {{ \Boot\Support\Str::limit($crash->description, 45) }}</div>
                                            <div class="font-small-2 text-muted">{{ \Boot\Support\Carbon::parse($crash->created_at)->format('d.m.Y H:i:s') }}</div>
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
                <h4 class="card-title">@lang('scada::crash.empty')</h4>
                <p class="card-text">
                    @lang('scada::crash.empty.description')
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
