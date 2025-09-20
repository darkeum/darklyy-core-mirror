import {_OPTIONS} from "../options.js";

/* ЗАГРУЗКА GRID EXT Callback's */

// #1 - preDrawCallback - вызывается перед отрисовкой таблицы
// Документация: https://datatables.net/reference/option/preDrawCallback
DataTable.Api.register('aGrid.callbacks.preDrawCallback', function (settings) {
    let api = this;
    // Можно добавить подготовительные операции перед отрисовкой
});

// #2 - serverParams - Загрузка параметров для серверного запроса
// Документация: https://datatables.net/reference/option/ajax.data
DataTable.Api.register('aGrid.callbacks.serverParams', function (data) {
    let api = this;

    // Добавление имени колонки к информации о сортировке
    data['order'].forEach(function (items, index) {
        data['order'][index]['column_name'] = data['columns'][items.column]['name'];
    });

    // Скрытие полей поиска для скрытых колонок и показ лоадера
    api.aGrid.fn.hideSearchInputs(api.table().columns().responsiveHidden().toArray());
    api.aGrid.ui.loader(true); // Show Loader

    // console.log(data)

    return data;
});

// #3 - createdRow - Создание строк в таблице
// Документация: https://datatables.net/reference/option/createdRow
DataTable.Api.register('aGrid.callbacks.createdRow', function (row, data, dataIndex) {
    let api = this;
    // Добавление быстрых действий к строке
    api.aGrid.mods.list.visible.actions.quickRow(row);
});

// #4 - headerCallback - Создание Header'а таблицы
// Документация: https://datatables.net/reference/option/headerCallback
DataTable.Api.register('aGrid.callbacks.headerCallback', function (thead, data, start, end, display) {
    let api = this;
    // Можно добавить кастомную логику для заголовка таблицы
});

// #5 - drawCallback - Отрисовка таблицы после обновления данных
// Документация: https://datatables.net/reference/option/drawCallback
DataTable.Api.register('aGrid.callbacks.drawCallback', function (settings) {
    let api = this;

    // Получение количества строк и информации о колонках
    let rows = this.table().rows().count();     // Кол-во строк count
    let columns = settings.aoColumns;           // Столбцы - Данные

    // Установка заголовков, обработка пустой таблицы, обновление иконок
    api.aGrid.fn.setTitles(columns);      // Установка title для столбцов // ПЕРЕДЕЛАТЬ!
    api.aGrid.ui.table.empty(rows);       // Работа с пустой таблицей
    feather.replace();                    // Обновление иконок внутри таблицы (Feather Icons)
    api.aGrid.ui.bootstrap.reload();      // Перезагрузка всех Bootstrap элементов в контейнере aGrid
    // api.aGrid.ui.loader(false);        // Скрыть Loader (закомментировано)
});

// #6 - stateLoadCallback - Загрузка состояния таблицы
// Документация: https://datatables.net/reference/option/stateLoadCallback
DataTable.Api.register('aGrid.callbacks.stateLoadCallback', function (settings, callback) {
    let api = this;
    // Кастомная логика загрузки состояния таблицы
});

// #7 - stateLoadParams - Обработка параметров состояния
// Документация: https://datatables.net/reference/option/stateLoadParams
DataTable.Api.register('aGrid.callbacks.stateLoadParams', function (data) {
    let api = this;
    // Модификация данных состояния перед загрузкой
});

