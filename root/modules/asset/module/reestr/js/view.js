function openDoc(){
    $(".docContainer").each(function(i, elem){
        let type = $(elem).data("type");
        if(+type === 1){
            let id = $(elem).data("id");
            $(".docContainer").removeClass("activeDocContainer");
            $(elem).addClass("activeDocContainer");
            updateBlockDocView(id).then(function(){
                $("#blockDocViewCon").show("fold", { direction: "left" }, 1000);
                let uidId = $('#uidId').val();
                let uidFIO = $('#uidFIO').val();
                if(uidId !== ""){
                    let data = {
                        id: uidId,
                        text: uidFIO
                    };

                    let newOption = new Option(data.text, data.id, false, false);
                    $('#uid').select2('data', null);
                    $('#uid').append(newOption).trigger('change');
                }
            });
            return false;
        }
    });
}

openDoc();

function updateBlockInfo(){
    let id = $("#mainId").val();
    $.post("/administration/reestr/update/blockInfo/"+id, function(data){
        $("#blockInfo").html(data);
    });
}

function updateBlockDoc(){
    return new Promise(function(resolve, reject){
        let id = $("#mainId").val();
        $.post("/administration/reestr/update/blockDoc/"+id, function(data){
            $("#blockDoc").html(data);
            updateTypeDoc();
            resolve();
        });
    });
}

function updateBlockDocView(idDoc){
    return new Promise(function(resolve, reject){
        let mainId = $("#mainId").val();
        $.post("/administration/reestr/update/blockDocView/"+mainId, {idDoc:idDoc}, function(data){
            $("#blockDocViewForm").html(data);
            resolve();
        });
    });
}

function updateTypeDoc(){
    let typeDoc = JSON.parse($("#typeDocSave").val());
    $("#addDocBtn").prop("disabled", true);
    $(".docContainer").each(function(i, elem){
        let type = $(elem).data("type");
        if(type === 1){
            delete typeDoc[type];
        }
    });
    $("#typeDoc").html('<option value="-1" selected disabled>Выберите тип документа</option>');
    for(key in typeDoc){
        $("#typeDoc").append('<option value="'+key+'">'+typeDoc[key]+'</option>');
    }
}

updateTypeDoc();

function checkScan(){
    let mainId = $("#mainId").val();
    $.post("/administration/reestr/checkScan/"+mainId, function(){
        updateBlockInfo();
    });
}

function loadBlock(status){
    switch(status){
        case "show":
            $("#loadBlock").show();
            $("#blockDocViewCon").addClass("filterBlur");
        break;
        case "hide":
            $("#loadBlock").hide();
            $("#blockDocViewCon").removeClass("filterBlur");
        break;
    }
}

$(document).on("click", "#signatures", function(){
    let id = $("#mainId").val();
    let status = $(this).data("status");
    status = +status === 0 ? 1 : 0; 
    $.post("/administration/reestr/changeFieldReestr", {field: "sign", value: status, id:id}, function(){
        updateBlockInfo();
    });
});

$(document).on("click", "#addDoc", function(){
    $("#addDocModal").modal("show");
});

$(document).on("click", "#addDocBtn", function(){
    let id = $("#mainId").val();
    let typeDoc = $("#typeDoc").val();
    $.post("/administration/reestr/addDoc",{id:id, typeDoc:typeDoc}, function(){
        updateBlockDoc();
    });
});

let flagDel = true;
$(document).on("click", ".delDoc", function(){
    flagDel = false;
    let idDoc = $(this).data("id");
    $("#delDocModalBtn").data("id", idDoc);
    $("#delDocModal").modal("show");
    setTimeout(function(){
        flagDel = true;
    }, 100);
});

$(document).on("click", "#delDocModalBtn", function(){
    let idDoc = $(this).data("id");
    let id = $("#mainId").val();
    $.post("/administration/reestr/delDoc",{id:id, idDoc:idDoc}, function(){
        updateBlockDoc();
        $("#blockDocViewCon").hide("fold", { direction: "left" }, 1000);
        checkScan();
    });
});

