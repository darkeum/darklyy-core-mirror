$(function() {
    $(".select2").select2();
    func_update_panel_header();
    func_update_panel_main();
    func_update_panel_info();
    sort_main();
});

function sort_type() {
    $("#type_list").sortable({
        axis: "y",
        stop: function(event, ui) {
            const data = [];
            $("#type_list").children().each((i, elem) => {
                const id = $(elem).data("id");
                data.push(save_field_type(id, 'position', i));
            });
            Promise.all(data).then(() => {
                update_panel_main_head();
            });
        }
    });
}

function sort_main() {
    $("#panelMain_body").sortable({
        axis: "y",
        stop: function(event, ui) {
            $(".move_container_create").each((i, elem) => {
                const id = $(elem).data("id");
                save_field_data(id, 'position', i);
            });
        }
    });
}

function func_update_panel_header() {
    xeditable_title();
    xeditable_comment_main();
}

function func_update_panel_main() {
    xeditable_comment_data();
    xeditable_comment_type();
    xeditable_folder_name();
    sort_main();
}

function func_update_panel_info() {
    xeditable_comment_type();
    sort_type();
    files_comment_x();
}

function getMainId() {
    return $("#mainId").val();
}

function open_modal(id) {
    $(`#${id}`).modal("show");
}

function save_field_modal(field, save) {
    return new Promise((resolve, reject) => {
        const value = $("#" + save).val();
        save_field(field, value).then(() => {
            update_panel_info();
            resolve();
        });
        switch (field) {
            case "uid":
                append_user_chat(value);
                break;
        }
    });
}

function update_panel_progress() {
    const mainId = getMainId();
    $.post("/administration/module/boiler/bid/panel_progress/" + mainId, function(data) {
        $("#panelProgress").html(data);
    });
}

function update_panel_header() {
    const mainId = getMainId();
    $.post("/administration/module/boiler/bid/panel_header/" + mainId, function(data) {
        $("#panelHeader").html(data);
        func_update_panel_header();
    });
}

function update_panel_main() {
    const mainId = getMainId();
    $.post("/administration/module/boiler/bid/panel_main/" + mainId, function(data) {
        $("#panelMain").html(data);
        func_update_panel_main();
    });
}

function update_panel_main_head() {
    const mainId = getMainId();
    $.post("/administration/module/boiler/bid/panel_main_head/" + mainId, function(data) {
        $("#panelMain_head").html(data);
        func_update_panel_main();
    });
}

function update_panel_main_body() {
    const mainId = getMainId();
    $.post("/administration/module/boiler/bid/panel_main_body/" + mainId, function(data) {
        $("#panelMain_body").html(data);
        func_update_panel_main();
    });
}


function update_panel_info() {
    const mainId = getMainId();
    $.post("/administration/module/boiler/bid/panel_info/" + mainId, function(data) {
        $("#panelInfo").html(data);
        func_update_panel_info();
    });
}

function update_panel_all_total() {
    update_panel_total();
    update_panel_supply_total();
}

function update_panel_total() {
    const mainId = getMainId();
    $.post("/administration/module/boiler/bid/get_all_total/" + mainId, function(data) {
        $("#panelTotal_total").html(Price.priceSet(data));
    });
}

function update_panel_supply_total() {
    let mainId = getMainId();
    $.post("/administration/module/boiler/bid/get_all_supply_total/" + mainId, function(data) {
        $("#panelTotal_supply_total").html(Price.priceSet(data));
    });
}

function update_org(org) {
    return new Promise((resolve, reject) => {
        update_numb(org).then(() => {
            update_panel_header();
            resolve();
        });
    });
}

function getOrgSelect() {
    return $("#orgSelect").val();
}

function update_numb(org) {
    return new Promise((resolve, reject) => {
        let mainId = getMainId();
        $.post("/administration/module/boiler/bid/update_numb/" + mainId, { org }, function() {
            resolve();
        });
    });
}

function save_files() {
    let mainId = getMainId();
    let data = $("#filesModalForm").serialize();
    $.post("/administration/module/boiler/bid/save_files/" + mainId, data, function() {
        update_panel_info();
    });
}

