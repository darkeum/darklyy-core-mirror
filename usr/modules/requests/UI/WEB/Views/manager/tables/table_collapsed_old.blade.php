@php
    $electric_sum = 0;
    $station_sum = 0;
@endphp
<div class="table-responsive">
    <table class="table">

        <thead>
        <th class='text-center py-1'>Номер в каталоге</th>
        <th class='text-center py-1'>Артикул</th>
        <th class="text-center py-1">Название</th>
        <th class='text-center py-1'>Количество</th>
        <th class='text-center py-1'>Цена за единицу</th>
        <th class='text-center py-1'>Суммарная цена</th>
        </thead>

        {{-- Электрика --}}
        <thead>
            <tr>
                <th colspan="6" class="collapsed" data-bs-toggle="collapse" href="#electric" role="button" aria-expanded="false" aria-controls="electric">
                    Электрика
                </th>
            </tr>
        </thead>
        <tbody class="collapse" id="electric">
        @foreach($electric as $item)
            <tr class="">
                <td class='text-center'>{{$item['tm_cid']}}</td>
                <td class='text-center'>{{$item['tm_sku']}}</td>
                <td data-bs-toggle="tooltip" data-bs-placement="top"
                    title="{{$item['tm_title']}}">{{ mb_strimwidth($item['tm_title'],0,85,"...")}}
                    руб.
                </td>
                <td class='text-center'>{{$item['tm_count']}}</td>
                <td class='text-center'>{{number_format($item['tm_price'], 0, '', ' ')}} <i
                            class="fa-light fa-ruble-sign"></i></td>
                <td class='text-center'>{{number_format($item['total_price'], 0, '', ' ')}} <i
                            class="fa-light fa-ruble-sign"></i></td>
                @php
                    $electric_sum += $item['total_price']
                @endphp
            </tr>
        @endforeach
        </tbody>
        {{-- Электрика --}}

        {{-- Станция --}}
        <thead>
            <tr>
                <th colspan="6" class="collapsed" data-bs-toggle="collapse" href="#station" role="button" aria-expanded="false" aria-controls="station">
                    <div class="d-flex justify-between">
                        Станция
                        <span class="ms-auto">ываыва</span>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody class="collapse" id="station">
        @foreach($station  as $item)
            <tr>
                <td class='text-center'> {{ $item['tm_cid'] }}</td>
                <td class='text-center'>{{ $item['tm_sku'] }}</td>
                <td data-bs-toggle="tooltip" data-bs-placement="top" title="{{$item['tm_title']}}">
                    {{ mb_strimwidth($item['tm_title'],0,85,"...") }} руб.
                </td>
                <td class='text-center'>{{ $item['tm_count'] }}</td>
                <td class='text-center'>
                    {{ number_format($item['tm_price'], 0, '', ' ') }}
                    <i class="fa-light fa-ruble-sign"></i>
                </td>
                <td class='text-center'>
                    {{ number_format($item['total_price'], 0, '', ' ') }}
                    <i class="fa-light fa-ruble-sign"></i>
                </td>
                @php
                    $station_sum += $item['total_price']
                @endphp
            </tr>
        @endforeach
        </tbody>
        {{-- Станция --}}

    </table>
</div>


<div class="card-body invoice-padding pb-0">
    <div class="row invoice-sales-total-wrapper">
        <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">

            <p class="card-text mb-0">
                <span class="fw-bold">ИТОГО:</span>
                {{--<span class="ms-75">Alfie Solomons</span>--}}
            </p>

        </div>
        <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
            <div class="invoice-total-wrapper">
                <div class="invoice-total-item">
                    <p class="invoice-total-title">Станция:</p>
                    <p class="invoice-total-amount">{{number_format($station_sum, 0, '', ' ')}}
                        <i class="fa-light fa-ruble-sign"></i></p>
                </div>
                <div class="invoice-total-item">
                    <p class="invoice-total-title">Электрика:</p>
                    <p class="invoice-total-amount">{{number_format($electric_sum, 0, '', ' ')}}
                        <i class="fa-light fa-ruble-sign"></i></p>
                </div>
                <div class="invoice-total-item">
                    <p class="invoice-total-title">Скидка:</p>
                    <p class="invoice-total-amount">0 <i class="fa-light fa-ruble-sign"></i></p>
                </div>
                <div class="invoice-total-item">
                    <p class="invoice-total-title">Налог:</p>
                    <p class="invoice-total-amount">0 <i class="fa-light fa-percent"></i></p>
                </div>
                <hr class="my-50"/>
                <div class="invoice-total-item">
                    <p class="invoice-total-title">Всего:</p>
                    <p class="invoice-total-amount">{{number_format($electric_sum + $station_sum, 0, '', ' ')}}
                        <i class="fa-light fa-ruble-sign"></i></p>
                </div>
            </div>
        </div>
    </div>
</div>