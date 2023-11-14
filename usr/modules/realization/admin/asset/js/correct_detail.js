$(function(){
    init_header();
    $("#addFileModalBtn").on("click", async function(){
        $("#addFileModal").modal("hide");
        await $.post("/administration/module/realization/correct/file_add/"+getMainId(), $("#addFileModalForm").serialize());
        clearAll_();
        await update_header();
    })
})

function getMainId(){
    return $("#mainId").val();
}

function update_header(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/realization/correct_detail_header/"+mainId, function(data){
            $("#con_header").html(data);
            init_header();
            resolve();
        });
    });
}

function update_body(){
    return new Promise((resolve, reject) => {
        const mainId = getMainId();
        $.post("/administration/module/realization/correct_detail_body/"+mainId, function(data){
            $("#tov_list").html(data);
            resolve();
        });
    });
} 

function discountSet(data){
    return data + '%';
}

function init_header(){
    x_date();
}

function x_date(){
    $("#date").editable({
        type: 'date',
        pk: getMainId(),
        url: '/administration/module/realization/correct/change_x',
        title: 'Введите дату',
        viewformat: 'dd.mm.yyyy',
    });
}

function collection_fields(id){
    const elem = $("#tr_"+id);
    const result = {};
    result.count = string_to_number(elem.find(".count").text());
    //result.price_type = +elem.find(".price_type").children().first().val();
    result.s_price = string_to_number(elem.find(".s_price").text());
    result.discount = string_to_number(elem.find(".discount").text());
    result.discount_sum = string_to_number(elem.find(".discount_sum").text());
    result.nds_type = string_to_number(elem.find(".nds_type").text());
    result.nds = string_to_number(elem.find(".nds").text());
    result.total = string_to_number(elem.find(".total").text());
    //result.s_price_local = string_to_number(elem.find(".s_price_local").children().eq(0).text());

    result.position = string_to_number(elem.find(".position").text());
    //result.comment = elem.find(".comment").children().eq(0).text();
    result.nds_includes = +includes_nds_prop();
    //result.rep = +elem.hasClass("replace");
    result.cid = +elem.data("cid");

    return result;
}

function change_field_input_on(data = {id, selector, value, conversion}){
    data = conversion_check(data);
    const elem = $("#tr_"+data.id).find(data.selector);
    // console.log(elem.data("change"));
    if(elem.data("change") !== "change"){
        const template = field_input_template(data.id, data.selector, data.value, data.conversion);
        elem.html(template);
        $("#change_field_input").focus();
        $("#change_field_input").select();
        elem.data("change", "change");
    }
}

function change_field_input_off(data = {id, selector, value, conversion}){
    data = conversion_check(data);
    const elem = $("#tr_"+data.id).find(data.selector);
    change_field({id:data.id, selector:data.selector, value:data.value, conversion:data.conversion});
    elem.removeData("change");
    change_total(data.id);
}

function change_field(data = {id, selector, value, conversion}){
    data = conversion_check(data);
    const elem = $("#tr_"+data.id).find(data.selector);
    elem.html(data.conversion(data.value));
}

function conversion_check(data){
    if(!("conversion" in data)){
        data.conversion = (element) => { return element; }
    }
    return data;
}

function field_input_template(id, selector, value, conversion){
    return `<input type="text" id="change_field_input" class="form-control" value="${value}" 
    onblur="change_field_input_off({id:${id}, selector:'${selector}', value:string_to_number(gtv(this)), conversion:${conversion}});" 
    onkeydown="enter_blur(this)"
    >`;
}

function enter_blur(This){
    if(event.keyCode === 13){
        $(This).blur();
    }
}

function includes_nds_prop(value = null){
    if(value === null){
        return $("#includesNds").prop("checked");
    } else {
        $("#includesNds").prop("checked", value);
    }
}

function calculating_total(id){
    const fields = collection_fields(id);
    const includesNds = includes_nds_prop();
    let total = 0;

    if(includesNds){
        total = (fields.count * fields.s_price) - (fields.count * fields.s_price * (fields.discount / 100));
        //changeNds(parent);
    } else {
        let nds = calculating_nds(id);
        total = (fields.count * fields.s_price + nds) - (nds + fields.count * fields.s_price * (fields.discount / 100));
    }
    total = total.toFixed(2);
    return Number(total);
}

function calculating_nds(id){
    const fields = collection_fields(id);
    const includesNds = includes_nds_prop();
    let nds = 0;
    if(includesNds){
        nds = (fields.s_price * fields.count - fields.count * fields.s_price * (fields.discount / 100)) * fields.nds_type / (100 + fields.nds_type);
    } else {
        nds = (fields.s_price * fields.count - fields.count * fields.s_price * (fields.discount / 100)) * (fields.nds_type / 100);
    }
    nds = nds.toFixed(2);
    return Number(nds);
}

