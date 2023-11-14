$(function(){
    $(".select2-single").select2();
	$(".select2-single1").select2({placeholder: "Выберите контрагента"});
	$(".select2-single2").select2({placeholder: "Выберите заказ"});
});

let saveList = "";
$.post("/ajax.php?do=getOrgList", function(data){
    saveList = data;
});
let saveOrder = "";
$.post("/ajax.php?do=getOrderList", function(data){
    saveOrder = data;
});

function makeOrder(This){
    clear_modal();
    let count = $(This).parents().children(".count").html(); 
    $("#countModal").val(count);
    $("#id_modal").val($(This).data("id"));
    $("#sale_id_modal").val($(This).data("salesid"));
    modal_types("one");
    open_modal();
}

function clear_modal(){
    $("#new_order").select2("val", "");
    $("#order_numb").select2("val", "");
    $("#new_order").html(saveList);
    $("#order_numb").html(saveOrder);
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

function order_send_ajax(){
    let dd = $("#compl").serialize();
    $.post("/administration/module/supply/ajax_order_send_stock", dd, function(data){
        update_for_insert(data);
    });
}

function order_send_mas_ajax(){
    let id = get_checks();
    let dd = $("#compl").serialize().replace("&id=", "");
    let id_new = $.param({id});
    
    $.post("/administration/module/supply/ajax_order_send_mas_stock", dd+"&"+id_new, function(){
        update_for_insert();
    });
}

function update_for_insert(data = "Успешно"){
    notif("success","Информация", data);
    //загрузить новый список
    $.post("/ajax.php?do=getOrderList", function(data1){
        saveOrder = data1;
    });
    
    //обновление
    updateBody();
}

function updateBody(){
    let mainId = $("#mainId").val();
    $.post("/administration/module/supply/supply_stock_detail_list/"+mainId, function(data){
        $("#dev_list").html(data);
    });
}