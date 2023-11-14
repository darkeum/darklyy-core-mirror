@extends('requests::manager.body')

@section('content')

    <section class="invoice-preview-wrapper" id="demo">
        <div class="row invoice-preview">

            {{-- Заявка --}}
            <div class="col-xl-9 col-md-8 col-12">
                <div class="card invoice-preview-card">

                    <div class="card-body invoice-padding pb-0">
                        {{-- Header starts --}}
                        <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">

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

                                @if($unit->type == 6)
                                    <div class="invoice-date-wrapper">
                                        <p class="invoice-date-title">Итоговое наименование :</p>
                                        <p class="invoice-date">{{ $request_data[0]['info']['title'] ?? "Не указано"}}</p>
                                    </div>
                                @endif
                                
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

                                                            @default
                                                            Не указано
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

                                                            @default
                                                            Не указано
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

                                                            @default
                                                            Не указано
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
                                                            <span class="badge bg-danger"> {{ $obj }}</span>
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
                                                @if($request_data['model'] != 0)
                                                    <span class="fw-bold">
                                                        @foreach ($request_data['model'] as $item)
                                                            {{ $item }}
                                                        @endforeach
                                                    </span>
                                                @else
                                                    <span class="fw-bold text-danger">{{$request_data['model']}}</span>
                                                @endif
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

                                                            @default
                                                            <span class="badge bg-secondary">Не указан</span>

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

                    {{-- прочие файлы у type=6 --}}
                    @if ($unit->type == 6)
                        <livewire:requests::drawtable :unit="$unit"/>
                    @endif
                    {{-- прочие файлы у type=6 --}}

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
            {{-- Заявка --}}

            {{-- Заявка Actions --}}
            <div class="col-xl-3 col-md-4 col-12 invoice-actions mt-md-0 mt-2 no-print">

                <div class="sticky-top" style="top: 100px">
                    @if ($unit->type == 6)
                        <livewire:requests::transfertobid
                                :station_data="$station"
                                :electric_data="$electric"
                                :request_id="$unit->id"
                                :unit="$unit"
                        />
                    @endif

                    @if ($unit->type == 6)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Обновить цены</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <a href="{{ route('admin.requests.update.price', $unit) }}"
                                           class="btn btn-success w-100 waves-effect waves-float waves-light">
                                            Обновить
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <livewire:requests::rightmenu :unit="$unit" :count="$attachments->count()"/>
                </div>

            </div>
            {{-- Заявка Actions --}}

        </div>
    </section>

    {{--<a class="btn btn-success" id="basic">print</a>--}}

@endsection

@section('vendor-script')
    <script src="{{ asset('vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>
    <script src="{{ asset('vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('vendors/js/perfectscrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/jstree.min.js')) }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('js/scripts/pages/app-invoice.js') }}"></script>
    <script src="{{ asset(mix('js/scripts/extensions/ext-component-sweet-alerts.js')) }}"></script>
    <script src="{{ asset(mix('js/scripts/extensions/ext-component-tree.js')) }}"></script>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/animate/animate.min.css')) }}">
    {{-- <link rel="stylesheet" href="{{ asset(mix('fonts/font-awesome/css/font-awesome.min.css'))}}"> --}}
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jstree.min.css'))}}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/pages/app-invoice.css') }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-sweet-alerts.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">
@endsection

@push('scripts')
    <script src="{{ asset('js/scripts/custom/printThis.js') }}"></script>
    <script src="{{ asset('js/scripts/custom/jQuery.print.min.js') }}"></script>

    
    <script>
        $('#basic').on("click", function () {

            $("#demo").print({
                globalStyles: true,
                mediaPrint: false,
                stylesheet: null,
                noPrintSelector: ".no-print",
                iframe: true,
                append: null,
                prepend: null,
                manuallyCopyFormValues: true,
                deferred: $.Deferred(),
                timeout: 750,
                title: '123131321231233',
                doctype: '<!doctype html>'
            });

            // $("#demo").printThis({
            //     debug: false,               // show the iframe for debugging
            //     importCSS: true,            // import parent page css
            //     importStyle: true,          // import style tags
            //     // printContainer: true,       // print outer container/$.selector
            //     // loadCSS: "",                // path to additional css file - use an array [] for multiple
            //     // pageTitle: "",              // add title to print page
            //     removeInline: true,        // remove inline styles from print elements
            //     removeInlineSelector: ".no-print",  // custom selectors to filter inline styles. removeInline must be true
            //     // removeInlineSelector: "*",  // custom selectors to filter inline styles. removeInline must be true
            //     // printDelay: 1000,           // variable print delay
            //     // header: '12313123123123',               // prefix to html
            //     // footer: '3131313123132',               // postfix to html
            //     // base: false,                // preserve the BASE tag or accept a string for the URL
            //     // formValues: true,           // preserve input/form values
            //     // canvas: false,              // copy canvas content
            //     // doctypeString: '...',       // enter a different doctype for older markup
            //     // removeScripts: false,       // remove script tags from print content
            //     // copyTagClasses: true,       // copy classes from the html & body tag
            //     // copyTagStyles: true,        // copy styles from html & body tag (for CSS Variables)
            //     // beforePrintEvent: null,     // function for printEvent in iframe
            //     // beforePrint: null,          // function called before iframe is filled
            //     // afterPrint: null            // function called before iframe is removed
            // });

        });
    </script>

@endpush