function delFilesSweer(id) {
    swal({
        title: "Удалить файл",
        text: "Вы действительно хотите удалить этот файл?",
        icon: "warning",
        buttons: {
            confirm: {
                text: "Удалить",
                value: true,
                visible: true,
                className: "",
                closeModal: true,
            },
            cancel: {
                text: "Отмена",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
    }).then(willDel => {
        if (willDel) {
            delFile(id);
        }
    });
}

function delFile(id) {
    $.post("/administration/module/boiler/bid/del_files/" + id, function() {
        update_panel_info();
    });
}

function xeditable_title() {
    let mainId = getMainId();
    $('#title').editable({
        type: 'text',
        pk: mainId,
        emptytext: "Не указано",
        url: '/administration/module/boiler/bid/xeditable_change',
        title: 'Введите заголовок'
    });
}

function xeditable_folder_name() {
    $('.folder_name').editable({
        type: 'text',
        name: "name",
        emptytext: "Не указано",
        url: '/administration/module/boiler/bid/xeditable_change_folder',
        title: 'Введите наименование папки',
        validate: function(value) {
            if ($.trim(value) == '') {
                return 'Это поле обязательно к заполнению';
            }
        }
    });
}

function xeditable_comment_main() {
    let mainId = getMainId();
    $('#main_comment').editable({
        type: 'textarea',
        name: 'comment',
        pk: mainId,
        emptytext: "Не указано",
        url: '/administration/module/boiler/bid/xeditable_change',
        title: 'Введите комментарий'
    });
}

function xeditable_comment_type() {
    const emptytext = "Не указано";
    $('.type_comment').each(function() {
        var current_element = $(this);
        current_element.editable({
            type: 'textarea',
            name: 'comment',
            emptytext,
            url: '/administration/module/boiler/bid/xeditable_change_type',
            title: 'Введите комментарий',
            success: (responce, newValue) => {
                let id = current_element.data("pk");
                $(".tc_" + id).editable('setValue', newValue);
            }
        });
    });
}

function change_allow_category(id, status, category) {
    dis_allow_category_btn(category, !status);
    $.post("/administration/module/boiler/bid/change_allow_category", { id, status, category }, function() {

    });
}

function dis_allow_category_btn(id, status) {
    $("#btn_" + id).prop("disabled", status);
}

function del_boiler_bid_data_modal(id) {
    swal({
        title: "Удалить",
        text: "Вы действительно хотите удалить?",
        icon: "warning",
        buttons: {
            confirm: {
                text: "Удалить",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
            cancel: {
                text: "Отмена",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
    }).then(willDel => {
        if (willDel) {
            del_boiler_bid_data(id);
        }
    });
}

function del_boiler_bid_data(id) {
    $.post("/administration/module/boiler/bid/del_boiler_bid_data/" + id, function() {
        update_panel_main();
    });
}

function del_boiler_bid_folder_modal(id) {
    swal({
        title: "Удалить",
        text: "Вы действительно хотите удалить папку?",
        icon: "warning",
        buttons: {
            confirm: {
                text: "Удалить",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
            cancel: {
                text: "Отмена",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
    }).then(willDel => {
        if (willDel) {
            del_boiler_bid_folder(id);
        }
    });
}

function del_boiler_bid_folder(id) {
    $.post("/administration/module/boiler/bid/del_boiler_bid_folder/" + id, function() {
        update_panel_main_head();
    });
}

function add_boiler_bid_data_modal(type_id, category) {
    swal({
        title: "Создание заявки",
        text: "Вы действительно хотите взять заявку?",
        icon: "info",
        buttons: {
            confirm: {
                text: "Взять",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
            cancel: {
                text: "Отмена",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
    }).then(toTake => {
        if (toTake) {
            add_boiler_bid_data(type_id, category);
        }
    });
}

function add_boiler_bid_data(type_id, category) {
    $.post("/administration/module/boiler/bid/add_boiler_bid_data/", { id: type_id, category }, function() {
        update_panel_main();
    });
}

function get_type_select_insert(section, type = null) {
    $("#typeSelect").empty();
    get_type_select(section, type).then(data => {
        $("#typeSelect").html(data).change();
    });
}

function get_section_select_insert(section) {
    $("#sectionSelect").empty();
    get_section_select(section).then(data => {
        $("#sectionSelect").html(data).change();
    });
}

function get_type_select(section, type = null) {
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/type_select", { section, type }, function(data) {
            resolve(data);
        });
    });
}

function get_section_select(section) {
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/section_select", { section }, function(data) {
            resolve(data);
        });
    });
}

function get_boiler_bid(data = []) {
    let mainId = getMainId();
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/get_boiler_bid", { data, id: mainId }, function(result) {
            resolve(JSON.parse(result));
        });
    });
}
/*
$('#typeModal').on('hidden.bs.modal', function () {
    get_boiler_bid(["section", "type"]).then(result => {
        get_section_select_insert(result.section);
        get_type_select_insert(result.section, result.type);
    });
});
*/
function check_open_modal(id) {
    if ($("#" + id).hasClass("in")) {
        return true;
    } else {
        return false;
    }
}

