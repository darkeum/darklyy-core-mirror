$(function(){
    //console.log("~");
    $(document).on("click", ".logisticsBtn", function(){
    let id = $(this).data("id");
        $.post("/ajax.php?do=logisticsSetArrived", {logistics_id: id}, function(data){
            $("#modal-active-"+id).magnificPopup("close");
        $('#jqSupplyLogistics').trigger('reloadGrid');
            switch(data){
                case '1':
                    notif("success", "Информация", "Поставка оборудования успешно подтверждена");
                break;
                case '2':
                    notif("system", "Информация", "Поставка оборудования отмечена как не доставленная");
                break;
                case '3':
                    notif("danger", "Ошибка", "Что-то пошло не так...");
                break;
            }
            
        });
    });
});

/*
function removeReceipts(This){
    let status = +$(This).data("status");
    let viewId = $(This).data("view-id");
    let id = $(This).data("id");
    $("#removeReceiptsModalBtn").data("id", id);
    $(".removeReceiptsModalNum").html(viewId);
    if(status === 1){
        $("#removeReceiptsModalError").modal("show");
    } else {
        $("#removeReceiptsModal").modal("show");
    }
}

$(document).on("click", "#removeReceiptsModalBtn", function(){
    let id = $(this).data("id");
    $.post("/ajax.php?do=removeReceipts", {id:id}, function(){
        $("#jqReceipts").trigger("reloadGrid");
        notif("success", "Информация", "Документ поступления успешно удалено");
    });
});*/