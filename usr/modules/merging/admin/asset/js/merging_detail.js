$(function(){
    header_func();
    body_func();
});

function getMainId(){
    return $("#mainId").val();
}

function get_link(id){
    const result = {};

    const parent = result.tr = $(`#tr_${id}`);
    result.count = parent.find(".count");
    result.price = parent.find(".price");
    result.total = parent.find(".total");

    return result;
}

function get_data(id){
    const result = {};
    const link = get_link(id);
    result.id = id;
    result.cid = link.tr.data("cid");
    result.count = Price.string_to_number(link.count.html());
    result.price = Price.string_to_number(link.price.html());
    result.total = Price.string_to_number(link.total.html());
    return result;
}

function updateHeaderTitle(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/detail_header_title/"+mainId, function(data){
            $("#con_header_title").html(data);
            document.title = data+" - Панель управления";
            resolve();
        });
    });
}

function updateHeader(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/detail_header/"+mainId, function(data){
            $("#con_header").html(data);
            header_func();
            resolve();
        });
    });
}

function updateBodyBtn(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/detail_body_btn/"+mainId, function(data){
            $("#detail_body_btn").html(data);
            resolve();
        });
    });
}

function updateBody(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/detail_body/"+mainId, function(data){
            $("#tov_list").html(data);
            body_func();
            resolve();
        });
    });
}

function header_func(){
    editable_count();
    editable_stock();
    editable_org();
    editable_type();
    editable_date();
}

function body_func(){
    editable_count_detail();
    star_change_init();
    editable_price_detail();
    total_view();
}

function editable_count(){
    const mainId = getMainId();
    $('#count').editable({
        type: 'text',
        pk: mainId,
        url: '/administration/module/merging/x_change_filed',
        title: 'Введите количество',
        validate: function(value){
            return validate_number(value);
        }
    });
}

function editable_count_detail(){
    $('.count').editable({
        name: 'count',
        type: 'text',
        url: '/administration/module/merging/x_change_filed_detail',
        title: 'Введите количество',
        validate: function(value){
            return validate_number(value);
        }
    }).on("shown", function(e, editable){
        xeditable_select_value(editable);
    }).on("save", async function(e, params){
        await params.response
        total_view();
    });
}

function xeditable_select_value(editable){
    setTimeout(function(){
        const elem = editable.input.$input;
        elem.select();
    }, 0);
}

function validate_number(value){
    if ($.isNumeric(value) == '') {
        return 'Разрешены только цифры';
    }
}

function remove_item_modal(id){
    swal({
        title: "Удалить товар",
        text: "Вы точно хотите удалить?",
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
    .then((willDelete) => {
        if (willDelete) {
            remove_item(id).then(() => {
                updateBody();
            });
        }
    });
}

function remove_item(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/merging/remove_item/"+id, function(){
            resolve();
        });
    });
}

function get_stock(){
    const stock = $("#stock_data").val();
    return JSON.parse(stock);
}

function editable_stock(){
    const mainId = getMainId();
    const stock = get_stock();
    $("#stock_id").editable({
        type: "select",
        pk: mainId,
        url: "/administration/module/merging/x_change_filed",
        title: "Выберите склад",
        source: stock,
    }).on("save", function(){
        updateHeader();
        updateBody();
    });
}

function get_org(){
    const org = $("#org_data").val();
    return JSON.parse(org);
}

function editable_org(){
    const mainId = getMainId();
    const org = get_org();
    $("#org").editable({
        type: "select",
        pk: mainId,
        url: "/administration/module/merging/x_change_filed",
        title: "Выберите организацию",
        source: org,
    }).on("save", function(){
        updateHeaderTitle();
        updateHeader();
    });
}

function get_type(){
    const type = $("#type_data").val();
    return JSON.parse(type);
}

function editable_type(){
    const mainId = getMainId();
    const type = get_type();
    $("#type").editable({
        type: "select",
        pk: mainId,
        url: "/administration/module/merging/x_change_filed",
        title: "Выберите тип",
        source: type,
    }).on("save", function(){
        updateHeader();
    });
}

function get_merging_data(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/get_merging_data/"+mainId, function(data){
            resolve(JSON.parse(data));
        });
    });
}

