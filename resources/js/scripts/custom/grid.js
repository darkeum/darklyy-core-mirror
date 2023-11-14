'use strict';

class Grid {

    constructor(table) {
        this.table = table; // Текущая таблица всегда доступна в Классе
        this.options = {
            ignore : ['actions', 'checkbox']
        };
    }

    // api.settings()[0].aoColumns // Обращение к настройкам

    // Загрузка Grid функций (Boot loader)
    boot() {

        // Перенос элементов управления (В новое место)
        this.appendTools();

        // Вызов контекстного меню для table tr
        // this.contextMenu();

        // Создаем popover для g-modifier
        this.modifiers();

    }

    // СОЗДАНИЕ ТАБЛИЦЫ
    // Перенос кнопок управления в header
    appendTools() {
        let grid = $('.grid-container');
        let search = grid.find('.dataTables_filter');
        let search_id = search.attr('id');
        let search_class = search.attr('class');
        let search_text = $(search.children('label')).text();
        let search_input = $(search.children('label').children('input'));

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
        toolbar.find('#g-search').attr('id', search_id).addClass(search_class).append(search_input);
        toolbar.find('#g-control').append(buttons.children());

        // toolbar.find('#g-control').find('.btn').attr({
        //     'data-bs-toggle' : 'modal',
        //     'data-bs-target' : '#searchB',
        // });

    }

