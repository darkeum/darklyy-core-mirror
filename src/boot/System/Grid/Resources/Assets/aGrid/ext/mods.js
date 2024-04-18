import { _OPTIONS } from "../options.js";

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

    return api.aGrid;
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
    let api = this;
    let row = api.row(tr);

    let r_data = row.data(); // api.row( this ).data()
    let r_index = (r_data.id) ? r_data.id : row.index(); // api.row( this ).index()

    let columns = api.settings().init().columns; // Более гибкий вариант получения колонок
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