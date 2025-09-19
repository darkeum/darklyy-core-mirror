import {_OPTIONS} from "../options.js";

/* МОДИФИКАТОРЫ - mods */
// Инициализация popover modifiers - управление дополнительными функциями таблицы
DataTable.Api.register('aGrid.mods', function () {
    let button = $('.aGrid-mods-button');
    if (!button.length) {
        return false // Если кнопка модификаторов не найдена, выходим
    }

    let api = this;
    api.aGrid.mods.$ = {
        button: button
    };

    // Список для popover модификаторов - загрузка содержимого из data-атрибута
    let dropContent = $.get({url: api.aGrid.mods.$.button.data('content'), async: false}).responseText;
    api.aGrid.mods.$.dropContent = $(dropContent);

    // Проверка индикатора модифицирования
    api.aGrid.mods.fn.check();

    // Отключение прокрутки страницы при наведении на таблицу aGrid
    api.aGrid.mods.list.blocked();

    // Активация быстрого просмотра для таблицы aGrid
    api.aGrid.mods.list.visible();

    // Клик по кнопке модификаторов
    api.aGrid.mods.$.button.on('click', function (event) {
        // Создаем popover для aGrid-mods и выводим в нем список переключателей
        const popover = api.aGrid.mods.$.button.popover({
            title: api.aGrid.mods.$.button.attr('title'),
            content: api.aGrid.mods.$.dropContent,
            html: true,
            sanitize: false,
            offset: [0, 10],
            customClass: 'aGrid-mods search-options-popover',
            trigger: 'manual', // Ручное управление отображением
        });

        popover.popover('toggle'); // Переключение состояния popover

        // Скрытие popover при событии hidden
        api.aGrid.mods.$.button.on('hidden.bs.popover', function () {
            api.aGrid.mods.$.button.popover('dispose') // Уничтожение popover
        });

    });

    // Событие изменения состояния input в модификаторах
    $(document).on('change', '.aGrid-mods li input', function (event) {
        api.aGrid.mods.fn.check(event); // Проверка состояния
        api.aGrid.mods.list.blocked(event); // Обновление блокировки прокрутки
        api.aGrid.mods.list.visible(event); // Обновление быстрого просмотра

        // api.aGrid.modsActions(event.currentTarget.name, event);
    });

    return api.aGrid;
});

// ### ПРОВЕРКА МОДОВ - Проверка индикаторов модифицирования и запись в локальный кеш при изменении
DataTable.Api.register('aGrid.mods.fn.check', function (event = false) {
    let api = this;

    let module = api.aGrid.$.module;
    let modules = api.aGrid.$.store.namespace('modules'); // Получение пространства модулей из хранилища

    if (event) {
        // Сохранение состояния переключателя в хранилище
        modules.add(module, {[event.currentTarget.name]: event.currentTarget.checked})
    } else {
        // Восстановление состояний переключателей из хранилища
        $.each(modules.get(module), function (index, value) {
            let selector = '*[name="' + index + '"]';
            api.aGrid.mods.$.dropContent = api.aGrid.mods.$.dropContent.find(selector).attr('checked', value).end();
        });
    }

    // Проверка true в объекте для индикатора кнопки
    if (modules.get(module) && Object.values(modules.get(module)).includes(true)) {
        api.aGrid.$.container.addClass('mods'); // Добавление класса-индикатора
    } else {
        api.aGrid.$.container.removeClass('mods'); // Удаление класса-индикатора
    }
});

// ### СПИСОК МОДОВ
// МОД - Отключение прокрутки страницы при наведении на таблицу grid
// Документация Bootstrap Popover: https://getbootstrap.com/docs/5.3/components/popovers/
DataTable.Api.register('aGrid.mods.list.blocked', function (event = false) {
    let api = this;
    // https://jsbin.com/teqoqib/6/edit?html,js,output

    let module = api.aGrid.$.module;
    let modules = api.aGrid.$.store.namespace('modules');

    let checked = modules.get(module)?.blocked; // Получение состояния блокировки
    api.aGrid.$.container.toggleClass('blocked', !!(checked)); // Переключение CSS класса

    // Обработчик отключения прокрутки страницы
    let disabledScroll = (e) => {
        e.preventDefault() // Блокировка стандартного поведения прокрутки
    };

    // Обработка наведения на контейнер таблицы
    api.aGrid.$.container
        .mouseenter(function () {
            // Включение блокировки прокрутки при наведении
            if (modules.get(module)?.blocked) {
                document.addEventListener('mousewheel', disabledScroll, {passive: false})
            }
        })
        .mouseleave(function () {
            // Выключение блокировки прокрутки при уходе курсора
            document.removeEventListener('mousewheel', disabledScroll, {passive: false})
        });
});

