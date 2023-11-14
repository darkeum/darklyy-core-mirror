$(function(){
    //console.log("~");
    $(document).on("click", ".deleteBtn", function(){
        //console.log("~");
        let id = $(this).data("id");
        $("#modal-delete-"+id).magnificPopup("close");
        $.post("ajax.php?do=delSupplyOrder", {id:id}, function(data){
            setTimeout(function (){
                $('#jqSupplyOrder').trigger('reloadGrid');
            }, 150);
            console.log(data);
        });
    });
});

function del_modal(id){
    swal({
        title: "Вы уверены?",
        text: "Удалить заявку!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then(async (willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            await (await fetch(`/administration/module/supply/order_delete/${id}`)).text();
            await $('#jqSupplyOrder').trigger('reloadGrid');
            lw.off();
        }
      });
}

function close_supply_order(id, close){
    const text = close == 0 ? "Открыть" : "Закрыть";
    const form = new FormData;
    form.append("id", id);
    form.append("field", "close");
    form.append("value", close);
    swal({
        title: "Вы уверены?",
        text: `${text} счёт`,
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then(async (willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            await (await fetch(`/administration/module/supply/update_supply_order`, {
                method: "POST",
                body: form,
            })).text();
            await $('#jqSupplyOrder').trigger('reloadGrid');
            lw.off();
        }
      });
}