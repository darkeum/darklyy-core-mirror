import {_OPTIONS} from "../options.js";

/* ЗАГРУЗКА GRID EXT Callback's */

// #1 - preDrawCallback - ...
DataTable.Api.register('aGrid.callbacks.preDrawCallback', function (settings) {
    let api = this;
});

// #2 - serverParams - Загрузка параметров
DataTable.Api.register('aGrid.callbacks.serverParams', function (data) {
    let api = this;

    // ...
    data['order'].forEach(function (items, index) {
        data['order'][index]['column_name'] = data['columns'][items.column]['name'];
    });

    // ...
    api.aGrid.fn.hideSearchInputs(api.table().columns().responsiveHidden().toArray()); // Скрытие полей поиска
    api.aGrid.ui.loader(true); // Show Loader

    // console.log(data)

    return data;

});

// #3 - createdRow - Создание строк в таблице
DataTable.Api.register('aGrid.callbacks.createdRow', function (row, data, dataIndex) {
    let api = this;
    api.aGrid.mods.list.visible.actions.quickRow(row);
});

// #4 - headerCallback - Создание Header'а таблицы
DataTable.Api.register('aGrid.callbacks.headerCallback', function (thead, data, start, end, display) {
    let api = this;
});

// #5 - drawCallback - Отрисовка
DataTable.Api.register('aGrid.callbacks.drawCallback', function (settings) {
    let api = this;

    // var rows = api.rows({ page: 'current' }).nodes();
    // var columns = api.columns({ page: 'current' }).nodes();
    // var columns = api.columns().nodes();

    let rows = this.table().rows().count();     // Кол-во строк count
    let columns = settings.aoColumns;           // Столбцы - Данные

    // let columns = grid.table.settings()[0].aoColumns;
    // let columns = grid.table.columns().header().settings()[0].aoColumns;
    // let columns = api.columns().header().toArray();

    // log(grid.table.columns().header().settings()[0].aoColumns)
    // log(grid.table.settings()[0].aoColumns)
    // log(settings.settings.aoColumns)

    // Таблица
    // log(settings.nTable)
    // log(settings.oInstance)

    // let table = $(settings.nTable);
    // let header = $(settings.nTHead.children);

    //log(settings.aoColumns[3])
    //log(api.settings()[0].aoColumns)
    // log(api.columns().header().settings())
    // log(columns[3])

    api.aGrid.fn.setTitles(columns);      // Установка title для столбцов // ПЕРЕДЕЛАТЬ!
    api.aGrid.ui.table.empty(rows);       // Работа с пустой таблицей
    feather.replace();                    // Обновление иконок внутри таблицы
    api.aGrid.ui.bootstrap.reload();      // Перезагрузка всех Bootstrap элементов на странице
    // api.aGrid.ui.loader(false);        // Скрыть Loader
});

// #6 - stateLoadCallback - ...
DataTable.Api.register('aGrid.callbacks.stateLoadCallback', function (settings, callback) {
    let api = this;
});

// #7 - stateLoadParams - ...
DataTable.Api.register('aGrid.callbacks.stateLoadParams', function (data) {
    let api = this;
});