// #8 - initComplete - Инициализация завершена
// Документация: https://datatables.net/reference/option/initComplete
DataTable.Api.register('aGrid.callbacks.initComplete', function (settings, json) {
    let api = this;

    // Установка CSS классов для заголовка таблицы и SearchBuilder
    api.tables().header().to$().addClass('table-header');
    api.searchBuilder.container().addClass('search-builder-custom');
    // console.log(api.searchBuilder.container());

    let def = false;
    let cols = api.tables().header().to$().children('tr.aGrid-filters').children();
    // console.log(cols)

    // Генератор полей для поиска c фильтром исключений
    let ignore = _OPTIONS.ignore;
    // Фильтрация колонок: только объекты, не игнорируемые типы и видимые колонки
    let options = api.settings()[0].aoColumns.filter(
        (item) =>
            typeof (item) === 'object'
            && ignore.includes(item.type) === false
            && item.visible !== false
    );
    // console.log(options)

    // api.columns().search('');

    // WIP - ПЕРЕДЕЛАТЬ!
    // Итерация по всем ячейкам фильтров в заголовке таблицы
    cols.each(function (i) {

        // Получение конфигурации колонки
        let col = (options[i]) ? options[i] : false;
        col.placeholder = col.label; // Добавим placeholder
        // console.log(col);
        // console.log(col.type);

        // Генерация полей для поиска и сортировки (только для searchable колонок)
        if (col.searchable) {

            // Типы полей на switch - Добавление типов полей для Grid
            let input, select, select_options = [];
            switch (col.type) {

                // Выпадающий список select и Выпадающий список - Select2.js
                case 'select' :

                    // Проверка наличия опций для select
                    if (col.options) {

                        // console.log(col.options);

                        // Если есть "value" - Строки в список поданы
                        if (col.options.hasOwnProperty('value')) {
                            let values = JSON.parse(col.options.value);
                            let first = $('<option></option>');
                            let ajax_url = false;
                            // console.log(values)

                            // WIP - Если есть пункт для работы с AJAX
                            if (values.hasOwnProperty(_OPTIONS.select2.drop.ajax)) {
                                ajax_url = values[_OPTIONS.select2.drop.ajax];
                                delete values[_OPTIONS.select2.drop.ajax];
                                // console.log(ajax_url);
                                // ...
                            }

                            // Если есть пункт "ВСЕ" то он первый
                            if (values.hasOwnProperty(_OPTIONS.select2.drop.all)) {
                                let allText = values[_OPTIONS.select2.drop.all];
                                delete values[_OPTIONS.select2.drop.all];

                                first.attr({
                                    'value': _OPTIONS.select2.drop.all
                                }).html(allText);
                            }

                            // WIP - CUSTOM пункт будет первым
                            if (values.hasOwnProperty(_OPTIONS.select2.drop.custom)) {
                                let custom = JSON.parse(values[_OPTIONS.select2.drop.custom]);
                                delete values[_OPTIONS.select2.drop.custom];

                                first.attr({
                                    'value': Object.keys(custom)[0]
                                }).html(Object.values(custom)[0]);
                            }

                            // Создание options для select
                            $.each(values, function (index, item) {
                                let option = $('<option></option>');

                                //
                                if (col.hasOwnProperty('default') && col.default == index) {
                                    def = true;
                                    option.attr('selected', 'selected');
                                    api.column(col.idx).search(col.default);
                                }

                                option.attr({
                                    'value': index
                                }).html(item);
                                select_options.push(option);

                            });
                            // console.log(select_options)

                            // Сортируем по алфавиту пункты в Select если нужно
                            if (col.options.hasOwnProperty('order')) {
                                select_options.sort(function (x, y) {
                                    if (col.options.order == true) {
                                        return $(x).text() > $(y).text() ? 1 : -1;
                                    } else {
                                        return $(x).text() < $(y).text() ? 1 : -1;
                                    }
                                });
                            }

                            // Добавляем первый элемент в select
                            select_options.unshift(first);

                            // Создаем select и добавляем options
                            select = $('<select data-default-value="1"></select>');
                            select.attr({
                                'id': col.data,
                                'class': 'form-select form-select-sm'
                            }).html(select_options);
                            $(this).append(select);

                            // Обработчик изменения значения select
                            $('select', this).on('change', function () {
                                if (api.column(col.idx).search() !== this.value) {
                                    api.column(col.idx).search(this.value).draw();
                                }
                            });

                            // Инициализация Select2 если требуется
                            if (col.options.hasOwnProperty('select2')
                                && col.options.select2 == true) {
                                api.aGrid.ui.select2(select, {
                                    url: ajax_url // URL для AJAX-загрузки данных
                                });
                            }
                        }
                    }

                    break;

                // Даты - WIP ПЕРЕДЕЛАТЬ !!!
                case 'date_range':
                    // Создание input для диапазона дат
                    input = $('<input />');
                    input.attr({
                        'type': 'text',
                        'id': 'date-range-' + col.data,
                        'class': 'form-control form-control-sm flatpickr-range',
                        'placeholder': col.placeholder
                    });

                    // Если есть значение по умолчанию то ставим его
                    if (col.hasOwnProperty('default')) {
                        def = true;
                        input.attr('value', col.default);
                        api.column(col.idx).search(col.default);
                    }

                    $(this).append(input);

                    // Локализация flatpickr
                    flatpickr.localize(flatpickr.l10ns[$('html').attr('lang')]);
                    let rangePicker = $('#date-range-' + col.data);

                    // Инициализация flatpickr для выбора диапазона дат
                    if (rangePicker.length) {
                        rangePicker.flatpickr({
                            mode: 'range',
                            enableTime: col.configs.displayFormat.includes(' '), // Включение времени если есть пробел в формате
                            time_24hr: true,
                            dateFormat: col.configs.displayFormat, // Формат даты
                            locale: {
                                rangeSeparator: ' - '
                            },
                            onChange: function (selectedDates, dateStr, instance) {
                                // Логика при изменении дат
                                $(instance.element).attr('title', dateStr) // Установка title
                            },
                            onOpen: function (selectedDates, dateStr, instance) {
                                // Логика при открытии picker'а
                                if ($(instance.input).val() == '') {
                                    $(rangePicker).val('');
                                }
                            },
                            onClose: function (selectedDates, dateStr, instance) {
                                // Логика при закрытии picker'а
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
                            },
                            onReady: function (_, __, fp) {
                                // Логика при готовности picker'а
                            }
                        });
                    }

                    // Обработчик изменения значения date range
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
                    // Создание текстового input для поиска
                    input = $('<input />');
                    input.attr({
                        'type': 'text',
                        'id': col.data,
                        'class': 'form-control form-control-sm',
                        'placeholder': col.placeholder,
                    });

                    // Если есть значение по умолчанию то ставим его
                    if (col.hasOwnProperty('default')) {
                        def = true;
                        input.attr('value', col.default);
                        api.column(col.idx).search(col.default);
                    }

                    $(this).append(input);

                    // Обработчик нажатия Enter в текстовом поле
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

    // Дополнительные операции после инициализации
    def && api.draw(); // Запрос по поиску если есть default в запросе
    api.aGrid.ui.table.scroll(); // Настройка скроллинга таблицы

});
