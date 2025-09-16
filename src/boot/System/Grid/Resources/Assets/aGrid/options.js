let container = $('.aGrid-container');

export let _OPTIONS = {
    container: container,
    // table: api, // Текущая таблица всегда доступна в Классе
    module: container.data('module'), // Название модуля
    ignore: ['actions', 'checkbox'],
    store: store.namespace('aGrid'), // Система хранения в локальном кеше браузера для aGrid
    dropContent: false, // Список для popover модификаторов
    select2: {
        drop_all: '__ALL_SELECT_GRID', // Индикатор пункта "Все"
        drop_custom: '__CUSTOM_SELECT_GRID', // Индикатор пункта "Custom" ...
        drop_ajax: '__AJAX_SELECT_GRID', // Индикатор пункта "Custom" ...
    }
};

// DataTable.Api.register('aGrid.options', function () {
//     let api = this;
//     api.aGrid.$ = _OPTIONS;
// });