function check_minus_count(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/check_minus_count_merging/"+mainId, function(data){
            if(data == ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function check_cid_have(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/check_cid_have/"+mainId, function(data){
            if(data == ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function end_quest(type){
    check_cid_have().then(() => {
        check_minus_count().then(() => {
            swal({
                title: type == 1 ? "Завершить заявку" : "Отменить завершение заявки",
                text: type == 1 ? "Вы точно хотите завершить заявку?" : "Вы точно хотите отменить заявку?",
                icon: "info",
                buttons: {
                    confirm: {
                        text: type == 1 ? "Завершить" : "Выполнить",
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
            .then((endBtn) => {
                if (endBtn) {
                    end().then(() => {
                        updateHeader();
                        updateBody();
                        updateBodyBtn();
                    });
                }
            });
        }, data => {
            swal("Ошибка", "Запрещено "+(type == 1 ? "комплектовать" : "раскомпектовывать")+" в минус (id: "+data+")", "error");
        });
    }, data => {
        swal("Ошибка", data, "error");
    });
}

function end(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/end_quest/"+mainId, function(){
            resolve();
        });
    });
}

function check_minus_count2(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/check_minus_count_merging2/"+mainId, function(data){
            if(data == ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function start_quest(type){
    check_minus_count2().then(() => {
        swal({
            title: type == 1 ? "Отменить завершение заявки" : "Завершить заявку",
            text: type == 1 ? "Вы точно хотите отменить заявку?" : "Вы точно хотите завершить заявку?",
            icon: "info",
            buttons: {
                confirm: {
                    text: type == 1 ? "Выполнить" : "Завершить",
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
        .then((startBtn) => {
            if (startBtn) {
                start().then(() => {
                    updateHeader();
                    updateBody();
                    updateBodyBtn();
                });
            }
        });
    }, data => {
        swal("Ошибка", "Запрещено "+(type == 1 ? "раскомпектовывать" : "комплектовать")+" в минус (id: "+data+")", "error");
    });
}

function start(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/merging/start_quest/"+mainId, function(){
            resolve();
        });
    });
}

function update_filed(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/merging/update_filed", {id, field, value}, function(){
            resolve();
        });
    });
}

function update_filed_detail(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/merging/update_filed_detail", {id, field, value}, function(data){
            if(data == ""){
                resolve();
            } else {
                swal("Ошибка!", data, "error");
                reject();
            }
        });
    });
}

transferModal.this_create = (data) => {
    return new Promise((resolve, reject) => {
        $.post("/administration/module/merging/create_transfer_merging", data, function(transfer_id){
            resolve(transfer_id);
        })
    });
}

const objectModal = {
    id: "objectChangeModal",
    open(){
        $("#"+this.id).modal("show");
    },
    close(){
        $("#"+this.id).modal("hide");
    },
    change(){
        const lw = new LW;
        lw.on();

        const id = getMainId();
        const field = "object";
        const value = $("#object").val();
        
        update_filed(id, field, value).then(() => {
            updateHeader().then(() => {
                lw.off();
            });
        });
    }
}

function editable_date(){
    const pk = getMainId();
    $('#date').editable({
        type: "date",
        pk,
        url: "/administration/module/merging/x_change_filed",
        format: 'yyyy-mm-dd',    
        viewformat: 'dd.mm.yyyy',    
        datepicker: {
                weekStart: 1
        }
    });
}

function star_change_init(){
    $(".star_change").on("click", function(){
        const id = $(this).data("id");
        const star = +!$(this).data("star");
        const class_text = $(this).data("class_text");
        $(this).toggleClass(class_text);
        update_filed_detail(id, "star", star);
    });
}

function editable_price_detail(){
    $('.price').editable({
        name: "price",
        type: 'text',
        url: '/administration/module/merging/x_change_filed_detail',
        title: "Введите цену",
        validate: function(value){
            return validate_number(value);
        },
        display: function(value) {
            $(this).text(Price.priceSet(value));
        } 
    }).on("shown", function(e, editable){
        xeditable_select_value(editable);
    }).on("save", async function(e, params){
        await params.response
        total_view();
    });
}

function total_view(){
    let total = 0;
    $(".tr_item").each((i, item) => {
        const id = $(item).data("id");
        const link = get_link(id);
        const data = get_data(id);
        const t = data.count * data.price;
        total += t;
        link.total.html(Price.priceSet(t));
    });
    $(".allSum").html(Price.priceSet(total));
}

function createMergingAdditional(){
    swal({
        title: "Вы уверены?",
        text: "Создать доукомплектовка",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then(async (willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            const data = await ( await fetch(`/administration/module/merging/create_merging_additional/${getMainId()}`) ).json();
            lw.off();
            window.open(`/administration/module/merging/detail_additional/${data.id}`);
        }
      });
}