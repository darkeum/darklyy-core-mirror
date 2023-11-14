function save_field(field, value, id = false){
    return new Promise((resolve, reject) => {
        let mainId;
        if(id){
            mainId = id;
        } else {
            mainId = getMainId();
        }
        $.post("/administration/module/boiler/bid/save_field/", {id:mainId, field, value}, function(){
            resolve();
        });
    });
}

function save_field_data_detail(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/save_field_data_detail", {id, field, value}, function(){
            resolve();
        });
    });
} 

function save_field_type(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/save_field_type", {id, field, value}, function(){
            resolve();
        });
    });
}

function save_field_data(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/save_field_data", {id, field, value}, function(){
            resolve();
        });
    });
}

function save_field_folder(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/save_field_folder", {id, field, value}, function(){
            resolve();
        });
    });
}

function append_boiler_bid(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/append_field_boiler_bid", {id, field, value}, function(){
            resolve();
        });
    });
}

function remove_boiler_bid(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/remove_field_boiler_bid", {id, field, value}, function(){
            resolve();
        });
    });
}

function add_boiler_bid_users(data){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/add_boiler_bid_users", data, function(){
            resolve();
        });
    });
}

function del_boiler_bid_users(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/boiler/bid/del_boiler_bid_users/"+id, function(){
            resolve();
        });
    });
}

function xeditable_comment_data(){
    $('.comment_data').editable({
        type: 'textarea',
        name: 'comment',
        emptytext: "Не указано",
        url: '/administration/module/boiler/bid/xeditable_change_data',
        title: 'Введите комментарий'
    });
}

function status_bid_auto(id = null){
    return new Promise((resolve, reject) => {
        if(id === null){
            id = $("#bbid").val();
        }
        $.post("/administration/module/boiler/bid/status_bid_auto/"+id, function(){
            resolve();
        })
    });
}

