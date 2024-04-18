import {} from "/aGrid/ext/callbacks.js";
import {} from "/aGrid/ext/dt.js";
import {} from "/aGrid/ext/fn.js";
import {} from "/aGrid/ext/mods.js";
import {} from "/aGrid/ext/ui.js";
import {} from "/aGrid/ext/utils.js";
import { _OPTIONS } from "./options.js";

// Инициализация aGrid EXT
DataTable.Api.register('aGrid', function () {
    let api = this;
    // api.aGrid.$ = _OPTIONS;

    // api.aGrid.dt();
    // api.aGrid.fn();
    // api.aGrid.ui();
    // api.aGrid.mods();
    api.aGrid.dt().fn().ui().mods();

    // TEST
    // console.log(this.store.getAll())

    return api; // или api.aGrid
});

// DataTable.Api.register();
// DataTable.Api.extend();

// DataTable.Api.register('aGrid.', function () {});

// Параметры до загрузки Grid
// $.extend(true, $.fn.dataTable.defaults, {});
// $.extend(true, $.fn.dataTable.Api, {
//     'aaaaaaaaaaaaa': 'aaaaaaaaaaaaaaaaaa'
// })
// console.log($.fn.dataTable())

$(document).ready(function () {

});