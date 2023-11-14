$(function(){
    func_update_list();
});

$transport.update_list_func = func_update_list;

$transport.change_driver = async function(data){
    await $.post(`/administration/module/logistics/logistics_driver/change_driver`, data);
}

function func_update_list(){
    x_change_count();
}

function getMainId(){
    return $("#mainId").val();
}

function update_info_data(){
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/logistics/logistics_driver/update_info/"+id, function(data){
            resolve(data);
        })
    });
}

function update_list_data(){
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/logistics/logistics_driver/update_list/"+id, function(data){
            resolve(data);
        })
    });
}

function pick_up_modal(supply_o){
    const checks = get_checkbox_checked();
    const request = {supply_o, id:checks};
    if(checks.length > 0){
        swal({
            title: "Подтверждение",
            text: "Выбранные товары забраны, остальные будут возвращены как не забранные",
            icon: "info",
            buttons: ["Отмена", true],
        })
          .then((send) => {
            if (send) {
                pick_up_lw(request)
            }
        });
    } else {
        swal({
            title: "Подтверждение",
            text: "Товары в заявке не забраны, они будут возвращены как не забранные",
            icon: "warning",
            buttons: ["Отмена", true],
        })
          .then((send) => {
            if (send) {
                pick_up_lw(request);
            }
        });
    }
}

function pick_up(request){
    //{supply_o, id}
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/logistics_driver/pick_up", request, function(){
            resolve();
        })
    });
}

async function pick_up_lw(request){
    const lw = new LW;
    lw.on();
    await pick_up(request);
    await update_list();
    lw.off();
}

function x_change_count(){
    const item = $('.item__count');
    item.editable({
        url: '/administration/module/logistics/logistics_driver/x_update_field_detail',
        title: 'Введите количество',
        name: "count",
        validate: function(value) {
            if($.trim(value) == '') {
                return 'Заполните поле!';
            }
        }
    });
    item.on("save", function(e, params){
        setTimeout(() => {
            const id = $(this).data("pk");
            const data = get_field_data(id);
            set_total(id);
            set_all_count(data.supply_o);
        }, 100);
    });
}

function get_field(id){
    const result = {};
    const item = $(`#item_${id}`);
    result.item = item;
    result.count = item.find(".item__count");
    result.total = item.find(".item__total");
    return result;
}

function get_field_data(id){
    const result = {};
    const field = get_field(id);

    result.count = Price.string_to_number(field.count.text());
    result.price = Price.string_to_number(field.item.data("price"));
    result.total = Price.string_to_number(field.total.text());

    result.id = id;
    result.supply_o = field.item.data("supply_o");

    return result;
}

function get_total(id){
    const data = get_field_data(id);
    const total = data.count * data.price;
    return +total;
}

function get_all_total(supply_o){
    let total = 0;
    $(`.item_${supply_o}`).each((i, item) => {
        const id = $(item).data("id");
        total += get_total(id);
    });
    return +total;
}

function set_total(id){
    const field = get_field(id);
    const data = get_field_data(id);
    const total = get_total(id);
    field.total.html(Price.priceSet(total).replace(".", ","));
    set_all_total(data.supply_o);
}

function set_all_total(supply_o){
    const all_total = get_all_total(supply_o);
    $(`#total_${supply_o}`).html(Price.priceSet(all_total).replace(".", ","));
}

function get_all_count(supply_o){
    let count = 0;
    $(`.item_${supply_o}`).each((i, item) => {
        const id = $(item).data("id");
        const data = get_field_data(id);
        count += data.count;
    });
    return +count;
}

function set_all_count(supply_o){
    const all_count = get_all_count(supply_o);
    $(`#count_${supply_o}`).html(all_count);
}