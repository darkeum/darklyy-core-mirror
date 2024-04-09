let _OPTIONS = {
    ignore: ['actions', 'checkbox'],
};

// Инициализация aGrid EXT
DataTable.Api.register('aGrid', function () {
    let api = this;
    let container = $('.aGrid-container');

    api.aGrid.$ = {
        container: container,
        table: api, // Текущая таблица всегда доступна в Классе
        module: container.data('moduleName'), // Название модуля
        options: {
            ignore: _OPTIONS.ignore
        },
        store: store.namespace('aGrid'), // Система хранения в локальном кеше браузера для aGrid
        dropContent: false, // Список для popover модификаторов
    };

    // Перенос элементов управления (В новое место)
    api.aGrid.fn.appendTools();

    // Вызов контекстного меню для table tr
    // api.aGrid.contextMenu();

    // Инициализация aGrid-mods
    api.aGrid.mods();

    api.aGrid.dt.events();
    api.aGrid.dt.mouse.events();

    // Показать версию
    // console.log('aGrid EXT: Запущен!);

    // TEST
    // console.log(this.store.getAll())

    return api;
});

/* ************************************************************************ */

// События DT - https://www.datatables.net/reference/event/
DataTable.Api.register('aGrid.dt.events', function () {
    let api = this;

    // https://datatables.net/reference/option
    api
        //
        .on('init.dt', function (e, settings, column, state) {
            console.log('init.dt');
        })
        // Отрисовка таблицы (Дублирующий вызов)
        .on('draw.dt', function (e, datatable) {
            // console.log('draw.dt')
        })
        // Сортировка
        .on('order.dt', function () {
            // let order = api.order();
            // console.log("Ordered column " + order[0][0] + ", in direction " + order[0][1]);
        })

        // ### Select
        // Выбор элементов (строк, столбцов или ячеек) отменен.
        .on('deselect', function (e, dt, type, indexes) {
            // console.log(e);
            // if (type === 'row') {
            //     var data = table
            //         .rows(indexes)
            //         .data()
            //         .pluck('id');
            //
            //     // do something with the ID of the deselected items
            // }
        })
        // Выбраны элементы (строки, столбцы или ячейки).
        .on('select', function (e, dt, type, indexes) {
            // console.log(e)
            // if (type === 'row') {
            //     var data = table
            //         .rows(indexes)
            //         .data()
            //         .pluck('id');
            //
            //     // do something with the ID of the selected items
            // }
        })
        // Перед размытием выбора элемента в DataTable (можно отменить).
        .on('select-blur', function (e, dt, target, originalEvent) {
            // console.log(e)
            // if ($(e.target).parents('div.modal').length) {
            //     e.preventDefault();
            // }
        })
        // Выберите событие изменения типа элемента.
        .on('selectItems', function (e, dt, items) {
            console.log('Элементы, которые нужно выбрать, теперь доступны: ', items);
        })

        // Выберите событие изменения стиля.
        .on('selectStyle', function (e, dt, style) {
            console.log('Selection style is now: ', style);
        })
        // Действие пользователя приведет к выбору элементов в таблице.
        .on('user-select', function (e, dt, type, cell, originalEvent) {
            // console.log(e)
            // if (originalEvent.target.nodeName.toLowerCase() === 'img') {
            //     e.preventDefault();
            // }
        })

        // Изменение ширины столбцов (Калькуляция)
        .on('column-sizing.dt', function (e, settings) {
            // console.log(e)
            // console.log(settings)
        })
        //
        .on('column-visibility.dt', function (e, settings, column, state) {
            // console.log(
            //     'Column ' + column + ' has changed to ' + (state ? 'visible' : 'hidden')
            // );
        })
        // Изменение размера таблицы
        .on("responsive-resize", function (e, datatable, columns) {
            // Скрытие полей поиска
            datatable.aGrid.fn.hideSearchInputs(columns);
            // Перерисовка ширины столбцов
            datatable.columns.adjust().responsive.recalc();
        })
        // Изменение размера экрана
        .on('responsive-display', function (e, datatable, row, showHide, update) {
            // console.log('Details for row ' + row.index() + ' ' + (showHide ? 'shown' : 'hidden'));

            // Скрытие полей поиска
            // datatable.aGrid.fn.hideSearchInputs(columns);
            // Перерисовка ширины столбцов
            // datatable.columns.adjust().responsive.recalc();
        })
        // Отрисовка processing
        .on('processing.dt', function (e, settings, processing) {
            api.aGrid.ui.loader(processing)
        })
});

