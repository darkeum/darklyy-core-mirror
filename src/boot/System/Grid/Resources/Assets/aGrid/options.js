// Определение контейнера aGrid
let container = $('.aGrid-container');

// Экспорт объекта глобальных настроек aGrid
export let _OPTIONS = {
    container: container, // jQuery объект контейнера таблицы
    // table: api, // Текущая таблица всегда доступна в Классе (закомментировано)
    module: container.data('module'), // Название модуля из data-атрибута
    ignore: ['actions', 'checkbox'], // Типы колонок, которые игнорируются в различных операциях
    store: store.namespace('aGrid'), // Система хранения в локальном кеше браузера для aGrid
    dropContent: false, // Список для popover модификаторов (инициализируется позже)
    select2: {
        drop_all: '__ALL_SELECT_GRID', // Индикатор пункта "Все" в select2
        drop_custom: '__CUSTOM_SELECT_GRID', // Индикатор пункта "Custom" в select2
        drop_ajax: '__AJAX_SELECT_GRID', // Индикатор пункта для AJAX-загрузки в select2
    }
};

// Зарезервировано для будущей регистрации опций в DataTables API (закомментировано)
// DataTable.Api.register('aGrid.options', function () {
//     let api = this;
//     api.aGrid.$ = _OPTIONS; // Присвоение опций объекту aGrid
// });
