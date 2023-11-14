@php
    $total = $bank_detail['sum'] - $bank_pay_total
@endphp

<div class="row">
    <div class="col-12">
        <div class="card m-0">
            <div class="card-header d-flex justify-content-between align-items-center px-0 py-50">

                @if($total > 0)
                    <div class="btn-group">
                        <button type="button" class="btn btn-success" id="button-append">Добавить</button>
                        <button type="button" class="btn btn-danger" id="button-clear">Очистить</button>
                    </div>
                @endif

                <div class="d-flex align-items-center">
                    <div class="">Осталось распределить :</div>
                    <div id="total" class="fw-bold fs-2 ms-50">{{ number_format($total, 2, ',') }}</div>
                    <div class="fw-bold fs-4 ms-50">₽</div>
                </div>

            </div>

            <div class="">
                <table class="table table-bordered">
                    <thead class="table-dark text-center">
                    <tr>
                        <th class="col-3">Уникальный идентификатор</th>
                        <th class="col-auto">Документ</th>
                        <th class="col-3">Сумма, ₽</th>
                        <th class="col-2">Действия</th>
                    </tr>
                    </thead>

                    {{--{{ dd($bank_detail['module']) }}--}}
                    <tbody id="detail-tbody" class="border" data-module="{{ $bank_detail['module'] }}">
                    {{--<tbody id="detail-tbody" class="border" data-module="supply_order">--}}
                    {{-- data-module="supply_order_select или sales_select" читается class *_select --}}

                    @forelse($bank_pay->reverse() as $item)

                        @if ($bank_detail['module'] == 'sales')

                            @php
                                $link = "/administration/module/sales/order/" .$item['pid'];
                                $document = "Заказ №". $item['sales']['numb'] ." " . $item['sales']['organization']['name']
                            @endphp

                            {{--{{ $link = "/administration/module/sales/order/" .$item['pid'] }}--}}
                            {{--{{ $document = "Заказ №". $item['sales']['numb'] ." " . $item['sales']['organization']['name'] }}--}}

                        @elseif($bank_detail['module'] == 'supply_order')
                            @php
                                $link = "/administration/module/supply/order_detail/" . $item['pid'];
                                $document = 'Счёт от поставщика №'. $item['supply_order']['numb'];
                            @endphp

                            {{--{{ $link = "/administration/module/supply/order_detail/" . $item['pid'] }}--}}
                            {{--{{ $document = 'Счёт от поставщика №'. $item['supply_order']['numb'] }}--}}

                        @else

                            @php
                                $link = false;
                                $document = "Оплата модуля: ". $bank_detail['module'] ." id: " . $item['pid']
                            @endphp
                            {{--{{ $link = false }}--}}
                            {{--{{ $document = "Оплата модуля: ". $bank_detail['module'] ." id: " . $item['pid'] }}--}}

                        @endif

                        <tr id="tr_{{ $item['id'] }}" data-id="{{ $item['id'] }}">
                            <td class="item-id">{{ $item['id'] }}</td>
                            <td class="item-document" data-pid="{{ $item['pid'] }}" data-module="{{ $bank_detail['module'] }}">
                                {{ $document }}
                            </td>
                            <td class="item-sum">{{ number_format($item['sum'], 2) }}</td>
                            <td class="item-buttons">
                                <div class="item-buttons-group btn-group">
                                    @if($link)
                                        <a href="{{ $link }}" target="_blank" class="btn btn-sm btn-info">Перейти</a>
                                    @endif
                                    <button type="button" class="btn btn-primary btn-sm button-edit" data-id="{{ $item['id'] }}">
                                        Редактировать
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm button-delete" data-id="{{ $item['id'] }}">
                                        Удалить
                                    </button>
                                </div>
                            </td>
                        </tr>

                    @empty

                        <tr id="tr-empty">
                            <td class="p-0" colspan="4">
                                <div class="text-center w-100 py-50">Пусто</div>
                            </td>
                        </tr>

                    @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>