// #8 - initComplete - Инициализация завершена
DataTable.Api.register('aGrid.callbacks.initComplete', function (settings, json) {
    let api = this;

    api.tables().header().to$().addClass('table-header');               // Установить css-класс заголовка таблицы
    api.searchBuilder.container().addClass('search-builder-custom');    // Установить css-класс searchBuilder
    // console.log(api.searchBuilder.container());

    // Генератор полей для поиска c фильтром исключений
    let ignore = _OPTIONS.ignore;
    let options = api.settings()[0].aoColumns.filter(
        (item) =>
            typeof (item) === 'object'
            && ignore.includes(item.type) === false
            && item.visible !== false
    );
    // console.log(options)

    // WIP - ПЕРЕДЕЛАТЬ!
    // $('.aGrid-container table .aGrid-filters th').each(function (i) {
    api.tables().header().to$().children('tr.aGrid-filters').children().each(function (i) {

        // Все от столбца в COL
        let col = (options[i]) ? options[i] : false;
        col.placeholder = col.label; // Добавим placeholder
        // console.log(col);
        // console.log(col.type);

        // Генерация полей для поиска и сортировки
        if (col.searchable) {

            // Типы полей на switch - Добавление типов полей для Grid
            let input, select, select_options = [];
            switch (col.type) {

                // Выпадающий список select и Выпадающий список - Select2.js
                case 'select' :

                    // console.log(col);
                    // console.log(col.options.hasOwnProperty('value'));

                    // А есть ли "options" ?
                    if (col.options) {

                        // Если есть "value" - Строки в список поданы
                        if (col.options.hasOwnProperty('value')) {
                            let values = JSON.parse(col.options.value);
                            let first = $('<option></option>');
                            let ajax_url = false;
                            // console.log(values)

                            // WIP - Если есть пункт для работы с AJAX
                            if (values.hasOwnProperty('__AJAX_SELECT_GRID')) {
                                ajax_url = values.__AJAX_SELECT_GRID;
                                delete values.__AJAX_SELECT_GRID;
                                // console.log(ajax_url);
                                // ...
                            }

                            // Если есть пункт "ВСЕ" то он первый
                            if (values.hasOwnProperty('__ALL_SELECT_GRID')) {
                                let all = values.__ALL_SELECT_GRID; // Сохраняем в переменной
                                delete values.__ALL_SELECT_GRID; // Удаляем из объекта

                                // Добавим attr к первому созданному пункту
                                first.attr({
                                    'value': '__ALL_SELECT_GRID'
                                }).html(all);
                            }

                            // WIP - CUSTOM пункт будет первым
                            if (values.hasOwnProperty('__CUSTOM_SELECT_GRID')) {
                                let custom = JSON.parse(values.__CUSTOM_SELECT_GRID); // Сохраняем в переменной
                                delete values.__CUSTOM_SELECT_GRID; // Удаляем из объекта

                                // Добавим attr к первому созданному пункту
                                first.attr({
                                    'value': Object.keys(custom)[0]
                                }).html(Object.values(custom)[0]);
                            }

                            // Переберем пункты Select/Select2
                            $.each(values, function (index, item) {
                                // Создаем option'ы для select'а
                                let option = $('<option></option>');
                                option.attr({
                                    'value': index, // Не ставить value если 0 КОСТЫЛЬ
                                }).html(item);
                                select_options.push(option);
                            });
                            // console.log(select_options)

                            // Сортируем по алфавиту пункты в Select если нужно (order - true/false) - TODO WIP
                            if (col.options.hasOwnProperty('order')) {
                                select_options.sort(function (x, y) {
                                    // order - true/false
                                    if (col.options.order == true) {
                                        return $(x).text() > $(y).text() ? 1 : -1;
                                    } else {
                                        return $(x).text() < $(y).text() ? 1 : -1;
                                    }
                                });
                            }

                            // Добавляем первый элемент в select (Всё)
                            select_options.unshift(first);

                            // Создаем select выпадающий список и добавляем в него option'ы
                            select = $('<select></select>');
                            select.attr({
                                'id': col.data,
                                'class': 'form-select form-select-sm'
                                // 'class': 'form-select'
                            }).html(select_options);
                            $(this).append(select);

                            // Событие изменения Select
                            $('select', this).on('change', function () {
                                if (api.column(col.idx).search() !== this.value) {
                                    api.column(col.idx).search(this.value).draw();
                                    // console.log(col.idx, this.value)
                                }
                            });

                            // WIP - Добавить Select2.js к Select если нужно
                            if (col.options.hasOwnProperty('select2')
                                // && typeof col.options.select2 == 'boolean' //
                                && col.options.select2 == true) {
                                api.aGrid.ui.select2(select, {
                                    url: ajax_url // по умолчанию false или может быть ссылка на запрос
                                });
                            }
                        }
                    }

                    break;

                // Даты - WIP ПЕРЕДЕЛАТЬ !!!
                case 'date_range':
                    // Создаём input
                    input = $('<input />');
                    input.attr({
                        'type': 'text',
                        'id': 'date-range-' + col.data,
                        'class': 'form-control form-control-sm flatpickr-range',
                        'placeholder': col.placeholder
                    });
                    $(this).append(input);

                    flatpickr.localize(flatpickr.l10ns[$('html').attr('lang')]);
                    let rangePicker = $('#date-range-' + col.data);

                    if (rangePicker.length) {
                        rangePicker.flatpickr({
                            mode: 'range',
                            enableTime: col.configs.displayFormat.includes(' '), // Проверка пробела чтобы понять нужна ли дата
                            // noCalendar: true,
                            time_24hr: true,
                            dateFormat: col.configs.displayFormat, // Формат даты
                            // positionElement: '',
                            locale: {
                                rangeSeparator: ' - '
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                if (selectedDates[0] == undefined) {
                                    // $(instance.input).removeClass('text-center');
                                }
                                if (selectedDates[1] != undefined) {
                                    // $(instance.input).addClass('text-center');
                                    // console.log(api.column(col.idx));
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

                                // if (api.column(col.idx).search() !== this.value) {
                                //     api.column(col.idx).search(this.value).draw();
                                // }

                            },
                            onReady: function (_, __, fp) {
                                // fp.calendarContainer.classList.add('flatpickr-calendar');
                            }
                        });
                    }

                    // $('input', this).on('keypress', function (e) {
                    //     if (e.which === 13) {
                    //         $(this).attr("disabled", "disabled");
                    //         if (api.column(col.idx).search() !== this.value) {
                    //             api.column(col.idx).search(this.value).draw();
                    //         }
                    //         $(this).removeAttr("disabled");
                    //     }
                    // });

                    $('input', this).on('change', function () {
                        $(this).attr('disabled', 'disabled');
                        if (api.column(col.idx).search() !== this.value) {
                            api.column(col.idx).search(this.value).draw();
                        }
                        $(this).removeAttr('disabled');
                    });

                    break;

                // По умолчанию поле поиска простой input type text
                default:
                    // Создаем input
                    input = $('<input />');
                    input.attr({
                        'type': 'text',
                        'id': col.data,
                        'class': 'form-control form-control-sm',
                        'placeholder': col.placeholder,
                    });
                    $(this).append(input);

                    $('input', this).on('keypress', function (e) {
                        if (e.which === 13) {
                            $(this).attr("disabled", "disabled");
                            if (api.column(col.idx).search() !== this.value) {
                                api.column(col.idx).search(this.value).draw();
                            }
                            $(this).removeAttr("disabled");
                        }
                    });

            }

        }
    });

    // ###
    // api.aGrid.ui.select2($(".aGrid-filters th select")); // Использование select2
    // api.aGrid.ui.loader(false); // Скрыть Loader

    api.aGrid.ui.table.scroll();
});
