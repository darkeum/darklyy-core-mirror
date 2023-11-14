async function viewSupplyOrderFunc(id){
        $("#viewSupplyOrder").modal("show");
        const str = await viewSupplyOrderFuncData(id);
        $("#bodyViewSuppplyOrder").html('<ul>'+str+'</ul>');
}

async function viewSupplyOrderFuncData(id){
    return await $.post(`/administration/module/supply/supply_order_modal_view/${id}`);
}

async function removeOnPassFunc(id){
    $("#removeOnPass").modal("show");
    const str = await removeOnPassFuncData(id);
    $("#bodyRemoveOnPass").html('<ul>'+str+'</ul>');
    $("#removeOnPassConfirm").data("id", id);
}

async function removeOnPassFuncData(id){
    return await $.post(`/administration/module/supply/supply_order_modal_delete/${id}`);
}

function updateHead(){
    return new Promise((resolve, reject) => {
        const mainId = $("#mainId").val();
        $.post("/administration/module/supply/ajax/detail_head/"+mainId, function(data){
            $("#dev_head").html(data);
            resolve();
        });
    });
}

function updateBody(){
    return new Promise((resolve, reject) => {
        const mainId = $("#mainId").val();
        $.post("/administration/module/supply/ajax/detail_list/"+mainId, function(data){
            $("#dev_list").html(data);
            resolve();
        });
    });
}


    $(document).on("click", "#removeOnPassConfirm", async function(){
        // let supply_d = $(this).data("id");
        const id = [];
        $(".removeCheck:checked").each((i, elem) => {
            id.push($(elem).val());
        });
        $("#removeOnPass").modal("hide");
        const json = await $.post("/ajax.php?do=removeOnPassSupplyDetail", {id});
        const data = JSON.parse(json);
        if(data.error.length > 0){
            const p = [];
            data.error.forEach((value, index, array) => {
                p.push(`<div>${value}</div>`);
            });
            const error = p.join("");
            const content = document.createElement("div");
            content.innerHTML = error
            swal({
                title: "Ошибка!", 
                icon: "error",
                content,
            });
        }
        notif("success","Успешно удалено", "");
        updateBody();
    });

    function warehousePositionDel(id){
        $("#viewWarehousePositionDel").modal("show");
        $("#warehousePositionDelBtn").data("id", id);
    }
    
    $(document).on("click", "#warehousePositionDelBtn", function(){
        let id = $(this).data("id");
        $.post("/ajax.php?do=warehousePositionDelSupplyDetail", {id:id}, function(data){
            notif("success","Успешно", "");
            updateBody();
        });
    });
    
    //согласовать со складом
    $(document).on("click", "#awtw", function(){
        $("#awtwModal").modal("show");
    });
    
    $(document).on("click", "#awtwSaveBtn", function(){
        let supply_id = window.location.href.split("/").splice(-1, 1).join("");
        let id = get_checks();
        if(id.length > 0){
            $.post("/ajax.php?do=agreeWithTheWarehouseSupply", {supply_id, id}, function(data){
                notif("success","Успешно", "");
            });
        } else {
            notif("error","Ошибка", "Товары не выбраны");
        }
    });

        // let saveList = "";
        // $.post("/ajax.php?do=getOrgList", function(data){
        //     saveList = data;
        // });

        // let saveOrder = "";
        // $.post("/ajax.php?do=getOrderList", function(data){
        //     saveOrder = data;
        // });
        
        $(document).on("click", ".getList", function(){
            let id = $(this).data("myid");
            //console.log(id);
            // $("#new_order_"+id).html(saveList);
            
            $.post("/ajax.php?do=getOrderList", function(data){
                 $("#order_numb-"+id).html(data);
            });
        });
    
    $(document).on("click", ".makeOrder", function(){
        //console.log("~");
        clear_modal();
        const link = $(this).parent().parent().parent();
        const count = link.children(".count").html(); 
        const count_send = link.children(".count_send").html();
        const c = count_send <= count ? count : (count - count_send); 
        $("#countModal").val(c);
        $("#id_modal").val($(this).data("id"));
        $("#sale_id_modal").val($(this).data("salesid"));
        modal_types("one");
        open_modal();
    });

    function clear_modal(){
        $("#new_order").select2("val", "");
        $("#order_numb").select2("val", "");
        // $("#new_order").html(saveList);
        // $("#order_numb").html(saveOrder);
    }

    function modal_types(type){
        switch(type){
            case "one":
                $("#div_count").show();
                $("#modalComplBtn").show();
                $("#modalComplBtn2").hide();
            break;
            case "more":
                $("#div_count").hide();
                $("#modalComplBtn").hide();
                $("#modalComplBtn2").show();
            break;
        }
    }

    function open_modal(){
        $("#modal-compl").modal("show");
    }

    function order_send_mas(){
        clear_modal();
        modal_types("more");
        open_modal();
    }

    function order_send_mas_ajax(){
        let id = get_checks();
        let dd = $("#compl").serialize().replace("&id=", "");
        let id_new = $.param({id});
        
        $.post("/administration/module/supply/ajax/order_send_mas", dd+"&"+id_new, function(){
            update_for_insert();
        });
    }

    $(document).on("click", "#modalComplBtn", function(){
        let dd = $("#compl").serialize();
        $.post("/administration/module/supply/ajax/order_send", dd, function(data){
            update_for_insert(data);
        });
    });

    function update_for_insert(data = "Успешно"){
        notif("success","Информация", data);
        //загрузить новый список
        $.post("/ajax.php?do=getOrderList", function(data1){
            // saveOrder = data1;
        });
        
        //обновление
        updateBody();
    }


