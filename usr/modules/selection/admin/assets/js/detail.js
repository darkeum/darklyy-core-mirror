$(function () {
    let mainId = $("#mainId").val();
    updateHeader(mainId);
    Refresh(mainId);
});

function updateHeader(mainId) {
    $.post("/administration/module/selection/ajax/detail_header/" + mainId, function (data) {
        $("#admin-form").html(data);
    });
}

function Refresh(mainId, notifCheck = false) {
    $.post("/administration/module/selection/ajax/detail_tovar_list/" + mainId, function (data) {
        $("#tov_list").html(data);
        if (notifCheck) {
            notif('primary', 'Информация', 'Данные получены');
        }
    });
}

$(document).on("click", ".refresh", function () {
    let mainId = $("#mainId").val();
    Refresh(mainId, true);
});

$(document).on("click", "#addScheme", function () {
    $("#schemeModal").modal("show");
});

$(document).on("click", "#schemeSave", function () {
    let form = $("#schemeForm").serialize();
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=fileSaveSelection", "mainId=" + mainId + "&mod=selection_scheme&type=aid_scheme&module=selection&name=1&" + form, function () {
        updateHeader(mainId);
    });
});

$(document).on("click", "#addSize", function () {
    $("#sizeModal").modal("show");
});

$(document).on("click", "#sizeSave", function () {
    let form = $("#sizeForm").serialize();
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=fileSaveSelection", "mainId=" + mainId + "&mod=selection_size&type=aid_size&module=selection&name=2&" + form, function () {
        updateHeader(mainId);
    });
});

function deleteModal(aThis) {
    let id = $(aThis).data("id");
    $("#delModal").modal("show");
    $("#delBtn").data("id", id);
}

$(document).on("click", "#delBtn", function () {
    let id = $(this).data("id");
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=delDetailSelection", { id: id }, function () {
        Refresh(mainId);
    });
});

$("#tov_list").sortable({
    update: function (event, ui) {
        //console.log($(this).children());
        $(this).children().each(function (index) {
            $(this).find('td').first().html(index + 1);
        });

        let position = $(".position");
        let json = {};
        for (let i = 0; i < position.length; i++) {
            json[$(position[i]).data("id")] = $(position[i]).html();
        }
        let res = JSON.stringify(json);

        $.post("ajax.php?do=setPositionSelection", { res: res }, function (data) {

        });

    },
    axis: "y",
    cancel: ".noSort"
});

$(document).on("keydown", ".rubber", function (event) {
    $(this).width(($(this).val().length + 4) * 8);
    if (event.which == 13) {
        $(this).blur();
    }
});

//количество
$(document).on("click", ".count", function () {
    //console.log("~");
    $(this).addClass("countInp");
    $(this).removeClass("count");
    $(this).html('<input type="number" id="countInp" class="form-control rubber" value="' + $(this).text().replace(/ /g, "") + '" style="width: ' + (($(this).text().replace(/ /g, "").length + 6) * 8) + 'px">');
    $("#countInp").focus();
    $("#countInp").select();
});

$(document).on("blur", "#countInp", function () {
    let id = $(this).parent().parent().addClass("changeElem");
    //console.log(id);
    let count = $(this).val();
    $(".countInp").html(count);
    $(".countInp").addClass("count");
    $(".count").removeClass("countInp");
});

//сохранение 
$(document).on("click", ".saveChange", function(){
    let elem = $(".changeElem");
    let json = {};
    for(let i=0;i<elem.length;i++){
        let id = $(elem[i]).data("id");
        let count = $(elem[i]).children(".count").text().replace(/ /g, "");
        json[id] = {
            "count": count,
        }
    }
    let saveId = $("#saveId").val();
    json = JSON.stringify(json);
    $("#saveId").val("");
    
    $.post("/ajax.php?do=saveElemSelection", {id: $("#mainId").val(), json:json, saveId:saveId}, function(data){
        notif("success", "Успешно", "Товар успешно обновлены");
    });
    
});

//код
$(document).on("click", "#code", function(){
    $('#code').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите код",
        url: '/ajax.php?do=setSelection',
    });
    $('#code').editable('show');
});

$(document).on('click', '#type', function(){
    getTypeBlock().done(function(result){
        $('#type').editable({
            type: 'select',
            pk: $("#mainId").val(),
            url: '/ajax.php?do=setSelection',
            source: result,
        });
        $('#type').editable('show');
    });
});

function getTypeBlock() {
    var url = "/ajax.php?do=getTypeBlockSelection";
    return $.ajax({
        type: 'POST',
        url: url,
    });
}

$(document).on("click", "#rem_aid_scheme", function(){
    $("#rem_aid_scheme_modal").modal("show");
});

$(document).on("click", "#rem_aid_scheme_btn", function(){
    $.post("/ajax.php?do=removeFileSelection", {type: "aid_scheme", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#rem_aid_size", function(){
    $("#rem_aid_size_modal").modal("show");
});

$(document).on("click", "#rem_aid_size_btn", function(){
    $.post("/ajax.php?do=removeFileSelection", {type: "aid_size", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#sub", function(){
    $("#delAllModal").modal("show");
});

$(document).on("click", "#delAllModalBtn", function(){
    let data = $("#tablesForm").serialize();
    $.post("/ajax.php?do=delAllSelectionDetail", data, function(){
        Refresh($("#mainId").val());
    });
});

$(document).on("dblclick", ".title", function(){
    let id = $(this).parents("tr").data("id");
    window.open('https://atri-energo.ru/administration/module/selection/addProductMin/'+id,'1549627856974','width=1400,height=850,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
    return false;
});

function changeProduct(id, parentId, num, count){
    let mainId = $("#mainId").val();
    $.post("/administration/module/selection/ajax/detail_tovar_list/" + mainId, {cid:id, parentId:parentId, num:num, count:count}, function (data) {
        $("#tr_"+parentId).html(data);
        let saveId = $("#saveId").val();
        if(saveId.length > 0){
            saveId = JSON.parse(saveId);
        } else {
            saveId = {};
        }
        saveId[parentId] = id;
        saveId = JSON.stringify(saveId);
        $("#saveId").val(saveId);
    });
}