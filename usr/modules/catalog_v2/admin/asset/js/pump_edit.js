window.onbeforeunload = function() {
	if($("."+Pump.changeItem).length > 0){
		return "Уйти?";
	}
};

function add_change(This){
    $(This).addClass(Pump.changeItem);
}

function del_all_change(){
    $("."+Pump.changeItem).removeClass(Pump.changeItem);
}

function getMainId(){
    return $("#mainId").val();
}

function save(){
    const form = $("#admin-form").serializeArray();
    const lw = new LW;
    lw.on();
    const id = getMainId();
    const pump = new Pump;
    pump.update(id, form).then(() => {
        lw.off();
        notif("success","Успешно!", "Данные сохранены!");
        del_all_change();
    }, error => {
        lw.off();
        swal({
            title: "Ошибка!",
            text: "Данные не сохранены! Ошибка: "+error,
            icon: "error",
            buttons: {
                cancel: {
                    text: "ОК",
                    value: false,
                    visible: true,
                    className: "",
                    closeModal: true,
                  },
                  confirm: {
                    text: "Удалить",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                  }
            },
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
                lw.on();
                del_all_change();
                pump.delete(id).then(() => {
                    location.href = '/administration/module/catalog_v2/pump';
                });
            }
          });
    });
} 