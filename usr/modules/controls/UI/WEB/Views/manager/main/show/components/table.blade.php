<div>
    <div class="table-responsive">
        @include('elements.preloader', ['wire' => true])
        <table class="table table-bordered">
            <thead>
            <tr>
                <th rowspan="2" class="text-center align-middle px-50 border-end">@lang('controls::lang.storage.num')</th>
                <th rowspan="2" class="text-center align-middle px-50 border-end">@lang('controls::lang.storage.article')</th>
                <th rowspan="2" class="align-middle px-50 border-end col-4">@lang('controls::lang.storage.name')</th>
                <th rowspan="2" class="text-center align-middle px-50 border-end col-auto" title="@lang('controls::lang.quantity')">
                    <i class="feather-layers"></i>
                </th>
                <th rowspan="2" class="text-center align-middle px-50 border-end">@lang('controls::lang.pay.ndr')</th>
                <th colspan="2" class="text-center align-middle px-50 border-end">@lang('controls::lang.pay.price')</th>
                <th colspan="3" class="text-center align-middle px-50 border-end">@lang('controls::lang.pay.result')</th>
                <th rowspan="2" class="text-center align-middle px-50" title="@lang('controls::lang.storage.action')">
                    <i class="feather-settings"></i>
                </th>
            </tr>
            <tr>
                <th class="text-center align-middle px-50 py-50">@lang('controls::lang.pay.sales')</th>
                <th class="text-center align-middle px-50 py-50 border-end">@lang('controls::lang.pay.purchases')</th>
                <th class="text-center align-middle px-50 py-50">@lang('controls::lang.pay.sales')</th>
                <th class="text-center align-middle px-50 py-50">@lang('controls::lang.pay.purchases')</th>
                <th class="text-center align-middle px-50 py-50 border-end" title="@lang('controls::lang.pay.difference')">
                    {{--<i data-feather='divide'></i>--}}
                    РАЗНИЦА
                </th>
            </tr>
            </thead>
            <tbody id="control-tbody">
            @foreach($control_detail as $item)
                <tr>
                    <td class="px-50 text-center border-end">{{ $loop->iteration }}</td>
                    <td class="px-50 text-center border-end">{{ $item->s_sku }}</td>
                    <td class="px-50 border-end">{{ $item->s_name }}</td>
                    <td class="px-50 text-center border-end">{{ $item->count }}</td>
                    <td class="px-50 text-nowrap text-center border-end">{{ strip_tags($item->ndr) }}</td>

                    <td class="px-50 text-center border-start">{{ $item->supply_order_detail->price_now }}</td>
                    <td class="px-50 text-center">{{ $item->sales_detail->s_price ?? '-' }}</td>

                    <td class="px-50 text-center border-start">{{ $item->supply_order_detail->total }}</td>
                    <td class="px-50 text-center">
                        @if($item->sales_detail != null)
                            <div class="text-center">
                                {{ $item->sales_detail->total }}
                            </div>

                        @else
                            <i data-feather='minus'></i>
                        @endif
                    </td>
                    @php($item_total = (isset($item->sales_detail->total)) ? $item->sales_detail->total - $item->supply_order_detail->total : 0)
                    <td class="px-50 text-center border-end {{ ($item_total > 0) ? 'text-success' : 'text-danger fw-bold' }}">
                        {{ ($item_total > 0) ? '+' : '' }}{{ round($item_total, 3) }}
                    </td>

                    <td class="px-50 text-center">
                        <a class="btn btn-sm btn-outline-secondary dropdown-toggle-split"
                           aria-expanded="false"
                           onclick="confirmDelete({{ $item->id }})"
                           data-bs-original-title="Удалить"
                           data-bs-toggle="tooltip"
                           data-placement="top"
                        >
                            <i class="feather-trash-2"></i>
                        </a>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    {{-- Сумма / Расчет / Ввод результатов и тд. --}}
    <div class="card-body invoice-padding pb-0 border-top">
        <div class="row invoice-sales-total-wrapper">
            <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
                <p class="card-text mb-0">
                    <span class="fw-bold">@lang('controls::lang.seller'):</span> <span class="ms-75">@lang('controls::lang.atrienergo')</span>
                </p>
            </div>
            <div class="col-md-6 d-flex justify-content-end order-md-2 order-1 text-nowrap">
                <div class="invoice-total-wrapper">
                    <div class="invoice-total-item">
                        <p class="invoice-total-title">@lang('controls::lang.pay.purchase_results'):</p>
                        <p class="invoice-total-amount">{{ $sales_total }}</p>
                    </div>
                    <div class="invoice-total-item">
                        <p class="invoice-total-title">@lang('controls::lang.pay.sales_results'):</p>
                        @if($sales_total == null)
                            <p class="invoice-total-amount">-</p>
                        @else
                            <p class="invoice-total-amount @if($total_diff > 0) text-success @endif">{{ $total_diff }}</p>
                        @endif
                    </div>
                    {{--<div class="invoice-total-item">--}}
                    {{--    <p class="invoice-total-title">Скидка:</p>--}}
                    {{--    <p class="invoice-total-amount"></p>--}}
                    {{--</div>--}}
                    {{--<div class="invoice-total-item">--}}
                    {{--    <p class="invoice-total-title">Tax:</p>--}}
                    {{--    <p class="invoice-total-amount">21%</p>--}}
                    {{--</div>--}}

                    <hr class="my-50"/>

                    <div class="invoice-total-item">
                        <p class="invoice-total-title">@lang('controls::lang.pay.total'):</p>
                        <p class="invoice-total-amount">
                            {{ $supply_order_total }}
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
    {{-- Сумма / Расчет / Ввод результатов и тд. --}}

    <hr class="invoice-spacing"/>

    {{-- Комментарий / Подсказка и тд. --}}
    <div class="card-body invoice-padding pt-0">
        <div class="row">
            <div class="col-12">
                <span class="fw-bold">@lang('controls::lang.pay.note'):</span>
                <span>
                    @lang('controls::lang.pay.company') <b>@lang('app.name_' . check_company(true))</b> @lang('controls::lang.pay.gratitude')
                </span>
            </div>
        </div>
    </div>
    {{-- Комментарий / Подсказка и тд. --}}

    <script>
        window.addEventListener('swal:delete', event => {
            Swal.fire({
                title: event.detail.message,
                icon: event.detail.type,
                showCancelButton: true,
                confirmButtonText: 'Удалить!',
                cancelButtonText: 'Назад',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('delete')
                }
            })
        });
        window.addEventListener('swal:modal', event => {
            Swal.fire({
                title: event.detail.message,
                icon: event.detail.type,
            });
        });
    </script>

</div>