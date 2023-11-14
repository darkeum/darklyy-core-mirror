<div>
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
                    {{ trans('controls::lang.pay.application') }}
                    <span class="invoice-number">№{{ module_perfix('fcontrol', $order->id). '(Счет №'.module_perfix('supply_order', $order->supply_o).')'}}</span>
                </h4>
                <div class="invoice-date-wrapper">
                    <p class="invoice-date-title">{{ trans('controls::lang.storage.date_create') }}:</p>
                    <p class="invoice-date">
                        {{ \Boot\Support\Carbon::parse($order->date)->format('d.m.Y') }}
                    </p>
                </div>
                <div class="invoice-date-wrapper">
                    <p class="invoice-date-title me-1">{{ trans('controls::lang.pay.date_pay') }}:</p>
                    <input type="text" id="plan_pay_date" class="form-control-sm flatpickr date-max-width" placeholder="YYYY-MM-DD" value="{{ $order->plan_pay_date }}" />
                </div>
                <div class="invoice-date-wrapper">
                    <p class="invoice-date-title">{{ trans('controls::lang.pay.num') }} / {{ trans('controls::lang.pay.date_invoice') }}:</p>
                    <p class="invoice-date me-1">{{ $order->pay_num }} </p> /
                    <input type="text" id="pay_date" class="form-control-sm flatpickr date-max-width ms-1" placeholder="YYYY-MM-DD" value="{{ $order->pay_date }}" />
                </div>
                <div class="invoice-date-wrapper">
                    <p class="invoice-date-title">{{ trans('controls::lang.pay.partial_pay') }}:</p>
                    <div class="ms-1">
                        <div role="button" onclick="dateModal('{{ $order->id }}', '{{ router('admin.controls.save_date', ['id' => $order->id]) }}')">{{ trans('controls::lang.pay.date_add') }} <i class="fas fa-plus text-success"></i></div>
                        <div id="date_pay">
                            @foreach($payDate as $date)
                                <div id="date_{{$date->id}}" style="margin-bottom: 5px;border-bottom: 1px grey solid;">
                                    {{ \Boot\Support\Carbon::parse($date->date)->format('d.m.Y') }}
                                    <i style="cursor:pointer" class="fas fa-times text-danger cp" onclick="dateModalDelete('{{ $date->id }}', '{{ router('admin.controls.delete_date', ['id' => $date->id]) }}');"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <hr class="invoice-spacing" />

    <!-- Address and Contact starts -->
    <div class="card-body invoice-padding pt-0">
        <div class="row invoice-spacing">
            <div class="col-xl-6 p-0">
                <h6 class="mb-2">{{ trans('controls::lang.pay.invoice') }}:</h6>
                <h6 class="mb-25">{{ trans('controls::lang.pay.responsible') }}: {{ $shortUserName }}</h6>
                <div class="d-flex">
                    <p class="card-text mb-25 me-1">{{ trans('controls::lang.pay.provider') }}:</p>
                    <a target="_blank" href="{{ url(ADMIN . '/module/organization/edit', $order->id) }}">{{ $order->supply_order->organization->name }}</a>
                </div>
            </div>
            <div class="col-xl-6 p-0 mt-xl-0 mt-2">
                <h6 class="mb-2">{{ trans('controls::lang.pay.app_details') }}:</h6>
                <table>
                    <tbody>
                    <tr>
                        <td class="pe-1">{{ trans('controls::lang.storage.status') }}:</td>
                        <td>{!! $status !!}</td>
                    </tr>
                    <tr>
                        <td class="pe-1">{{ trans('controls::lang.pay.pay') }}:</td>
                        <td>{!! $statusPay !!}</td>
                    </tr>
                    <tr>
                        <td class="pe-1">{{ trans('controls::lang.pay.documents_invoice') }}:</td>
                        <td>
                            @forelse($attach as $item)
                                <div class="fw-bold">
                                    <a target="_blank" href="/download/{{$item['id']}}">{{$item['name']}}</a>
                                </div>
                            @empty
                                <div class="fst-italic">{{ trans('controls::lang.absent') }}</div>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <td class="pe-1">{{ trans('controls::lang.pay.linked_sales') }}:</td>
                        <td>
                            @forelse($sales as $sale)
                                <div class="fw-bold">
                                    <a href="{{ url(ADMIN. '/module/sales/order', $sale->sales_detail->sales->id) }}" target='_blank'>{{$sale->sales_detail->sales->numb}}</a>
                                    -
                                    <a href="{{ url(ADMIN. '/object/view', (int) $sale->sales_detail->sales->object) }}" target='_blank'>{{$sale->sales_detail->sales->objects->name}}</a>
                                </div>
                            @empty
                                <div class="fst-italic">{{ trans('controls::lang.absent') }}</div>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <td class="pe-1">{{ trans('controls::lang.comment') }}:</td>
                        <td>
                            @if($commentSupply)
                                {!! $commentSupply !!}
                            @else
                                <b>{{ trans('controls::lang.absent') }}</b>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="pe-1">{{ trans('controls::lang.pay.files') }}:</td>
                        <td>
                            @if($attachFiles->count() > 0)
                                <ul>
                                    @foreach($attachFiles as $files)
                                        <li><a href="/download/{{ $files->id }}">{{ $files->name }}</a></li>
                                    @endforeach
                                </ul>
                            @else
                                <b>{{ trans('controls::lang.absent') }}</b>
                            @endif
                        </td>
                    </tr>
                    @if(count($comments) > 0)
                        <tr>
                            <td>{{ trans('controls::lang.comment') }}:</td>
                            <td class="alert alert-primary">
                                @foreach($comments as $comment)
                                    <div>
                                        <b>{{ user_short_name($comment->user) . ' ' . \Boot\Support\Carbon::parse($comment->date)->format('d.m.Y') }}</b>
                                    </div>
                                    <div>{{ $comment->text }}</div>
                                @endforeach
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('swal:modal', event => {
            Swal.fire({
                title: event.detail.message,
                icon: event.detail.type,
            });
        });
    </script>
</div>