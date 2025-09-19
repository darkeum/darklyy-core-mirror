import {_OPTIONS} from "../options.js";

/* ФУНКЦИИ - fn */
// Регистрация основного метода aGrid.fn для DataTables API
DataTable.Api.register('aGrid.fn', function () {
    let api = this;

    // Перенос элементов управления в новое место
    api.aGrid.fn.appendTools();

    return api.aGrid;
});

// Перенос элементов управления (В новое место)
// Оптимизация расположения элементов управления таблицы
DataTable.Api.register('aGrid.fn.appendTools', function () {
    let api = this;
    let container = api.aGrid.$.container;
    let search = container.find('.dataTables_filter');
    let search_id = search.attr('id'); // Оптимизация - сохранение ID
    let search_class = search.attr('class'); // Оптимизация - сохранение классов
    let search_text = $(search.children('label')).text(); // Оптимизация - текст метки
    let search_input = $(search.children('label').children('input')); // Оптимизация - поле ввода

    // Настройка атрибутов поля поиска
    search_input.attr({
        'placeholder': search_text,
        // 'aria-label' : 'text',
        'aria-describedby': search_id
    }).addClass('form-control-sm');

    let buttons = container.find('.dt-buttons');
    // Настройка кнопок DataTables
    buttons.find('.btn')
        // .attr({
        //     'data-bs-toggle' : 'modal',
        //     'data-bs-target' : '#searchB',
        // })
        .addClass('btn-sm');

    // Открепляем элементы от исходного расположения
    search.detach();
    buttons.detach();

    let toolbar = $('.btn-toolbar');
    //toolbar.find('.input-group-text').append(search_text); // Показать вместо иконки поиска "Поиск"

    // Перенос поля поиска в тулбар
    toolbar.find('#aGrid-search').attr('id', search_id).addClass(search_class).append(search_input);
    // Перенос кнопок управления в тулбар
    toolbar.find('#aGrid-controls').append(buttons.children());

    // toolbar.find('#aGrid-controls').find('.btn').attr({
    //     'data-bs-toggle' : 'modal',
    //     'data-bs-target' : '#searchB',
    // });
});

// Установка tooltip для столбцов со сменой текста при сортировке столбца, а так же установка шестерни для действий
DataTable.Api.register('aGrid.fn.setTitles', function (columns) {
    columns.forEach(function (item, index) {

        let unit = $(item.nTh); // Получение DOM элемента заголовка столбца

        let labelSort = unit.attr('aria-label');
        let label = (labelSort) ? labelSort : item.label;

        // Параметры configs из настроек столбца
        let configs = item.configs;

        // v1 - определение текста tooltip
        let tooltip = configs.tooltip;
        let title = (tooltip) ? tooltip : label;

        // ДЕЙСТВИЯ
        // Отдельное исключение для "Действий" - Принудительный title tooltip
        if (item.type === 'actions' && tooltip == false) {
            title = item.label
        }

        // Изменить 'Действия' на i.class Иконку // Иконка добавлена в settings потока dt
        if (unit.is(':empty') && configs.icon) {

            let icon = $('<i></i>');
            if (configs.icon.indexOf(' ') >= 0) {
                // Если содержит пробел - это готовый класс
                icon.attr({class: configs.icon});
            } else {
                // Иначе - feather иконка
                icon.attr({'data-feather': configs.icon});
            }
            unit.html(icon);
        }

        // Установка атрибутов для Bootstrap tooltip
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
    let row_filters = col_search.children('tr').eq(1); // Вторая строка заголовка (фильтры)
    // let row_filters = col_search.children('tr.aGrid-filters');

    // Обработка видимости полей поиска в зависимости от видимости столбцов
    columns.forEach(function (item, i) {
        let row = row_filters.children(`th[data-visible='${i}']`);
        (item === false) ? row.hide() : row.show(); // Скрыть/показать поле поиска
    });
});

// Рендер данных в таблице - кастомная функция отображения данных
// Документация: https://datatables.net/reference/option/columns.render
DataTable.Api.register('aGrid.fn.render', function (data, type, row, meta) {
    // Проверка на display (отображение в таблице)
    if (type === 'display') {

        let render;

        // Основные параметры
        let unit = meta.settings.aoColumns[meta.col]; // Настройки текущего столбца

        // Кастомные параметры Grid (Все кастомные параметры тут !!!)
        let configs = unit.configs;

        // Проверка данных и возможности с ними работать
        if (data !== null && data !== '') {

            let div = $('<div class="render"></div>');
            let col_class = unit.sClass; // CSS класс столбца
            let r_class = col_class ? col_class : '';
            let boostrap_attributes = {};

            // Добавить префикс если он есть
            configs.prefix && (data = configs.prefix + data);

            // ### Boostrap js tools
            // Popover - настройка всплывающих подсказок
            if (configs.popover) {
                // popover хранит в себе placement
                let popover_data;

                // Экранирование кавычек для HTML атрибутов
                if (data.toString().includes('"')) {
                    popover_data = data.replace(/"/g, "'");
                } else {
                    popover_data = data;
                }

                boostrap_attributes = {
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

                // Вывод скрытого текста в столбце с обрезкой
                case 'text':
                    let truncate;
                    div.addClass('d-grid').attr(boostrap_attributes);

                    let children = $('<div></div>').html(data).children();
                    if (children.length > 0) {
                        // Если есть дочерние элементы - оборачиваем их
                        truncate = children.addClass('d-block text-truncate').prop('outerHTML');
                    } else {
                        // Иначе - обычный текст с обрезкой
                        truncate = $('<span class="d-block text-truncate"></span>').html(data);
                    }

                    render = div.html(truncate).prop('outerHTML');
                    break;

                // Развертываемый контент (зарезервировано)
                case 'expand':
                    // fit-content можно использовать... но...
                    break;

                // Вывод поля ввода в столбце (WIP - Work In Progress)
                case 'input' :
                    // let input = $('<input class="form-control render render-input" />')
                    //     .addClass(r_class)
                    //     .attr(boostrap_attributes)
                    //     .val(data);
                    // render = input.prop('outerHTML');
                    break;

                // По умолчанию - возвращаем исходные данные
                default:
                    render = data;
                    break;
            }
        } else {
            render = data;
        }

        // Дополнительные элементы (вложения, значки) - закомментировано
        // let other = unit.other;
        // if (other) {
        //     render += '<span class="position-absolute top-0 end-0 badge badge-render bg-transparent text-muted">' +
        //         '<i data-feather="paperclip"></i>' +
        //         '<i data-feather="image"></i>' +
        //         '<i data-feather="codepen"></i>' +
        //         '</span>';
        // }

        return render;
    } else {
        // Для других типов (filter, type, sort) возвращаем исходные данные
        return data;
    }
});
