{{--{{ dd($test3) }}--}}
{{--@foreach($test as $t)--}}
{{--{{ $t->s_name }}<br><br>--}}
{{--@endforeach--}}

{{--<div class="btn btn-sm"></div>--}}

<div class="card-header">
    <h4 class="card-title">Заказы по объекту</h4>

    <div class="heading-elements">

        <ul class="list-inline mb-0">
            <li>
                    <span class="badge bg-success cursor-pointer"
                          id="table-accordion-switch"
                          data-bs-toggle="tooltip"
                          data-bs-placement="left"
                          title="Развернуть все строки"
                    >
                        <i class="feather-eye"></i>
                        {{--<span>Развернуть</span>--}}
                    </span>
            </li>
            <li>
                    <span class="badge bg-danger cursor-pointer"
                          id="table-accordion-switch-hide"
                          data-bs-toggle="tooltip"
                          data-bs-placement="right"
                          title="Свернуть все строки"
                    >
                        <i class="feather-eye-off"></i>
                        {{--<span>Свернуть</span>--}}
                    </span>
            </li>
        </ul>

    </div>

</div>

<div class="card-body">

    <div class="table-responsive">
        <table class="table table-condensed table-striped table-hover table-accordion">

            <thead>
            <tr>
                <th class="col-1 text-center">
                    <i class="feather-hash"></i>
                </th>
                <th>Заказ</th>
                <th class="col-1 text-center">Тип</th>
                <th class="col-1 text-center">Статус</th>
                <th class="col-1 text-center">
                    <i class="feather-settings"></i>
                </th>
            </tr>
            </thead>

            <tbody>

            @forelse($object->sales as $sale)

                {{--                    {{ dd($sale) }}--}}

                {{-- Заказ --}}
                <tr data-bs-toggle="collapse" data-bs-target="#collapse-target-{{ $sale->id }}"
                    class="accordion-toggle" title="Развернуть строку"
                >
                    {{-- ID --}}
                    <td class="text-center">{{ $sale->id }}</td>
                    {{-- ID --}}

                    {{-- Заказ --}}
                    <td>
                        <div>
                            <a
                                    {{--href="{{ route('admin.sale', $sale->id) }}"--}}
                                    href="{{ url('administration/module/sales2/' . $sale->id) }}"
                                    {{--href="#"--}}
                                    target="_blank" class="fw-bolder"
                            >
                                {{ $sale->numb }}
                            </a>
                            <div class="font-small-4 text-primary">
                                {{ $sale->title ? $sale->title : 'Не указан' }}
                            </div>
                        </div>
                    </td>
                    {{-- Заказ --}}

                    {{-- Тип --}}
                    <td class="text-center">
                            <span class="badge rounded-pill bg-secondary">
                                {{ $sales['types'][$sale->sales_type] }}
                            </span>
                    </td>
                    {{-- Тип --}}

                    {{-- Статус --}}
                    <td class="text-center">
                            <span class="badge rounded-pill bg-success">
                                {{ $sales['status'][$sale->status] }}
                            </span>
                    </td>
                    {{-- Статус --}}

                    {{-- Дейсвтия --}}
                    <td class="text-center">
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                    data-bs-toggle="dropdown">
                                <i data-feather="more-vertical"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">
                                    <i data-feather="edit" class="me-50"></i>
                                    <span>Редактировать</span>
                                </a>
                                {{--<a class="dropdown-item" href="#">--}}
                                {{--    <i data-feather="trash" class="me-50"></i>--}}
                                {{--    <span>Удалить</span>--}}
                                {{--</a>--}}
                            </div>
                        </div>
                    </td>
                    {{-- Дейсвтия --}}

                </tr>
                {{-- Заказ --}}

                {{-- Детали заказа --}}
                <tr>
                    <td colspan="12" class="p-0 border-0">
                        <div class="accordion-body collapse p-0" id="collapse-target-{{ $sale->id }}">

                            <table class="table table-condensed table-striped">

                                <thead>
                                <tr class="">
                                    <th class="col-1 text-center px-1">
                                        <i class="feather-hash"></i>
                                    </th>
                                    <th class="">Номенклатура</th>
                                    <th class="col-1 text-center text-truncate">Кол-во</th>
                                    <th class="col-1 text-center">Цена</th>
                                    <th class="col-1 text-center">Сумма</th>
                                    <th class="col-1 text-center">Статус</th>
                                    <th class="col-1 text-center">
                                        <i class="feather-settings"></i>
                                    </th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr class="">

                                    <td class="text-center">1</td>

                                    <td class="">
                                        <div class="d-flex justify-content-left align-items-center">
                                            <div class="d-flex flex-column">
                                                <a href="#"
                                                   class="{{--text-truncate--}} text-body position-relative">
                                                        <span class="fw-bolder">
                                                            Тепловычислитель ТВ7-04.1М; RS232, литиевая батарея АА с блоком питания
                                                        </span>
                                                </a>
                                                <small class="text-muted" title="Скопировать артикул"
                                                       data-value="6131157">
                                                    Артикул:
                                                    <span>6131157</span>
                                                    <i class="feather-copy"></i>
                                                </small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <span class="fw-bold">1</span>
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-left align-items-center">
                                            <div class="d-flex flex-column position-relative">

                                                    <span class="badge badge-sm bg-danger position-absolute top-5 start-70 translate-middle"
                                                          title="Скидка: 10.00%">
                                                        10.00%
                                                    </span>

                                                <a class="text-center mt-1">
                                                        <span class="fw-bolder">
                                                            28200.00 ₽
                                                        </span>
                                                </a>
                                                <small class="text-muted text-center">Вид цены: 0</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                            <span class="fw-bolder text-center">
                                                25380.00 ₽
                                            </span>
                                    </td>

                                    {{-- Статус --}}
                                    <td class="text-center">
                            <span class="badge rounded-pill bg-success">
                                {{ $sales['status'][$sale->status] }}
                            </span>
                                    </td>
                                    {{-- Статус --}}

                                    {{-- Дейсвтия --}}
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0"
                                                    data-bs-toggle="dropdown">
                                                <i data-feather="more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="#">
                                                    <i data-feather="edit" class="me-50"></i>
                                                    <span>Редактировать</span>
                                                </a>
                                                {{--<a class="dropdown-item" href="#">--}}
                                                {{--    <i data-feather="trash" class="me-50"></i>--}}
                                                {{--    <span>Удалить</span>--}}
                                                {{--</a>--}}
                                            </div>
                                        </div>
                                    </td>
                                    {{-- Дейсвтия --}}

                                </tr>
                                </tbody>

                            </table>

                        </div>
                    </td>
                </tr>
                {{-- Детали заказа --}}

            @empty
                <td class="text-center bg-light-secondary" colspan="100%">
                    <div class="fw-bolder">Пусто</div>
                </td>
            @endforelse

            </tbody>
        </table>
    </div>

