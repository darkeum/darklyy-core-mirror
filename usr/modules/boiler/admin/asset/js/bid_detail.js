$(function(){
    $("#tov_list").sortable({
      update: function( event, ui ) {
        
        let position = $(".position");
        let json = {};
        position.each(function(index, elem) {
            $(this).html(index + 1);
            json[$(elem).data("id")] = index + 1;
        });
      
        let res = JSON.stringify(json);
        $.post("/administration/module/boiler/bid/data_set_position", {data:res}, function(){

        });
      },
      axis: "y",
      handle: ".position"
    });

    $(document).on("click", ".AddProduct", function(){
        const cat = get_category();
        Cookies.set('catalogSearch', '1');
        Cookies.set('checkCatalog', 'point_'+cat);
    });

    xeditable_comment_data();
});

function get_category(){
    return $("#category").val();
}

function getMainId(){
    return $("#mainId").val();
}

function update_header(){
    return new Promise((resolve, reject) => {
        const mainId = $("#mainId").val();
        $.post("/administration/module/boiler/bid/data_detail_header/"+mainId, function(data){
            $("#con_header").html(data);
            xeditable_comment_data();
            resolve();
        });
    });
}

function update_body(){
    return new Promise((resolve, reject) => {
        const mainId = $("#mainId").val();
        $.post("/administration/module/boiler/bid/data_detail_body/"+mainId, function(data){
            $("#tov_list").html(data);
            resolve();
        });
    });
}

function discountSet(data){
    return data + '%';
}

function fields_link(id){
    const elem = $("#tr_"+id);
    const result = {};
    result.count = elem.children(".count");
    result.price_type = elem.children().children(".priceTypeSelect");
    result.s_price = elem.children(".price");
    result.discount = elem.children(".discount");
    result.discount_sum = elem.children(".discount_sum");
    result.type_nds = elem.children(".type_nds");
    result.nds = elem.children(".nds");
    result.total = elem.children(".total");
    result.s_price_local = elem.children(".s_price_local").children().eq(0);

    result.position = elem.children(".position");
    result.comment = elem.children(".comment").children().eq(0);

    return result;
}

function collection_fields(id){
    const elem = $("#tr_"+id);
    const link = fields_link(id);
    const result = {};
    result.count = +link.count.text();
    result.price_type = +link.price_type.val();
    result.s_price = string_to_number(link.s_price.text());
    result.discount = string_to_number(link.discount.text());
    result.discount_sum = string_to_number(link.discount_sum.text());
    result.type_nds = string_to_number(link.type_nds.text());
    result.nds = string_to_number(link.nds.text());
    result.total = string_to_number(link.total.text());
    result.s_price_local = string_to_number(link.s_price_local.text());

    result.position = string_to_number(link.position.text());
    result.comment = link.comment.text();
    result.nds_includes = +includes_nds_prop();
    result.rep = +elem.hasClass("replace");
    result.cid = +elem.data("cid");

    return result;
}

function get_data_price(id){
	const data = collection_fields(id);
	const price = new Price;
	return price.get_all({
        price: data.s_price,
        count: data.count,
        discount: data.discount,
        nds_proc: data.type_nds,
        nds_includes: data.nds_includes,
    });
}  

function change_price_type(id, cid, price_type){
    let wait = new Promise((resolve, reject) => {
        if(price_type == 4){
            resolve();
        } else {
            get_price(cid, price_type).then(value => {
                change_field({id, selector:".price", value, conversion:priceSet});
                resolve();
            });
        }
    });
    wait.then(() => {
        change_total(id);
    });
}

function get_price(cid, price_type){
    return new Promise((resolve, reject) => {
        $.post("ajax.php?do=get_price_item", {cid, price_type}, function(data){
            resolve(data);
        });
    });
}

$priceTypeSelect.set_price = function(data){
    const id = data.id;
    if(data.type == 4){
        save_change_id(id)
    } else {
        change_field({id, selector:".price", value:data.data.price, conversion:priceSet});
        change_total(id);
    }
}

function change_field_input_on(data = {id, selector, value, conversion}){
    data = conversion_check(data);
    const elem = $("#tr_"+data.id).children(data.selector);
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
    const elem = $("#tr_"+data.id).children(data.selector);
    change_field({id:data.id, selector:data.selector, value:data.value, conversion:data.conversion});
    elem.removeData("change");
    change_total(data.id);
}

