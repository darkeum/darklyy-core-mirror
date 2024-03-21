/*
*
* Класс для работы с DataTables
*
*/

'use strict';

class aGrid {

    constructor(table) {
        this.container = $('.aGrid-container'); // Контейнер
        this.module = this.container.data('moduleName'); // Название модуля
        this.table = table; // Текущая таблица всегда доступна в Классе
        this.options = {
            ignore: ['actions', 'checkbox']
        };
        this.store = store.namespace('aGrid'); // Система хранения в локальном кеше браузера для aGrid
        // this.dropContent = false; // Список для popover модификаторов

        // ПЕРЕДЕЛАТЬ МУЛЬТИ-ВЫЗОВ класса в layout.blade
    }

    // Загрузка Grid функций (Boot loader)
    boot() {

        // Запуск
        console.log('aGrid: Started, version 0.1');

        // Перенос элементов управления (В новое место)
        this.appendTools();

        // Вызов контекстного меню для table tr
        // this.contextMenu();

        // Инициализация aGrid-mods
        this.modsInit();

        console.log(this.store.getAll())
    }

    // LOAD GRID Callback's
    // #1 - preDrawCallback
    // preDrawCallback(data) {}

    // #2 - Загрузка параметров
    serverParams(data) {
        let grid = this;

        // ...
        data['order'].forEach(function (items, index) {
            data['order'][index]['column_name'] = data['columns'][items.column]['name'];
        });

        // ...
        grid.hideSearchInputs(grid.table.columns().responsiveHidden().toArray()); // Скрытие полей поиска
        grid.loader(true); // Show Loader

        // console.log(data)

        return data;
    }

    // #3 - Создание строк в таблице
    createdRow(row, data, dataIndex) {
        let grid = this;
        grid.qRow(row);
    }

    // #4 - Создание Header'а таблицы
    // headerCallback(thead, data, start, end, display) {}

    // #5 - Отрисовка - drawCallback
    drawCallback(settings) {
        let grid = this;

        // var api = this.api();
        // var rows = api.rows({ page: 'current' }).nodes();
        // var columns = api.columns({ page: 'current' }).nodes();
        // var columns = api.columns().nodes();

        let rows = grid.table.rows().count();
        let columns = settings.aoColumns;

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

        grid.setTitles(columns);
        grid.emptyTable(rows); // Работа с пустой таблицей
        feather.replace(); // Обновление иконок внутри таблицы
        grid.bootstrapReload(); // Перезагрузка всех Bootstrap элементов на странице
        grid.loader(false); // Hide Loader

    }

    // #6 - ...
    // stateLoadCallback () {},

    // #7 - ...
    // stateLoadParams (data) {},

    // #8 - Инициализация завершена
    // initComplete(settings, json) {}

    /* ************************************************************************ */
    // СОЗДАНИЕ ТАБЛИЦЫ
    // Перенос кнопок управления в header
    appendTools() {
        let grid = $('.aGrid-container');
        let search = grid.find('.dataTables_filter');
        let search_id = search.attr('id'); // Оптимизация
        let search_class = search.attr('class'); // Оптимизация
        let search_text = $(search.children('label')).text(); // Оптимизация
        let search_input = $(search.children('label').children('input')); // Оптимизация

        search_input.attr({
            'placeholder': search_text,
            // 'aria-label' : 'text',
            'aria-describedby': search_id
        }).addClass('form-control-sm');

        let buttons = grid.find('.dt-buttons');
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

    }

    // Установка tooltip для столбцов со сменой текста при сортировке столбца так же установка шестерни для действий
    // ПЕРЕДЕЛАТЬ !!!
    setTitles(columns) {
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
    }

    /* ************************************************************************ */

    // Модификаторы таблицы - Init ###################
    // Инициализация popover modifiers
    modsInit() {
        let grid = this;

        this.button = $('.aGrid-mods-button'); // Кнопка
        // Список для popover модификаторов
        this.dropContent = $.get({url: this.button.data('content'), async: false}).responseText;
        this.dropContent = $(this.dropContent);

        // Проверка индикатора модифицирования
        this.modsCheck();

        // Отключение прокрутки страницы при наведении на таблицу aGrid
        this.modsBlocked();

        // Активация быстрого просмотра для таблицы aGrid
        this.modsVisible();

        // Клик по кнопке
        grid.button.on('click', function (event) {
            // $('body').on('click', '.aGrid-mods-button', function (event) {

            // Создаем popover для aGrid-mods и выводим в нем список переключалок
            const popover = grid.button.popover({
                title: grid.button.attr('title'),
                content: grid.dropContent,
                // container: grid.container.parents(),
                html: true,
                sanitize: false,
                offset: [0, 10],
                customClass: 'aGrid-mods search-options-popover',
                trigger: 'manual',
            });

            popover.popover('toggle');

            // Показать popover
            // grid.button.on('show.bs.popover', function () {});

            // Скрыть popover
            grid.button.on('hidden.bs.popover', function () {
                grid.button.popover('dispose')
            });

        });

        // Событие если input будет изменен
        $(document).on('change', '.aGrid-mods li input', function (event) {
            grid.modsCheck(event);
            grid.modsBlocked(event);
            grid.modsVisible(event);

            // grid.modsActions(event.currentTarget.name, event);
        });
    }

