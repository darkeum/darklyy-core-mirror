$transport.change_driver = async function(data){
    await $.post(`/administration/module/logistics/logistics_delivery_driver/change_driver`, data);
}

function getMainId(){
    return $("#mainId").val();
}

function update_info_data(){
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/logistics/logistics_delivery_driver/update_info/"+id, function(data){
            resolve(data);
        })
    });
}

function update_list_data(){
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/logistics/logistics_delivery_driver/update_list/"+id, function(data){
            resolve(data);
        })
    });
}

function update_field_detail(request){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/logistics_delivery_driver/update_field", request, function(){
            resolve();
        })
    });
}

async function delivered_modal(id){
    swal({
        title: "Вы уверены?",
        text: "Подтвердить доставку груза",
        icon: "info",
        buttons: ["Отмена", true],
      }).then(async willDelivered => {
        if (willDelivered) {
            const lw = new LW;
            lw.on();
            await delivered(id);
            await update_list();
            lw.off();
        }
      });
}

async function delivered(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/logistics/logistics_delivery_driver/delivered/"+id, function(){
            resolve();
        });
    });
}