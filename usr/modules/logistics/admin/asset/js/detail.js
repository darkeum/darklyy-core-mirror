$(function(){
    func_update_list();
    view_type_init();
    $("#search").on("keydown", function(e){
        if(e.keyCode == 13){
            update_list_lw();
        }
    });

    view_type_checked($(".view_type:checked").parent());

    $("#transport_companies_modal_select").select2();
});

function get_link(id){
    const result = {};
    result.id_ = id;
    const item = result.item = $(`#item_${id}`);
    result.sku = item.find(`.item__sku`);
    result.name = item.find(`.item__name`);
    result.count = item.find(`.item__count`);
    result.total = item.find(`.item__total`);
    result.unit = item.find(`.item__unit`);
    result.date_ship = item.find(`.item__date_ship`);
    result.date_end = item.find(`.item__date_end`);
    return result;
}

function get_data(id){
    const link = get_link(id);
    const result = {};
    result.id_ = id;
    result.sku = link.sku.text();
    result.name = link.name.text();
    result.count = Price.string_to_number(link.count.text());
    result.total = Price.string_to_number(link.total.text());
    result.unit = link.unit.text();
    result.date_ship = link.date_ship.text();
    result.date_end = link.date_end.text();
    return result;
}

$transport.update_list_func = func_update_list;

$driverModal.save = logistics_driver;

function driverModal(){
    $driverModal.open();
}

function view_type_init(){
    $(".view_type").on("change", function(){
        view_type_checked($(this).parent());
        update_list_lw();
    });
}

function view_type_checked(item){

    $(".legend_item").css({
        "font-weight": "400",
    });
    item.css({
        "font-weight": "bold",
    });
}

function update_field_detail(request){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/update_field_detail", request, function(){
            resolve();
        })
    });
}

function update_info_data(){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/update_info", function(data){
            resolve(data);
        })
    });
}

async function update_list_data(){
    const request = $("#panel_info_form").serialize();
    return await $.post("/administration/module/logistics/update_list", request);
}

function func_update_list(){
    x_transport_companies();
    count_change();
}

function item_delete_modal(id){
    swal({
        title: "Вы уверены?",
        text: "Удалить позицию",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            item_delete(id)
            .then(() => update_list_lw())
        }
      });
}

function item_delete(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/detail_delete/"+id, function(){
            resolve();
        })
    });
}

function count_change(){
    $(".item__count").on("click", function(){
        count_change_item(this);
    });
}

function count_change_item(This){
    const item = $(This);
    const id = item.data("id");
    const count = item.data("count");
    const value = Price.string_to_number(item.text());
    const inp = "count_input";
    item.off().html(`<input type="number" value="${value}" class="form-control" id="${inp}">`);
    $(`#${inp}`).focus().select();
    $(`#${inp}`).on("blur keydown", function(p){
        if(p.type == "blur" || (p.type == "keydown" && p.which == 13)){
            const new_value = $(this).val() == "" ? count : $(this).val();
            const parent = $(this).parent();
            parent.html(new_value).on("click", function(){
                count_change_item(this);
            });
        }
    });
}

// function count_change_item(This){
//     const item = $(This);
//     const id = item.data("id");
//     const count = item.data("count");
//     const value = Price.string_to_number(item.text());
//     const inp = "count_input";
//     item.off().html(`<input type="number" value="${value}" class="form-control" id="${inp}">`);
//     $(`#${inp}`).focus().select();
//     $(`#${inp}`).on("blur keydown", function(p){
//         if(p.type == "blur" || (p.type == "keydown" && p.which == 13)){
//             let new_value = $(this).val();
//             let flag = true;
//             update_field_detail({id, field: "count", value:new_value});
//             if(new_value == ""){
//                 flag = false;
//                 new_value = count;
//             }
//             const parent = $(this).parent();
//             parent.html(new_value).on("click", function(){
//                 count_change_item(this);
//             });
//             const icb = "item__count-bold";
//             if(flag){
//                 parent.addClass(icb);
//             } else {
//                 parent.removeClass(icb);
//             }
//         }
//     });
// }

function logistics_driver(request){
    return new Promise((resolve, reject) => {
        request.count = {};
        request.id.forEach(id => {
            const data = get_data(id);
            request.count[ id ] = data.count;
        });
        $.post("/administration/module/logistics/logistics_driver/add", request, function(){
            resolve();
        })
    });
}

function get_transport_companies(){
    return JSON.parse($("#transport_companies").val());
}

function x_transport_companies(){
    const source = get_transport_companies();
    $('.item__transport_companies').editable({
        name: "transport_companies",
        emptytext: "Не выбрано",
        type: "select",
        url: '/administration/module/logistics/x_update_field_detail',
        source,
    });
}

function logistics_auto_add_modal(){
    swal({
        title: "Вы уверены?",
        text: "Загрузить позиции",
        icon: "warning",
        buttons: ["Отмена", true],
      })
      .then((load) => {
        if (load) {
            logistics_auto_add().then(data => {
                update_list().then(() => swal("Успешно!", `Добавлено позиций: ${data.count}`, "success"));
            }, error => {
                swal("Ошибка!", `Произошла ошибка (${error})`, "error");
            });
        }
      });
}

function logistics_auto_add(){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/auto_add", function(data){
            try{
                const json = JSON.parse(data);
                resolve(json);
            } catch(e){
                reject(data);
            }
        });
    });
}

function notif_modal(id){
    const modal = $("#notifModal");
    modal.modal("show");
    $("#notifModalBtn").off().on("click", function(){
        const form = $("#notifModalForm");
        const request = form.serialize();
        logistics_notif_send(id, request).then(() => {
            update_list_lw();
        });
        modal.modal("hide");
        form[0].reset();
    });
}

function logistics_notif_send(id, request){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/notif_send/"+id, request, function(){
            resolve();
        });
    });
}

async function mas_delete(id){
    await $.post("/administration/module/logistics/mas_detail_delete", {id});
}


function mas_transport_companies_modal(){
    const checks = get_checkbox_checked();
    if(checks.length > 0){
        const modal = $("#transport_companies_modal");
        modal.modal("show");
        $("#transport_companies_modal_btn").off().on("click", async function(){
            const transport_companies = $("#transport_companies_modal_select").val();
            modal.modal("hide");
            await mas_transport_companies(checks, transport_companies);
            await update_list_lw();
        });
    } else {
        checkbox_error();
    }
}

async function mas_transport_companies(id, transport_companies){
    await $.post("/administration/module/logistics/mas_transport_companies", {id, transport_companies});
}

function date_modal(){
    const modal = $("#date_modal");
    modal.modal("show");
    $("#date_modal_btn").on("click", function(){
        $("#date_ship_send").val($("#date_ship").val());
        $("#date_ship_active_send").prop("checked", $("#date_ship_active").prop("checked"))
        modal.modal("hide");
        update_list_lw();
    });
}