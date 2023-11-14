function updateHead(){
    let id = $("#mainId").val();
    $.post("/administration/module/storage/assembly/assembly_detail_head/"+id, function(data){
        $("#admin-form").html(data);
    });
}

function updateBody(){
    let id = $("#mainId").val();
    $.post("/administration/module/storage/assembly/assembly_detail_body/"+id, function(data){
        $("#dev_list").html(data);
    });
}

function delTov(This){
    let id = $(This).data("id");
    let text = $(This).data("text");

    $("#delModalTov").html(text);
    $("#delModalBtn").data("id", id);
    $("#delModal").modal("show");
}

function delModalBtn(This){
    let id = $(This).data("id");
    del_tov(id);
}

function del_tov(id){
    $.post("/administration/module/storage/assembly/assembly_del_tov", {id:id}, function(){
        updateBody();
    });
}

function update_field(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/storage/assembly/update_field", {id, field, value}, function(){
            resolve();
        });
    });
}

function update_status(id, status){
    $.post("/administration/module/storage/assembly/assembly_update_status", {id:id, status:status}, function(){
        updateBody();
    });
}

function cancel_tov_modal(id){
    $("#cancelTovModalBtn").data("id", id);
    $("#cancelTovModal").modal("show");

    $("#cancelTovModalInp").focus();
    $("#cancelTovModalInp").select();
}

function cancel_tov_modal_btn(This){
    let id = $(This).data("id");
    let count = $("#cancelTovModalInp").val();
    cancel_tov(id, count);
    $("#cancelTovModalInp").val(0);
}

function cancel_tov(id, count){
    Promise.all([update_field(id, 'count', count), update_field(id, 'status', 3), update_field(id, 'count_now', count),]).then(() => {
        updateBody();
    });
}

function check_status(id){
    return new Promise(function(resolve, reject){
        $.post("/administration/module/storage/assembly/assembly_check_status", {id:id}, function(data){
            if(data === ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function final_modal(id){
    check_status(id).then(function(){
        $("#finalModalBtn").data("id", id);
        $("#finalModal").modal("show");
    }, function(data){
        swal("Ошибка!", data, "error");
    });
    
}

function final_modal_btn(This){
    let id = $(This).data("id");
    const lw = new LW;
    lw.on();
    final(id).then(function(){
        lw.off();
        updateHead();
        updateBody();
        swal("Успешно!", "Заявка успешно отправлена", "success");
    });
}

function final(id){
    return new Promise(function(resolve, reject){
        $.post("/administration/module/storage/assembly/assembly_final", {id:id}, function(){
            resolve();
        });
    });
}

$(document).on("keydown", ".ruberInp", function(e){
    if(e.keyCode === 13){
        $(this).blur();
    }
})

function change_count_focus(This, id){
    if(!$(This).hasClass("change")){
        let count = $(This).text();
        $(This).html(`<input type="number" min="1" onblur="change_count_blur(this, `+id+`)" value="`+count+`" id="changeInp" class="form-control ruberInp">`);
        $(This).addClass("change");
        $("#changeInp").focus();
        $("#changeInp").select();
    }
}

function change_count_blur(This, id){
    let count = $(This).val();
    $(This).parent().removeClass("change");
    $(This).parent().html(count);
    change_count(id, count);
}

function change_count(id, count){
    $.post("/administration/module/storage/assembly/assembly_change_count", {id:id, count:count}, function(){

    });
}

function selectAct(){
    let type = $("#act").val();
    actRouter(type);
}

function getCheckboxActive(){
    let id = [];
    $(".checks:checked").each(function(i, elem){
        let local_id = $(elem).val();
        if(local_id != undefined || local_id != null || local_id != ""){
            id.push(local_id);
        }
    });
    return id;
}

function actRouter(type){
    let id = getCheckboxActive();
    if(id.length > 0){
        switch(type){
            case "confirm":
                confirm_item(id);
            break;
        }
    }
}

function confirm_item(id){
    const job = [];
    id.forEach((value, index, array) => {
        job.push(update_field(value, 'status', 2));
        const count = get_field(value).count_save;
        job.push(update_field(value, 'count', count));
    });
    Promise.all(job).then(() => {
        updateBody();
    });
}

function print_modal(){
    $("#print_modal").modal("show");
}

function get_field(id){
    const tr = $("#tr_"+id);
    const result = [];
    
    result.count_save = +tr.find(".count_save").eq(0).text();

    return result;
}

transferModal.this_create = create_transfer_assembly;

function create_transfer_assembly(data){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/storage/assembly/create_transfer_assembly", data, function(transfer_id){
            resolve(transfer_id);
        })
    });
}

async function transferModalOpen(){
    const id = $("#mainId").val();
    const lw = new LW;
    lw.on();
    const data = await (await fetch(`/administration/module/storage/assembly/check_sales_type/${id}`)).json();
    lw.off();
    if(data.status){
        transferModal.open();
    } else {
        swal("Внимание!", "Продажа, не является заявкой на производство!", "warning");
    }
}