// Импорт настроек
import {_OPTIONS} from "./options.js";

// Импорт расширений
import {} from "./ext/callbacks.js";
import {} from "./ext/dt.js";
import {} from "./ext/fn.js";
import {} from "./ext/mods.js";
import {} from "./ext/ui.js";
import {} from "./ext/utils.js";

// Инициализация aGrid EXT
DataTable.Api.register('aGrid', function () {
    let api = this;

    api.aGrid.$ = _OPTIONS;

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
