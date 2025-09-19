import {_OPTIONS} from "../options.js";

// Регистрация метода aGrid.dt для DataTables API
// Документация: https://datatables.net/reference/api/
DataTable.Api.register('aGrid.dt', function () {
    let api = this;

    // Инициализация событий DataTables и обработчиков мыши
    api.aGrid.dt.events();
    api.aGrid.dt.mouse.events();

    // Возвращает экземпляр aGrid для цепочки вызовов
    return api.aGrid;
});

// События DT - обработка событий DataTables
// Документация: https://www.datatables.net/reference/event/
DataTable.Api.register('aGrid.dt.events', function () {
    let api = this;

    // https://datatables.net/reference/option
    api
        // Инициализация DT - вызывается при инициализации DataTables
        .on('init.dt', function (e, settings, column, state) {
            // console.log('init.dt');
            // Место для кода, выполняемого после инициализации таблицы
        })
        // Отрисовка таблицы - вызывается при каждой перерисовке таблицы
        .on('draw.dt', function (e, datatable) {
            // console.log('draw.dt')
            // Дублирующий вызов (возможно, нужен для дополнительной логики)
        })
        // Сортировка DT - вызывается при изменении порядка сортировки
        .on('order.dt', function () {
            // let order = api.order();
            // console.log("Ordered column " + order[0][0] + ", in direction " + order[0][1]);
            // Можно добавить логику обработки сортировки
        })
        // ### Select снять со строки
        // Выбор элементов (строк, столбцов или ячеек) отменен
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
            // Логика обработки отмены выбора элементов
        })
        // Выбраны элементы (строки, столбцы или ячейки)
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
            // Логика обработки выбора элементов
        })
        // Перед размытием выбора элемента в DataTable (можно отменить)
        .on('select-blur', function (e, dt, target, originalEvent) {
            // console.log(e)
            // if ($(e.target).parents('div.modal').length) {
            //     e.preventDefault();
            // }
            // Предотвращение потери фокуса при определенных условиях
        })
        // Выберите событие изменения типа элемента
        .on('selectItems', function (e, dt, items) {
            // console.log('Элементы, которые нужно выбрать, теперь доступны: ', items);
            // Доступ к элементам, которые будут выбраны
        })
        // Выберите событие изменения стиля выделения
        .on('selectStyle', function (e, dt, style) {
            // console.log('Selection style is now: ', style);
            // Обработка изменения стиля выделения
        })
        // Действие пользователя приведет к выбору элементов в таблице
        .on('user-select', function (e, dt, type, cell, originalEvent) {
            // console.log(e)
            // if (originalEvent.target.nodeName.toLowerCase() === 'img') {
            //     e.preventDefault();
            // }
            // Предотвращение выбора определенных элементов (например, изображений)
        })
        // Изменение ширины столбцов (Калькуляция)
        .on('column-sizing.dt', function (e, settings) {
            // console.log(e)
            // console.log(settings)
            // Логика обработки изменения размеров столбцов
        })
        // Изменение видимости столбца
        .on('column-visibility.dt', function (e, settings, column, state) {
            // console.log(
            //     'Column ' + column + ' has changed to ' + (state ? 'visible' : 'hidden')
            // );
            // Реакция на изменение видимости столбцов
        })
        // Изменение размера таблицы (responsive)
        .on("responsive-resize", function (e, datatable, columns) {
            // Скрытие полей поиска для скрытых колонок
            datatable.aGrid.fn.hideSearchInputs(columns);
            // Перерисовка ширины столбцов с учетом responsive
            datatable.columns.adjust().responsive.recalc();
        })
        // Изменение отображения responsive details
        .on('responsive-display', function (e, datatable, row, showHide, update) {
            // console.log('Details for row ' + row.index() + ' ' + (showHide ? 'shown' : 'hidden'));

            // Скрытие полей поиска
            // datatable.aGrid.fn.hideSearchInputs(columns);
            // Перерисовка ширины столбцов
            // datatable.columns.adjust().responsive.recalc();
        })
        // Отрисовка processing - отображение/скрытие индикатора загрузки
        .on('processing.dt', function (e, settings, processing) {
            // Показ/скрытие лоадера в зависимости от состояния processing
            api.aGrid.ui.loader(processing)
        })
});

// События мыши DT - Обработка событий мыши для таблицы
DataTable.Api.register('aGrid.dt.mouse.events', function () {
    let api = this;

    // Обработка событий мыши для строк таблицы
    api
        // Клик по строке таблицы
        .on('click', 'tr', function (e) {
            // grid.rowSelected(this) // Временно убрано...
            // Логика выделения строки при клике
        })
        // Наведение мыши на строку
        .on('mouseover', 'tr', function () {
            // Эффекты при наведении на строку
        })
        // Вход курсора в область строки
        .on('mouseenter', 'tr', function () {
            // Дополнительные эффекты при входе в строку
        })
        // Выход курсора из области строки
        .on('mouseleave', 'tr', function () {
            // Очистка эффектов при выходе из строки
        })
        // Двойной клик по строке таблицы
        .on('dblclick', 'tr', function (event) {
            if (event.ctrlKey) {
                // Открытие URL при двойном клике с зажатым Ctrl
                grid.openUrl(this);
            }
            // Другие действия при двойном клике
        })
        // Клик по render для раздвижения (закомментировано)
        // .on('click', 'tr.selected .render span', function (event) {
        // })
        // Обработка события по (+) для разворачивания скрытого контента
        .on('click', 'tr td.dtr-control', function () {
            // Логика для responsive control (развертывание/свертывание деталей)
        })
});
