import {_OPTIONS} from "../options.js";

DataTable.Api.register('aGrid.ui', function () {
    let api = this;

    // Вызов контекстного меню для table tr
    // api.aGrid.ui.contextMenu();

    api.aGrid.ui.table.scroll();

    return api.aGrid;
});

// ### Bootstrap
// Перезагрузка всех Bootstrap элементов в контейнере aGrid
DataTable.Api.register('aGrid.ui.bootstrap.reload', function () {
    // Удалить все старые tooltip'ы и popover'ы
    // $('.tooltip').detach();
    // $('.popover').detach();

    // tooltips
    // let tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    // let tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
    $('.aGrid-container [data-bs-toggle="tooltip"]').tooltip();

    // popovers
    // let popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    // let popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
    $('.aGrid-container [data-bs-toggle="popover"]').popover();
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
    if (options.hasOwnProperty('url') && options.url) {
        let ajax = {
            ajax: {
                url: options.url,
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
            },
            minimumInputLength: 3,
        };
        options = {...options, ...ajax};
        // console.log(options);
    }

    /*
      Define the adapter so that it's reusable
*/
    $.fn.select2.amd.define('select2/selectAllAdapter', [
        'select2/utils',

        'select2/dropdown',
        'select2/dropdown/attachBody',
        'select2/dropdown/search',

        'select2/results',

        'select2/selection/base',
        'select2/selection/single',
        'select2/selection/multiple',
        'select2/selection/placeholder',
        'select2/selection/allowClear',
        'select2/selection/search',
        // 'select2/selection/selectionCss',
        'select2/selection/eventRelay',

    ], function (
        Utils,
        Dropdown,
        attachBody,
        search,
        results,
        Sbase,
        Ssingle,
        Smultiple,
        Splaceholder,
        SallowClear,
        Ssearch,
        // SselectionCss,
        SeventRelay,
    ) {

        // var CustomSelectionAdapter = Utils.Decorate(MultipleSelection, Placeholder);
        // CustomSelectionAdapter = Utils.Decorate(CustomSelectionAdapter, SelectionSearch);
        // CustomSelectionAdapter = Utils.Decorate(CustomSelectionAdapter, EventRelay);
        // return CustomSelectionAdapter;

        function SelectAll() {
        }

        SelectAll.prototype.render = function (decorated) {
            var self = this,
                $rendered = decorated.call(this),
                $selectAll = $(
                    '<li class="select2-results__option" id="__ALL_SELECT_GRID" role="option" aria-selected="true" data-select2-id="__ALL_SELECT_GRID">Все</li>'
                );

            // if (!this.$element.prop("multiple")) {
            //     // this isn't a multi-select -> don't add the buttons!
            //     return $rendered;
            // }

            console.log($rendered)

            // $rendered.find('.select2-results').prepend($selectAll);

            // $selectAll.on('click', function (e) {
            //     // var $results = $rendered.find('.select2-results__option[aria-selected=false]');
            //     var $results = $rendered.find('.select2-results__option');
            //     // $results.each(function () {
            //     //     self.trigger('select', {
            //     //         data: $(this).data('data')
            //     //     });
            //     // });
            //     // self.trigger('close');
            // });
            // $unselectAll.on('click', function (e) {
            //     var $results = $rendered.find('.select2-results__option[aria-selected=true]');
            //     $results.each(function () {
            //         self.trigger('unselect', {
            //             data: $(this).data('data')
            //         });
            //     });
            //     self.trigger('close');
            // });

            return $rendered;
        };

        return Utils.Decorate(
            Utils.Decorate(
                // Dropdown,
                // attachBody,
                // search,
                // results,

                Sbase,
                Ssingle,
                Smultiple,
                Splaceholder,
                SallowClear,
                Ssearch,
                // SselectionCss,
                SeventRelay,
            ),
            SelectAll
        );

    });

    $.fn.select2.amd.define('select2/selectAllAdapter2', [
        'jquery',
        // 'fmjs/amd/src/fm.timer',
        'select2/utils',
        'select2/dropdown',
        "select2/dropdown/attachBody",
        'select2/dropdown/attachContainer',
        "select2/dropdown/closeOnSelect",
        'select2/dropdown/search',
        'select2/dropdown/minimumResultsForSearch',
        // 'select2/src/js/jquery.select2',
    ], function ($, Utils, DropdownAdapter, AttachBody, AttachContainer, CloseOnSelect, DropdownSearch, MinimumResultsForSearch) {

        // var $selects = $('select');
        // var fm = new FM();

        var drop = Utils.Decorate(
            DropdownAdapter,
            DropdownSearch
        );

        drop.prototype.render = function () {
            let $rendered = DropdownAdapter.prototype.render.call(this);
            let $search = $(
                '<span class="select2-custom-header">' +
                // '<span class="select2-custom-header__title"><a>Все</a></span>' +
                '<span class="select2-custom-header__close"></span>' +
                '</span>' +
                '<span class="select2-search select2-search--dropdown">' +
                '<input class="select2-search__field" type="search" tabindex="-1"' +
                ' autocomplete="off" autocorrect="off" autocapitalize="none"' +
                ' spellcheck="false" role="textbox" />' +
                '</span>'
            );

            this.$searchContainer = $search;
            this.$search = $search.find('input');
            $rendered.prepend($search);

            // let li = $('<li></li>')
            // li.attr({
            //     'value': this.$element.children('option')[0].value
            // }).html(this.$element.children('option')[0].text);

            // this.$element = $element;
            // this.options = options;
            // var $dropdown = $(
            //     '<span class="select2-dropdown">' +
            //     '<span class="select2-results"></span>' +
            //     '</span>'
            // );
            // $dropdown.attr('dir', this.options.get('dir'));
            // this.$dropdown = $dropdown;

            // console.log($rendered);
            // console.log(this);

            return $rendered;
        }

        var dropdownAdapter = Utils.Decorate(
            drop,
            AttachContainer
        );

        dropdownAdapter = Utils.Decorate(dropdownAdapter, AttachBody, CloseOnSelect, MinimumResultsForSearch);

        var opts = {
            // theme: 'custom',
            // minimumResultsForSearch: 15,
            // dropdownAdapter: dropdownAdapter,
        };

        // $selects.each(function(index, el) {
        //     var $this = $(this);
        //     $this.select2($.extend(opts, {
        //         placeholder: $this.attr('placeholder')
        //     }));
        // });

        // Seems like we shouldn't have to do this ourselves,
        // but width: 'resolve' doesn't seem to work on resize
        // $(window).on('resize', fm.debounce(function() {
        //     var data = $selects.data('select2');
        //     if (data && data.$element) {
        //         var width = data.$element.width();
        //         $selects.select2($.extend(opts, { width: width }));
        //     }
        // }));

        return dropdownAdapter;
    });

    $.fn.select2.amd.define("SingleSelectSearchAdapter", [
            "select2/utils",
            "select2/dropdown",
            "select2/dropdown/attachBody",
            "select2/dropdown/attachContainer",
            "select2/dropdown/closeOnSelect",
            "select2/dropdown/minimumResultsForSearch",
            "select2/dropdown/search"
        ],
        function (Utils, Dropdown, AttachBody, AttachContainer, Search, CloseOnSelect, MinimumResultsForSearch) {

            // Decorate Dropdown with Search features
            let dropdownWithHeader = Utils.Decorate(Dropdown, Search);

            dropdownWithHeader.prototype.render = function () {
                let $rendered = Dropdown.prototype.render.call(this);

                let headerTitle = this.options.options.headerTitle ? this.options.options.headerTitle : "Sélectionnez une option";

                let $search = $(
                    '<span class="select2-custom-header">' +
                    // '<span class="select2-custom-header__title">' + headerTitle + '</span>' +
                    '<span class="select2-custom-header__close"></span>' +
                    '</span>' +
                    '<span class="select2-search select2-search--dropdown">' +
                    '<input class="select2-search__field" type="search" tabindex="-1"' +
                    ' autocomplete="off" autocorrect="off" autocapitalize="none"' +
                    ' spellcheck="false" role="textbox" />' +
                    '</span>'
                );

                this.$searchContainer = $search;
                this.$search = $search.find('input');

                $rendered.prepend($search);

                return $rendered;
            };

            // Decorate the dropdown+search with necessary containers
            let adapter = Utils.Decorate(dropdownWithHeader, AttachContainer);
            adapter = Utils.Decorate(adapter, AttachBody, CloseOnSelect, MinimumResultsForSearch);

            return adapter;
        }
    );

    // Параметры По умолчанию
    let $default = {
        text: 'Нет результатов',
        width: '100%',
        sorter: function(data) {
            // Сортируем по алфавиту но "Все" всегда первый
            return data.sort(function(a, b) {
                // Элемент с value = 0 всегда на первом месте
                if (a.id === _OPTIONS.select2.drop_all) return -1;
                if (b.id === _OPTIONS.select2.drop_all) return 1;

                // Остальные элементы сортируются по тексту
                return a.text.localeCompare(b.text);
            });
        },
        // dropdownParent: $('body'),
        // theme: 'custom',
        // tags: true,
        // placeholder: "Нажмите чтобы выбрать",
        // dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter2'),
        // dropdownAdapter: $.fn.select2.amd.require("SingleSelectSearchAdapter"),

        containerCssClass: 'form-select2-sm',
        dropdownCssClass: '',
        dropdownAutoWidth: true, // Автоматическая ширина выпадающего списка
        language: {
            inputTooShort: function (args) {
                // args.minimum is the minimum required length
                // args.input is the user-typed text
                var remainingChars = args.minimum - args.input.length;
                var message = 'Пожалуйста введите ' + remainingChars + ' или больше символов';
                // return "Введите больше символов";
                return message;
            },
            inputTooLong: function (args) {
                // args.maximum is the maximum allowed length
                // args.input is the user-typed text
                return "Вы набрали слишком много";
            },
            errorLoading: function () {
                return "Ошибка загрузки результатов";
            },
            loadingMore: function () {
                return "Загрузка дополнительных результатов";
            },
            noResults: function () {
                return "Результаты не найдены";
                // return '<div class="alert alert-danger" role="alert"><div class="alert-body text-center">' + text + '</div></div>';
            },
            searching: function () {
                return "Поиск...";
            },
            maximumSelected: function (args) {
                // args.maximum is the maximum number of items the user may select
                return "Ошибка загрузки результатов.";
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
