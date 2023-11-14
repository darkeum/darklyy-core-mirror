{{--@extends('requests::manager.body')--}}
@extends('layouts/fullLayoutMaster')

@section('content')

    <div class="invoice-print p-3">
        <div class="card invoice-preview-card">

            <div class="card-body invoice-padding pb-0">
                {{-- Header starts --}}
                <div class="{{--invoice-spacing--}}invoice-header d-flex justify-content-between flex-md-row flex-column pb-2">

                    {{-- --}}
                    <div>

                        <div class="logo-wrapper mb-0"></div>

                        <p class="card-text mb-25 fs-4 fw-bold">
                            Наименование у заказчика
                        </p>
                        <p class="card-text mb-1">
                            {{ $unit->title_user ?? 'Не указано' }}
                        </p>

                        <p class="card-text mb-25 fs-4 fw-bold">Тип заявки</p>
                        <p class="card-text mb-0">
                                    <span class="badge bg-dark">
                                        {{ ($unit->type ?? false) ? $types[$unit->type] : $types[0] }}
                                    </span>
                        </p>

                    </div>
                    {{-- --}}

                    {{-- --}}
                    <div class="mt-md-0 mt-2">

                        <h4 class="invoice-title">
                            Заявка
                            <span class="invoice-number">#{{ $unit->id }}</span>
                        </h4>

                        <div class="invoice-date-wrapper">
                            <p class="invoice-date-title">Статус :</p>
                            <livewire:requests::status :status="$unit->status"/>
                        </div>

                        <div class="invoice-date-wrapper">
                            <p class="invoice-date-title">Дата :</p>
                            <p class="invoice-date">{{ date_format($unit->created_at, 'd.m.Y') }}</p>
                        </div>

                    </div>
                    {{-- --}}

                </div>
                {{-- Header ends --}}
            </div>

            <hr class="invoice-spacing"/>

            @if ($unit->type == 5)
                {{-- Информация о запросе --}}
                <div class="card-body invoice-padding pt-0">

                    <h5 class="card-title text-center">Информация о запросе</h5>

                    <div class="row invoice-spacing">

                        {{-- Рабочие параметры --}}
                        <div class="col-xl-6 p-0 mt-xl-2 mt-2">
                            <h6 class="mb-1">Рабочие параметры :</h6>
                            <table>
                                <tbody>
                                <tr>
                                    <td class="pe-1">Обозначение рабочей точки:</td>
                                    <td><span class="fw-bold">{{ $request_data['work_mark'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Расход, (Q):</td>
                                    <td><span class="fw-bold">{{ $request_data['q'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Напор, (H):</td>
                                    <td><span class="fw-bold">{{ $request_data['h'] }}</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Рабочие параметры --}}

                        {{-- Исходные данные --}}
                        <div class="col-xl-6 p-0 mt-xl-2 mt-2">
                            <h6 class="mb-1">Исходные данные :</h6>
                            <table>
                                <tbody>
                                <tr>
                                    <td class="pe-1">Температура:</td>
                                    <td><span class="fw-bold">{{ $request_data['t'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Плотность:</td>
                                    <td><span class="fw-bold">{{ $request_data['density'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Вязкость:</td>
                                    <td><span class="fw-bold">{{ $request_data['viscosity'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Коэффициент запаса:</td>
                                    <td><span class="fw-bold">{{ $request_data['coefficient'] }}</span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Исходные данные --}}

                        {{-- Тип переключения --}}
                        <div class="col-xl-6 p-0 mt-xl-2 mt-2">
                            <h6 class="mb-1">Тип переключения :</h6>
                            <table>
                                <tbody>
                                <tr>
                                    <td class="pe-1">Число насосов:</td>
                                    <td><span class="fw-bold">{{ $request_data['pump_count'] }}</span></td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Pезервный насос:</td>
                                    <td>
                                                    <span class="fw-bold">
                                                        @if ($request_data['pump_reserve'] == 0)
                                                            Нет
                                                        @else
                                                            Да
                                                        @endif
                                                    </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Тип переключения --}}

                        {{-- Дополнительные данные --}}
                        <div class="col-xl-6 p-0 mt-xl-2 mt-2">
                            <h6 class="mb-1">Дополнительные данные :</h6>
                            <table>
                                <tbody>
                                <tr>
                                    <td class="pe-1">Назначение оборудования :</td>
                                    <td>
                                                    <span class="fw-bold">
                                                        @switch($request_data['type'])
                                                            @case(1)
                                                            Отопление
                                                            @break

                                                            @case(2)
                                                            Циркуляция ГВС
                                                            @break

                                                            @case(3)
                                                            Подпитка отопления
                                                            @break

                                                            @case(4)
                                                            Повысительный ХВС / ГВС
                                                            @break

                                                            @case(5)
                                                            Пожарные насосы
                                                            @break
                                                        @endswitch
                                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Частотный преобразователь :</td>
                                    <td>
                                                    <span class="fw-bold">
                                                        @switch($request_data['frequency'])
                                                            @case(1)
                                                            С ПЧ
                                                            @break

                                                            @case(2)
                                                            Без ПЧ
                                                            @break
                                                        @endswitch
                                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Подключение к сети :</td>
                                    <td>
                                                    <span class="fw-bold">
                                                        @switch($request_data['voltage'])
                                                            @case(1)
                                                            1~ 230 V / 50 Hz
                                                            @break

                                                            @case(2)
                                                            3~ 400 V / 50 Hz
                                                            @break
                                                        @endswitch
                                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Критерий поиска :</td>
                                    <td>
                                                    <span class="fw-bold">
                                                        @switch($request_data['pumpSelector'])
                                                            @case(0)
                                                            По назначению
                                                            @break

                                                            @case(1)
                                                            По модели
                                                            @break

                                                            @case(2)
                                                            Прямой выбор
                                                            @break
                                                        @endswitch
                                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Объект :</td>
                                    <td>
                                                    <span class="fw-bold">
                                                        @if ($obj != 'Отсутствует')
                                                            <div class="col-6">
                                                                @foreach ($obj as $item)
                                                                    <div class="col-12"> {{ $item->name }} </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="col-6"> {{ $obj }}</div>
                                                        @endif
                                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Организация :</td>
                                    <td>
                                        <span class="fw-bold">{{ $org->name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Исключить из поиска не подходящие насосы :</td>
                                    <td>
                                        <span class="fw-bold">
                                            @if ($request_data['pump_switch'] == 0)
                                                Нет
                                            @else
                                                Да
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- Дополнительные данные --}}

                        {{-- О насосе --}}
                        <div class="col-12 p-0 mt-xl-2 mt-2">
                            <h6 class="mb-1">О насосе :</h6>
                            <table>
                                <tbody>
                                <tr>
                                    <td class="pe-1">Модель :</td>
                                    <td>
                                                    <span class="fw-bold">
                                                        @foreach ($request_data['model'] as $item)
                                                            {{ $item }}
                                                        @endforeach
                                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Название насоса:</td>
                                    <td>
                                                    <span class="fw-bold">
                                                        @foreach ($manafacture as $item)
                                                            {{ $item->name }}
                                                        @endforeach
                                                    </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Номер в каталоге:</td>
                                    <td>
                                        <span class="fw-bold">{{ $catalog->id }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Название в каталоге:</td>
                                    <td>
                                        <span class="fw-bold">{{ $catalog->title }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Артикул:</td>
                                    <td>
                                        <span class="fw-bold">{{ $catalog->sku }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="pe-1">Тип насоса:</td>
                                    <td>
                                                    <span class="fw-bold">
                                                        @switch($request_data['typeBinary'])
                                                            @case(1)
                                                            Одинарный
                                                            @break

                                                            @case(2)
                                                            Сдвоенный
                                                            @break
                                                        @endswitch
                                                    </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        {{-- О насосе --}}

                    </div>

                </div>
                {{-- Информация о запросе --}}
            @endif

            {{-- Таблицы со стоимостью станции --}}
            @if ($unit->type == 6)
                @include('requests::manager.tables.table')
                {{-- @include('requests::manager.tables.table_collapsed') --}}
            @endif

            {{-- Файлы --}}
            <livewire:requests::filestable :unit="$unit"/>
            {{-- Файлы --}}

            {{-- Примечание --}}
            @if ($unit->request ?? false)
                <hr class="invoice-spacing"/>
                <div class="card-body invoice-padding pt-0">
                    <div class="row">
                        <div class="col-12">
                            <span class="fw-bold">Текст заявки:</span>
                            {!! $unit->request !!}
                        </div>
                    </div>
                </div>
            @endif
            {{-- Примечание --}}

        </div>
    </div>

@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('vendors/js/perfectscrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/jstree.min.js')) }}"></script>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    {{-- <link rel="stylesheet" href="{{ asset(mix('fonts/font-awesome/css/font-awesome.min.css'))}}"> --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jstree.min.css'))}}">
@endsection

@section('page-script')
    {{--<script src="{{ asset('js/scripts/pages/app-invoice.js') }}"></script>--}}
    <script src="{{asset('js/scripts/pages/app-invoice-print.js')}}"></script>
    <script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/extensions/ext-component-tree.js')) }}"></script>
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    {{--<link rel="stylesheet" href="{{ asset('css/base/pages/app-invoice.css') }}">--}}
    <link rel="stylesheet" href="{{asset(mix('css/base/pages/app-invoice-print.css'))}}">

    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <style>
        @media print {

            .printSelected > * {
                display: none;
            }

            .printSelected > .printSelection {
                display: block;
                padding: 25px;
            }
        }

        .printSelection {
            display: none;
        }
    </style>
@endsection

{{--@push('scripts')--}}
{{--@endpush--}}