$(document).on("click", ".docContainer", function(){
    let idDoc = $(this).data("id");
    if(flagDel){
        $(".docContainer").removeClass("activeDocContainer");
        $(this).addClass("activeDocContainer");
        updateBlockDocView(idDoc).then(function(){
            $("#blockDocViewCon").show("fold", { direction: "left" }, 1000);
            let uidId = $('#uidId').val();
            let uidFIO = $('#uidFIO').val();
            if(uidId !== ""){
                let data = {
                    id: uidId,
                    text: uidFIO
                };
                
                let newOption = new Option(data.text, data.id, false, false);
                $('#uid').select2('data', null);
                $('#uid').append(newOption).trigger('change');
            }
        });
    }
});

$(document).on("change", "#typeDoc", function(){
    $("#addDocBtn").prop("disabled", false);
});

$(document).on("change", "#file_doc, #file_scan", function(){
    let extension = $(this).val().split(".").pop();
    let attrId = $(this).attr("id");
    let array;
    let flag = true;
    switch(attrId){
        case "file_doc":
            array = ["doc", "docx"];
        break;
        case "file_scan":
            array = ["pdf"];
        break;
    }
    if(array.indexOf(extension) === -1){
        flag = false;
    }
    if(flag){
        loadBlock("show");
        let idDoc = $("#thisIdDoc").val();
        let id = $("#mainId").val();
        let data = new FormData($("#blockDocViewForm")[0]);
        data.set("reestr_id", id);
        $.ajax({
            url: '/administration/reestr/saveFile',
            data: data,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function () {
                updateBlockDocView(idDoc).then(function(){
                    loadBlock("hide");
                });
                if(attrId === "file_scan"){
                    checkScan();
                }
            }
        });
    } else {
        swal("Ошибка!", "Такой тип файла не поддерживается!", "error");
        $(this).val("");
    }
    
});

$(document).on("click", ".delFile", function(){
    let typeFile = $(this).data("type");
    $("#delFileModalBtn").data("type", typeFile);
    $("#delFileModal").modal("show");
});

$(document).on("click", "#delFileModalBtn", function(){
    let typeFile = $(this).data("type");
    let idDoc = $("#thisIdDoc").val();
    let id = $("#mainId").val();
    $.post("/administration/reestr/delFile", {id:id, typeFile:typeFile, idDoc:idDoc}, function(){
        updateBlockDocView(idDoc);
        if(typeFile === "scan"){
            checkScan();
        }
    });
});

$(document).on("click", ".changeStatus", function(){
    let status = $(this).data("status");
    $("#changeStatusSelect").val(status).change();
    if(+status === 0){
        $("#changeStatusModalBtn").prop("disabled", true);
    }
    $("#changeStatusModal").modal("show");
});

$(document).on("change", "#changeStatusSelect", function(){
    let value = $(this).val();
    if(+value !== 0){
        $("#changeStatusModalBtn").prop("disabled", false);
    }
    if(+value === 2){
        $(".uidChange").show();
        $("#changeStatusModalBtn").prop("disabled", true);
    } else {
        $(".uidChange").hide();
        $("#changeStatusModalBtn").prop("disabled", false);
    }
});

$(document).on("change", "#uid", function(){
    $("#changeStatusModalBtn").prop("disabled", false);
});

$(document).on("click", "#changeStatusModalBtn", function(){
    let idDoc = $("#thisIdDoc").val();
    let id = $("#mainId").val();
    let status = $("#changeStatusSelect").val();
    let uid = $("#uid").val();

    $.post("/administration/reestr/changeStatus", {idDoc:idDoc, id:id, status:status, uid:uid}, function(){
        updateBlockDoc();
        updateBlockDocView(idDoc);
    });
});

let flagChangeComm = true;
let saveComm = "";
$(document).on("keydown", "#comment", function(){
    if(flagChangeComm){
        saveComm = $(this).val();
        flagChangeComm = false;
    }
});

