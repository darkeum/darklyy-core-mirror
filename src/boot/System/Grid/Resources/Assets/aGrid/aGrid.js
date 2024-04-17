import {} from "/aGrid/ext/callbacks.js";
import {} from "/aGrid/ext/dt.js";
import {} from "/aGrid/ext/fn.js";
import {} from "/aGrid/ext/mods.js";
import {} from "/aGrid/ext/ui.js";
import {} from "/aGrid/ext/utils.js";

let _OPTIONS = {
    ignore: ['actions', 'checkbox'],
};

// Инициализация aGrid EXT
DataTable.Api.register('aGrid', function () {
    let api = this;
    let container = $('.aGrid-container');

    api.aGrid.$ = {
        container: container,
        table: api, // Текущая таблица всегда доступна в Классе
        module: container.data('moduleName'), // Название модуля
        options: {
            ignore: _OPTIONS.ignore
        },
        store: store.namespace('aGrid'), // Система хранения в локальном кеше браузера для aGrid
        dropContent: false, // Список для popover модификаторов
    };

    // Перенос элементов управления (В новое место)
    api.aGrid.fn.appendTools();

    // Вызов контекстного меню для table tr
    // api.aGrid.contextMenu();

    // Инициализация aGrid-mods
    api.aGrid.mods();

    api.aGrid.dt.events();
    api.aGrid.dt.mouse.events();

    // Показать версию
    // console.log('aGrid EXT: Запущен!);

    // TEST
    // console.log(this.store.getAll())

    return api;
});

/* ************************************************************************ */

// DataTable.Api.register('aGrid.', function () {});

// DataTable.Api.register();
// DataTable.Api.extend();

// Параметры до загрузки Grid
// $.extend(true, $.fn.dataTable.defaults, {});
// $.extend(true, $.fn.dataTable.Api, {
//     'aaaaaaaaaaaaa': 'aaaaaaaaaaaaaaaaaa'
// })
// console.log($.fn.dataTable())

$(document).ready(function () {

});