    // Проверка индикаторов модифицирования и запись в локальный кеш при изменении
    modsCheck(event = false) {
        let grid = this;
        let modules = grid.store.namespace('modules');

        if (event) {
            modules.add(this.module, {[event.currentTarget.name]: event.currentTarget.checked})
        } else {
            $.each(modules.get(this.module), function (index, value) {
                let selector = '*[name="' + index + '"]';
                grid.dropContent = grid.dropContent.find(selector).attr('checked', value).end();
            });
        }

        // Проверка true в объекте для индикатора кнопки
        if (modules.get(this.module) && Object.values(modules.get(this.module)).includes(true)) {
            grid.container.addClass('mods');
        } else {
            grid.container.removeClass('mods');
        }
    }

    // ###############################################

    // modsActions(action, event = false) {}

    // ### МОДИФИКАТОРЫ - МОДЫ - СПИСОК
    // МОД - Отключение прокрутки страницы при наведении на таблицу grid
    modsBlocked(event = false) {
        // https://jsbin.com/teqoqib/6/edit?html,js,output

        // if (event.currentTarget.name == 'blocked') {}
        let grid = this;
        let modules = grid.store.namespace('modules');

        let checked = modules.get(grid.module)?.blocked; // event.currentTarget.checked
        grid.container.toggleClass('blocked', !!(checked));

        // Обработчик отключения прокрутки страницы
        let disabledScroll = (e) => {
            e.preventDefault()
        };

        // Обработка наведения на init блок - Принудительно если галочка стоит то :
        grid.container
            .mouseenter(function () {
                // Остановить
                if (modules.get(grid.module)?.blocked) {
                    document.addEventListener('mousewheel', disabledScroll, {passive: false})
                }
            })
            .mouseleave(function () {
                // Возобновить
                document.removeEventListener('mousewheel', disabledScroll, {passive: false})
            });

        // grid.container.unbind("mouseenter");
        // grid.container.unbind("mouseleave");
    }

    // МОД - Включение быстрого просмотра строки
    modsVisible(event = false) {

        // if (event.currentTarget.name == 'visible') {}
        let grid = this;
        let modules = grid.store.namespace('modules');

        // if (event) {}

        // Нумерация
        let checked = modules.get(this.module)?.visible; // event.currentTarget.checked
        grid.modsColsNum(!!(checked))

        let selector = 'tr td:last-child';
        // $(document)
        $('tbody')
            .on('mouseenter', selector, function (event) {
                if (modules.get(grid.module)?.visible) {
                    $(this).popover('show');
                }
            })
            .on('mouseleave', selector, function (event) {
                if (modules.get(grid.module)?.visible) {
                    $(this).popover('hide');
                }
            });
    }

    // МОД - Нумерация столбцов таблицы
    modsColsNum(param) {

        // Массив игнорирования (Столбцы с этими типами будут игнорироваться при создании строк карточки)
        let ignore = this.options.ignore;

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
    }

    // ###############################################

    // "Быстрый просмотр строки"
    qRow(tr) {
        let table = this.table;

        let row = table.row(tr);

        let r_data = row.data(); // table.row( this ).data()
        let r_index = (r_data.id) ? r_data.id : row.index(); // table.row( this ).index()

        let columns = table.settings().init().columns; // Более гибкий вариант получения колонок
        let content = '';
        const entries = Object.entries(r_data);

        // Массив игнорирования (Столбцы с этими типами будут игнорироваться при создании строк карточки)
        let ignore = this.options.ignore;

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

    }

    // Вызов контекстного меню для table tr
    contextMenu() {

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
    }

    // Скрытие полей поиска (При изменении ширины таблицы. Работает на родных функциях DT)
    hideSearchInputs(columns) {

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

    }

