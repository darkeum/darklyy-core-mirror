function add_modal(){
    $("#addModal").modal("show");
}

function add_modal_save(request = {}){
    const form = $("#addModalForm").serializeArray();
    let error = true;
    form.forEach(elem => {
        if(elem.name === "cid" && elem.value != ""){
            error = false;
        }
    });
    if(error){
        notif("error","Выберите товар", "Выберите товар");
    } else {
        $("#addModal").modal("hide");
        const lw = new LW;
        lw.on();
        const pump = new Pump;
        pump.add(form).then(id => {
            lw.off();
            reload_grid();
            const link = "/administration/module/catalog_v2/pump/edit/"+id;
            if("open" in request){
                location.href = link;
            } else {
                window.open(link);
            }
            
        });
    }
}

function reload_grid(){
    $('#jqPump').trigger('reloadGrid');
}

function del_modal(id){
    const pump = new Pump;
    pump.del_modal(id).then(() => {
        reload_grid();
    })
}