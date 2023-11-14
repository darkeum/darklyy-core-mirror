<div class="card-body invoice-padding pb-0 mb-1">
    <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
        <div>
            <div class="logo-wrapper">
                @include('layouts.svg.' . check_company(true))
                <h3 class="text-primary invoice-logo">
                    @lang('app.name_' . check_company(true))
                </h3>
            </div>
            <p class="card-text mb-25">
                @lang('app.address')
            </p>
            <p class="card-text mb-25">
                @lang('app.email')
            </p>
            <p class="card-text mb-0">
                @lang('app.phone_num_text')
            </p>
        </div>
        <div class="mt-md-0 mt-2">
            <h4 class="invoice-title">
                {{$topContent->title}}
                <span class="invoice-number">#{{module_perfix('fcontrol_storage', $topContent->id)}}</span>
            </h4>
            <div class="invoice-date-wrapper d-flex">
                <p class="invoice-date-title">{{ trans('controls::lang.storage.date_create') }}:</p>
                <p class="invoice-date ms-1">
                    {{date("d.m.Y", strtotime($topContent->date))}}
                </p>
            </div>
            <div class="invoice-date-wrapper d-flex">
                <div class="invoice-date-title">{{ trans('controls::lang.storage.status') }}:</div>
                <div class="ms-1">
                    @switch($topContent->status)
                        @case(1)
                            <span class='badge rounded-pill badge-light-danger' data-toggle='tooltip'>{{$status[$topContent->status]}}</span>
                            @break
                        @case(2)
                            <span class='badge rounded-pill badge-light-success' data-toggle='tooltip'>{{$status[$topContent->status]}}</span>
                            @break
                        @case(3)
                            <span class='badge rounded-pill badge-light-warning' data-toggle='tooltip'>{{$status[$topContent->status]}}</span>
                            @break
                    @endswitch
                </div>
            </div>
        </div>
    </div>
</div>