function uid_data_change(id, uid, name) {
    const select = $('#dataUidSelect');
    select.empty();
    if (uid != '') {
        const data = { id: uid, text: name };
        const newOption = new Option(data.text, data.id, false, false);
        select.append(newOption);
    }
    select.trigger('change');
    $("#dataUidBtn").data("id", id);
    open_modal("dataUidModal");
}

function save_uid_data_change(This) {
    let id = $(This).data("id");
    let field = "uid";
    let value = $("#dataUidSelect").val();
    save_field_data(id, field, value).then(() => {
        update_panel_main();
    });
    append_user_chat(value);
}

function append_user_chat(uid) {
    return new Promise((resolve, reject) => {
        let id = getMainId();
        $.post("/administration/module/boiler/bid/append_user_chat", { id, uid }, function() {
            resolve();
        });
    });
}

let ess = {};
let ess_restore = true;

function easy_select_save(id, type = 'select') {
    let value = $("#" + id).val();
    let text = "";
    switch (type) {
        case "select":
            let data = $("#" + id).select2("data");
            if (data.length > 0) {
                text = data[0].text;
            }
            break;
    }
    ess = {
        id,
        type,
        text,
        value,
    };
    ess_restore = true;
}

function false_ess_restore() {
    ess_restore = false;
}

function isNotEmpty(obj) {
    for (let key in obj) {
        return true;
    }
    return false;
}

function easy_select_restore() {
    if (isNotEmpty(ess)) {
        switch (ess.type) {
            default: $("#" + ess.id).val(ess.value);
            break;
            case "select":
                    let id = ess.id;
                let data = { id: ess.value, text: ess.text };
                if ($('#' + id).find("option[value='" + data.id + "']").length || data.id === null) {
                    $('#' + id).val(data.id).trigger('change');
                } else {
                    let newOption = new Option(data.text, data.id, true, true);
                    $('#' + id).append(newOption).trigger('change');
                }
                break;
        }
    }
}

$(document).on('hidden.bs.modal', '.customModal', function() {
    if (ess_restore) {
        easy_select_restore();
    }
});

function save_field_type_modal(field, save, id) {
    return new Promise((resolve, reject) => {
        let value = $("#" + save).val();
        save_field_type(id, field, value).then(() => {
            resolve();
        });
    });
}

function insert_field_type(data) {
    return new Promise((resolve, reject) => {
        let id = getMainId();
        $.post("/administration/module/boiler/bid/insert_filed_type", { data, id }, function() {
            resolve();
        })
    });
}

function del_boiler_bid_type_modal(id) {
    swal({
        title: "Удалить",
        text: "Вы действительно хотите удалить тип?",
        icon: "warning",
        buttons: {
            confirm: {
                text: "Удалить",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
            cancel: {
                text: "Отмена",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
    }).then(del => {
        if (del) {
            del_boiler_bid_type(id).then(() => {
                update_panel_main();
                update_panel_info();
            });
        }
    });
}

function del_boiler_bid_type(id) {
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/del_boiler_bid_type", { id }, function() {
            resolve();
        });
    });
}

function change_type_selected(id) {
    let mainId = getMainId();
    Cookies.set("boiler_bid_type_" + mainId, id);
    update_panel_main();
}

function clear_select_save(id) {
    false_ess_restore();
    save_field(id, '').then(() => {
        $("#" + id + "Select").val("").trigger('change');
        update_panel_info();
    });
}

function modal_view_uid_client_info(id) {
    $("#client_info_modal").modal("show");
    $("#client_info_modal_body").html("Загрузка данных...");
    get_client_info(id).then(data => {
        const str = [];
        //console.log(data)
        for (key in data) {
            str.push(`<div class="mb5">
                <span>${data[key].title}: </span>
                <span>${data[key].data}</span>
            </div>`);
        }
        $("#client_info_modal_body").html(str.join(""));
    });
}

