<div class="table-responsive">
    <table class="table table-striped table-hover table-sm">

        <thead>
            <th class='text-center py-1 col-1'>Номер в каталоге</th>
            <th class='align-middle text-center py-1'>Артикул</th>
            <th class="align-middle py-1">Название</th>
            <th class='align-middle text-center py-1'>Количество</th>
            <th class='align-middle text-center py-1'>Цена за единицу</th>
            <th class='align-middle text-center py-1 col-2'>Сумма</th>
        </thead>

        {{-- Электрика --}}
        <thead>
        <tr>
            <th colspan="6" class="collapsed" data-bs-toggle="collapse" href="#electric" role="button" aria-expanded="false" aria-controls="electric">
                Электрика
                <i class="fa-regular fa-circle-plus"></i>
            </th>
        </tr>
        </thead>
        <tbody class="collapse" id="electric">
        @foreach($electric as $item)
            <tr class="border-bottom">
                <td class='text-center'>{!! $item['tm_cid'] !!}</td>
                <td class='text-center'>{!! $item['tm_sku'] !!}</td>
                <td>
                    {!! $item['tm_title']!!}
                </td>
                <td class='text-center'>{{ $item['tm_count'] }}</td>
                <td class='text-center'>
                    {{ number_format($item['tm_price'], 2, '.', ' ') }}
                    <i class="fa-light fa-ruble-sign"></i></td>
                <td class='text-center'>
                    {{ number_format($item['total_price'], 2, '.', ' ') }}
                    <i class="fa-light fa-ruble-sign"></i>
                </td>
            </tr>
        @endforeach
        </tbody>
        {{-- Электрика --}}

        {{-- Станция --}}
        <thead>
        <tr>
            <th colspan="6" class="collapsed" data-bs-toggle="collapse" href="#stantion" role="button" aria-expanded="false" aria-controls="stantion">
                Станция
                <i class="fa-regular fa-circle-plus"></i>
            </th>
        </tr>
        </thead>
        <tbody class="collapse" id="stantion">
        @foreach($station  as $item)
            <tr class="">
                <td class='text-center'> {{ $item['tm_cid'] }}</td>
                <td class='text-center'>{{ $item['tm_sku'] }}</td>
                <td>
                    {{ $item['tm_title'] }}
                </td>
                <td class='text-center'>{{ $item['tm_count'] }}</td>
                <td class='text-center'>
                    {{ number_format($item['tm_price'], 2, '.', ' ') }}
                    <i class="fa-light fa-ruble-sign"></i>
                </td>
                <td class='text-center'>
                    {{ number_format($item['total_price'], 2, '.', ' ') }}
                    <i class="fa-light fa-ruble-sign"></i>
                </td>
            </tr>
        @endforeach
        </tbody>
        {{-- Станция --}}

    </table>
</div>

<div class="card-body d-flex justify-content-between border-top mb-1 border-bottom pb-1">

            <p class="card-text mb-0">
                <span class="fw-bold">ИТОГО:</span>
            </p>


        <div class="order-md-2 order-1">

            <div class="d-flex justify-content-between">
                <span class="invoice-total-title me-1">Электрика:</span>
                <span class="invoice-total-amount">
                    <strong>
                        {{ number_format($electric->sum('total_price'), 2, '.', ' ') }}
                        <i class="fa-light fa-ruble-sign"></i>
                    </strong>
                </span>
            </div>

            <div class="d-flex justify-content-between">
                <span class="invoice-total-title">Станция:</span>
                <span class="invoice-total-amount">
                    <strong>
                        {{ number_format($station->sum('total_price'), 2, '.', ' ') }}
                        <i class="fa-light fa-ruble-sign"></i>
                    </strong>
                </span>
            </div>

            <hr class="my-50"/>

            <div class="d-flex justify-content-between">
                <span class="invoice-total-title">Всего:</span>
                <span class="invoice-total-amount">
                    <strong>
                        {{ number_format($electric->sum('total_price') + $station->sum('total_price'), 2, '.', ' ') }}
                        <i class="fa-light fa-ruble-sign"></i>
                    </strong>
                </span>
            </div>

        </div>


</div>

@push('scripts')
@endpush