// МОД - Включение быстрого просмотра строки
DataTable.Api.register('aGrid.mods.list.visible', function (event = false) {
    let api = this;

    let module = api.aGrid.$.module;
    let modules = api.aGrid.$.store.namespace('modules');

    // Включение/выключение нумерации столбцов
    let checked = modules.get(module)?.visible;
    api.aGrid.mods.list.visible.actions.colsNum(!!(checked))

    // Обработчики событий для popover быстрого просмотра
    let selector = 'tr td:last-child';
    $('tbody')
        .on('mouseenter', selector, function (event) {
            // Показать popover при наведении
            if (modules.get(module)?.visible) {
                $(this).popover('show');
            }
        })
        .on('mouseleave', selector, function (event) {
            // Скрыть popover при уходе курсора
            if (modules.get(module)?.visible) {
                $(this).popover('hide');
            }
        });
});

// ### ДЕЙСТВИЯ МОДОВ
// ДЕЙСТВИЕ - "Быстрый просмотр строки" - создание popover с данными строки
DataTable.Api.register('aGrid.mods.list.visible.actions.quickRow', function (tr) {
    let api = this;
    let row = api.row(tr);

    let r_data = row.data(); // Данные строки
    let r_index = (r_data.id) ? r_data.id : row.index(); // ID или индекс строки

    let columns = api.settings().init().columns; // Получение настроек колонок
    let content = '';
    const entries = Object.entries(r_data); // Преобразование данных в массив [ключ, значение]

    // Массив игнорирования (Столбцы с этими типами будут игнорироваться)
    let ignore = _OPTIONS.ignore;

    // Собираем блок div для контента popover
    content += '<div class="qq-view-item">';

    // Собираем строки с данными
    let counter = 0;
    entries.forEach(function (elem, index) {

        let header = columns[index];
        let text = elem[1];

        // Пропускаем игнорируемые колонки
        if (ignore.includes(header.configs.type) || header.visible === false) {
            content += ''
        } else {
            counter++;
            // Форматирование элемента данных
            content +=
                '<div class="mt-1">' +
                '<div class="avatar float-start bg-light-primary rounded me-1">' +
                '<div class="avatar-content">' + counter + '</div></div>' +
                '<div class="more-info">' +
                '<small class="hr-text hr-text-left my-50">' + header.title + '</small>' +
                '<h6 class="mb-0">' + ((text !== null) ? text : '') + '</h6>' +
                '</div></div>';
        }

    });
    content += '</div>';

    // Создаем popover для последней ячейки строки
    const popover = $(tr).children('td:last-child').popover({
        title: 'Карточка строки #' + r_index,
        content: content,
        html: true,
        sanitize: false,
        customClass: 'qq-view',
        trigger: 'manual', // Ручное управление
        placement: 'left', // Позиция слева
    });
});

// ДЕЙСТВИЕ - Нумерация столбцов таблицы
DataTable.Api.register('aGrid.mods.list.visible.actions.colsNum', function (param) {
    let api = this;

    // Массив игнорирования (Столбцы с этими типами будут игнорироваться)
    let ignore = _OPTIONS.ignore;

    // Обработка каждого заголовка таблицы
    $('.dataTables_scrollHeadInner .aGrid-titles th').each(function (index, unit) {

        let data = $(unit).data();
        // Пропускаем игнорируемые типы колонок
        if (!ignore.includes(data.type)) {
            switch (param) {
                case true:
                    // Добавление номера колонки
                    let unit = $('<span class="col-number"></span>').text(index + 1);
                    $(this).prepend(unit);
                    break;

                case false:
                    // Удаление номера колонки
                    $(this).children('.col-number').remove();
                    break;
            }
        }
    });
});