    // Установка tooltip для столбцов со сменой текста при сортировке столбца так же установка шестерни для действий
    setTitles(columns) {
        columns.forEach(function (item, index) {

            let unit = $(item.nTh); // Если settings или item; // Если api.columns()

            let labelSort = unit.attr('aria-label');
            let label = (labelSort) ? labelSort : item.label;

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
    modifiers() {
        let grid = this;
        let body = $('body');
        let unit = $('#g-modifier');
        let title = unit.attr('title');

        body.on('click', '#g-modifier', function (event) {

            // Получаем html код из блока
            let content = $('#g-modifiers').html();

            // Создаем popover
            const popover = unit.popover({
                title: title,
                content: content,
                html: true,
                sanitize: false,
                offset: [0, 10],
                customClass: 'g-modifiers search-options-popover',
                trigger: 'manual',
                placement: 'bottom',
            });

            popover.popover('toggle');

            // show
            unit.on('show.bs.popover', function () {

            });

            // hide
            unit.on('hidden.bs.popover', function () {
                unit.popover('dispose')
            });

        });

        // Отключение прокрутки страницы при наведении на таблицу grid
        this.modifierBlocked();

        // Активация быстрого просмотра для таблицы grid
        this.modifierVisible();

        // Проверка индикатора модифицирования
        this.modifiersCheck();
        $(document).on('change', '.g-modifiers li input', function (event) {
            grid.modifiersCheck();
        });
    }

    // Проверка индикатора модифицирования
    modifiersCheck() {
        let grid = this;
        let modules = store.namespace('modules');
        let uri = window.location.href.split('/')[5];
        let config = modules.get(uri);

        if (!config) {
            return false
        }

        // массив значений конфига
        let arr = Object.values(modules.get(uri));
        let even = (element) => element === true;

        if (arr.some(even)) {
            $('.grid-container').addClass('modifier')
        } else {
            $('.grid-container').removeClass('modifier')
        }

    }

    // ###############################################

    // Модификаторы - Список #########################
    // Отключение прокрутки страницы при наведении на таблицу grid
    modifierBlocked() {

        // https://jsbin.com/teqoqib/6/edit?html,js,output

        // init - Блока Инициализации по наведению
        let container = $('.grid-container');
        let selector = '*[data-table="blocked"]';
        let block = $(selector);

        let modules = store.namespace('modules');
        let uri = window.location.href.split('/')[5];

        let checked = modules.get(uri)?.blocked;

        // Проверка Галочки фиксации скролла
        block.attr('checked', checked);
        (checked) ? container.toggleClass("blocked", checked) : false;

        // Обработчик отключения прокрутки страницы
        let disabledScrollHandler = (e) => {
            e.preventDefault()
        }

        // Работа с локальным кэшем + Обработка изменения чекбокса
        $(document).on('change', selector, function (event, checked) {
            modules.add(uri, {'blocked': event.currentTarget.checked})
            block.attr('checked', event.currentTarget.checked);
            container.toggleClass("blocked", checked);

            if (modules.get(uri)?.blocked) {
                document.addEventListener('mousewheel', disabledScrollHandler, {passive: false})
            } else {
                document.removeEventListener('mousewheel', disabledScrollHandler, {passive: false})
            }
        });

        // Обработка наведения на init блок - Принудительно если галочка стоит то :
        container.mouseenter(function () {
            // log('Остановить')
            if (block.prop('checked')) {
                document.addEventListener('mousewheel', disabledScrollHandler, {passive: false})
            }
        }).mouseleave(function () {
            // log('Возобновить')
            if (block.prop('checked')) {
                document.removeEventListener('mousewheel', disabledScrollHandler, {passive: false})
            }
        });
    }

    // Включение быстрого просмотра строки
    modifierVisible() {

        let $this = this;
        let selector = '*[data-table="visible"]';
        let block = $(selector);

        let modules = store.namespace('modules');
        let uri = window.location.href.split('/')[5];
        let checked = modules.get(uri)?.visible;

        block.attr('checked', checked);
        if (checked) {
            $this.tableNumbering('add');
        }

        // Работа с локальным кэшем + Обработка изменения чекбокса
        $(document).on('change', selector, function (event) {
            modules.add(uri, {'visible': event.currentTarget.checked})
            block.attr('checked', event.currentTarget.checked);

            if (event.currentTarget.checked) {
                $this.tableNumbering('add')
            } else {
                $this.tableNumbering('del')
            }

        });

        // let table = this.table;

        // $(document)
        $('tbody')
            .on('mouseenter', 'tr td:last-child', function (event) {
                if (modules.get(uri)?.visible) {
                    // $(this).popover('toggle');
                    $(this).popover('show');
                }

                // Пример
                // var table = $(this).parents('table');
                // var rowIdx = $(table).DataTable().row( this ).index();
                // console.log(rowIdx)

                // console.log(table.row( this ).index())

            })
            .on('mouseleave', 'tr td:last-child', function (event) {
                if (modules.get(uri)?.visible) {
                    // $(this).popover('toggle');
                    $(this).popover('hide');
                }
            });
    }

    // Нумерация столбцов таблицы
    tableNumbering(param) {

        // Массив игнорирования (Столбцы с этими типами будут игнорироваться при создании строк карточки)
        let ignore = this.options.ignore;
        let counter = 0;

        $('.dataTables_scrollHeadInner .grid-titles th').each(function (index, unit) {

            let data = $(unit).data();
            if (!ignore.includes(data.type)) {
                switch (param) {
                    case 'add':
                        counter++;
                        let unit = $('<span class="col-number"></span>').text(counter);

                        // if ($(this).attr('data-type') !== 'actions') {
                            $(this).prepend(unit);
                        // }
                        break;

                    case 'del':
                        $(this).children('.col-number').remove();
                        break;

                    default:
                        return false;
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
            // selector: '.grid-container table.table tbody tr td:last-child',
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

        let col_search = $('.grid-container table thead');
        let row_filters = col_search.children('tr').eq(1);
        // let row_filters = col_search.children('tr.grid-filters');

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

        // Проверка на display
        if (type === 'display') {

            // Проверка данных и возможности с ними работать
            if (data !== null && data !== '') {

                let col_class = unit.sClass;
                let r_class = col_class ? col_class : '';
                let boostrap_tools = '';

                // let popover = configs.popover;
                // let prefix = configs.prefix;

                // Добавить префикс если он есть
                data = configs.prefix + data;

                // ### Boostrap js tools
                // Popover
                if (configs.popover) {

                    let popover_title = '';
                    let popover_data = data.replace(/"/g, "'");
                    // let b_t_data = (rType !== 'default') ? data.replace(/"/g, "'") : '';
                    // popover хранит в себе placement

                    boostrap_tools += ' data-bs-toggle="popover"' +
                        ' title="' + popover_title + '"' +
                        // ' data-bs-title="' + popover_title + '"' +
                        ' data-bs-trigger="hover"' +
                        ' data-bs-html="true"' +
                        ' data-bs-custom-class="grid-popover"' +
                        ' data-bs-placement="' + configs.popover + '"' +
                        ' data-bs-content="' + popover_data + '"';
                }

                // Типы render внутри DataGrid
                switch (rType) {

                    // Простой блок без скрытия
                    case 'simple' :
                        render = '<div class="render"' + boostrap_tools + '>' + data + '</div>'
                        break;

                    case 'text':
                        render = '<div class="d-grid render"' + boostrap_tools + '><span class="d-block text-truncate">' + data + '</span></div>';
                        break;

                    case 'input' :
                        render = '<input class="form-control render ' + r_class + '"' + boostrap_tools + ' value="' + data + '" />'
                        break;

                    // case 'checkbox' :
                    //     // render = '<div class="form-check"><input class="form-check-input dt-checkboxes" type="checkbox" value="" id="checkbox' + data + '" /><label class="form-check-label" for="checkbox' + data + '"></label></div>'
                    //     render = '1313'
                    //     break;

                    // WIP (Тестовый вариант)
                    case 'expand':
                        render = '<div class="d-grid render expand"' + boostrap_tools + '>' +
                            '<span class="d-block text-truncate">' + data + '</span>' +
                            '</div>';
                        break;

                    // default:
                    //     return data;
                }
            }

            // Если есть вложения или еще что то ... (Значки вложений) WIP
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

    // Bootstrap
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
    bootstrapPopover() {

    }

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

    // test
    test() {

    }

    // ...
    log(data) {
        console.log(data)
    }

}