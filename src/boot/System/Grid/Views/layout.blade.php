{{-- Тело главного Grid --}}

@php
    function columns () {

    }
@endphp

<section>
    <div class="row">
        <div class="col-12">

            <div class="card grid-container">
                <div class="card-header py-1">

                    {{-- Заголовок --}}
                    <div class="col-12 col-xl-6">
                        <h4 class="card-title">{{ $title }}</h4>
                    </div>
                    {{-- Заголовок --}}

                    {{-- Панель поиска и параметров --}}
                    <div class="col-12 col-xl-6">
                        <div class="btn-toolbar justify-content-end">

                            {{-- Поле поиска --}}
                            @include('grid::layouts.table.search')
                            {{-- Поле поиска --}}

                            {{-- Кнопки управления --}}
                            @include('grid::layouts.table.control')
                            {{-- Кнопки управления --}}

                            {{-- Место под свои кнопки --}}
                            @if(isset($data['options']['button']))
                                {!! $data['options']['button'] !!}
                            @endif
                            {{-- Место под свои кнопки --}}

                            {{-- Модификаторы таблицы --}}
                            @include('grid::layouts.table.modifiers')
                            {{-- Модификаторы таблицы --}}

                        </div>
                    </div>
                    {{-- Панель поиска и параметров --}}

                </div>

                <table id="{{ $data['id'] }}"
                       class="table table-striped table-bordered compact table-sm table-sm-px {{--table-responsive--}} datatables-basic {{--dt-responsive--}} dt-column-search {{--dt-behavior--}} {{--display--}} {{--nowrap--}} grid-table"
                >
                    {{-- Table Header --}}
                    <thead class="">

                    @php
                        function ignored ($type):bool {
                            $arr = ['actions', 'checkbox'];
                            return in_array($type, $arr);
                        };
                    @endphp

                    {{-- Заголовки столбцов --}}
                    <tr class="grid-titles">
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
                    <tr class="grid-filters">
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

        const g_gen = new UiCreator();
        const log = function (data) {
            return console.log(data)
        }

        // log(store.getAll())
        // grid.log(store.getAll());

        // Параметры до загрузки Grid
        // $.extend(true, $.fn.dataTable.defaults, {});

        // Главный скрипт
        $(document).ready(function () {

            /* *** ОТРИСОВКА ТАБЛИЦЫ *** */
            const table = $('#{{ $data['id'] }}').DataTable(
                {
                    ajax: {
                        url: '{{ $url }}',
                        type: 'POST'
                    },

                    dom: '{!! $data['dom'] !!}',

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
                                className: 'dropdown-menu d-inline grid-container-collection',
                                closeButton: false,
                                button: {
                                    tag: 'a',
                                    className: 'dt-button dropdown-item',
                                    active: 'active',
                                    disabled: 'disabled'
                                }
                            }
                        },
                        buttonCreated: function (config, button) {
                            return config.buttons ? $('<div class="btn-group position-relative" role="group"/>').append(button) : button;
                        },

                        buttons: [
                            {
                                extend: 'searchBuilder',
                                text: feather.icons['plus'].toSvg({class: 'me-50 font-small-4'}) + 'Расширенный поиск',
                                className: 'btn-outline-secondary',
                                align: 'button-right',
                                background: false,
                            },
                                @if ($nav['export'])
                            {
                                extend: 'collection',
                                text: feather.icons['share'].toSvg({class: 'font-small-4 me-50'}) + 'Экспорт',
                                className: 'btn-outline-secondary dropdown-toggle',
                                align: 'button-right',
                                attr: {
                                    // 'data-bs-toggle': 'dropdown',
                                },
                                // background: false,
                                buttons: [
                                        @if ($nav['export.detail']['print'])
                                    {
                                        extend: 'print',
                                        text: feather.icons['printer'].toSvg({class: 'font-small-4 me-50'}) + 'Распечатать',
                                        className: 'dropdown-item',
                                        action: newexportaction
                                    },
                                        @endif
                                        @if ($nav['export.detail']['csv'])
                                    {
                                        extend: 'csv',
                                        text: feather.icons['file-text'].toSvg({class: 'font-small-4 me-50'}) + 'Сохранить в CSV',
                                        className: 'dropdown-item',
                                        action: newexportaction
                                    },
                                        @endif
                                        @if ($nav['export.detail']['excel'])
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
                                        @if ($nav['export.detail']['pdf'])
                                    {
                                        extend: 'pdf',
                                        text: feather.icons['clipboard'].toSvg({class: 'font-small-4 me-50'}) + 'Сохранить в PDF',
                                        className: 'dropdown-item',
                                        action: newexportaction
                                    },
                                        @endif
                                        @if ($nav['export.detail']['copy'])
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
                    scrollX: false,
                    // scrollXInner: false,
                    // scrollCollapse: true, // Высота таблицы будет максимальной если false даже если пуста!
                    // stateSave: true, // Не дает использовать appendTools и PerfectScrollbar

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

                            @if($col['type'] == 'checkbox')
                                @php $checkbox_target[] = $key; @endphp
                                @endif

                            title: "{{ $col['title'] }}",
                            label: "{{ $col['label'] }}",
                            name: "{{ $col['db'] }}",

                            width: "{{ $col['width'] }}",
                            visible: {{ $col['visible'] ? 'true' : 'false' }},
                            className: "{{ $col['className'] ?? '' }} col-{{ $col['type'] }}",

                            sortable: {{ isset($col['sortable']) ? ($col['sortable'] ? 'true' : 'false') : 'true' }},
                            searchable: {{ isset($col['search']) ? ($col['search'] === false ? 'false' : 'true') : 'false' }},

                            {!! isset($col['other']) ? 'other: ' . \Boot\System\Grid\Utils\Utils::jsonEncode($col['other']) . ',' : false !!}
                            data: "{{ $col['name'] }}",

                            @if(($col['render']))render: function (data, type, row, meta) {
                                return grid.render(data, type, row, meta, "{{ $col['render'] }}")
                            },
                            @endif

                                {!! isset($col['options']) ? 'options: ' . \Boot\System\Grid\Utils\Utils::jsonEncode($col['options']) . ',' : false !!}
                                {!! isset($col['priority']) ? 'priority: ' . $col['priority'] . ',' : false !!}
                                {!! isset($col['priority']) ? 'responsivePriority: ' . $col['priority'] . ',' : false !!}


                            {{-- WIP - Кастомные параметры для колонок - ДОВЕСТИ ДО УМА !!! --}}
                            configs: {
                                {{--icon: "{{ $col['icon'] ?? false }}",--}}
                                    {!! isset($col['icon']) ? 'icon: "' . $col['icon'] . '",' : false !!}
                                    {{--tooltip: "{{ $col['tooltip'] ?? false }}",--}}
                                    {!! isset($col['tooltip']) ? 'tooltip: "' . $col['tooltip'] . '",' : false !!}
                                    {{--popover: "{{ $col['popover'] }}",--}}
                                    {!! ($col['render']) ? 'popover: "' . $col['popover'] . '",' : false !!}
                                    {{--displayFormat: "{{ $col['format_date'] ?? false }}",--}}
                                    {!! isset($col['format_date']) ? 'displayFormat: "' . $col['format_date'] . '",' : false !!}
                                type: "{{ $col['type'] }}",
                                {{--type: "{{ $col['type'] }}",--}}
                                prefix: "{{ $col['prefix'] }}",
                            }
                            {{-- WIP - Кастомные параметры для колонок --}}

                            {{---}}
                            data: {
                                _: 'phone',
                                filter: 'phone_filter',
                                display: 'phone_display'
                            },
                            render: {
                                _: 'plain',
                                filter: 'filter',
                                display: 'display'
                            },
                            defaultContent: '',

                            cellType: 'th',
                            contentPadding: 'mmm',
                            createdCell: 'Только в columnDefs',

                            orderable: false,
                            orderData: 0,
                            orderDataType: 'dom-text'
                            {{----}}
                        },@endforeach
                    ],

                    // Расширенные параметры колонок
                    columnDefs: [
                            @foreach ($cols as $key => $col)
                            @if($col['type'] == 'checkbox'){
                                targets: [{{ $key }}],
                                render: function (data, type, full, meta) {
                                    return ('<div class="form-check"> <input class="form-check-input dt-checkboxes" type="checkbox" value="" id="checkbox' + data + '" /><label class="form-check-label" for="checkbox' + data + '"></label></div>'
                                );
                                    },
                                checkboxes: {
                                selectRow: true, // Отмечать чекбокс при клике на строке таблицы
                                selectAllRender:
                                    '<div class="form-check"> <input class="form-check-input" type="checkbox" value="" id="checkboxSelectAll" /><label class="form-check-label" for="checkboxSelectAll"></label></div>'
                                },
                            },
                            @endif
                        @endforeach
                    ],

                    select: {
                        style: 'multi',
                        // selector: 'td:first-child > input.checkbox'
                    },

                    // Перевод GRID
                    language: {!! collect($language)->toJson(JSON_UNESCAPED_UNICODE) !!},

                    // Кастомные параметры
                    // Использование table.settings()[0].oInit.settings.param или settings.oInit.settings.param из функций событий
                    {{--settings: {--}}
                    {{--    // param: value,--}}
                    {{--    grid: {--}}
                    {{--        mouse: {!! \Boot\System\Grid\Utils\Utils::jsonEncode($mouse) !!},--}}
                    {{--    },--}}
                    {{--},--}}

                    /* *** СОБЫТИЯ *** */
                    // #1 -
                    // preDrawCallback: function (data) {
                    //     log('preDrawCallback')
                    // },

                    // #2 - Загрузка параметров
                    serverParams: function (data) {
                        // log('fnServerParams');

                        let api = this.api();
                        let grid = new Grid(api);

                        //
                        data['order'].forEach(function (items, index) {
                            data['order'][index]['column_name'] = data['columns'][items.column]['name'];
                        });

                        // LOAD
                        grid.hideSearchInputs(api.columns().responsiveHidden().toArray()); // Скрытие полей поиска
                        grid.loader(true); // Show Loader

                    }, // END - fnServerParams

                    // #3 - Создание строк в таблице
                    createdRow: function (row, data, dataIndex) {

                        let api = this.api();
                        let grid = new Grid(api);

                        // grid.qRow(row);
                        grid.createdRow(row, data, dataIndex);

                    }, // END - createdRow

                    // #4 - Создание Header'а таблицы
                    headerCallback: function (thead, data, start, end, display) {
                        // log('headerCallback')
                    },

                    // #5 - Отрисовка
                    drawCallback: function (settings) {

                        let api = this.api();
                        let grid = new Grid(api);

                        grid.drawCallback(settings);

                    }, // END - drawCallback

                    // #6 - ...
                    // stateLoadCallback: function () {},

                    // #7 - ...
                    // stateLoadParams: function (data) {},

                    // #8 - Инициализация завершена
                    initComplete: function (settings, json) {
                        // console.log('fnInitComplete');

                        let api = this.api();
                        let grid = new Grid(api);

                        // Установить css-класс заголовка таблицы
                        api.tables().header().to$().addClass('table-header');
                        // Установить css-класс searchBuilder
                        api.searchBuilder.container().addClass('search-builder-custom');

                        // console.log(api.searchBuilder.container());

                        // Генератор полей для поиска c фильтром исключений
                        const filter = ['checkbox', 'actions'];
                        let options = api.settings()[0].aoColumns.filter(
                            (item) =>
                                typeof (item) === 'object'
                                && filter.includes(item.type) === false
                                && item.visible !== false
                        );
                        // console.log(options)

                        // WIP - ПЕРЕДЕЛАТЬ!
                        api.tables().header().to$().children('tr.grid-filters').children().each(function (i) { // #3 - Новый
                            // $('.grid-container table .grid-filters th').each(function (i) { // #2 - Рабочий

                            let option = (options[i]) ? options[i] : false;
                            let placeholder = option.label;
                            let type = option.type;

                            // console.log(option);
                            // console.log(type);

                            // Генерация полей для поиска и сортировки
                            if (option.searchable) {

                                // Типы полей на switch - Добавление типов полей для Grid
                                let input, select, select_options = [];
                                switch (type) {

                                    // Выпадающий список
                                    case 'select2' :
                                        // let select_options = [];
                                        option.options.value.split(';').forEach(function (items, index) {
                                            let item = items.split(':');

                                            // Создаем option'ы для select'а
                                            let option = $('<option></option>');
                                            option.attr({
                                                'value': item[0],
                                            }).html(item[1]);
                                            select_options.push(option);
                                        });

                                        // Создаем select выпадающий список
                                        select = $('<select></select>');
                                        select.attr({
                                            'id': option.data,
                                            'class': 'form-select form-select-sm d-table-select-sm'
                                        }).html(select_options);
                                        $(this).append(select);

                                        $('select', this).on('change', function () {
                                            if (table.column(option.idx).search() !== this.value) {
                                                table.column(option.idx).search(this.value).draw();
                                            }
                                        });

                                        break;

                                    case 'select' :

                                        const values = JSON.parse(option.options.value);
                                        // Object.keys(values).forEach(function (item, index) {

                                        let option_tpl = $('<option></option>');
                                        option_tpl.attr({
                                            'value': '__ALL_SELECT_GRID'
                                        }).html('Все');
                                        select_options.push(option_tpl);
                                        $.each(values, function (index, item) {

                                            // Создаем option'ы для select'а
                                            let option = $('<option></option>');
                                            option.attr({
                                                // 'value': index, // Было
                                                'value': index, // Не ставить value если 0 КОСТЫЛЬ
                                            }).html(item);
                                            select_options.push(option);
                                        });

                                        // Создаем select выпадающий список
                                        select = $('<select></select>');
                                        select.attr({
                                            'id': option.data,
                                            'class': 'form-select form-select-sm d-table-select-sm'
                                        }).html(select_options);
                                        $(this).append(select);

                                        $('select', this).on('change', function () {
                                            if (table.column(option.idx).search() !== this.value) {
                                                table.column(option.idx).search(this.value).draw();
                                                console.log(option.idx)
                                                console.log(this.value)
                                            }
                                        });

                                        break;

                                    // Даты - WIP ПЕРЕДЕЛАТЬ !!!
                                    case 'date_range':
                                        // Создаём input
                                        input = $('<input />');
                                        input.attr({
                                            'type': 'text',
                                            'id': 'date-range-' + option.data,
                                            'class': 'form-control form-control-sm flatpickr-range',
                                            'placeholder': placeholder
                                        });
                                        $(this).append(input);

                                        flatpickr.localize(flatpickr.l10ns.{!! app()->getLocale() !!});
                                        let rangePicker = $('#date-range-' + option.data);

                                        if (rangePicker.length) {
                                            rangePicker.flatpickr({
                                                mode: 'range',
                                                enableTime: option.configs.displayFormat.includes(' '), // Проверка пробела чтобы понять нужна ли дата
                                                // noCalendar: true,
                                                time_24hr: true,
                                                dateFormat: option.configs.displayFormat, // Формат даты

                                                locale: {
                                                    rangeSeparator: ' - '
                                                },
                                                onChange: function (selectedDates, dateStr, instance) {
                                                    if (selectedDates[0] == undefined) {
                                                        // $(instance.input).removeClass('text-center');
                                                    }
                                                    if (selectedDates[1] != undefined) {
                                                        // $(instance.input).addClass('text-center');

                                                        // console.log(table.column(option.idx));
                                                    }

                                                    // title к полям выбора даты
                                                    $(instance.element).attr('title', dateStr)

                                                },
                                                onOpen: function (selectedDates, dateStr, instance) {

                                                    // console.log($(instance.input).val());
                                                    if ($(instance.input).val() == '') {
                                                        $(rangePicker).val('');
                                                    }
                                                    // $(rangePicker).removeClass('text-center');
                                                },
                                                onClose: function (selectedDates, dateStr, instance) {
                                                    let startDate = '', endDate = new Date();
                                                    if (selectedDates[0] != undefined) {
                                                        startDate = selectedDates[0].getMonth() + 1
                                                            + '/' + selectedDates[0].getDate()
                                                            + '/' + selectedDates[0].getFullYear();
                                                        $('.start_date').val(startDate);
                                                    }
                                                    if (selectedDates[1] != undefined) {
                                                        endDate = selectedDates[1].getMonth() + 1
                                                            + '/' + selectedDates[1].getDate()
                                                            + '/' + selectedDates[1].getFullYear();
                                                        $('.end_date').val(endDate);
                                                    }
                                                    $(rangePicker).trigger('change').trigger('keyup');

                                                    // if (table.column(option.idx).search() !== this.value) {
                                                    //     table.column(option.idx).search(this.value).draw();
                                                    // }

                                                },
                                                onReady: function (_, __, fp) {
                                                    fp.calendarContainer.classList.add("grid-pack");
                                                }
                                            });
                                        }

                                        // $('input', this).on('keypress', function (e) {
                                        //     if (e.which === 13) {
                                        //         $(this).attr("disabled", "disabled");
                                        //         if (table.column(option.idx).search() !== this.value) {
                                        //             table.column(option.idx).search(this.value).draw();
                                        //         }
                                        //         $(this).removeAttr("disabled");
                                        //     }
                                        // });

                                        $('input', this).on('change', function () {
                                            $(this).attr("disabled", "disabled");
                                            if (table.column(option.idx).search() !== this.value) {
                                                table.column(option.idx).search(this.value).draw();
                                            }
                                            $(this).removeAttr("disabled");
                                        });

                                        break;

                                    // По умолчанию поле поиска простой input type text
                                    default:
                                        // Создаем input
                                        input = $('<input />');
                                        input.attr({
                                            'type': 'text',
                                            'id': option.data,
                                            'class': 'form-control form-control-sm',
                                            'placeholder': placeholder,
                                        });
                                        $(this).append(input);

                                        $('input', this).on('keypress', function (e) {
                                            if (e.which === 13) {
                                                $(this).attr("disabled", "disabled");
                                                if (table.column(option.idx).search() !== this.value) {
                                                    table.column(option.idx).search(this.value).draw();
                                                }
                                                $(this).removeAttr("disabled");
                                            }
                                        });

                                }

                            }

                            grid.loader(false); // Hide Loader
                        });

                        // ### LOAD
                        grid.select2($(".grid-filters th select")); // Использование select2
                        // grid.loader(false); // Hide Loader

                    } //  END - fnInitComplete

                }
            );

            /* *** END - ОТРИСОВКА ТАБЛИЦЫ *** */

            // Создаем Grid для функций вне событий DataTable
            const grid = new Grid(table);

            /* *** КОНСТРУКТОР ТАБЛИЦЫ *** */
            // Loader Grid JS - Загрузка одной функцией
            grid.boot();

            // Используем PerfectScrollbar
            let ps = new PerfectScrollbar('.dataTables_scrollBody', {
                // suppressScrollX: true,
                // wheelPropagation: false, // Стандартные методы отключения прокрутки страницы вверх
                wheelSpeed: 0.3, // от 0.2 до 0.4 нормально грузит (0.3 оптимально вроде...)
                minScrollbarLength: 50,
            });
            /* *** END - КОНСТРУКТОР ТАБЛИЦЫ *** */

            // table.on( 'stateLoadParams.dt', function (e, settings, data) {
            //     log('stateLoadParams');
            // });

            // table.on( 'stateLoaded.dt', function (e, settings, data) {
            //     log('stateLoaded');
            // });

            // table.on( 'stateSaveParams.dt', function (e, settings, data) {
            //     // log('stateSaveParams');
            // });

            /* *** СОБЫТИЯ ТАБЛИЦЫ *** */
            table
                // (Дублирующий вызов)
                // .on( 'init.dt', function ( e, settings, column, state ) {
                //     log('init.dt');
                // })

                // Отрисовка processing
                // .on('processing.dt', function (e, settings, processing) {})

                // Отрисовка таблицы (Дублирующий вызов)
                // .on( 'draw.dt', function (e, datatable) {
                //     // log('draw.dt');
                // })

                // Сортировка
                // .on( 'order.dt',  function () {
                //     // let order = table.order();
                //     // log("Ordered column " + order[0][0] + ", in direction " + order[0][1]);
                // })

                // Изменение ширины столбцов (Калькуляция)
                // .on('column-sizing.dt', function (e, settings) {})

                // Изменение размера таблицы
                .on("responsive-resize", function (e, datatable, columns) {

                    // log('hide');

                    // Скрытие полей поиска
                    grid.hideSearchInputs(columns);

                    // log(datatable)
                    // log(columns)

                    // Перерисовка ширины столбцов
                    table.columns.adjust()
                        .responsive.recalc();

                    // feather.replace();
                })

                //
                .on('responsive-display', function (e, datatable, row, showHide, update) {
                    console.log('Details for row ' + row.index() + ' ' + (showHide ? 'shown' : 'hidden'));
                })

                // Не работает
                // .on( 'column-visibility.dt', function ( e, settings, column, state ) {
                //     console.log(
                //         'Column '+ column +' has changed to '+ (state ? 'visible' : 'hidden')
                //     );
                // })


                /* *** Обработка событий мыши *** */
                // Клик по tr таблицы
                .on('click', 'tr', function (e) {
                    // grid.rowSelected(this) // Временно убрано...
                })

                //
                // .on('mouseover', 'tr', function () {})

                //
                // .on('mouseenter', 'tr', function () {})

                //
                // .on('mouseleave', 'tr', function () {})

                // x2 Клик по tr таблицы
                .on('dblclick', 'tr', function (event) {
                    if (event.ctrlKey) {
                        grid.openUrl(this);
                    }
                })

                // Клик по render для раздвижения
                .on('click', 'tr.selected .render span', function (event) {
                    // grid.renderExpand(this); WIP
                })

            // Обработка события по (+) (Разворачивание скрытого)
            // .on('click', 'tr td.dtr-control', function () {})

            // Обработка Кликов вне блока grid-container
            $(document).on('click', function (e) {
                if (!$(e.target).closest(".grid-container, ul.context-menu-list, .toast-player-container").length) {
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


        /* ********************* ФУНКЦИИ ********************* */
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

        // *** *** *** *** *** *** *** *** *** *** *** *** *** ***

    </script>
@endpush

@section('page-style')

    {{-- Тестовый Style CSS - После разработки все пернести в Grid SCSS --}}
    <style>

        table.dataTable th {
            /*font-size: 0.7rem !important;*/
        }

        table.dataTable td {
            position: relative;
        }

        table.dataTable td .badge-render i, table.dataTable td .badge-render svg {
            margin-left: 3px;
        }

        #g-control .dt-button-collection.dtb-collection-closeable:has(.search-builder-custom) {
            width: 50% !important;
        }
        #g-control .dt-button-collection.dtb-collection-closeable {
            left: auto !important;
            right: 0;
        }

        .grid-container .dataTables_scrollBody table {
            z-index: 0 !important;
        }

        {{-- CSS render-class --}}
          table.dataTable > tbody > tr > td > input.render-class {
            border-color: transparent;
            background: transparent;
            text-overflow: ellipsis;
        }

        table.dataTable > tbody > tr.selected > td > input.render-class {
            /*border: 1px solid #ffffff52;*/
            color: white;
            box-shadow: none;
        }

        {{-- CSS render-class --}}

        @media (max-width: 575.98px) {
            div.dataTables_wrapper {
                overflow: hidden !important;
            }
        }

        div.dataTables_wrapper .dt-button-collection > div.dtsb-searchBuilder[role=menu] {
            display: block;
            transform: none;
            box-shadow: none;
        }

        div.dtb-popover-close {
            top: 2px;
            right: 2px;
        }

        .search-builder-custom .dtsb-inputCont select, .search-builder-custom .dtsb-inputCont input {
            height: 100%;
        }

    </style>
    {{-- Тестовый Style CSS - После разработки все пернести в Grid SCSS --}}

@endsection

@section('vendor-style')

    {{-- vendor css files --}}
    {{-- datatable --}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/last/datatables.css') }}">
{{--    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/full/datatables.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('vendors/css/tables/datatable/extensions/dataTables.checkboxes.css') }}">
    {{-- datatable --}}

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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>

    {{-- DATATABLE --}}
    {{--<script src="{{ asset('vendors/js/tables/datatable/last/datatables.min.js') }}"></script>--}}
    <script src="{{ asset('vendors/js/tables/datatable/last/datatables.js') }}"></script>
{{--    <script src="{{ asset('vendors/js/tables/datatable/full/datatables.min.js') }}"></script>--}}
    {{--<script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>--}}

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
    <script src="{{ asset('js/scripts/custom/grid.js') }}"></script>

@endsection
