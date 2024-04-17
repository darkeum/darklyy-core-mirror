DataTable.Api.register('aGrid.dt', function () {
    let api = this;

    api.aGrid.dt.events();
    api.aGrid.dt.mouse.events();
});

// События DT - https://www.datatables.net/reference/event/
DataTable.Api.register('aGrid.dt.events', function () {
    let api = this;

    // https://datatables.net/reference/option
    api
        // Инициализация DT
        .on('init.dt', function (e, settings, column, state) {
            // console.log('init.dt');
        })
        // Отрисовка таблицы (Дублирующий вызов)
        .on('draw.dt', function (e, datatable) {
            // console.log('draw.dt')
        })
        // Сортировка DT - Столбца
        .on('order.dt', function () {
            // let order = api.order();
            // console.log("Ordered column " + order[0][0] + ", in direction " + order[0][1]);
        })
        // ### Select снять со строки
        // Выбор элементов (строк, столбцов или ячеек) отменен.
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
        })
        // Выбраны элементы (строки, столбцы или ячейки).
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
        })
        // Перед размытием выбора элемента в DataTable (можно отменить).
        .on('select-blur', function (e, dt, target, originalEvent) {
            // console.log(e)
            // if ($(e.target).parents('div.modal').length) {
            //     e.preventDefault();
            // }
        })
        // Выберите событие изменения типа элемента.
        .on('selectItems', function (e, dt, items) {
            // console.log('Элементы, которые нужно выбрать, теперь доступны: ', items);
        })

        // Выберите событие изменения стиля.
        .on('selectStyle', function (e, dt, style) {
            // console.log('Selection style is now: ', style);
        })
        // Действие пользователя приведет к выбору элементов в таблице.
        .on('user-select', function (e, dt, type, cell, originalEvent) {
            // console.log(e)
            // if (originalEvent.target.nodeName.toLowerCase() === 'img') {
            //     e.preventDefault();
            // }
        })

        // Изменение ширины столбцов (Калькуляция)
        .on('column-sizing.dt', function (e, settings) {
            // console.log(e)
            // console.log(settings)
        })
        //
        .on('column-visibility.dt', function (e, settings, column, state) {
            // console.log(
            //     'Column ' + column + ' has changed to ' + (state ? 'visible' : 'hidden')
            // );
        })
        // Изменение размера таблицы
        .on("responsive-resize", function (e, datatable, columns) {
            // Скрытие полей поиска
            datatable.aGrid.fn.hideSearchInputs(columns);
            // Перерисовка ширины столбцов
            datatable.columns.adjust().responsive.recalc();
        })
        // Изменение размера экрана
        .on('responsive-display', function (e, datatable, row, showHide, update) {
            // console.log('Details for row ' + row.index() + ' ' + (showHide ? 'shown' : 'hidden'));

            // Скрытие полей поиска
            // datatable.aGrid.fn.hideSearchInputs(columns);
            // Перерисовка ширины столбцов
            // datatable.columns.adjust().responsive.recalc();
        })
        // Отрисовка processing
        .on('processing.dt', function (e, settings, processing) {
            api.aGrid.ui.loader(processing)
        })
});

// События мыши DT - Обработка событий мыши
DataTable.Api.register('aGrid.dt.mouse.events', function () {
    let api = this;

    // Клик по tr таблицы
    api
        .on('click', 'tr', function (e) {
            // grid.rowSelected(this) // Временно убрано...
        })
        .on('mouseover', 'tr', function () {
        })
        .on('mouseenter', 'tr', function () {
        })
        .on('mouseleave', 'tr', function () {
        })
        // x2 Клик по tr таблицы
        .on('dblclick', 'tr', function (event) {
            if (event.ctrlKey) {
                grid.openUrl(this);
            }
        })
        // Клик по render для раздвижения
        // .on('click', 'tr.selected .render span', function (event) {
        // })
        // Обработка события по (+) (Разворачивание скрытого)
        .on('click', 'tr td.dtr-control', function () {
        })
});