$(document).on("click", "#warehousePositionBtn", function(){
    let id = $(this).data("id");
    $("#viewWarehousePosition").modal("hide");
    send_warehousePositionSupplyDetail(id);
});

function send_warehousePositionSupplyDetail(id){
    $.post("/ajax.php?do=warehousePositionSupplyDetail", {id}, function(data){
        notif("success","Успешно", "");
        updateBody();
    });
}


function delModal(id){
    $("#delModalBtn").data("id", id);
    $("#delModal").modal("show");
}

function delTov(This){
    let id = $(This).data("id");
    del_tov(id);
}

function del_tov(id){
    let mainId = window.location.href.split("/").splice(-1, 1).join("");
    $.post("/ajax.php?do=delTovSupplyDetail", {id:id, sid:mainId}, function(){
        updateBody();
    });
}

function iSeeThisFuck(id, status = 5){
    updateValue(id, "status", status).then(() => {
        updateBody();
    });
}

function updateValue(id, field, value){
    return new Promise((resolve, reject) => {
        let supply_id = window.location.href.split("/").splice(-1, 1).join("");
        $.post("/ajax.php?do=updateValueSupplyDetail", {id:id, supply_id:supply_id, field:field, value:value}, function(){
            resolve();
        });
    });
}

$(document).on("change", ".checks", function(){
	check_checks();
});

function check_checks(){
    let checks = $(".checks:checked");
    if(checks.length > 0){
        $("#sub").prop("disabled", false);
        $("#awtw").addClass("holder-active");
    } else {
        $("#sub").prop("disabled", true);
        $("#awtw").removeClass("holder-active");
    }
}

function get_checks(){
    let id = [];
    $(".checks:checked").each(function(i, elem){
		id.push($(elem).val());
    });
    return id;
}

async function check_can_delete(id){
    const form = new FormData;
	const id_array = Array.isArray(id) ? id : [id];
	id_array.forEach(id => {
		form.append("id[]", id);
	})
    form.append("module", "supply");
	return await( await fetch(`/administration/module/supply/check_can_delete`, {
		method: "POST",
		body: form,
	}) ).json();
}

async function select_help(){
    const lw = new LW;
    let value = $("#act").val();
    let id = get_checks();
    let flag = false;
    let header = "";
    let body = "";
    let btn = "";
    switch(value){
        case "mas_supply_order":
            order_send_mas();
        break;
        case "mas_storage":
            flag = true;
            header = "Массовая складская позиция";
            body = "Вы действительно хотите отправить в складскую позицию?";
            btn = "Добавить";
        break;
        case "mas_del":
            lw.on();
            const ccd = await check_can_delete(id);
            lw.off();
            if(ccd.status){
                flag = true;
                header = "Массовое удаление";
                body = "Вы действительно хотите удалить?";
                btn = "Удалить";
            } else {
                flag = false;
                error_modal_show_move(ccd.data);
            }
        break;
    }
    if(flag){
        $("#help_modal_header").html(header);
        $("#help_modal_body").html(body);
        $("#help_modal_btn").html(btn);
        $("#help_modal").modal("show");
    }
}

function error_modal_show_move(data){
    swal({
        title: "Внимание!",
          text: data.error,
          icon: "warning",
        buttons : {
            go: {
                text: "Перейти",
                value: true,
                visible: true,
                className: "",
                closeModal: true,
            },
            cancel: {
                text: "Закрыть",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        }
    }).then((go) => {
        if(go){
            window.open(data.link);
        }
    });
}

function select_help_modal(){
    let value = $("#act").val();
    let id = get_checks();
    switch(value){
        case "mas_supply_order":
            
        break;
        case "mas_storage":
            send_warehousePositionSupplyDetail(id);
        break;
        case "mas_del":
            del_tov(id);
        break;
    }
}

function end_manager_modal(){
    swal({
        title: "Вы уверены?",
        text: "Вы точно хотите завершить заявку?",
        icon: "info",
        buttons: {
            confirm: {
                text: "Завершить",
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
      }).then((willEnd) => {
        if (willEnd) {
            const lw = new LW;
            lw.on();
            end_manager().then(() => {
                updateHead().then(() => {
                    lw.off();
                    swal("Успешно!", "Вы завершили свой путь", "success");
                });
            });
        }
      });
}

function end_manager(){
    return new Promise((resolve, reject) => {
        const mainId = $("#mainId").val();
        $.post("/administration/module/supply/end_manager/"+mainId, function(){
            resolve();
        })
    });
}

function close_supply_modal(id){
    swal({
        title: "Вы уверены?",
        text: "Вы точно хотите закрыть заявку?",
        icon: "info",
        buttons: {
            confirm: {
                text: "Завершить",
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
      }).then(async (willEnd) => {
        if (willEnd) {
            const lw = new LW;
            lw.on();
            await close_supply(id);
            await updateHead();
            lw.off();
        }
      });
}

async function close_supply(id){
    return await ( await fetch(`/administration/module/supply/close_supply/${id}`) ).json();
}