    // Рендер данных в таблице
    render(data, type, row, meta, rType = null) {

        // Основные параметры
        let unit = meta.settings.aoColumns[meta.col];

        // Кастомные параметры Grid (Все кастомные параметры тут !!!)
        let configs = unit.configs;

        let render = '';

        // console.log(data);
        // console.log(type);
        // console.log(row);
        // console.log(meta);
        console.log(configs);

        // Проверка на display
        if (type === 'display') {

            // Проверка данных и возможности с ними работать
            if (data !== null && data !== '') {

                let col_class = unit.sClass;
                let r_class = col_class ? col_class : '';
                let boostrap_tools = '';

                // configs.popover;
                // configs.prefix;

                // Добавить префикс если он есть
                data = configs.prefix + data;

                // ### Boostrap js tools
                // Popover
                if (configs.popover) {

                    let popover_data = data.replace(/"/g, "'");
                    // let b_t_data = (rType !== 'default') ? data.replace(/"/g, "'") : '';
                    // popover хранит в себе placement

                    boostrap_tools += ' data-bs-toggle="popover"' +
                        // ' title=""' +
                        // ' data-bs-title=""' +
                        ' data-bs-trigger="hover"' +
                        ' data-bs-html="true"' +
                        ' data-bs-custom-class="render-popover"' +
                        ' data-bs-placement="' + configs.popover + '"' +
                        ' data-bs-content="' + popover_data + '"';
                }

                // Типы render внутри DataGrid
                switch (rType) {

                    // Простой блок без скрытия
                    case 'simple':
                        render = '<div class="render render-default"' + boostrap_tools + '>' + data + '</div>'
                        break

                    // Вывод скрытого текста в столбце
                    case 'text':
                        render = '<div class="d-grid render render-text"' + boostrap_tools + '><span class="d-block text-truncate">' + data + '</span></div>';
                        break;

                    // Вывод поля ввода с столбце (WIP)
                    case 'input' :
                        render = '<input class="form-control render render-input ' + r_class + '"' + boostrap_tools + ' value="' + data + '" />'
                        break;

                    // Вывод чекбокса (WIP) А надо???
                    // case 'checkbox' :
                    //     // render = '<div class="form-check"><input class="form-check-input dt-checkboxes" type="checkbox" value="" id="checkbox' + data + '" /><label class="form-check-label" for="checkbox' + data + '"></label></div>'
                    //     render = '1313'
                    //     break;

                    // WIP (Тестовый вариант)
                    case 'expand':
                        render = '<div class="d-grid render render-expand expand"' + boostrap_tools + '>' +
                            '<span class="d-block text-truncate">' + data + '</span>' +
                            '</div>';
                        break;


                    default:
                        render = data;
                        break;
                }
            }

            // Если есть вложения или еще что то ... (Значки вложений) (WIP)
            let other = unit.other;
            if (other) {

                // console.log(other.attach);
                // console.log(other);

                render += '<span class="position-absolute top-0 end-0 badge badge-render bg-transparent text-muted">' +
                    '<i data-feather="paperclip"></i>' +
                    '<i data-feather="image"></i>' +
                    '<i data-feather="codepen"></i>' +
                    '</span>';
            }
        }

        // Возвращаем готовый div блок
        return render;
    }

    // Обработка клика по render (Response) WIP !!!
    renderExpand(t) {
        let table = this.table;
        let render = $(t);

        table.$('tr').not('.selected').find('.render span').addClass('text-truncate');
        render.toggleClass("text-truncate");

    }

    // ### Bootstrap
    // Перезагрузка всех Bootstrap элементов на странице
    bootstrapReload() {

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
    }

    // bootstrap Popover
    // bootstrapPopover() {}
    // ###############################################

    // Переход по ссылкам на JS
    openUrl(t, param = false) {

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
    }

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
    loader(display = false) {
        let loading = $('.dataTables_processing.dts_loading');
        let h_dataTables_scrollBody = $('.dataTables_scrollBody').outerHeight();

        loading.css({
            // top: 'auto',
            height: h_dataTables_scrollBody,
            display: display ? 'block' : 'none'
        });
    }

    // Управление select2'ом
    select2(unit, text = 'Нет результатов') {
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
    }

    // Работа с пустой таблицей (Если в таблице нет данных)
    emptyTable(rows) {
        let table = $(this.table);
        if (rows === 0) {
            table.addClass('h-100 overflow-hidden');
        } else {
            table.removeClass('h-100 overflow-hidden');
        }
    }

    // config
    config(param, module = false) {

        let modules = store.namespace('modules');
        let uri;

        if (module) {
            uri = module
        } else {
            uri = window.location.href.split('/')[5];
        }

        // WIP
        if (param === 'uri') {
            return window.location.href.split('/')[5];
        }

        return modules.get(uri)?.[param];

    }


    /* *** Вспомогательные функции *** */
    // Работа с Буфером обмена
    // Пишем
    clipboardWrite(text) {
        navigator.clipboard.writeText(text)
            .then(() => {
                this.notif('success', 'Скопировано', text)
            })
            .catch(err => {
                console.log('Что-то пошло не так', err);
            });
    }

    // Читаем
    clipboardRead() {
        navigator.clipboard.readText()
            .then(text => {
                // `text` содержит текст, прочитанный из буфера обмена
            })
            .catch(err => {
                // возможно, пользователь не дал разрешение на чтение данных из буфера обмена
                console.log('Что-то пошло не так', err);
            });
    }

    // Уведомления через toastr
    notif(type = 'success', header = '', message = '') {
        toastr[type](message, header, {
            positionClass: "toast-bottom-right",
        });
    }

    // ...
    log(data) {
        console.log(data)
    }

}