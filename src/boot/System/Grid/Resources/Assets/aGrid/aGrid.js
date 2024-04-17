window.aGrid._OPTIONS = {
    container: $('.aGrid-container'),
    ignore: ['actions', 'checkbox'],
};

import {} from "/aGrid/ext/callbacks.js";
import {} from "/aGrid/ext/dt.js";
import {} from "/aGrid/ext/fn.js";
import {} from "/aGrid/ext/mods.js";
import {} from "/aGrid/ext/ui.js";
import {} from "/aGrid/ext/utils.js";

// Инициализация aGrid EXT
DataTable.Api.register('aGrid', function () {
    let api = this;
    let container = $('.aGrid-container');

    api.aGrid.$ = {
        container: container,
        table: api, // Текущая таблица всегда доступна в Классе
        module: container.data('module'), // Название модуля
        options: {
            ignore: window.aGrid._OPTIONS.ignore
        },
        store: store.namespace('aGrid'), // Система хранения в локальном кеше браузера для aGrid
        dropContent: false, // Список для popover модификаторов
    };
    
    api.aGrid.dt();
    api.aGrid.fn();
    api.aGrid.ui();
    api.aGrid.mods();

    // TEST
    // console.log(this.store.getAll())

    return api;
});

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