// События мыши DT - Обработка событий мыши
DataTable.Api.register('aGrid.dt.mouse.events', function () {
    let api = this;

    // Клик по tr таблицы
    api
        .on('click', 'tr', function (e) {
            // grid.rowSelected(this) // Временно убрано...
        })
        .on('mouseover', 'tr', function () {
        })
        .on('mouseenter', 'tr', function () {
        })
        .on('mouseleave', 'tr', function () {
        })
        // x2 Клик по tr таблицы
        .on('dblclick', 'tr', function (event) {
            if (event.ctrlKey) {
                grid.openUrl(this);
            }
        })
        // Клик по render для раздвижения
        // .on('click', 'tr.selected .render span', function (event) {
        // })
        // Обработка события по (+) (Разворачивание скрытого)
        .on('click', 'tr td.dtr-control', function () {
        })
});
/* ************************************************************************ */
/* ЗАГРУЗКА GRID EXT Callback's */

// #1 - preDrawCallback - ...
DataTable.Api.register('aGrid.callbacks.preDrawCallback', function (data) {
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

    api.aGrid.fn.setTitles(columns);    // Установка title для столбцов // ПЕРЕДЕЛАТЬ!
    api.aGrid.ui.emptyTable(rows);      // Работа с пустой таблицей
    feather.replace();                  // Обновление иконок внутри таблицы
    api.aGrid.ui.bootstrapReload();     // Перезагрузка всех Bootstrap элементов на странице
    // api.aGrid.ui.loader(false);         // Скрыть Loader
});

// #6 - stateLoadCallback - ...
DataTable.Api.register('aGrid.callbacks.stateLoadCallback', function () {
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

                // Выпадающий список
                case 'select' :

                    let values = JSON.parse(col.options.value);
                    let option = $('<option></option>');

                    option.attr({
                        'value': '__ALL_SELECT_GRID'
                    }).html('Все');
                    select_options.push(option);

                    $.each(values, function (index, item) {

                        // Создаем option'ы для select'а
                        let option = $('<option></option>');
                        option.attr({
                            'value': index, // Не ставить value если 0 КОСТЫЛЬ
                        }).html(item);
                        select_options.push(option);
                    });

                    // Создаем select выпадающий список
                    select = $('<select></select>');
                    select.attr({
                        'id': col.data,
                        'class': 'form-select form-select-sm'
                    }).html(select_options);
                    $(this).append(select);

                    $('select', this).on('change', function () {
                        if (api.column(col.idx).search() !== this.value) {
                            api.column(col.idx).search(this.value).draw();
                            // console.log(col.idx)
                            // console.log(this.value)
                        }
                    });

                    break;

                // Выпадающий список - Select2.js // ПОКА НЕ ИСПОЛЬЗУЕТСЯ В ПРОЕКТЕ (Старый вариант данных)
                /*/
                case 'select2' :
                    // console.log(col.options.value.split(';'))
                    col.options.value.split(';').forEach(function (items, index) {
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
                        'id': col.data,
                        'class': 'form-select form-select-sm d-table-select-sm'
                    }).html(select_options);
                    $(this).append(select);

                    $('select', this).on('change', function () {
                        if (api.column(col.idx).search() !== this.value) {
                            api.column(col.idx).search(this.value).draw();
                        }
                    });

                    break;
                /**/

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
    api.aGrid.ui.select2($(".aGrid-filters th select")); // Использование select2
    // api.aGrid.ui.loader(false); // Скрыть Loader
});

/* ************************************************************************ */
/* ФУНКЦИИ - fn */

// Перенос элементов управления (В новое место)
DataTable.Api.register('aGrid.fn.appendTools', function () {
    let api = this;
    let container = api.aGrid.$.container;
    let search = container.find('.dataTables_filter');
    let search_id = search.attr('id'); // Оптимизация
    let search_class = search.attr('class'); // Оптимизация
    let search_text = $(search.children('label')).text(); // Оптимизация
    let search_input = $(search.children('label').children('input')); // Оптимизация

    search_input.attr({
        'placeholder': search_text,
        // 'aria-label' : 'text',
        'aria-describedby': search_id
    }).addClass('form-control-sm');

    let buttons = container.find('.dt-buttons');
    buttons.find('.btn')
        // .attr({
        //     'data-bs-toggle' : 'modal',
        //     'data-bs-target' : '#searchB',
        // })
        .addClass('btn-sm');

    // Открепляем пустышки
    search.detach();
    buttons.detach();

    let toolbar = $('.btn-toolbar');
    //toolbar.find('.input-group-text').append(search_text); // Показать вместо иконки поиска "Поиск"
    toolbar.find('#aGrid-search').attr('id', search_id).addClass(search_class).append(search_input);
    toolbar.find('#aGrid-controls').append(buttons.children());

    // toolbar.find('#aGrid-controls').find('.btn').attr({
    //     'data-bs-toggle' : 'modal',
    //     'data-bs-target' : '#searchB',
    // });
});

