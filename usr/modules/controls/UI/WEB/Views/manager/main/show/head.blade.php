<div class="card-body invoice-padding pb-0">
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
            @lang('controls::lang.pay.application')
            <span class="invoice-number">#{{ $control->id }}</span>
        </h4>
        <div class="invoice-date-wrapper">
            <p class="invoice-date-title me-3">@lang('controls::lang.storage.date_create')</p>
            <p class="invoice-date">
                {{ \Boot\Support\Carbon::parse($control->date)->format('d.m.Y') }}
            </p>
        </div>
        <div class="invoice-date-wrapper">
            <p class="invoice-date-title me-2">@lang('controls::lang.pay.date_pay')</p>
            <input type="text" id="plan_pay_date" class="form-control-sm flatpickr date-max-width" placeholder="YYYY-MM-DD" value="{{ $control->plan_pay_date }}" />
        </div>
    </div>
</div>
</div>

<hr class="invoice-spacing" />

<!-- Address and Contact starts -->
<div class="card-body invoice-padding pt-0">
    <div class="row invoice-spacing">
        <div class="col-xl-6 p-0">
            <h6 class="mb-2">@lang('controls::lang.pay.invoice'):</h6>
            <h6 class="mb-25">@lang('controls::lang.pay.responsible'): {{ user_short_name($control->user) }}</h6>
            <p class="card-text mb-25">@lang('controls::lang.pay.provider'): {{ $control->supply_order->organization->name }}</p>
            <div class="d-flex">
                <p class="card-text mb-25" style="margin-right: 5px;">@lang('controls::lang.pay.invoice_provider'):</p>
                <div class="fw-bold">
                    <a href='{{ url(ADMIN. '/module/supply/order_detail', $control->supply_o) }} ' target='_blank'>{{$control->supply_order->numb}}</a>
                </div>
            </div>
        </div>
        <div class="col-xl-6 p-0 mt-xl-0 mt-2">
            <h6 class="mb-2">@lang('controls::lang.pay.app_details'):</h6>
            <table>
                <tbody>
                <tr>
                    <td class="pe-1">@lang('controls::lang.storage.status'):</td>
                    <td>{!! $control->status_text !!}</td>
                </tr>
                <tr>
                    <td class="pe-1">@lang('controls::lang.pay.pay'):</td>
                    <td class="badge-light-primary">{!! $control->pay_text !!}</td>
                </tr>
                <tr>
                    <td class="pe-1">@lang('controls::lang.pay.documents_invoice'):</td>
                    <td>
                        @forelse($attach as $item)
                            <div class="fw-bold">
                                <a target="_blank" href="/download/{{$item['id']}}">{{$item['name']}}</a>
                            </div>
                        @empty
                            <div class="fst-italic">@lang('controls::lang.absent')</div>
                        @endforelse
                    </td>
                </tr>
                <tr>
                    <td class="pe-1">@lang('controls::lang.pay.linked_sales'):</td>
                    <td>
                        @forelse($sales as $sale)
                            <div class="fw-bold">
                                <a href="{{ url(ADMIN. '/module/sales/order', $sale['id']) }}" target='_blank'>{{$sale['numb']}}</a>
                                -
                                <a href="{{ url(ADMIN. '/object/view', $sale['object_']['id']) }}" target='_blank'>{{$sale['object_']['name']}}</a>
                            </div>
                        @empty
                            <div class="fst-italic">@lang('controls::lang.absent')</div>
                        @endforelse
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col">@lang('controls::lang.comment'):</div>
        <div class="col">
            @forelse($comments as $comment)
                <div class="border p-2">
                    <div class="fw-bold">
                        {{user_short_name($comment->user)}}
                        {{$comment->date}}
                    </div>
                    <div>{{$comment->text}}</div>
                </div>
            @empty
                <div class="fst-italic">@lang('controls::lang.absent')</div>
            @endforelse</div>
    </div>
</div>