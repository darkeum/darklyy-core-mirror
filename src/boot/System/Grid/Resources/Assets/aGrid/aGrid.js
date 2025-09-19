// Импорт настроек
import {_OPTIONS} from "./options.js";

// Импорт расширений (подключаются побочные эффекты)
import {} from "./ext/callbacks.js";    // Callback-функции DataTables
import {} from "./ext/dt.js";           // Расширения DataTables API
import {} from "./ext/fn.js";           // Функциональные расширения
import {} from "./ext/mods.js";         // Модификаторы таблицы
import {} from "./ext/ui.js";           // UI утилиты
import {} from "./ext/utils.js";        // Вспомогательные утилиты

// Инициализация aGrid EXT - основной метод расширения DataTables
DataTable.Api.register('aGrid', function () {
    let api = this;

    // Сохранение опций в объекте aGrid
    api.aGrid.$ = _OPTIONS;

    // Последовательная инициализация всех компонентов aGrid
    // api.aGrid.dt();    // Инициализация DataTables событий
    // api.aGrid.fn();    // Инициализация функций
    // api.aGrid.ui();    // Инициализация UI компонентов
    // api.aGrid.mods();  // Инициализация модификаторов

    // Цепочный вызов всех компонентов
    api.aGrid.dt().fn().ui().mods();

    // TEST - отладочная информация
    // console.log(this.store.getAll())

    return api; // Возврат API для цепочных вызовов
});

// Зарезервировано для будущих расширений:
// DataTable.Api.register();
// DataTable.Api.extend();

// DataTable.Api.register('aGrid.', function () {});

// Глобальная конфигурация DataTables до загрузки Grid (закомментировано)
// $.extend(true, $.fn.dataTable.defaults, {}); // Расширение стандартных настроек
// $.extend(true, $.fn.dataTable.Api, {
//     'aaaaaaaaaaaaa': 'aaaaaaaaaaaaaaaaaa' // Пример добавления свойств в API
// })
// console.log($.fn.dataTable()) // Отладочный вывод

// Инициализация при готовности документа
$(document).ready(function () {
    // Место для кода, выполняемого после полной загрузки DOM
});