// Установка tooltip для столбцов со сменой текста при сортировке столбца, а так же установка шестерни для действий
DataTable.Api.register('aGrid.fn.setTitles', function (columns) {
    columns.forEach(function (item, index) {

        let unit = $(item.nTh); // Если settings или item; // Если api.columns()

        let labelSort = unit.attr('aria-label');
        let label = (labelSort) ? labelSort : item.label;

        // api.settings()[0].aoColumns // Обращение к настройкам

        // Параметры configs
        let configs = item.configs;

        // v1
        let tooltip = configs.tooltip;
        let title = (tooltip) ? tooltip : label;

        // console.log(item)
        // console.log(item.icon)

        // ДЕЙСТВИЯ
        // Отдельное исключение для "Действий" - Принудительный title tooltip
        if (item.type === 'actions' && tooltip == false) {
            title = item.label
        }

        // Изменить 'Действия' на i.class Иконку // Иконка добавлена в settings потока dt
        if (unit.is(':empty') && configs.icon) {

            // console.log(item);

            let icon = $('<i></i>');
            if (configs.icon.indexOf(' ') >= 0) {
                icon.attr({class: configs.icon});
            } else {
                icon.attr({'data-feather': configs.icon});
            }
            unit.html(icon);
        }

        unit.attr({
            'data-bs-toggle': 'tooltip',
            'data-bs-placement': 'bottom',
            'title': title
        });

    });
});

// Скрытие полей поиска (При изменении ширины таблицы. Работает на родных функциях DT) - ПЕРЕДЕЛАТЬ!
DataTable.Api.register('aGrid.fn.hideSearchInputs', function (columns) {
    // console.log(table.columns().visible())

    let col_search = $('.aGrid-container table thead');
    let row_filters = col_search.children('tr').eq(1);
    // let row_filters = col_search.children('tr.aGrid-filters');

    // console.log(columns)
    // console.log(row_filters.children('th').eq(1))

    // Поиск на скрытие/показ
    columns.forEach(function (item, i) {
        let row = row_filters.children(`th[data-visible='${i}']`);
        (item === false) ? row.hide() : row.show();
    });
});

