function delModal(id){
    swal({
        title: "Вы уверены?",
        text: "Вы точно хотите удалить заявку на сборку?",
        icon: "info",
        buttons: {
            confirm: {
                text: "Удалить",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
            cancel: {
                text: "Отмена",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
      })
      .then((willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            update_field_main(id, 'del', 1).then(() => {
                swal("Успешно", "Заявка удалена", "success");
                $('#jqAssembly').trigger('reloadGrid');
                lw.off();
            });
        }
      });
}

function update_field_main(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/storage/assembly/update_field_main", {id, field, value}, function(){
            resolve();
        });
    });
}