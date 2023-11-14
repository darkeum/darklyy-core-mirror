<div class="row" wire:poll="reload">
    <div class="col-xl-2 col-md-4 col-6 mb-14">
        <div class="card">
            <div class="card-body text-center">
                @if ($has_poll)
                    <div class="badge p-2 bg-label-success rounded"><i class="fa-light fa-check ti-md"></i></div>
                @else
                    <div class="badge p-2 bg-label-danger rounded"><i class="fa-light fa-xmark ti-md"></i></div>
                @endif

                <h5 class="card-title pt-2">@lang('scada::uptime.poll')</h5>
                <small class="text-muted">@lang('scada::uptime.poll.last')</small>
                <p class="mb-1">{{ $poll }}</p>
                <div class="pt-1">
                    @if ($has_poll)
                        <span class="badge bg-label-success">@lang('scada::uptime.poll.active.on')</span>
                    @else
                        <span class="badge bg-label-danger">@lang('scada::uptime.poll.active.off')</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-6 mb-14">
        <div class="card">
            <div class="card-body text-center">
                <div class="badge p-2 bg-label-success rounded"><i class="fa-light fa-check ti-md"></i></div>
                <h5 class="card-title pt-2">WEBSOCKET</h5>
                <small class="text-muted">обновлено</small>
                <p class="mb-1">{{ \Boot\Support\Carbon::now()->format('d.m.Y H:i:s') }}</p>
                <div class="pt-1">
                    <span class="badge bg-label-success">Активна</span>
                </div>
            </div>
        </div>
    </div>
</div>