function get_client_info(id) {
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/get_client_info/" + id, function(data) {
            resolve(JSON.parse(data));
        });
    });
}

function toggle_tov_list(id) {
    return new Promise((resolve, reject) => {
        $("#tov_list_" + id).slideToggle(function() {
            resolve();
        });
        $("#tov_list_btn_" + id).toggleClass("fa-chevron-up");
        $("#tov_list_btn_" + id).toggleClass("fa-chevron-down");
    });
}

function check_open_tov_list(id) {
    return $("#tov_list_" + id).css("display") === "block";
}

function load_tov_list(id) {
    console.log("load");
    const load = $("#tov_list_load_" + id);
    load.html("Загрузка данных...");
    get_tov_list(id).then(data => {
        load.html(data);
    });
}

function get_tov_list(id) {
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/get_tov_list/" + id, function(data) {
            resolve(data);
        });
    });
}

async function checkbox_toggle(id, action, selector = ".checkbox_detail_") {
    const lw = new LW;
    lw.on();
    const jobs = [];
    $(selector + id).each((i, item) => {
        const l_id = $(item).data("id");
        $(item).prop("checked", action);
        jobs.push({
            id: l_id,
            action: +action,
        });
    });
    checkbox_all_change(id, check_all_checkbox(id));
    for (job of jobs) {
        await save_field_data_detail(job.id, "checkbox", job.action);
    }
    console.log("message")
    lw.off();
    update_panel_all_total();
    update_panel_main_head();
}

function checkbox_all_change(id, action, selector = "#checkbox_all_") {
    $(selector + id).prop("checked", action);
}

function check_all_checkbox(id, selector = ".checkbox_detail_") {
    let flag = true;
    $(selector + id).each((i, elem) => {
        if (!$(elem).prop("checked")) {
            flag = false;
            return false;
        }
    });
    return flag;
}

function check_none_checkbox(id, selector) {
    let flag = false;
    $(selector + id).each((i, elem) => {
        if ($(elem).prop("checked")) {
            flag = true;
            return false;
        }
    });
    return flag;
}

function dis_btn(action, selector) {
    $(selector).prop("disabled", action);
}

function create_sales_modal() {
    const mainId = getMainId();
    $("#create_sales_modal").modal("show");
    const body = $("#create_sales_modal_body");
    body.html("Загрузка данных...");
    load_type_list(mainId).then(data => {
        body.html(data);
        body.append(`<label for="union_sales" class="mt20" style="font-weight: bold;"><input type="checkbox" id="union_sales" class="mr20" name="union" checked>Объединять товары?</label>`);
    });
}

function create_spec_modal() {
    const mainId = getMainId();
    $("#create_spec_modal").modal("show");
    const body = $("#create_spec_modal_body");
    body.jstree({
        "core": {
            "themes": {
                "responsive": false
            },
            // so that create works
            "check_callback": true,
            'data': {
                'url': function(node) {
                    return "/administration/module/boiler/bid/get_type_list_spec/" + mainId; // Demo API endpoint -- Replace this URL with your set endpoint
                },
                'data': function(node) {
                    return {
                        'parent': node.id
                    };
                }
            }
        },
        "plugins": ["dnd", "types", "checkbox"]
    });
    body.on("changed.jstree", function(e, data) {
        data.selected.forEach(function(elem) {
            if (elem.search('v') != -1)
                console.log(elem);

        });
    });
    // body.html("Загрузка данных...");
    // load_type_list_spec(mainId).then(data => {
    //     body.html(data);
    // });
}

function error_modal_create_sales(data) {
    swal("Внимание!", "Вы не можете создать продажу пока не заполнены: " + data.join(', '), "warning");
}

function load_type_list(id) {
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/get_type_list/" + id, function(data) {
            resolve(data);
        });
    });
}

function load_type_list_spec(id) {
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/get_type_list_spec/" + id, function(data) {
            resolve(data);
        });
    });
}

