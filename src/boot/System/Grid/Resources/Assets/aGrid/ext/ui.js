/* УТИЛИТЫ - ui - Управление визуальными элементами */

import {_OPTIONS} from "../options.js";

// Основной метод инициализации UI утилит
DataTable.Api.register('aGrid.ui', function () {
    let api = this;

    // Инициализация скролла таблицы
    api.aGrid.ui.table.scroll();

    return api.aGrid;
});

// ### Bootstrap утилиты
// Перезагрузка всех Bootstrap элементов в контейнере aGrid
DataTable.Api.register('aGrid.ui.bootstrap.reload', function () {

    // Удалить лишние popover в контейнере
    $('.aGrid-container .popover.render-popover').remove();

    // Инициализация Bootstrap tooltips во всем контейнере aGrid
    $('.aGrid-container [data-bs-toggle="tooltip"]').tooltip();

    // Инициализация Bootstrap popovers во всем контейнере aGrid
    $('.aGrid-container [data-bs-toggle="popover"]').popover();
});

// Управление Loader'ом - отображение/скрытие индикатора загрузки
DataTable.Api.register('aGrid.ui.loader', function (display = false) {
    let loading = $('.dataTables_processing.dts_loading');
    let h_dataTables_scrollBody = $('.dataTables_scrollBody').outerHeight();

    // Настройка отображения и размеров лоадера
    loading.css({
        height: h_dataTables_scrollBody,
        display: display ? 'block' : 'none'
    });
});

