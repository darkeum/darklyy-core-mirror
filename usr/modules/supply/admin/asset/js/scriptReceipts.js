$(function(){
    //console.log("~");
    $(document).on("click", ".receiptsBtn", function(){
    let id = $(this).data("id");
        $.post("/ajax.php?do=receiptsSetActive", {receipt_id: id}, function(data){
            $("#modal-active-"+id).magnificPopup("close");
        $('#jqReceipts').trigger('reloadGrid');
            switch(data){
                case '1':
                    notif("success", "Информация", "Документ поступления успешно проведен");
                break;
                case '2':
                    notif("system", "Информация", "Проведение документа поступления успешно отменено");
                break;
                case '3':
                    notif("danger", "Ошибка", "Что-то пошло не так...");
                break;
				case '4':
                    notif("danger", "Ошибка", "Чтобы провести поступление необходимо приложить оригинал документа поступления!");
                break;
            }
            
        });
    });
});

$(function(){
    $(document).on("click", ".syncBtn", function(){
        let receipts_id = $(this).data("id");
        $("#modal-active-"+receipts_id).magnificPopup("close");
        $.post("/ajax.php?do=receipts_sync", {receipts_id:receipts_id}, function(data){
            $("#jqReceipts").trigger("reloadGrid");
            switch(data){
                case '1':
                    notif("success", "Информация", "Документ успешно зарегистрирован");
                break;
                case '2':
					notif("danger", "Ошибка", "Что-то пошло не так...");
                break;
            }
        });
    });
    
});

function removeReceipts(This){
    let status = +$(This).data("status");
    let viewId = $(This).data("view-id");
    let id = $(This).data("id");
    $("#removeReceiptsModalBtn").data("id", id);
    $(".removeReceiptsModalNum").html(viewId);
    check_status_confirm(id).then(() => {
        $("#removeReceiptsModal").modal("show");
    }, () => {
        $("#removeReceiptsModalError").modal("show");
    });
}

function check_status_confirm(id){
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=check_status_confirm_receipts", {id}, function(data){
            if(data === ""){
                resolve();
            } else {
                reject();
            }
        });
    });
}

$(document).on("click", "#removeReceiptsModalBtn", function(){
    let id = $(this).data("id");
    $.post("/ajax.php?do=removeReceipts", {id}, function(){
        $("#jqReceipts").trigger("reloadGrid");
        notif("success", "Информация", "Документ поступления успешно удалено");
    });
});