$(document).on("keyup", "#comment", function(){
    let value = $(this).val();
    if(value !== saveComm){
        $(this).animate({borderColor: "yellow", borderWidth: "2px"}, 200);
    } else {
        $(this).animate({borderColor: "#6dd4e0", borderWidth: "1px"}, 200);
    }
});

$(document).on("change", "#comment", function(){
    $(this).animate({borderColor: "#6dd4e0", borderWidth: "1px"}, 200);
    let value = $(this).val();
    if(value !== saveComm){
        let id = $("#mainId").val();
        let idDoc = $("#thisIdDoc").val();
        $.post("/administration/reestr/saveComm", {id:id, idDoc:idDoc, comment:value}, function(){

        });
    }
    flagChangeComm = true;
    saveComm = null;
});

$(document).on("click", ".changeTitle", function(){
    $(this).removeClass("changeTitle");
    let value = $(this).text();
    if(value === "Отсутствует"){
        value = "";
    }
    $(this).html('<input type="text" value="'+value+'" placeholder="Введите номер документа" id="changeTitleInp" class="form-control">');
    $("#changeTitleInp").select();
    $("#changeTitleInp").focus();
});

$(document).on("change", "#changeTitleInp", function(){
    let value = $(this).val();
    let text = value;
    if(value === ""){
        text = "Отсутствует";
    }
    $(this).parent().addClass("changeTitle");
    $(this).parent().html(text);
    let id = $("#mainId").val();
    let idDoc = $("#thisIdDoc").val();
    $.post("/administration/reestr/saveTitle", {id:id, idDoc:idDoc, title:value}, function(){
        updateBlockDoc().then(function(){
            $(".docContainer").each(function(i, elem){
                let code = $(elem).data("id");
                if(+code === +idDoc){
                    $(elem).addClass("activeDocContainer");
                    return false;
                }
            });
        });
    });
});

$(document).on("click", ".objectChange", function(){
    let object = $(this).data("object");
    let object_text = $(this).data("object_text");
    $('#object').select2('data', null);
    if(object !== ""){
        let data = {
            id: object,
            text: object_text
        };
    
        let newOption = new Option(data.text, data.id, false, false);
        $('#object').append(newOption).trigger('change');
    }
    $("#objectChangeModal").modal("show");
});

$(document).on("click", "#objectChangeModalBtn", function(){
    let object = $("#object").val();
    let id = $("#mainId").val();

    $.post("/administration/reestr/changeFieldReestr", {value:object, id:id, field:"object"}, function(){
        updateBlockInfo();
    });
});

$(document).on("dblclick", "#date", function(){
    if(!$(this).hasClass("change")){
        $(this).addClass("change");
        let date = $(this).text();
        if(date === 'Отсутсвует'){
            date = "";
        } else {
            date = date.split(".");
            date = date[2]+"-"+date[1]+"-"+date[0];
        }
        $(this).html(`<input type="date" class="form-control" id="dateChange" value="`+date+`">`);
        $("#dateChange").select();
        $("#dateChange").focus();
    }
});

$(document).on("keyup", "#dateChange", function(e){
    if(e.keyCode === 13){
        let date = $(this).val();
        let id = $("#mainId").val();
        $.post("/administration/reestr/changeFieldReestr", {value:date, id:id, field:"date_view"}, function(){
            updateBlockInfo();
        });
    }
});

$(document).on("click", "#oid", function(){
    let oid = $(this).data("oid");
    let oid_text = $(this).text();
    $('#oid_select').select2('data', null);
    if(oid !== ""){
        let data = {
            id: oid,
            text: oid_text
        };
    
        let newOption = new Option(data.text, data.id, false, false);
        $('#oid_select').append(newOption).trigger('change');
    }
    $("#oidChangeModal").modal("show");
});

$(document).on("click", "#oidChangeModalBtn", function(){
    let oid = $("#oid_select").val();
    let id = $("#mainId").val();

    $.post("/administration/reestr/changeFieldReestr", {value:oid, id:id, field:"oid"}, function(){
        updateBlockInfo();
    });
});