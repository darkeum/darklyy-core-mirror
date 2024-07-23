{{-- Тело главного Grid --}}

@php
    function params ($col, $key, $empty = false) {
        if (isset($col[$key])) {
            return "{$key}: '{$col[$key]}',";
        }
    }
@endphp

<section>
    <div class="row">
        <div class="col-12">

            <div class="card aGrid-container" data-module="{{ Request::segment(3) }}">
                <div class="card-header py-50 pe-50">

                    {{-- Заголовок --}}
                    <div class="col-12 col-xl-6">
                        <h4 class="card-title aGrid-container-title">{{ $title }}</h4>
                    </div>
                    {{-- Заголовок --}}

                    {{-- Панель поиска и параметров --}}
                    <div class="col-12 col-xl-6">
                        <div class="btn-toolbar justify-content-end aGrid-toolbar">

                            {{-- Поле поиска --}}
                            @if (!empty($nav['search']) and $nav['search'])
                                @include('grid::layouts.toolbar.search')
                            @endif
                            {{-- Поле поиска --}}

                            {{-- Кнопки управления --}}
                            @include('grid::layouts.toolbar.control')
                            {{-- Кнопки управления --}}

                            {{-- Место под свои кнопки --}}
                            @if(isset($data['options']['button']))
                                {!! $data['options']['button'] !!}
                            @endif
                            {{-- Место под свои кнопки --}}

                            {{-- Модификаторы таблицы --}}
                            @if (!empty($nav['mods']) and $nav['mods'])
                                @include('grid::layouts.toolbar.mods.button')
                            @endif
                            {{-- Модификаторы таблицы --}}

                        </div>
                    </div>
                    {{-- Панель поиска и параметров --}}

                </div>

                <table id="{{ $data['id'] }}" data-module-name="{{ Request::segment(3) }}"
                       class="table table-striped table-bordered compact table-sm table-sm-px {{--table-responsive--}} datatables-basic {{--dt-responsive--}} dt-column-search {{--dt-behavior--}} {{--display--}} {{--nowrap--}} aGrid-table"
                >
                    {{-- Table Header --}}
                    <thead class="{{--d-none--}}">

                    @php
                        function ignored ($type):bool {
                            $arr = config('grid.ignored');
                            return in_array($type, $arr);
                        };
                    @endphp

                    {{-- Заголовки столбцов --}}
                    <tr class="aGrid-titles">
                        @foreach ($cols as $k => $col)

                            {{--<th rowspan="{{ (array_key_last($cols) == $k and $col['type'] == 'actions' and $col['type'] == 'checkbox') ? 2 : 1 }}"--}}
                            <th rowspan="{{ ignored($col['type']) ? 2 : 1 }}"
                                data-visible="{{ $k }}"
                                data-db="{{ $col['db'] }}"
                                data-type="{{ $col['type'] }}"
                            >
                                {!! $col['title'] ?? $col['db'] !!}
                            </th>
                        @endforeach
                    </tr>
                    {{-- Заголовки столбцов --}}

                    {{-- Поля поиска столбцов --}}
                    <tr class="aGrid-filters">
                        @foreach ($cols as $k => $col)
                            {{--@if(array_key_last($cols) !== $k or $col['type'] !== 'actions' or $col['type'] !== 'checkbox')--}}
                            @if(!ignored($col['type']))
                                <th
                                    data-db="{{ $col['db'] }}"
                                    data-type="{{ $col['type'] }}"
                                    data-visible="{{ $k }}"
                                ></th>
                            @endif
                        @endforeach
                    </tr>
                    {{-- Поля поиска столбцов --}}

                    </thead>
                    {{-- Table Header --}}

                    {{--<tbody class="table-sm table-sm-px"></tbody>--}}
                    {{-- table-sm-px - очень важный класс для выравнивая DT --}}

                </table>
            </div>
        </div>
    </div>
</section>
{{-- Тело главного Grid --}}

