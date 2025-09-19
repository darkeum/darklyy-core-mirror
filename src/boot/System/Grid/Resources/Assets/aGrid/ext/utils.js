/* УТИЛИТЫ - utils - Вспомогательные утилиты */

import {_OPTIONS} from "../options.js";

// Основной метод инициализации утилит
DataTable.Api.register('aGrid.utils', function () {
    let api = this;
    //
    return api.aGrid;
});

// config - ПЕРЕДЕЛАТЬ! (В разработке)
// Утилита для работы с конфигурационными параметрами модулей
DataTable.Api.register('aGrid.utils.config', function (param, module = false) {
    // Запланированная функциональность для работы с конфигурацией модулей
    // let modules = store.namespace('modules');
    // let uri;
    //
    // if (module) {
    //     uri = module
    // } else {
    //     uri = window.location.href.split('/')[5]; // Получение URI из URL
    // }
    //
    // // WIP - Work In Progress
    // if (param === 'uri') {
    //     return window.location.href.split('/')[5]; // Возврат части URI
    // }
    //
    // return modules.get(uri)?.[param]; // Возврат параметра конфигурации
});

// Уведомления через toastr
// Документация: https://github.com/CodeSeven/toastr
DataTable.Api.register('aGrid.utils.notif', function (type = 'success', header = '', message = '') {
    // Отображение уведомления с использованием toastr
    toastr[type](message, header, {
        positionClass: "toast-bottom-right", // Позиция уведомления в правом нижнем углу
    });
});

// Переход по ссылкам на JavaScript
DataTable.Api.register('aGrid.utils.openUrl', function (t, param = false) {
    let data = this.table.row(t).data(); // Получение данных строки

    // #1 - Альтернативный подход (закомментирован)
    // Полный url из uri
    // let path = location.pathname.substring(1).split('/');
    // let arr = [ path[0] , path[1] , path[2], data.id ];
    // или только ID
    // let arr = [data.id];
    //
    // if (param) {
    //     arr.push(param) // Добавление параметра если указан
    // }
    // window.open(arr.join('/')).focus(); // Открытие URL в новом окне
    // location.href = arr.join('/'); // Переход по URL

    // #2 - Текущая реализация
    let url;
    let actions = $(data.action); // Получение элементов действий из данных

    if ($(actions[0]).attr('href')) {
        // Если первый элемент действий имеет href атрибут
        url = $(actions[0]).attr('href');
    } else {
        // Если href находится у дочернего элемента <a>
        url = $(actions[0]).children('a').attr('href')
    }

    // Открытие URL в новом окне с фокусом
    window.open(url).focus();
    return false; // Предотвращение стандартного поведения
});

// Работа с Буфером обмена
// Запись текста в буфер обмена
// Документация: https://developer.mozilla.org/ru/docs/Web/API/Clipboard_API
DataTable.Api.register('aGrid.utils.clipboard.write', function (text) {
    // Использование Clipboard API для записи текста
    navigator.clipboard.writeText(text)
        .then(() => {
            // Успешное копирование - показ уведомления
            this.notif('success', 'Скопировано', text)
        })
        .catch(err => {
            // Обработка ошибок копирования
            console.log('Что-то пошло не так', err);
        });
});

// Чтение текста из буфера обмена
DataTable.Api.register('aGrid.utils.clipboard.read', function () {
    // Использование Clipboard API для чтения текста
    navigator.clipboard.readText()
        .then(text => {
            // Успешное чтение - текст доступен в переменной `text`
            // `text` содержит текст, прочитанный из буфера обмена
        })
        .catch(err => {
            // Обработка ошибок чтения (например, нет разрешения)
            console.log('Что-то пошло не так', err);
        });
});

// Утилита для логирования данных в консоль
DataTable.Api.register('aGrid.utils.log', function (data) {
    console.log(data) // Простое логирование в консоль
});