function change_field(data = {id, selector, value, conversion}){
    data = conversion_check(data);
    const elem = $("#tr_"+data.id).children(data.selector);
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

function get_price_type(id){
    return +$("#tr_"+id).children(".price_type").children().first().val();
}

function check_price_type(id){
    const price_type = get_price_type(id);
    return price_type === 4;
}

function warning_price_type(){
    swal("Ошибка!", "Поменяйте тип цены на произвольный", "warning");
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
    // const fields = collection_fields(id);
    // const includesNds = includes_nds_prop();
    // let total = 0;

    // if(includesNds){
    //     total = (fields.count * fields.s_price) - (fields.count * fields.s_price * (fields.discount / 100));
    //     //changeNds(parent);
    // } else {
    //     let nds = calculating_nds(id);
    //     total = (fields.count * fields.s_price + nds) - (nds + fields.count * fields.s_price * (fields.discount / 100));
    // }
    // total = total.toFixed(2);
    // return Number(total);
    const data = get_data_price(id);
    return data.total;
}

function calculating_nds(id){
    // const fields = collection_fields(id);
    // const includesNds = includes_nds_prop();
    // let nds = 0;
    // if(includesNds){
    //     nds = (fields.s_price * fields.count - fields.count * fields.s_price * (fields.discount / 100)) * fields.type_nds / (100 + fields.type_nds);
    // } else {
    //     nds = (fields.s_price * fields.count - fields.count * fields.s_price * (fields.discount / 100)) * (fields.type_nds / 100);
    // }
    // nds = nds.toFixed(2);
    // return Number(nds);
    const data = get_data_price(id);
    return data.nds;
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

function calculating_discount_sum(id){
    // const fields = collection_fields(id);
    // let discount_sum = fields.s_price * fields.count * fields.discount / 100;
    // discount_sum = discount_sum.toFixed(2);
    // return Number(discount_sum);
    const data = get_data_price(id);
    return data.discount_sum;
}

function save_change_id(id){
    $("#tr_"+id).addClass("change");
}

function save_change(){
    const mainId = $("#mainId").val();
    const save = {};
    $("#tov_list").children(".change, .replace").each(function(i, elem){
        const id = $(elem).data("id");
        save[id] = collection_fields(id);
    });
    if(Object.keys(save).length > 0){
        $.post("/administration/module/boiler/bid/save_data_detail", {data: save, id:mainId}, function(){
            swal("Успешно", "Данные сохранены", "success");
            update_body();
        });
    }
}

function del_tov_modal(id){
    swal({
        title: "Удалить",
        text: "Вы действительно хотите удалить этот товар?",
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
        if(willDelete) {
            del_tov(id).then(() => {
                update_body();
            });
        }
      });
}

function del_tov(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/del_tov_data_detail/"+id, function(){
            resolve();
        });
    });
}

//get_boiler_bid_data_detail_field
function gbbddf(id, field){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/get_boiler_bid_data_detail_field", {id, field}, function(price){
            resolve(price);
        })
    });
}

function change_local_data_modal(data = {id, func, field, modal_id}){
    data.func(data.id, data.field).then(item => {
        $(`#${data.modal_id}`).modal("show");
        $(`#${data.modal_id}_input`).val(item);
        $(`#${data.modal_id}_btn`).data("id", data.id);
    });
}

function save_local_data_modal(data = {id, field, value}){
    save_field_data_detail(data.id, data.field, data.value).then(() => {
        //update_body();
        let value = data.value;
        const no_val = "Не указан";
        let flag = true;
        const elem = $("#tr_"+data.id).children("."+data.field);

        switch(data.field){
            default:
                if(value == ""){
                    value = no_val;
                }
            break;
            case "date_post":
                if(value == ""){
                    value = no_val;
                } else {
                    const date_post = value.split("-");
                    value = date_post[2]+'.'+date_post[1]+'.'+date_post[0];
                }
            break;
            case "s_price_local":
                flag = false;
                get_supply_price_temp(data.id).then(temp => {
                    elem.html(temp);
                    set_all_supply_total(get_all_supply_total());
                });
            break;
        }
        if(flag){
            elem.children().eq(0).html(value);
        }
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

function set_all_supply_total(value){
    $(".all_supply_total").html(priceSet(value));
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

function get_supply_price_temp(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/get_supply_price_temp/"+id, function(data){
            resolve(data);
        });
    });
}

// function change_tov(tov_id){
//     window.open('/administration/module/boiler/bid/addProductMin/'+tov_id,'1549627856974','width=1400,height=850,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
// }

function get_tov_item(id, cid){
    return new Promise((resolve, reject) => {
        const field = collection_fields(id);
        $.post("/administration/module/boiler/bid/get_tov_item", {id, cid, field}, function(data){
            resolve(data);
        });
    });
}

function replace_tov_item(id, item){
    const save = $("#tr_"+id);
    save.after(item);
    save.remove();
    $("#tr_"+id).addClass("replace");
}
 
function end_quest(){
    const id = $("#mainId").val();
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
            save_field_data(id, "status", 2).then(() => {
                update_header();
                notif_end_request();
                status_bid_auto();
            });
            swal("Успешно", "Заявка завершена", "success");
        } 
    });
}

function start_quest(){
    const id = $("#mainId").val();
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
            save_field_data(id, "status", 1).then(() => {
                update_header();
                status_bid_auto();
            });
            swal("Успешно", "Завершение заявки отменененно", "success");
        } 
    });
}

