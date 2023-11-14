$(function(){
    $("#worker").select2();
});

function changeField(This, field, data = false){
    return new Promise((resolve, reject) => {
        let value = $(This).val();
        let id = $("#mainId").val();
        if(field === "worker"){
            if(!Array.isArray(value)){
                value = [value];
            }
            value = JSON.stringify(value);
        }
        $.post("/administration/command/changeField", {id, field, value, data}, function(){
            resolve();
        });
    });
}

function changeDestination(This, key, field, type = "edit"){
    return new Promise((resolve, reject) => {
        let value = $(This).val();
        let id = $("#mainId").val();
        $.post("/administration/command/changeDestination", {id, key, field, value, type}, function(){
            resolve();
        });
    });
}

function loadFile(This, field){
    let data = new FormData();
    data.append('file', $(This)[0].files[0]);
    data.append('id', $("#mainId").val());
    data.append('field', field);
    $.ajax({
        url: '/administration/command/loadFile',
        data: data,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function ( ) {
            updateAll();
        }
    });
}

function updateAll(){
    let id = $("#mainId").val();
    $.post("/administration/command/ajax_detail/"+id, function(data){
        $("#admin-form").html(data);
        $("#worker").select2();
    });
}

function delFileModal(id, field){
    $("#delFileModal").modal("show");
    $("#delFileModalBtn").data("id", id);
    $("#delFileModalBtn").data("field", field);
}

function delFile(id, field){
    let comm_id = $("#mainId").val();
    $.post("/administration/command/delFile", {id:id, comm_id:comm_id, field:field}, function(){
        updateAll();
    });
}

function numb_create(){
    return new Promise((resolve, reject) => {
        let id = $("#mainId").val();
        $.post("/administration/command/numb_create", {id}, function(){
            resolve();
        });
    });
}