{{-- WIP Идея по переносу конструктора поиска в модальное окно --}}
{{-- Modal --}}
{{--<div--}}
{{--        class="modal fade"--}}
{{--        id="searchB"--}}
{{--        tabindex="-1"--}}
{{--        aria-labelledby="searchB_title"--}}
{{--        aria-hidden="true"--}}
{{--        data-bs-backdrop="false"--}}
{{-->--}}
{{--    <div class="modal-dialog modal-lg">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title" id="searchB_title">--}}
{{--                    Конструктор поиска--}}
{{--                </h5>--}}
{{--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--            </div>--}}
{{--            <div class="modal-body" id="searchB_body">--}}

{{--            </div>--}}
{{--            <div class="modal-footer d-none">--}}
{{--                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Accept</button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{-- Modal --}}

@push('scripts')
    <script type="text/javascript" data-info="Darklyy Grid Table - DataTable JS">

        // const g_gen = new UiCreator(); // UI либа
        // console.log(store.getAll())

        // Параметры до загрузки Grid
        // $.extend(true, $.fn.dataTable.defaults, {});

        // Главный скрипт DataTable
        $(document).ready(function () {

            /* *** ОТРИСОВКА ТАБЛИЦЫ *** */
            const table = $('#{{ $data['id'] }}').DataTable(
                {
                    //
                    ajax: {
                        url: '{{ $url }}',
                        type: 'POST'
                    },

                    dom: '{!! $data['dom'] !!}', // Шаблон DOM дерева DataTables

                    deferRender: true, // Отложенный рендеринг для дополнительной скорости инициализации

                    autoWidth: true, // Интеллектуальная обработка ширины столбца DataTables управления функциями
                    fixedHeader: false, // Фиксированный заголовок
                    fixedColumns: false, // Фиксированные столбцы

                    orderCellsTop: true, // Сортировка к верху

                    // "Резиновость"
                    responsive: true, // Резиновый дизайн таблицы (Сворачивание столбцов)
                    // responsive: {
                    //     details: false
                    // },
                    // responsive: {
                    //     breakpoints: [
                    //         {name: 'bigdesktop', width: Infinity},
                    //         {name: 'meddesktop', width: 1480},
                    //         {name: 'smalldesktop', width: 1280},
                    //         {name: 'medium', width: 1188},
                    //         {name: 'tabletl', width: 1024},
                    //         {name: 'btwtabllandp', width: 848},
                    //         {name: 'tabletp', width: 768},
                    //         {name: 'mobilel', width: 480},
                    //         {name: 'mobilep', width: 320}
                    //     ]
                    // },

                    // Конструктор поиска
                    searchBuilder: {},

                    // Кнопки (Управления)
                    buttons: {

                        // Переназначение контейнера для кнопок для datatables
                        // vendors\js\tables\datatable/full\datatables.js 79273
                        dom: {
                            container: {
                                // className: 'dt-buttons btn-group flex-wrap ms-1'
                            },
                            // buttonContainer: {
                            //     tag: ''
                            // },
                            // buttonLiner: {
                            //     tag: ''
                            // },
                            button: {
                                className: 'btn',
                                // className: 'btn btn-sm',
                                // tag: 'a',
                            },
                            collection: {
                                tag: 'div',
                                className: 'dropdown-menu d-inline aGrid-container-collection',
                                closeButton: false,
                                button: {
                                    tag: 'a',
                                    className: 'dt-button dropdown-item',
                                    active: 'active',
                                    disabled: 'disabled'
                                }
                            }
                        },

                        //
                        buttonCreated: function (config, button) {
                            return config.buttons ? $('<div class="btn-group position-relative" role="group"/>').append(button) : button;
                        },

                        //
                        buttons: [
                                @if (!empty($nav['search']['builder']) and $nav['search']['builder'])
                            {
                                extend: 'searchBuilder',
                                // text: feather.icons['plus'].toSvg({class: 'me-50 font-small-4'}) + 'Расширенный поиск',
                                text: '<i class="feather-plus font-small-4 me-50"></i>' + 'Расширенный поиск',
                                className: 'btn-outline-secondary',
                                align: 'button-right',
                                background: false,
                            },
                                @endif

                                @if ($nav['export'])
                            {
                                extend: 'collection',
                                // text: feather.icons['share'].toSvg({class: 'font-small-4 me-50'}) + 'Экспорт',
                                text: '<i class="feather-share font-small-4 me-50"></i>' + 'Экспорт',
                                className: 'btn-outline-secondary dropdown-toggle',
                                align: 'button-right',
                                attr: {
                                    // 'data-bs-toggle': 'dropdown',
                                },
                                // background: false,
                                buttons: [
                                        @if ($nav['export']['print'])
                                    {
                                        extend: 'print',
                                        text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + 'Распечатать',
                                        className: 'dropdown-item',
                                        action: newexportaction
                                    },
                                        @endif
                                        @if ($nav['export']['csv'])
                                    {
                                        extend: 'csv',
                                        text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Сохранить в CSV',
                                        className: 'dropdown-item',
                                        action: newexportaction
                                    },
                                        @endif
                                        @if ($nav['export']['excel'])
                                    {
                                        extend: 'excel',
                                        text: feather.icons['file'].toSvg({class: 'font-small-4 me-50'}) + 'Сохранить в Excel',
                                        className: 'dropdown-item',
                                        action: newexportaction,
                                        exportOptions: {
                                            columns: [
                                                @foreach ($cols as $key => $col)
                                                    @if($col['visible'] == true || $col['visible'] == 'true')
                                                    @if($col['excel_hidden'] != true || $col['excel_hidden'] != 'true')
                                                    {{$key}},
                                                @endif
                                                    @else
                                                    @if($col['excel_only'] == true || $col['excel_only'] == 'true')
                                                    {{$key}},
                                                @endif
                                                @endif
                                                @endforeach
                                            ]
                                        }
                                    },
                                        @endif
                                        @if ($nav['export']['pdf'])
                                    {
                                        extend: 'pdf',
                                        text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Сохранить в PDF',
                                        className: 'dropdown-item',
                                        action: newexportaction
                                    },
                                        @endif
                                        @if ($nav['export']['copy'])
                                    {
                                        extend: 'copy',
                                        text: feather.icons['copy'].toSvg({class: 'font-small-4 me-50'}) + 'Скопировать',
                                        className: 'dropdown-item',
                                        action: newexportaction
                                    }
                                    @endif
                                ],
                                init: function (api, node, config) {
                                    // let n = $(node);
                                    // n.removeClass('btn-secondary');
                                    // n.parent().removeClass('btn-group');
                                    // setTimeout(function () {
                                    //     n.closest('.dt-buttons').removeClass('btn-group').addClass('d-inline-flex');
                                    // }, 50);
                                }
                            }
                            @endif
                        ],

                    },

                    //
                    scrollY: '61vh', // Высота таблицы по умолчанию
                    scrollX: false,  //
                    // scrollXInner: false,
                    // scrollCollapse: true, // Высота таблицы будет максимальной если false даже если пуста!

                    // Обработчик прокрутки
                    scroller: {
                        loadingIndicator: true,
                        rowHeight: 49.2, // 50
                        // boundaryScale: 0.9,
                        // displayBuffer: 5,
                    },

                    // paging: true, // Пагинация для прокрутки должна быть true (По умолчанию true) !!!

                    processing: true, //
                    serverSide: true, //
                    // colReorder: true, // Перетаскивание столбцов (По умолчанию false)

                    // Сортировка ASC и DESC Нужного столбца по умолчанию
                    @if (isset($data['short']))
                    order: [
                        ['{!! $data['short']['index'] !!}', '{!! $data['short']['order'] !!}'],
                    ],
                    @endif

                    // Параметры колонок
                    columns: [

                            @foreach ($cols as $key => $col){

                            @if($col['type'] == 'checkbox') @php($checkbox_target[] = $key) @endif
                                {{--{{ $col['type'] == 'checkbox' ? $checkbox_target[] = $key : '' }}--}}

                            title: "{{ $col['title'] ?? '' }}",
                            label: "{{ $col['label'] ?? '' }}",
                            name: "{{ $col['db'] ?? '' }}",

                            width: "{{ $col['width'] }}",
                            visible: {{ $col['visible'] ? 'true' : 'false' }},
                            className: "{{ $col['className'] ?? '' }} col-{{ $col['type'] }}",

                            sortable: {{ isset($col['sortable']) ? ($col['sortable'] ? 'true' : 'false') : 'true' }},
                            searchable: {{ isset($col['search']) ? ($col['search'] === false ? 'false' : 'true') : 'false' }},

                            {!! isset($col['other']) ? 'other: ' . $col['other'] . ',' : false !!}
                            data: "{{ $col['name'] }}",

                            @if(($col['render']))render: function (data, type, row, meta) {
                                return table.aGrid.fn.render(data, type, row, meta)
                            },
                            @endif

                                {!! isset($col['options']) ? 'options: ' . $col['options'] . ',' : false !!}
                                {!! isset($col['priority']) ? 'priority: ' . $col['priority'] . ',' : false !!}
                                {!! isset($col['priority']) ? 'responsivePriority: ' . $col['priority'] . ',' : false !!}

                                {{-- Все необходимые данные для JS можно переделать тут в configs  --}}
                            configs: {
                                {{--displayFormat: "{{ $col['format_date'] ?? false }}",--}}
                                    {!! isset($col['format_date']) ? 'displayFormat: "' . $col['format_date'] . '",' : false !!}

                                icon: "{{ $col['icon'] ?? null }}",
                                type: "{{ $col['type'] ?? null }}",
                                prefix: "{{ $col['prefix'] ?? null }}",
                                render: "{{ $col['render'] ?? null }}",

                                {!! isset($col['tooltip']) ? 'tooltip: "' . $col['tooltip'] . '",' : false !!}
                                    {!! ($col['render']) ? 'popover: "' . $col['popover'] . '",' : false !!}
                            }
                            {{-- Все необходимые данные для JS можно переделать тут в configs  --}}

                            // data: {
                            //     _: 'phone',
                            //     filter: 'phone_filter',
                            //     display: 'phone_display'
                            // },
                            // render: {
                            //     _: 'plain',
                            //     filter: 'filter',
                            //     display: 'display'
                            // },
                            // defaultContent: '',
                            //
                            // cellType: 'th',
                            // contentPadding: 'mmm',
                            // createdCell: 'Только в columnDefs',
                            //
                            // orderable: false,
                            // orderData: 0,
                            // orderDataType: 'dom-text'

                        },@endforeach
                    ],

                    // Расширенные параметры колонок - ПЕРЕДЕЛАТЬ!!!
                    columnDefs: [
                            @foreach ($cols as $key => $col)
                            @if($col['type'] == 'checkbox'){
                            targets: [{{ $key }}],
                            // className: '',
                            // render: function (data, type, full, meta) {
                            //     return ('<div class="form-check"><input class="form-check-input dt-checkboxes" type="checkbox" value="" id="checkbox' + data + '" /><label class="form-check-label" for="checkbox' + data + '"></label></div>'
                            //     );
                            // },
                            render: function (data, type, full, meta) {
                                return ('<input class="form-check-input dt-checkboxes" type="checkbox" value="" id="checkbox' + data + '" />');
                            },
                            checkboxes: {
                                // selectRow: true, // Отмечать чекбокс при клике на строке таблицы
                                // selectAllRender:
                                //     '<div class="form-check"><input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>'
                                selectAllRender: '<input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" />'
                            },

                        },
                        @endif
                        @endforeach
                    ],

                    //
                    select: {
                        style: 'os', // Варианты os, single, multi # https://datatables.net/extensions/select/
                        // blurable: true,
                        // selector: 'td:first-child > input.checkbox'
                    },

                    // Перевод DataTables
                    language: {!! $language !!},

                    // Кастомные параметры
                    // Использование table.settings()[0].oInit.settings.param или settings.oInit.settings.param из функций событий
                    {{--settings: {--}}
                    {{--    // param: value,--}}
                    {{--    grid: {--}}
                    {{--        mouse: {!! \Boot\System\Grid\Utils\Utils::jsonEncode($mouse) !!},--}}
                    {{--    },--}}
                    {{--},--}}


                    // stateSave - Сохранение состояния — восстановление состояния таблицы при перезагрузке страницы.
                    // stateSave: true,
                    // #0 - Обратный вызов, который определяет, куда и как должно быть загружено сохраненное состояние.
                    stateLoadCallback: function (settings, callback) {
                        // let api = this.api();
                        // api.aGrid.callbacks.stateLoadCallback(settings, callback);
                        // callback(); // Что бы загрузка продолжилась выполнить
                    },

                    // #0 - Обратный вызов, который определяет, как и где хранится состояние таблицы.
                    stateSaveCallback: function (settings, data) {
                        // Send an Ajax request to the server with the state object
                        // $.ajax({
                        //     url: '/state_save',
                        //     data: data,
                        //     dataType: 'json',
                        //     type: 'POST',
                        //     success: function () {}
                        // });
                    },

                    // #0 - Состояние загружено - обратный вызов для манипулирования данными.
                    stateLoadParams: function (settings, data) {
                        let api = this.api();
                        api.aGrid.callbacks.stateLoadParams(settings, data);
                    },

                    // #0 - Обратный вызов с загруженным состоянием.
                    stateLoaded: function (settings, data) {
                        // alert('Saved filter was: ' + data.search.search);
                    },

                    /* *** СОБЫТИЯ - Callback's *** */ // https://datatables.net/reference/option/
                    // #1 - Обратный вызов перед отрисовкой.
                    preDrawCallback: function (settings) {
                        let api = this.api();
                        api.aGrid.callbacks.preDrawCallback(settings);
                    },

                    // #2 - Загрузка параметров
                    serverParams: function (data) {
                        let api = this.api();
                        data = api.aGrid.callbacks.serverParams(data);
                    },

                    // #3 - Обратный вызов для каждого случая, когда для тела таблицы создается элемент TR.
                    createdRow: function (row, data, dataIndex) {
                        let api = this.api();
                        api.aGrid.callbacks.createdRow(row, data, dataIndex);
                    },

                    // #4 - Обратный вызов отрисовки строки.
                    rowCallback: function (row, data) {
                        // if (data.grade == 'A') {
                        //     $('td:eq(4)', row).html('<b>A</b>');
                        // }
                    },

                    // #5 - Создание Header'а таблицы - Функция обратного вызова отображения заголовка.
                    headerCallback: function (thead, data, start, end, display) {
                        let api = this.api();
                        api.aGrid.callbacks.headerCallback(thead, data, start, end, display);
                    },

                    // #6 - В нижнем колонтитуле отображается функция обратного вызова.
                    footerCallback: function (tr, data, start, end, display) {
                        // $(tr).find('th').eq(0).html('Starting index is ' + start);
                    },

                    // #7 - Функция обратного вызова для форматирования номера.
                    formatNumber: function (toFormat) {
                        // return toFormat.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "'");
                    },

                    // #8 - Обратный вызов отображает сводную информацию таблицы.
                    infoCallback: function (settings, start, end, max, total, pre) {
                        // return start + ' to ' + end;
                    },

                    // #9 - Сохранение состояния - обратный вызов для манипулирования данными.
                    stateSaveParams: function (settings, data) {
                        // data.search.search = '';
                    },

                    // #10 - Функция, которая вызывается каждый раз, когда Data Tables выполняет отрисовку.
                    drawCallback: function (settings) {
                        let api = this.api();
                        // $(this.api().table().header()).removeClass('d-none');
                        api.aGrid.callbacks.drawCallback(settings);
                    },

                    // #11 - Инициализация всего DT
                    initComplete: function (settings, json) {
                        let api = this.api();
                        api.aGrid.callbacks.initComplete(settings, json);
                    }

                }
            ).aGrid();

            // table.aGrid();
            // table.aGrid.dt.events();

            // console.log(table)
            /* *** END - ОТРИСОВКА ТАБЛИЦЫ *** */

            // Обработка Кликов вне блока aGrid-container !!! ПЕРЕРОС
            $(document).on('click', function (e) {
                if (!$(e.target).closest(".aGrid-container, ul.context-menu-list, .toast-player-container").length) {
                    table.$('tr.selected').toggleClass('selected');
                }
                // return;
            });

            /* *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** *** */

            /* ### TEST ZONE ### */
            // table.settings()[0].oInit.settings.param или settings.oInit.settings.param
            // const g_settings = table.settings()[0].oInit.settings;
            // console.log(g_settings);

            // $(".text-truncate").on("click", function() {
            //     // var index = $(this).index() + 1;
            //     // $('table tr td:nth-child(' + index  + ')').toggleClass("text-truncate");
            //     alert('123')
            // });

            // Функция поиска Ellipsis
            // const isEllipsisActive = (e) => {
            //     return (e.offsetWidth < e.scrollWidth);
            // }
            // const div = document.querySelector('div')
            // console.log(isEllipsisActive(div))

            // ***
            // console.log(table.searchBuilder.container());
            // console.log(table.settings()[0].aoColumns);

            // searchBuilder обработка нового
            // onShown event
            // var shownModalTrigger = document.getElementById('searchB');
            // var shownModal = new bootstrap.Modal(shownModalTrigger, {
            //     title: 'Modal Shown Event',
            //     trigger: 'click',
            //     placement: 'right'
            // });
            //
            // shownModalTrigger.addEventListener('shown.bs.modal', function () {
            //     let sb = table.searchBuilder.container();
            //     table.searchBuilder.container().detach();
            //     sb.removeClass('dropdown-menu');
            //     sb.addClass('ererererer');
            //     $('#searchB_body').append(sb);
            // });
            // END - TEST ZONE
            /* ### END - TEST ZONE ### */

        });

        //
        function newexportaction(e, dt, button, config) {
            var self = this;
            var oldStart = dt.settings()[0]._iDisplayStart;
            dt.one('preXhr', function (e, s, data) {
                // Только один раз загрузите все данные с сервера...
                data.start = 0;
                data.length = 2147483647;
                dt.one('preDraw', function (e, settings) {
                    // Вызовите исходную функцию действия
                    if (button[0].className.indexOf('buttons-copy') >= 0) {
                        $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                    } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                        $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                    } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                        $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                            $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                    } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                        $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                            $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                    } else if (button[0].className.indexOf('buttons-print') >= 0) {
                        $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                    }
                    dt.one('preXhr', function (e, s, data) {
                        // DataTables считает, что первый отображаемый элемент имеет индекс 0, но мы этого не рисуем.
                        // Установите свойство таким, каким оно было до экспорта.
                        settings._iDisplayStart = oldStart;
                        data.start = oldStart;
                    });
                    // Перезагрузите сетку с исходной страницей. В противном случае функции API, такие как table.cell(this), не будут работать должным образом.
                    setTimeout(dt.ajax.reload, 0);
                    // Запретить отображение полных данных в DOM
                    return false;
                });
            });

            // Запросите сервер с новыми настройками одноразового экспорта
            dt.ajax.reload();
        }

    </script>
@endpush

@section('page-style')
@endsection

@section('vendor-style')

    {{-- vendor css files --}}
    {{-- DATATABLE --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/last/datatables.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('aGrid/aGrid.css') }}">
    {{-- DATATABLE --}}

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/pickers/flatpickr/flatpickr.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/forms/select/select2.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/jquery.contextMenu.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">

    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-context-menu.css')) }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/base/plugins/forms/pickers/form-flat-pickr.css') }}">