function notif_end_request(){
    return new Promise((resolve, reject) => {
        const id = $("#bbid").val();
        $.post("/administration/module/boiler/bid/notif_end_request/"+id, function(){
            resolve();
        })
    });
}

function change_purchase_price(){
    const step_name = [
        "","Цена закупки","Сумма закупки","Разница",
    ];
    const name = $("#purchase_price_title");
    let step = name.data("step");
    step++;
    if(step > 3){
        step = 1;
    }
    name.data("step", step);
    name.html(step_name[step]);

    const tov_list = $("#tov_list");
    const tov_item = tov_list.find(".tov_item");

    let all_supply_total = 0;
    tov_item.each((i, elem) => {
        const count = $(elem).find(".count");
        const count_value = string_to_number(count.text());
        const purchase_price = $(elem).find(".purchasePrice");
        const purchase_price_value = purchase_price.data("price");
        const total = $(elem).find(".total");
        const total_value = string_to_number(total.text());
        switch(+step){
            case 1:
                purchase_price.html(priceSet(purchase_price_value));
                all_supply_total += +purchase_price_value * count_value;
            break;
            case 2:
                const new_value_2 = purchase_price_value * count_value;
                purchase_price.html(priceSet(new_value_2));
                all_supply_total += +new_value_2;
            break;
            case 3:
                const new_value_3 = purchase_price_value * count_value;
                const new_value_4 = total_value - new_value_3;
                purchase_price.html(priceSet(new_value_4));
                all_supply_total += +new_value_4;
            break;
        }
        set_all_supply_total(all_supply_total);
    }); 
}

$(document).on("change", ".checks", function(){
    const sub = $("#sub");
    if($(".checks:checked").length > 0){
        sub.prop("disabled", false);
    } else {
        sub.prop("disabled", true);
    }
});

function subAct(){
    const type = $("#act").val();
    switch(type){
        default:
            alert('Действие не указано! Обратитесь к администратору');
        break;
        case "update_checked_price":
            swal({
                title: "Вы уверены?",
                text: "Обновление цен у выделенных позиций",
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
              .then((update) => {
                if (update) {
                    update_checked_price();
                }
              });
        break;
        case "discount":
			$("#masDiscountInp").val("");
			$("#masDiscountModal").modal("show");
        break;
        case "type_price":
            $("#masTypePriceModal").modal("show");
        break;
        case "del":
			$("#masDelModal").modal("show");
		break;
    }
}

function update_checked_price(){
    $(".checks:checked").each((i, elem) => {
        const id = $(elem).val();
        if(id != "on"){
            const data = collection_fields(id);
            $("#tr_"+id).find(".price_type").children().eq(0).val(data.price_type).change();
        }
    });
}

function setMasDiscount(){ 
	const discount = $("#masDiscountInp").val();
	$(".checks:checked").each(function(i, elem){
		let id = $(elem).val();
		if(!isNaN(id)){
            change_field({id, selector: ".discount", value: discount, conversion: discountSet});
            change_total(id);
		}
	});
}

function changeMasTypePrice(){
	const price_type = $("#masTypePriceModalSel").val();
	$(".checks:checked").each(function(i, elem){
		const id = $(elem).val();
		if(!isNaN(id)){
            const link = fields_link(id);
            link.price_type.val(price_type).change();
		}
	});
}

function masDel(){
    const data_del = [];
    const lw = new LW;
    lw.on();
    $(".checks:checked").each(function(i, elem){
		const id = $(elem).val();
		if(!isNaN(id)){
            data_del.push(del_tov(id));
		}
    });
    Promise.all(data_del).then(() => {
        update_body().then(() => {
            lw.off();
        });
    })
}

function get_weight_modal(){
    const id = $("#mainId").val();
    const lw = new LW;
    lw.on();
    get_weight(id).then(weight => {
        lw.off();
        if(weight.error.length > 0){
            swal("Ошибка", weight.error, "error");
        } else {
            const div = get_weight_text(weight);
            swal({
                title: "Информация",
                icon: "info",
                content: div,
            });
        }
    });
}

function get_weight_text(weight){
    const empty_weight = [];
    if(weight.empty_weight.length > 0){
        weight.empty_weight.forEach(item => {
            const sku = item.sku === null ? "отсутствует" : item.sku;
            empty_weight.push(`<li>поз. ${item.position} ${item.title} артикул ${sku}</li>`);
        })
    }
    const empty_weight_str = empty_weight.join("");
    const div = document.createElement("div");
    let empty_weight_str2 = "";
    if(empty_weight_str.length > 0){
        empty_weight_str2 = `<div>Нулевые позиции: <ul>${empty_weight_str}</ul></div>`;
    }
    div.innerHTML = `Вес равен <b>${weight.weight.toFixed(0)}</b> кг. ${empty_weight_str2}`;
    return div;
}

function get_weight(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/get_weight/"+id, function(weight){
            try{
                const json = JSON.parse(weight);
                resolve(json);
            } catch(e){
                resolve({error: `Ошибка данных (${e.message})`});
            }
        });
    });
}