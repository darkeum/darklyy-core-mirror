const transferModal = {
    id: "transferModal",
    open(){
        $("#"+this.id).modal("show");
    },
    close(){
        $("#"+this.id).modal("hide");
    },
    create(id){
        const form = $("#transferModalForm").serialize();
        this.this_create("id="+id+"&"+form).then(transfer_id => {
            if(transfer_id == "" || transfer_id.length > 10){
                swal("Ошибка", transfer_id, "error");
            } else {
                window.open(`/administration/module/storage/transfer/detail/${transfer_id}`);
            }
        });
    },
    this_create(data){
        return new Promise((resolve, reject) => {
            resolve("Функция создания перемещения не обьявлена, обратитесь к программистам!");
        });
    }
}