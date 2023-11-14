function archive_modal(value, id){
    const str = value == 0 ? "работу" : "архив";
    swal({
        title: "Перенести в "+str,
        text: "Вы действительно хотите перенести заявку в "+str,
        icon: "info",
        buttons: {
            confirm: {
                text: "Перенсти",
                value: true,
                visible: true,
                className: "",
                closeModal: true,
            },
            cancel: {
                text: "Отмена",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
    }).then(move => {
        if(move){
            save_field('archive', value, id).then(() => {
                $('#jqBid').trigger('reloadGrid');
            });
        }
    });
}

function create_boiler(){
    window.open('/administration/module/boiler/bid/add');
}