// Рендер данных в таблице
DataTable.Api.register('aGrid.fn.render', function (data, type, row, meta) {
    // Проверка на display
    if (type === 'display') {

        let render;

        // Основные параметры
        let unit = meta.settings.aoColumns[meta.col];

        // Кастомные параметры Grid (Все кастомные параметры тут !!!)
        let configs = unit.configs;

        // console.log(data);
        // console.log(type);
        // console.log(row);
        // console.log(meta);

        // console.log(configs); // было

        // Проверка данных и возможности с ними работать
        if (data !== null && data !== '') {

            let div = $('<div class="render"></div>');
            let col_class = unit.sClass;
            let r_class = col_class ? col_class : '';
            let boostrap_attributes = {};

            // configs.popover;
            // configs.prefix;

            // Добавить префикс если он есть
            configs.prefix && (data = configs.prefix + data);

            // ### Boostrap js tools
            // Popover
            if (configs.popover) {
                // popover хранит в себе placement
                let popover_data;

                // Проверка
                if (data.toString().includes('"')) {
                    popover_data = data.replace(/"/g, "'");
                } else {
                    popover_data = data;
                }

                boostrap_attributes = {
                    // 'title' : '',
                    // 'data-bs-title' : '',
                    'data-bs-toggle': 'popover',
                    'data-bs-trigger': 'hover',
                    'data-bs-html': 'true',
                    'data-bs-custom-class': 'render-popover',
                    'data-bs-placement': configs.popover,
                    'data-bs-content': popover_data,
                };

            }

            // Типы render внутри DataGrid
            switch (configs.render) {

                // Простой блок без скрытия
                case 'simple':
                    render = div.attr(boostrap_attributes).addClass('render-default').html(data).prop('outerHTML');
                    break;

                // Вывод скрытого текста в столбце
                case 'text':

                    let truncate;

                    div.addClass('d-grid').attr(boostrap_attributes);

                    let children = $('<div></div>').html(data).children();
                    if (children.length > 0) {
                        truncate = children.addClass('d-block text-truncate').prop('outerHTML');
                    } else {
                        truncate = $('<span class="d-block text-truncate"></span>').html(data);
                    }

                    render = div.html(truncate).prop('outerHTML');
                    break;

                //
                case 'expand':
                    // fit-content можно использовать... но...
                    break;

                // Вывод поля ввода с столбце (WIP)
                case 'input' :
                    // let input = $('<input class="form-control render render-input" />')
                    //     .addClass(r_class)
                    //     .attr(boostrap_attributes)
                    //     .val(data);
                    // render = input.prop('outerHTML');
                    break;

                default:
                    render = data;
                    break;
            }
        } else {
            render = data;
        }

        // Если есть вложения или еще что то ... (Значки вложений) (WIP)
        // let other = unit.other;
        // if (other) {
        //
        //     // console.log(other.attach);
        //     // console.log(other);
        //
        //     render += '<span class="position-absolute top-0 end-0 badge badge-render bg-transparent text-muted">' +
        //         '<i data-feather="paperclip"></i>' +
        //         '<i data-feather="image"></i>' +
        //         '<i data-feather="codepen"></i>' +
        //         '</span>';
        // }

        return render;
    } else {
        return data;
    }
});

// Вызов контекстного меню для table tr (НЕ ПЕРЕДЕЛАНО!)
DataTable.Api.register('aGrid.contextMenu', function () {
    let grid = this;
    let table = this.table;

    $.contextMenu({
        selector: 'tbody tr',
        // selector: 'disable', // Временно отключил дроп список
        // selectableSubMenu: true,
        events: {
            preShow: function (options, e) {
                grid.rowSelected(options)

                // console.log(this)
                // console.log(options)
                this.mouseTarget = e.target;
            },
            show: function (options) {

            },
            hide: function (options) {

            },
            activated: function (options) {
                feather.replace();
            }
        },
        callback: function (key, options) {
            let r = 'clicked ' + key;
            // let r = 'clicked ' + options.name;

            // console.log(key)
            // console.log(options)
            // console.log(options.$trigger)

            // console.log(table.row(options.$trigger).data())

            // console.log(options.items)
            // console.log(options.commands[key].name)

            grid.notif('success', false, r)
        },
        items: {
            'look': {
                name: 'Смотреть',
                icon: function (opt, $item, itemKey, item) {
                    $item.html('<i class="feather-eye"></i>' + item.name);
                    return 'context-menu-feather';
                },
                callback: function (key, options, e) {
                    grid.openUrl(options.$trigger);
                    // console.log(key)
                    // console.log(options.$trigger)
                    // console.log(e)

                    // let data = table.row(t).data();
                    // let data = table.row(options.$trigger).data();
                    // console.log(data)
                }
            },
            'edit': {
                name: 'Редактировать',
                icon: function (opt, $item, itemKey, item) {
                    $item.html('<i class="feather-edit"></i>' + item.name);
                    return 'context-menu-feather';
                },
                callback: function (key, options, e) {
                    // var r = 'Нажата ' + key + ' ' + options.items[key].name;
                    // toastr['success']('', r, {});
                    grid.openUrl(options.$trigger, 'edit');
                }
            },
            'copy': {
                name: 'Копировать',
                icon: function (opt, $item, itemKey, item) {
                    let sub = $item.find('ul.context-menu-list').detach();
                    $item.html('<i class="feather-copy"></i>' + item.name).append(sub);
                    // или
                    // 0==$item.children("i").length&&$item.prepend('<i class="feather-share"></i>');

                    return 'context-menu-feather';
                },
                items: {
                    'col': {
                        name: 'Текст',
                        icon: function (opt, $item, itemKey, item) {
                            // $item.html('<i data-feather="columns"></i>' + item.name);
                            $item.html('<i data-feather="type"></i>' + item.name);
                            return 'context-menu-feather';
                        },
                        callback: function (key, options, e) {

                            let target = $(options.events.mouseTarget);
                            let text = target.text();

                            // Если зажат Ctrl то копировать вместе с заголовками столбцов
                            // if (e.ctrlKey) {
                            //     let columns = table.settings().init().columns;
                            //     let index = target.index();
                            //     text = columns[index].title + ': ' + text
                            // }

                            text = (text !== '') ? text : false;

                            if (text) {
                                grid.clipboardWrite(text);
                            } else {
                                grid.notif('warning', 'Внимание', 'Пустое поле')
                            }

                            // console.log(table.row(options.$trigger).data())

                        }
                    },
                    'row': {
                        name: 'Строка',
                        icon: function (opt, $item, itemKey, item) {
                            $item.html('<i data-feather="columns" class="rotate-90"></i>' + item.name);
                            return 'context-menu-feather';
                        },
                        callback: function (key, options, e) {

                            let row = table.row(options.$trigger).data();
                            let columns = table.settings().init().columns;

                            let entries = Object.entries(row);
                            let arr = [];

                            entries.forEach(function (elem, index) {

                                let header = columns[index];
                                let text = elem[1];

                                if (header.data === 'action' || header.visible === false) {
                                    // ...
                                } else {
                                    let parseText = ($($.parseHTML(text)).text() !== '') ? $($.parseHTML(text)).text() : 'Не указано';
                                    // Если зажат Ctrl то копировать вместе с заголовками столбцов
                                    // if (e.ctrlKey) {
                                    //     parseText = header.title + ': ' + parseText
                                    // }

                                    parseText = header.title + ': ' + parseText;
                                    arr.push(parseText);
                                }
                            });

                            grid.clipboardWrite(arr.join(', '));
                        }
                    },
                }
            },
            'export': {
                name: 'Экспорт',
                icon: function (opt, $item, itemKey, item) {
                    let sub = $item.find('ul.context-menu-list').detach();

                    $item.html('<i class="feather-share"></i>' + item.name).append(sub);
                    // или
                    // 0==$item.children("i").length&&$item.prepend('<i class="feather-share"></i>');

                    return 'context-menu-feather';
                },
                items: {
                    'print': {
                        name: 'Распечатать',
                        icon: function (opt, $item, itemKey, item) {
                            $item.html('<i class="feather-printer"></i>' + item.name);
                            return 'context-menu-feather';
                        },
                    },
                    'save_csv': {
                        name: 'Сохранить в CSV',
                        icon: function (opt, $item, itemKey, item) {
                            $item.html('<i class="feather-file-text"></i>' + item.name);
                            return 'context-menu-feather';
                        },
                    },
                    'save_exl': {
                        name: 'Сохранить в Excel',
                        icon: function (opt, $item, itemKey, item) {
                            $item.html('<i class="feather-file"></i>' + item.name);
                            return 'context-menu-feather';
                        },
                    },
                    'save_pdf': {
                        name: 'Сохранить в PDF',
                        icon: function (opt, $item, itemKey, item) {
                            $item.html('<i class="feather-clipboard"></i>' + item.name);
                            return 'context-menu-feather';
                        },
                    },
                    'copy': {
                        name: 'Скопировать',
                        icon: function (opt, $item, itemKey, item) {
                            $item.html('<i class="feather-copy"></i>' + item.name);
                            return 'context-menu-feather';
                        },
                    },
                }
            },
            // fold1: {
            //     name: 'Sub Group',
            //     items: {
            //         'Foo Bar': {name: 'Foo bar'},
            //         fold1a: {
            //             name: 'Other group',
            //             items: {
            //                 Echo: {name: 'echo'},
            //                 Foxtrot: {name: 'foxtrot'},
            //                 Golf: {name: 'golf'}
            //             }
            //         }
            //     }
            // }
        }
    });
});

/* ************************************************************************ */

/* МОДИФИКАТОРЫ - mods */
// Инициализация popover modifiers
DataTable.Api.register('aGrid.mods', function () {
    let api = this;
    api.aGrid.mods.$ = {
        button: $('.aGrid-mods-button'), // Кнопка
    };

    // Список для popover модификаторов
    let dropContent = $.get({url: api.aGrid.mods.$.button.data('content'), async: false}).responseText;
    api.aGrid.mods.$.dropContent = $(dropContent);

    // Проверка индикатора модифицирования
    api.aGrid.mods.fn.check();

    // Отключение прокрутки страницы при наведении на таблицу aGrid
    api.aGrid.mods.list.blocked();

    // Активация быстрого просмотра для таблицы aGrid
    api.aGrid.mods.list.visible();

    // Клик по кнопке
    api.aGrid.mods.$.button.on('click', function (event) {
        // $('body').on('click', '.aGrid-mods-button', function (event) {

        // Создаем popover для aGrid-mods и выводим в нем список переключалок
        const popover = api.aGrid.mods.$.button.popover({
            title: api.aGrid.mods.$.button.attr('title'),
            content: api.aGrid.mods.$.dropContent,
            // container: grid.container.parents(),
            html: true,
            sanitize: false,
            offset: [0, 10],
            customClass: 'aGrid-mods search-options-popover',
            trigger: 'manual',
        });

        popover.popover('toggle');

        // Показ popover
        // grid.button.on('show.bs.popover', function () {});

        // Скрыть popover
        api.aGrid.mods.$.button.on('hidden.bs.popover', function () {
            api.aGrid.mods.$.button.popover('dispose')
        });

    });

    // Событие если input будет изменен
    $(document).on('change', '.aGrid-mods li input', function (event) {
        api.aGrid.mods.fn.check(event);
        api.aGrid.mods.list.blocked(event);
        api.aGrid.mods.list.visible(event);

        // api.aGrid.modsActions(event.currentTarget.name, event);
    });

    // return api.aGrid.mods.$;
});

// ### ПРОВЕРКА МОДОВ - Проверка индикаторов модифицирования и запись в локальный кеш при изменении
DataTable.Api.register('aGrid.mods.fn.check', function (event = false) {
    let api = this;

    let module = api.aGrid.$.module;
    let modules = api.aGrid.$.store.namespace('modules');

    if (event) {
        modules.add(module, {[event.currentTarget.name]: event.currentTarget.checked})
    } else {
        $.each(modules.get(module), function (index, value) {
            let selector = '*[name="' + index + '"]';
            api.aGrid.mods.$.dropContent = api.aGrid.mods.$.dropContent.find(selector).attr('checked', value).end();
        });
    }

    // Проверка true в объекте для индикатора кнопки
    if (modules.get(module) && Object.values(modules.get(module)).includes(true)) {
        api.aGrid.$.container.addClass('mods');
    } else {
        api.aGrid.$.container.removeClass('mods');
    }
});

// ### СПИСОК МОДОВ
// МОД - Отключение прокрутки страницы при наведении на таблицу grid
DataTable.Api.register('aGrid.mods.list.blocked', function (event = false) {
    let api = this;
    // https://jsbin.com/teqoqib/6/edit?html,js,output

    // if (event.currentTarget.name == 'blocked') {}

    let module = api.aGrid.$.module;
    let modules = api.aGrid.$.store.namespace('modules');

    let checked = modules.get(module)?.blocked; // event.currentTarget.checked
    api.aGrid.$.container.toggleClass('blocked', !!(checked));

    // Обработчик отключения прокрутки страницы
    let disabledScroll = (e) => {
        e.preventDefault()
    };

    // Обработка наведения на init блок - Принудительно если галочка стоит то :
    api.aGrid.$.container
        .mouseenter(function () {
            // Остановить
            if (modules.get(module)?.blocked) {
                document.addEventListener('mousewheel', disabledScroll, {passive: false})
            }
        })
        .mouseleave(function () {
            // Возобновить
            document.removeEventListener('mousewheel', disabledScroll, {passive: false})
        });

    // grid.container.unbind("mouseenter");
    // grid.container.unbind("mouseleave");
});

// МОД - Включение быстрого просмотра строки
DataTable.Api.register('aGrid.mods.list.visible', function (event = false) {
    let api = this;

    // if (event.currentTarget.name == 'visible') {}

    let module = api.aGrid.$.module;
    let modules = api.aGrid.$.store.namespace('modules');

    // if (event) {}

    // Нумерация
    let checked = modules.get(module)?.visible; // event.currentTarget.checked
    api.aGrid.mods.list.visible.actions.colsNum(!!(checked))

    let selector = 'tr td:last-child';
    // $(document)
    $('tbody')
        .on('mouseenter', selector, function (event) {
            if (modules.get(module)?.visible) {
                $(this).popover('show');
            }
        })
        .on('mouseleave', selector, function (event) {
            if (modules.get(module)?.visible) {
                $(this).popover('hide');
            }
        });
});

// ### ДЕЙСТВИЯ МОДОВ
// ДЕЙСТВИЕ - "Быстрый просмотр строки"
DataTable.Api.register('aGrid.mods.list.visible.actions.quickRow', function (tr) {
    let table = this;
    let row = table.row(tr);

    let r_data = row.data(); // table.row( this ).data()
    let r_index = (r_data.id) ? r_data.id : row.index(); // table.row( this ).index()

    let columns = table.settings().init().columns; // Более гибкий вариант получения колонок
    let content = '';
    const entries = Object.entries(r_data);

    // Массив игнорирования (Столбцы с этими типами будут игнорироваться при создании строк карточки)
    let ignore = _OPTIONS.ignore;

    // Собираем блок div
    content += '<div class="qq-view-item">';

    // Собираем строки
    let counter = 0;
    entries.forEach(function (elem, index) {

        // let id = elem[0];
        let header = columns[index];
        let text = elem[1];

        if (ignore.includes(header.configs.type) || header.visible === false) {
            content += ''
        } else {
            counter++;
            content +=
                '<div class="mt-1">' +
                '<div class="avatar float-start bg-light-primary rounded me-1">' +
                '<div class="avatar-content">' + counter + '</div></div>' +
                '<div class="more-info">' +
                '<small class="hr-text hr-text-left my-50">' + header.title + '</small>' +
                '<h6 class="mb-0">' + ((text !== null) ? text : '') + '</h6>' +
                // '<div class="hr-text my-50"></div>' +
                '</div></div>';
        }

    });
    content += '</div>';

    // Создаем popover
    const popover = $(tr).children('td:last-child').popover({
        // const popover = $(tr).children('td:first-child').popover({
        // title: 'Карточка строки #' + r_index,
        title: 'Карточка строки #' + r_index,
        content: content,
        // selector: '.aGrid-container table.table tbody tr td:last-child',
        html: true,
        sanitize: false,
        // offset: [0, 10],
        customClass: 'qq-view',
        // trigger: 'click',
        // trigger: 'hover',
        trigger: 'manual',
        placement: 'left',
    });

    // Активатор при наведении
    // $(tr).children('td:last-child').hover(function (event) {
    //     // if (event.ctrlKey) {
    //     //     popover.popover('toggle');
    //     // }
    //     popover.popover('toggle');
    // });

    // popover.on('show.bs.popover', function (event) {
    //     // alert('The popover is about to be shown.');
    //     log('The popover is about to be shown.');
    // });
    // $("[data-toggle='popover']").on('shown.bs.popover', function(){
    //     alert('The popover is now fully shown.');
    // });
    // $("[data-toggle='popover']").on('hide.bs.popover', function(){
    //     alert('The popover is about to be hidden.');
    // });
    // $("[data-toggle='popover']").on('hidden.bs.popover', function(){
    //     alert('The popover is now hidden.');
    // });
});

// ДЕЙСТВИЕ - Нумерация столбцов таблицы
DataTable.Api.register('aGrid.mods.list.visible.actions.colsNum', function (param) {
    let api = this;

    // Массив игнорирования (Столбцы с этими типами будут игнорироваться при создании строк карточки)
    let ignore = _OPTIONS.ignore;

    $('.dataTables_scrollHeadInner .aGrid-titles th').each(function (index, unit) {

        let data = $(unit).data();
        if (!ignore.includes(data.type)) {
            switch (param) {
                case true:
                    let unit = $('<span class="col-number"></span>').text(index + 1);

                    // if ($(this).attr('data-type') !== 'actions') {
                    $(this).prepend(unit);
                    // }
                    break;

                case false:
                    $(this).children('.col-number').remove();
                    break;
            }
        }
    });
});

/* ************************************************************************ */

// ### Bootstrap
// Перезагрузка всех Bootstrap элементов на странице
DataTable.Api.register('aGrid.ui.bootstrapReload', function () {
    // Удалить все старые tooltip'ы и popover'ы
    $('.tooltip').detach();
    $('.popover').detach();

    // $('[data-toggle="tooltip"]').tooltip();
    // $('[data-bs-toggle="popover"]').popover();

    // tooltips
    let tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    let tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // popovers
    let popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    let popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
});

// bootstrap Popover
// bootstrapPopover() {}

/* ************************************************************************ */

// Установка tr.selected (Выделение строки - Фон строки) - Возможно не НУЖНА!
// rowSelected(t, param = false) {
//     let table = this.table;
//     let row = $(t);
//
//     // Не выделять строку если таблица пуста
//     if (row.children().hasClass('dataTables_empty')) {
//         return false
//     }
//
//     // Выделять если таблица не пуста
//     if (!row.hasClass('selected') && !row.hasClass('child')) {
//         table.$('tr.selected').toggleClass('selected');
//         row.toggleClass('selected');
//     }
// }

// Управление Loader'ером
DataTable.Api.register('aGrid.ui.loader', function (display = false) {
    let loading = $('.dataTables_processing.dts_loading');
    let h_dataTables_scrollBody = $('.dataTables_scrollBody').outerHeight();

    loading.css({
        // top: 'auto',
        height: h_dataTables_scrollBody,
        display: display ? 'block' : 'none'
    });
});

// Управление select2'ом
DataTable.Api.register('aGrid.ui.select2', function (unit, text = 'Нет результатов') {
    unit.select2({
        containerCssClass: 'select-sm',
        dropdownCssClass: '',
        dropdownAutoWidth: true, // Автоматическая ширина выпадающего списка
        language: {
            noResults: function () {
                return '<div class="alert alert-danger" role="alert"><div class="alert-body text-center">' + text + '</div></div>';
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });
});

// Работа с пустой таблицей (Если в таблице нет данных)
DataTable.Api.register('aGrid.ui.emptyTable', function (rows) {
    // let table = $(this.table);
    // if (rows === 0) {
    //     table.addClass('h-100 overflow-hidden');
    // } else {
    //     table.removeClass('h-100 overflow-hidden');
    // }
});

/* ************************************************************************ */

/* УТИЛИТЫ - utils */
// config - ПЕРЕДЕЛАТЬ!
DataTable.Api.register('aGrid.utils.config', function (param, module = false) {

    // let modules = store.namespace('modules');
    // let uri;
    //
    // if (module) {
    //     uri = module
    // } else {
    //     uri = window.location.href.split('/')[5];
    // }
    //
    // // WIP
    // if (param === 'uri') {
    //     return window.location.href.split('/')[5];
    // }
    //
    // return modules.get(uri)?.[param];
});

// Уведомления через toastr
DataTable.Api.register('aGrid.utils.notif', function (type = 'success', header = '', message = '') {
    toastr[type](message, header, {
        positionClass: "toast-bottom-right",
    });
});

// Переход по ссылкам на JS
DataTable.Api.register('aGrid.utils.openUrl', function (t, param = false) {
    let data = this.table.row(t).data();

    // #1
    // Полный url из uri
    // let path = location.pathname.substring(1).split('/');
    // let arr = [ path[0] , path[1] , path[2], data.id ];

    // или только ID
    // let arr = [data.id];
    //
    // if (param) {
    //     arr.push(param)
    // }

    // window.open(arr.join('/')).focus();
    // location.href = arr.join('/');

    // #2
    let url;
    let actions = $(data.action);

    if ($(actions[0]).attr('href')) {
        // log(url)
        url = $(actions[0]).attr('href');
    } else {
        // log($(actions[0]).children('a').attr('href'))
        url = $(actions[0]).children('a').attr('href')
    }
    window.open(url).focus();
    return false;
});

// Работа с Буфером обмена
// Пишем
DataTable.Api.register('aGrid.utils.clipboard.write', function (text) {
    navigator.clipboard.writeText(text)
        .then(() => {
            this.notif('success', 'Скопировано', text)
        })
        .catch(err => {
            console.log('Что-то пошло не так', err);
        });
});

// Читаем текст, прочитанный из буфера обмена
DataTable.Api.register('aGrid.utils.clipboard.read', function () {
    navigator.clipboard.readText()
        .then(text => {
            // `text` содержит текст, прочитанный из буфера обмена
        })
        .catch(err => {
            // возможно, пользователь не дал разрешение на чтение данных из буфера обмена
            console.log('Что-то пошло не так', err);
        });
});

// Лог
DataTable.Api.register('aGrid.utils.log', function (data) {
    console.log(data)
});

// DataTable.Api.register('aGrid.', function () {});

// DataTable.Api.register();
// DataTable.Api.extend();

// Параметры до загрузки Grid
// $.extend(true, $.fn.dataTable.defaults, {});
// $.extend(true, $.fn.dataTable.Api, {
//     'aaaaaaaaaaaaa': 'aaaaaaaaaaaaaaaaaa'
// })
// console.log($.fn.dataTable())


$(document).ready(function () {

    // Используем PerfectScrollbar
    let ps = new PerfectScrollbar('.dataTables_scrollBody', {
        // suppressScrollX: true,
        // wheelPropagation: false, // Стандартные методы отключения прокрутки страницы вверх
        wheelSpeed: 0.3, // от 0.2 до 0.4 нормально грузит (0.3 оптимально вроде...)
        minScrollbarLength: 50,
    });

});