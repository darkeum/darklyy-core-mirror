$(function(){
    $(document).on("click", ".syncBtn", function(){
        let transfer_id = $(this).data("id");
        $("#modal-active-"+transfer_id).magnificPopup("close");
        $.post("/ajax.php?do=storage_transfer_sync", {transfer_id:transfer_id}, function(data){
            //$("#jqStorageTransfer").trigger("reloadGrid");
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

function endTransfer(id){
    checkStock(id).then(() => {
        check_minus(id).then(() => {
            check_count(id).then(() => {
                modal_o('#modal-active-'+id+'');
            }, data => {
                swal('Ошибка!', `Количество превышает допустимое! (id: ${data})`, 'error');
            });
        }, data => {
            error_minus(data);
        });
    }, data => {
        error_stock(data);
    });
}