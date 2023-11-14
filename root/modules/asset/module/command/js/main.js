function update_grid(){
    $('#jqCommand').trigger('reloadGrid');
}

function del_modal(id){
    swal({
        title: "Вы уверены?",
        text: "После удаления вы не сможете восстановить этот документ",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            const command = new Command;
            const lw = new LW;
            lw.on();
            command.delete(id).then(() => {
                update_grid();
                lw.off();
            });
        }
    });
}


