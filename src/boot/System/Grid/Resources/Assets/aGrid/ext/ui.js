import { _OPTIONS } from "../options.js";

DataTable.Api.register('aGrid.ui', function () {
    let api = this;

    // Вызов контекстного меню для table tr
    // api.aGrid.ui.contextMenu();

    api.aGrid.ui.table.scroll();

    return api.aGrid;
});

// ### Bootstrap
// Перезагрузка всех Bootstrap элементов на странице
DataTable.Api.register('aGrid.ui.bootstrap.reload', function () {
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

// Вызов контекстного меню для table tr (НЕ ПЕРЕДЕЛАНО!)
DataTable.Api.register('aGrid.ui.contextMenu', function () {
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

// Управление select2'ом
DataTable.Api.register('aGrid.ui.select2', function (unit, options = {}) {

    // Если есть Ajax ссылка
    if (options.hasOwnProperty('ajax') && options.ajax) {
        let ajax = {
            ajax: {
                url: options.ajax,
                dataType: 'json',
                type: "GET",
                // delay: 250,
                // quietMillis: 50,
                data: function (query) {
                    return {
                        query: query.term
                    };
                },
                processResults: function (data) {
                    return {
                        // results: data
                        results: $.map(data, function (item) {
                            // console.log(item)
                            return {
                                // text: item.text,
                                // id: item.id,
                                text: item,
                                id: item,
                                value: item,
                            }
                        })
                    };
                },
            }
        };
        options = {...options, ...ajax};
        // console.log(options);
    }

    // Параметры По умолчанию
    let $default = {
        text: 'Нет результатов',
        containerCssClass: 'select-sm',
        dropdownCssClass: '',
        minimumInputLength: 3,
        dropdownAutoWidth: true, // Автоматическая ширина выпадающего списка
        language: {
            inputTooShort: function(args) {
                // args.minimum is the minimum required length
                // args.input is the user-typed text
                return "Type more stuff";
            },
            inputTooLong: function(args) {
                // args.maximum is the maximum allowed length
                // args.input is the user-typed text
                return "You typed too much";
            },
            errorLoading: function() {
                return "Error loading results";
            },
            loadingMore: function() {
                return "Loading more results";
            },
            noResults: function() {
                return "No results found";
                // return '<div class="alert alert-danger" role="alert"><div class="alert-body text-center">' + text + '</div></div>';
            },
            searching: function() {
                return "Searching...";
            },
            maximumSelected: function(args) {
                // args.maximum is the maximum number of items the user may select
                return "Error loading results";
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    }
    options = {...$default, ...options};
    // console.log(options);

    unit.select2(options);
});

// Работа с пустой таблицей (Если в таблице нет данных)
DataTable.Api.register('aGrid.ui.table.empty', function (rows) {
    // let table = $(this.table);
    // if (rows === 0) {
    //     table.addClass('h-100 overflow-hidden');
    // } else {
    //     table.removeClass('h-100 overflow-hidden');
    // }
});

// Вызываем PerfectScrollbar на dataTables_scrollBody
DataTable.Api.register('aGrid.ui.table.scroll', function () {
    // Используем PerfectScrollbar
    let ps = new PerfectScrollbar('.dataTables_scrollBody', {
        // suppressScrollX: true,
        // wheelPropagation: false, // Стандартные методы отключения прокрутки страницы вверх
        wheelSpeed: 0.3, // от 0.2 до 0.4 нормально грузит (0.3 оптимально вроде...)
        minScrollbarLength: 50,
    });
});
