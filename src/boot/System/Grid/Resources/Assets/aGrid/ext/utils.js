import { _OPTIONS } from "../options.js";

DataTable.Api.register('aGrid.utils', function () {
    let api = this;
    //
    return api.aGrid;
});

/* УТИЛИТЫ - utils */
// config - ПЕРЕДЕЛАТЬ!
DataTable.Api.register('aGrid.utils.config', function (param, module = false) {

    // let modules = store.namespace('modules');
    // let uri;
    //
    // if (module) {
    //     uri = module
    // } else {
    //     uri = window.location.href.split('/')[5];
    // }
    //
    // // WIP
    // if (param === 'uri') {
    //     return window.location.href.split('/')[5];
    // }
    //
    // return modules.get(uri)?.[param];
});

// Уведомления через toastr
DataTable.Api.register('aGrid.utils.notif', function (type = 'success', header = '', message = '') {
    toastr[type](message, header, {
        positionClass: "toast-bottom-right",
    });
});

// Переход по ссылкам на JS
DataTable.Api.register('aGrid.utils.openUrl', function (t, param = false) {
    let data = this.table.row(t).data();

    // #1
    // Полный url из uri
    // let path = location.pathname.substring(1).split('/');
    // let arr = [ path[0] , path[1] , path[2], data.id ];

    // или только ID
    // let arr = [data.id];
    //
    // if (param) {
    //     arr.push(param)
    // }

    // window.open(arr.join('/')).focus();
    // location.href = arr.join('/');

    // #2
    let url;
    let actions = $(data.action);

    if ($(actions[0]).attr('href')) {
        // log(url)
        url = $(actions[0]).attr('href');
    } else {
        // log($(actions[0]).children('a').attr('href'))
        url = $(actions[0]).children('a').attr('href')
    }
    window.open(url).focus();
    return false;
});

// Работа с Буфером обмена
// Пишем
DataTable.Api.register('aGrid.utils.clipboard.write', function (text) {
    navigator.clipboard.writeText(text)
        .then(() => {
            this.notif('success', 'Скопировано', text)
        })
        .catch(err => {
            console.log('Что-то пошло не так', err);
        });
});

// Читаем текст, прочитанный из буфера обмена
DataTable.Api.register('aGrid.utils.clipboard.read', function () {
    navigator.clipboard.readText()
        .then(text => {
            // `text` содержит текст, прочитанный из буфера обмена
        })
        .catch(err => {
            // возможно, пользователь не дал разрешение на чтение данных из буфера обмена
            console.log('Что-то пошло не так', err);
        });
});

// Лог
DataTable.Api.register('aGrid.utils.log', function (data) {
    console.log(data)
});
