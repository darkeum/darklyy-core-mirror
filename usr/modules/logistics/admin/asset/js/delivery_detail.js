$driverModal.save = logistics_delivery_driver;

function update_info_data(){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/delivery/update_info", function(data){
            resolve(data);
        })
    });
}

function update_list_data(){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/delivery/update_list", function(data){
            resolve(data);
        })
    });
}

function logistics_delivery_driver(request){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/logistics_delivery_driver/add", request, function(){
            resolve();
        })
    });
}

function init_delivery_date(){
    $(".delivery_date").editable({
        type: 'date',
        name: 'delivery_date',
        placement: 'right',
        viewformat: "dd.mm.yyyy",
        title: 'Введите дату',
        url: '/administration/module/logistics/delivery/x_update',
    });
}

$transport.update_list_func = function(){
    init_delivery_date();
}

$(function(){
    init_delivery_date();
});