$(document).on("click", ".transferBtn", function(){
    let mainId = $(this).data("id");
    
    $.post("/ajax.php?do=transferSendStorage", {id:mainId}, function(data){
        $("#jqStorageTransfer").trigger("reloadGrid");
        $("#modal-active-"+mainId).magnificPopup("close");
        switch(data){
            case '1':
                notif("success", "Информация", "Перемещение успешно проведено");
                return 1;
            break;
            case '2':
                notif("system", "Информация", "Перемещение успешно отменено");
                return 2;
            break;
            case '3':
                notif("danger", "Ошибка", "Что-то пошло не так...");
                return 3;
            break;
            case '4':
                notif("danger", "Ошибка", "Ошибка");
                return 4;
            break;
        }
    });
});

function removeTransfer(This){
    let status = +$(This).data("status");
    let viewId = $(This).data("view-id");
    let id = $(This).data("id");
    $("#removeTransferModalBtn").data("id", id);
    $(".removeTransferModalNum").html(viewId);
    if(status === 1){
        $("#removeTransferModalError").modal("show");
    } else {
        $("#removeTransferModal").modal("show");
    }
}

$(document).on("click", "#removeTransferModalBtn", function(){
    let id = $(this).data("id");
    $.post("/ajax.php?do=removeTransfer", {id:id}, function(){
        $("#jqStorageTransfer").trigger("reloadGrid");
        notif("success", "Информация", "Перемещение успешно удалено");
    });
});

function mergingModal(){
    const checks = $(".cbox:checked");
    if(checks.length > 0){
        $("#merging_modal").modal("show");
    } else {
        swal("Ошибка!", "Вы не выбрали перемещения", "error");
    }
}

function mergingModalCreate(){
    const merging_id = $("#merging_id").val();
    const transfer_id = [];
    const data = $("#table_form_jqStorageTransfer").serializeArray();
    for(elem of data){
        const name = 'jqg_jqStorageTransfer_';
        if(elem.name.indexOf(name) != -1){
            transfer_id.push(elem.name.replace(name, ''));
        }
    }
    mergingCreate({transfer_id, merging_id}).then(merging_id => {
        window.open('/administration/module/merging/detail/'+merging_id);
    });
} 