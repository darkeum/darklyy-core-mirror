//console.log("~");

$(document).on("click", "#search", function(){
    //console.log("~");
    getDocShipStorage();
});

$(document).on("keyup", "#docNum", function(e){
    //console.log(e.keyCode);
    if(e.keyCode == 13){
        getDocShipStorage();
    }
});

function getDocShipStorage(){
    let docNum = $("#docNum").val();
    if(docNum.length > 0){
        $.post("/ajax.php?do=getDocShipStorage", {docNum: docNum}, function(data){
            $("#docPrint").html(data);
        });
    }
}

function close_request(id){
    swal({
        title: "Закрыть заявку",
        text: "Вы действительно хотите закрыть заявку?",
        icon: "info",
        buttons: {
            confirm: {
                text: "Выполнить",
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
      .then((willClose) => {
        if (willClose) {
            change_sales_field_value(id, "allow_shipment", '0').then(() => {
                $('#jqDocPrint').trigger('reloadGrid');
            });
        }
      });
}

function change_sales_field_value(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=change_sales_field_value", {id, field, value}, function(){
            resolve();
        });
    });
}