function create_spec_pre() {
    const body = $("#create_spec_modal_body");
    fetch('/administration/module/boiler/bid/get_spec/', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json;charset=utf-8'
            },
            body: JSON.stringify(body.jstree().get_selected(true))
        }).then(res => res.blob())
        .then(res => {
            const aElement = document.createElement('a');
            aElement.setAttribute('download', 'test.pdf');
            const href = URL.createObjectURL(res);
            aElement.href = href;
            // aElement.setAttribute('href', href);
            aElement.setAttribute('target', '_blank');
            aElement.click();
            URL.revokeObjectURL(href);
        });
    // console.log();
    // body.jstree().get_selected(true).forEach(function(elem) {
    //     if (elem.id.search('v') != -1)
    //         console.log(elem);

    // });
    // const data = $("#create_sales_modal_body").serialize();
    // create_sales(data).then((sales_id) => {
    //     window.open(`/administration/module/sales/order/${sales_id}`);
    // }, () => {
    //     swal("Внимание", "Товары отсутствуют", "error");
    // });

}

function create_sales_pre() {
    const data = $("#create_sales_modal_body").serialize();
    create_sales(data).then((sales_id) => {
        window.open(`/administration/module/sales/order/${sales_id}`);
    }, () => {
        swal("Внимание", "Товары отсутствуют", "error");
    });
}

function create_sales(type_id) {
    return new Promise((resolve, reject) => {
        let mainId = getMainId();
        $.post("/administration/module/boiler/bid/create_sales/" + mainId, type_id, function(data) {
            if (data === "") {
                reject()
            } else {
                resolve(data);
            }
        });
    })
}

function remove_boiler_bid_users_modal(id) {
    swal({
            title: "Удалить пользователя",
            text: "Вы действительно хотите удалить пользователя?",
            icon: "warning",
            buttons: {
                confirm: {
                    text: "Удалить",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                },
                cancel: {
                    text: "Отмена",
                    value: false,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
            },
        })
        .then((del) => {
            if (del) {
                del_boiler_bid_users(id).then(() => {
                    update_panel_info();
                });
            }
        });
}

function send_notif_help_modal() {
    const data = $("#send_notif_helpForm").serializeArray();
    const str = [];
    data.forEach(elem => {
        switch (elem.name) {
            default: str.push(elem.name + "=" + elem.value);
            break;
            case "send_notif_helpSelect":
                    str.push("uid[]=" + elem.value);
                break;
            case "send_notif_helpTextarea":
                    str.push("text=" + elem.value);
                break;
        }

    });
    $("#send_notif_helpSelect").val(null).trigger('change');
    $("#send_notif_helpTextarea").val("");
    const res = str.join("&");
    send_notif_help(res);
}

function send_notif_help(data) {
    //{id:"",uid:[],text:""}
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/send_notif_help", data, function() {
            resolve();
        })
    });
}

function select_all_modal() {
    swal({
            title: "Выбрать все позиции",
            text: "Вы действительно хотите выбрать все позиции?",
            icon: "info",
            buttons: {
                confirm: {
                    text: "Выбрать",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                },
                cancel: {
                    text: "Отмена",
                    value: false,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
            },
        })
        .then((select) => {
            if (select) {
                select_all_tov(1).then(() => {
                    update_panel_main();
                    update_panel_all_total();
                });
            }
        });
}

function select_all_off_modal() {
    swal({
            title: "Отменить выбор всех позиций",
            text: "Вы действительно хотите отменить выбор всех позиций?",
            icon: "info",
            buttons: {
                confirm: {
                    text: "Выполнить",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                },
                cancel: {
                    text: "Отмена",
                    value: false,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
            },
        })
        .then((select) => {
            if (select) {
                select_all_tov(0).then(() => {
                    update_panel_main();
                    update_panel_all_total();
                });
            }
        });
}

function select_all_tov(checkbox = 1) {
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/boiler/bid/select_all_tov/" + id, { checkbox }, function() {
            resolve();
        });
    });
}

function folder_modal_select() {
    return $("#folder_modal_select");
}

function folder_modal_input() {
    return $("#folder_modal_name");
}

function folder_modal(id, folder_id = "") {
    window.event.stopPropagation();
    $("#folder_modal").modal("show");
    $("#folder_modal_type_id").val(id);
    const inp_name = folder_modal_input();
    const sel = folder_modal_select();
    inp_name.val("").hide();
    sel.html(``);
    sel.append(`<option value="">Добавить в новую папку</option>`);
    if (folder_id != "") {
        sel.append(`<option value="null">Убрать из папки</option>`);
    }
    get_folder().then(data => {
        if (data.length == 0 || folder_id == "") {
            inp_name.show();
        }
        data.forEach(elem => {
            sel.append(`<option value="${elem.id}" ${folder_id == elem.id ? "selected" : ""}>${elem.name}</option>`);
        });
    })
}

