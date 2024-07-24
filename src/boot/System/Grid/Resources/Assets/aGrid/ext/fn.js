import {_OPTIONS} from "../options.js";

/* ФУНКЦИИ - fn */
DataTable.Api.register('aGrid.fn', function () {
    let api = this;

    // Перенос элементов управления (В новое место)
    api.aGrid.fn.appendTools();

    return api.aGrid;
});

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
