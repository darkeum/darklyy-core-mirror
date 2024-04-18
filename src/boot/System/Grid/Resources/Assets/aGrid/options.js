let container = $('.aGrid-container');

export let _OPTIONS = {
    container: container,
    // table: api, // Текущая таблица всегда доступна в Классе
    module: container.data('module'), // Название модуля
    ignore: ['actions', 'checkbox'],
    store: store.namespace('aGrid'), // Система хранения в локальном кеше браузера для aGrid
    dropContent: false, // Список для popover модификаторов
};

// DataTable.Api.register('aGrid.options', function () {
//     let api = this;
//     api.aGrid.$ = _OPTIONS;
// });