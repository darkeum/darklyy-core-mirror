function update_info_data(){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/storage/delivery_storage/update_info", function(data){
            resolve(data);
        })
    });
}

function update_list_data(){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/storage/delivery_storage/update_list", function(data){
            resolve(data);
        })
    });
}