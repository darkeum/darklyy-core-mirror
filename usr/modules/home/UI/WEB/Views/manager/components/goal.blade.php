<div class="col-lg-3 col-md-6 col-12">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">@lang('home::goal.title')</h4>

            <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                  data-bs-html="true"
                  title="<em>Подсказка</em> <u>через</u> <b>HTML</b>"
            >
                <i class="font-medium-3 text-muted cursor-pointer"
                   data-feather="help-circle"
                ></i>
            </span>

        </div>
        <div class="card-body p-0">
            <div class="text-center">
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked="">
                    <label wire:click="changeGoal('quarter')" class="btn btn-outline-primary btn-sm waves-effect" for="btnradio1">@lang('home::goal.quarter')</label>
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
                    <label wire:click="changeGoal('year')" class="btn btn-outline-primary btn-sm waves-effect" for="btnradio2">@lang('home::goal.year')</label>
                </div>
            </div>

            <div class="my-2" wire:ignore wire:key={{ $chart_id }}>

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
            <div class="row border-top text-center mx-0">
                <div class="col-6 border-end py-1">
                    <p class="card-text text-muted mb-0">@lang('home::goal.completed')</p>
                    <h3 class="fw-bolder mb-0">{{ $total }}</h3>
                </div>
                <div class="col-6 py-1">
                    <p class="card-text text-muted mb-0">{{ $progress_text }}</p>
                    <h3 class="fw-bolder mb-0">{{ $progress }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