</div>

{{--@push('styles')--}}
{{--@endpush--}}

@push('scripts')
    <script>
        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="collapse"]'))
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        $(document).ready(function () {
            let unit_switch = $('#table-accordion-switch');
            let unit_switch_hide = $('#table-accordion-switch-hide');

            unit_switch.on('click', function (e) {

                // if (!unit_switch.hasClass('badge-glow')) {
                //     unit_switch.toggleClass('badge-glow');
                // }

                $(".accordion-body.collapse").collapse("show");
            });

            unit_switch_hide.on('click', function (e) {
                $('.accordion-body.collapse').collapse('hide')
            });

            // $(".accordion-body.collapse")
            //     .on('show.bs.collapse', function () {
            //         unit_switch
            //             .removeClass('checked')
            //             .toggleClass('btn-outline-primary btn-outline-success')
            //             .attr({
            //                 title: 'Показать',
            //             }).children('i').toggleClass('feather-eye-off feather-eye');
            //         text.html('Показать');
            //     })
            //     .on('hide.bs.collapse', function () {
            //         unit_switch
            //             .addClass('checked')
            //             .toggleClass('btn-outline-success btn-outline-primary')
            //             .attr({
            //                 title: 'Скрыть',
            //             }).children('i').toggleClass('feather-eye feather-eye-off');
            //         text.html('Скрыть')
            //     });


        });
    </script>
@endpush