// Контекстное меню для строк таблицы (НЕ ПЕРЕДЕЛАНО!)
DataTable.Api.register('aGrid.ui.contextMenu', function () {
    let grid = this;
    let table = this.table;

    // Инициализация контекстного меню для строк таблицы
    $.contextMenu({
        selector: 'tbody tr',
        events: {
            preShow: function (options, e) {
                grid.rowSelected(options) // Выделение строки при показе меню
                this.mouseTarget = e.target; // Сохранение целевого элемента
            },
            show: function (options) {
                // Логика при показе меню
            },
            hide: function (options) {
                // Логика при скрытии меню
            },
            activated: function (options) {
                feather.replace(); // Обновление feather иконок
            }
        },
        callback: function (key, options) {
            // Обработчик выбора пункта меню
            let r = 'clicked ' + key;
            grid.notif('success', false, r) // Уведомление о действии
        },
        items: {
            'look': {
                name: 'Смотреть',
                icon: function (opt, $item, itemKey, item) {
                    $item.html('<i class="feather-eye"></i>' + item.name);
                    return 'context-menu-feather';
                },
                callback: function (key, options, e) {
                    grid.openUrl(options.$trigger); // Открытие URL просмотра
                }
            },
            'edit': {
                name: 'Редактировать',
                icon: function (opt, $item, itemKey, item) {
                    $item.html('<i class="feather-edit"></i>' + item.name);
                    return 'context-menu-feather';
                },
                callback: function (key, options, e) {
                    grid.openUrl(options.$trigger, 'edit'); // Открытие URL редактирования
                }
            },
            'copy': {
                name: 'Копировать',
                icon: function (opt, $item, itemKey, item) {
                    let sub = $item.find('ul.context-menu-list').detach();
                    $item.html('<i class="feather-copy"></i>' + item.name).append(sub);
                    return 'context-menu-feather';
                },
                items: {
                    'col': {
                        name: 'Текст',
                        icon: function (opt, $item, itemKey, item) {
                            $item.html('<i data-feather="type"></i>' + item.name);
                            return 'context-menu-feather';
                        },
                        callback: function (key, options, e) {
                            // Копирование текста ячейки
                            let target = $(options.events.mouseTarget);
                            let text = target.text();

                            if (text) {
                                grid.clipboardWrite(text); // Запись в буфер обмена
                            } else {
                                grid.notif('warning', 'Внимание', 'Пустое поле')
                            }
                        }
                    },
                    'row': {
                        name: 'Строка',
                        icon: function (opt, $item, itemKey, item) {
                            $item.html('<i data-feather="columns" class="rotate-90"></i>' + item.name);
                            return 'context-menu-feather';
                        },
                        callback: function (key, options, e) {
                            // Копирование всей строки с заголовками
                            let row = table.row(options.$trigger).data();
                            let columns = table.settings().init().columns;

                            let entries = Object.entries(row);
                            let arr = [];

                            entries.forEach(function (elem, index) {
                                let header = columns[index];
                                let text = elem[1];

                                if (header.data === 'action' || header.visible === false) {
                                    // Пропуск служебных колонок
                                } else {
                                    let parseText = ($($.parseHTML(text)).text() !== '') ? $($.parseHTML(text)).text() : 'Не указано';
                                    parseText = header.title + ': ' + parseText;
                                    arr.push(parseText);
                                }
                            });

                            grid.clipboardWrite(arr.join(', ')); // Запись в буфер обмена
                        }
                    },
                }
            },
            'export': {
                name: 'Экспорт',
                icon: function (opt, $item, itemKey, item) {
                    let sub = $item.find('ul.context-menu-list').detach();
                    $item.html('<i class="feather-share"></i>' + item.name).append(sub);
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
        }
    });
});

// Управление select2 - кастомизация выпадающих списков
DataTable.Api.register('aGrid.ui.select2', function (unit, options = {}) {

    // Настройка AJAX для select2 если указан URL
    if (options.hasOwnProperty('url') && options.url) {
        let ajax = {
            ajax: {
                url: options.url,
                dataType: 'json',
                type: "GET",
                data: function (query) {
                    return {
                        query: query.term // Поисковый запрос
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item, // Отображаемый текст
                                id: item, // ID элемента
                                value: item, // Значение элемента
                            }
                        })
                    };
                },
            },
            minimumInputLength: 3, // Минимальная длина для поиска
        };
        options = {...options, ...ajax}; // Объединение опций
    }

    // Кастомные адаптеры для select2 (закомментированы)
    // $.fn.select2.amd.define('select2/selectAllAdapter', [...]); // Адаптер "Выбрать все"
    // $.fn.select2.amd.define('select2/selectAllAdapter2', [...]); // Второй адаптер "Выбрать все"
    // $.fn.select2.amd.define("SingleSelectSearchAdapter", [...]); // Адаптер для одиночного выбора

    // Параметры по умолчанию для select2
    let $default = {
        text: _OPTIONS.select2.noresults,
        width: '100%',
        sorter: function (data) {
            // Сортировка с приоритетом для элемента "Все"
            return data.sort(function (a, b) {
                if (a.id === _OPTIONS.select2.drop.all) return -1;
                if (b.id === _OPTIONS.select2.drop.all) return 1;
                return a.text.localeCompare(b.text);
            });
        },
        containerCssClass: 'form-select2-sm',
        dropdownCssClass: '',
        dropdownAutoWidth: true,
        language: {
            inputTooShort: function (args) {
                var remainingChars = args.minimum - args.input.length;
                var message = 'Пожалуйста введите ' + remainingChars + ' или больше символов';
                return message;
            },
            inputTooLong: function (args) {
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
            },
            searching: function () {
                return "Поиск...";
            },
            maximumSelected: function (args) {
                return "Ошибка загрузки результатов.";
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    }
    options = {...$default, ...options}; // Объединение с переданными опциями

    unit.select2(options); // Инициализация select2
});

// Работа с пустой таблицей (закомментировано)
DataTable.Api.register('aGrid.ui.table.empty', function (rows) {
    // Логика для пустой таблицы
});

// Инициализация PerfectScrollbar для контейнера скролла таблицы
DataTable.Api.register('aGrid.ui.table.scroll', function () {
    // Использование PerfectScrollbar для кастомного скролла
    let ps = new PerfectScrollbar('.dataTables_scrollBody', {
        wheelSpeed: 0.3, // Скорость прокрутки
        minScrollbarLength: 50, // Минимальная длина скроллбара
    });
});