@endsection

@section('vendor-script')

    {{-- vendor files --}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>--}}
    <script src="{{ asset('vendors/js/moment/moment.min.js') }}"></script>

    {{-- DATATABLE --}}
    {{--<script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>--}}
    {{--<script src="{{ asset('vendors/js/tables/datatable/default/datatables.js') }}"></script>--}}
    <script src="{{ asset('vendors/js/tables/datatable/last/datatables.js') }}"></script>

    {{--<script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.bootstrap5.min.js')) }}"></script>--}}
    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
    {{--<script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap5.min.js')) }}"></script>--}}

    <script src="{{ asset('vendors/js/tables/datatable/datatables.checkboxes.min.js') }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>

    <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.rowGroup.min.js')) }}"></script>
    {{-- DATATABLE --}}

    <script src="{{ asset(mix('vendors/js/popper/popper.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>

    {{-- ОБЯЗАТЕЛЬНО ИСПРАВИТЬ - MIX не работает! !!! --}}
    {{--<script src="{{ asset(mix('vendors/js/pickers/flatpickr/dist/l10n/ru.js')) }}"></script>--}}
    <script src="{{ asset('vendors/js/pickers/flatpickr/dist/l10n/ru.js') }}"></script>
    {{-- ОБЯЗАТЕЛЬНО ИСПРАВИТЬ - MIX не работает! !!! --}}

    <script src="{{asset(mix('vendors/js/forms/select/select2.full.min.js'))}}"></script>

    <script src="{{ asset(mix('vendors/js/extensions/jquery.contextMenu.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/jquery.ui.position.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>

    {{--<script src="{{ asset('js/scripts/ui/ui-creator.js') }}"></script>--}}

@endsection

@push('scripts')
    <script type="module" src="{{ asset('aGrid/aGrid.js') }}"></script>
@endpush