function get_folder() {
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/boiler/bid/get_folder/" + id, function(data) {
            resolve(JSON.parse(data));
        });
    });
}

function folder_s() {
    const data = $("#folder_modal_body").serializeArray();
    folder_doit(data).then(() => {
        update_panel_main_head();
    });
}

function folder_doit(data) {
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/boiler/bid/folder_doit/" + id, data, function(data) {
            resolve();
        });
    });
}

function toggle_folder_name_inp(This) {
    const val = $(This).val();
    const inp_name = folder_modal_input();
    if (val === "") {
        inp_name.show();
    } else {
        inp_name.hide();
    }
}

function toggle_folder(This, selector) {
    const el = $(selector);
    el.slideToggle(function() {
        if (el.is(":hidden")) {
            Cookies.remove(selector);
        } else {
            Cookies.set(selector, true);
        }
    });
    const elem = $(This).children();
    elem.toggleClass("fa-chevron-down");
    elem.toggleClass("fa-chevron-up");
}

function add_boiler_bid_users_modal(data) {
    //{bid_id, type, uid, text}
    add_boiler_bid_users({ bid_id: data.bid_id, type: data.type, uid: data.uid }).then(() => {
        append_user_chat(data.uid);
        send_notif_help({ id: data.bid_id, uid: data.uid, text: `Вы были добавлены как ${data.text}` });
        update_panel_info();
    });
}

function progress_hover_color(This) {
    const elem = $(This);
    const base_color = elem.data("base_color");
    const main_key = elem.data("key");
    let color = base_color;
    const panelProgress = $("#panelProgress");
    panelProgress.children().each((i, element) => {
        const el = $(element);
        const key = el.data("key");
        change_color(el, color);
        if (key == main_key) {
            const default_color = el.data("default_color");
            color = default_color;
        }
    });
}

function progress_hover_out_color() {
    const panelProgress = $("#panelProgress");
    const progress_active = panelProgress.find(".progress_active");
    const progress_active_color = progress_active.data("base_color");
    const progress_active_key = progress_active.data("key");
    let color = progress_active_color;
    panelProgress.children().each((i, element) => {
        const el = $(element);
        const key = el.data("key");
        change_color(el, color);
        if (key == progress_active_key) {
            const default_color = el.data("default_color");
            color = default_color;
        }
    });
}

function change_color(el, color) {
    el.children().eq(0).css("background-color", color);
    el.children().eq(1).css("border-left", "10px solid " + color);
}

function files_comment_x() {
    const files_comment = $(".files_comment");
    files_comment.editable({
        type: 'text',
        title: 'Введите коммент',
        emptytext: 'Не указано',
        url: '/administration/module/boiler/bid/bid_files',
        name: 'files',
        params: function(params) {
            params.attach_id = $(this).data("attach_id");
            return params;
        },
    });
    files_comment.on('save', function(e, params) {
        update_panel_info();
    });
}

async function change_active_color(id, This) {
    const item = $(This);
    const active = item.prop("checked");
    const inp = $(`.input_color_type_${id}`);
    if (active) {
        inp.show();
    } else {
        inp.hide();
    }
    await save_field_type(id, "active_color", +active);
}

async function change_color_bid_type(id, This) {
    const item = $(This);
    await save_field_type(id, "color", item.val());
}

async function change_checkbox_detail(id, name, value) {
    checkbox_all_change(id, check_all_checkbox(id));
    await save_field_data_detail(id, name, value);
    update_panel_all_total();
    update_panel_main_head();
}

function delete_sales_link_modal(id) {
    swal({
            title: "Вы уверены?",
            text: "Удалить связь с продажей?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then(async(willDelete) => {
            if (willDelete) {
                const bid_id = getMainId();
                await delete_sales_link({ sales_id: id, bid_id });
                update_panel_info();
            }
        });
}

async function delete_sales_link(data) {
    await $.post(`/administration/module/boiler/bid/delete_sales_link`, data);
}