function change_total(id){
    const total = calculating_total(id);
    change_field({id, selector:".total", value:total, conversion:priceSet});

    const nds = calculating_nds(id);
    change_field({id, selector:".nds", value:nds, conversion:priceSet});

    const discount_sum = calculating_discount_sum(id);
    change_field({id, selector:".discount_sum", value:discount_sum, conversion:priceSet});

    save_change_id(id);

    set_all_total(get_all_total());
    set_all_nds(get_all_nds());
}

function save_change_id(id){
    $("#tr_"+id).addClass("change");
}

function calculating_discount_sum(id){
    const fields = collection_fields(id);
    let discount_sum = fields.s_price * fields.count * fields.discount / 100;
    discount_sum = discount_sum.toFixed(2);
    return Number(discount_sum);
}

function save_change(){
    const mainId = getMainId();
    const save = {};
    $("#tov_list").find(".change").each(function(i, elem){
        const id = $(elem).data("id");
        save[id] = collection_fields(id);
    });
    if(Object.keys(save).length > 0){
        saveChange(mainId, save).then(() => {
            swal("Успешно", "Данные сохранены", "success");
            update_body();
        });
    }
}

function saveChange(id, data){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/realization/correct_save", {id, data}, function(){
            resolve();
        });
    });
}

function get_all_total(){
    const tov_item = $("#tov_list").find(".tov_item");
    let total = 0;
    tov_item.each((i, elem) => {
        const id = $(elem).data("id");
        const result = collection_fields(id);
        total += result.total;
    });
    return total;
}

function set_all_total(value){
    $(".all_total").html(priceSet(value));
}

function get_all_supply_total(){
    const tov_item = $("#tov_list").find(".tov_item");
    let total = 0;
    tov_item.each((i, elem) => {
        const id = $(elem).data("id");
        const result = collection_fields(id);
        total += result.s_price_local * result.count;
    });
    return total;
}

function get_all_nds(){
    const tov_item = $("#tov_list").find(".tov_item");
    let total = 0;
    tov_item.each((i, elem) => {
        const id = $(elem).data("id");
        const result = collection_fields(id);
        total += result.nds;
    });
    return total;
}

function set_all_nds(value){
    $(".all_nds").html(priceSet(value));
}
 
function end_modal(){
    check_minus().then(() => {
        end_quest();
    }, data => {
        swal("Ошибка", `Нельзя отгружать в минус (${data})`, "error");
    });
}

function end_quest(){
    swal({
        title: "Завершить заявку",
        text: "Вы точно хотите завершить заявку?",
        icon: "info",
        buttons: {
            confirm: {
                text: "Завершить",
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
      .then((action) => {
        if (action) {
            end().then(() => {
                update_header();
                update_body();
                hide_save();
                swal("Успешно", "Заявка завершена", "success");
            });
        } 
    });
}

function end(){
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/realization/correct_end/"+id, function(){
            resolve();
        });
    });
}

function check_minus(){
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/realization/correct_check_minus/"+id, function(data){
            if(data == ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function start_quest(){
    const id = getMainId();
    swal({
        title: "Отменить завершение заявки",
        text: "Вы точно хотите отменить завершение заявки?",
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
      .then((action) => {
        if (action) {
            start().then(() => {
                update_header();
                update_body();
                show_save();
                swal("Успешно", "Завершение заявки отменененно", "success");
            });
        } 
    });
}

function start(){
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/realization/correct_start/"+id, function(){
            resolve();
        });
    }); 
}

function btn_save(){
    return $("#save_change");
}

function show_save(){
    const elem = btn_save();
    elem.show();
}

function hide_save(){
    const elem = btn_save();
    elem.hide();
}

function correctCorrectModalError(){
    swal("Ошибка", "Сначала проведите текущую корректировку", "error");
}

function correctCorrectModal(){
    swal({
        title: "Создание корректировки корректировки",
        text: "Вы точно хотите создать корректировку корректировки?",
        icon: "info",
        buttons: {
            confirm: {
                text: "Создать",
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
    }).then(isCreate => {
        if(isCreate){
            correctCorrect().then(id => {
                window.open('/administration/module/realization/correct_detail/'+id);
            });
        }
    });
}

function correctCorrect(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/realization/correctCorrect/"+id, function(data){
            resolve(data);
        });
    });
}

async function delete_modal(id){
    const lw = new LW;
    lw.on();
    await check_delete(id).then(data => {
        if(data.error){
            swal("Внимание!", data.error, "warning");
        } else {
            swal({
                title: "Вы уверены?",
                text: "Удалить",
                icon: "warning",
                buttons: true,
                dangerMode: true,
              })
              .then(async willDelete => {
                if (willDelete) {
                  await delete_correct_detail(id);
                  lw.on();
                  await update_body();
                  lw.off();
                }
              });
        }
    }, error => {
        swal("Ошибка!", error, "error");
    });
    lw.off();
}

function check_delete(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/realization/correct_check_delete/"+id, async function(json){
            try{
                const data = JSON.parse(json);
                resolve(data);
            } catch(e){
                reject(json);
            }
        });
    });
}

function delete_correct_detail(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/realization/correct_delete/"+id, function(){
            resolve();
        });
    });
}
