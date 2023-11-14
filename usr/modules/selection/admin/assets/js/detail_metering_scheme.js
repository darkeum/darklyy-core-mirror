$(function () {
    let mainId = $("#mainId").val();
    updateHeader(mainId);
    Refresh(mainId);
});

function updateHeader(mainId = null) {
	let Id;
	if(mainId === null) 
	{
		Id = $("#mainId").val();
	}
	else
	{
		Id = mainId;
	}
    $.post("/administration/module/selection/ajax/metering_detail_scheme_header/" + Id, function (data) {
        $("#admin-form").html(data);
    });
}

function Refresh(mainId, notifCheck = false) {
    $.post("/administration/module/selection/ajax/metering_detail_scheme_tovar_list/" + mainId, function (data) {
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
    $.post("/ajax.php?do=fileSaveMeteringScheme", "mainId=" + mainId + "&mod=metering_scheme&type=aid_scheme&module=metering_scheme&name=1&" + form, function () {
        updateHeader(mainId);
    });
});

$(document).on("click", "#addDt", function () {
    $("#dtModal").modal("show");
});

$(document).on("click", "#dtSave", function () {
    let form = $("#dtForm").serialize();
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=fileSaveMeteringScheme", "mainId=" + mainId + "&mod=metering_dt&type=aid_dt&module=metering_scheme&name=2&" + form, function () {
        updateHeader(mainId);
    });
});

$(document).on("click", "#addDd", function () {
    $("#ddModal").modal("show");
});

$(document).on("click", "#ddSave", function () {
    let form = $("#ddForm").serialize();
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=fileSaveMeteringScheme", "mainId=" + mainId + "&mod=metering_dd&type=aid_dd&module=metering_scheme&name=3&" + form, function () {
        updateHeader(mainId);
    });
});

$(document).on("click", "#addAuto", function () {
    $("#autoModal").modal("show");
});

$(document).on("click", "#autoSave", function () {
    let form = $("#autoForm").serialize();
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=fileSaveMeteringScheme", "mainId=" + mainId + "&mod=metering_auto&type=aid_auto&module=metering_scheme&name=4&" + form, function () {
        updateHeader(mainId);
    });
});

$(document).on("click", "#addEl", function () {
    $("#elModal").modal("show");
});

$(document).on("click", "#elSave", function () {
    let form = $("#elForm").serialize();
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=fileSaveMeteringScheme", "mainId=" + mainId + "&mod=metering_el&type=aid_el&module=metering_scheme&name=5&" + form, function () {
        updateHeader(mainId);
    });
});

$(document).on("click", "#addPd", function () {
    $("#pdModal").modal("show");
});

$(document).on("click", "#pdSave", function () {
    let form = $("#pdForm").serialize();
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=fileSaveMeteringScheme", "mainId=" + mainId + "&mod=metering_pd&type=aid_pd&module=metering_scheme&name=6&" + form, function () {
        updateHeader(mainId);
    });
});

$(document).on("click", "#addShu", function () {
    $("#shuModal").modal("show");
});

$(document).on("click", "#shuSave", function () {
    let form = $("#shuForm").serialize();
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=fileSaveMeteringScheme", "mainId=" + mainId + "&mod=metering_shu&type=aid_shu&module=metering_scheme&name=7&" + form, function () {
        updateHeader(mainId);
    });
});

$(document).on("click", "#addPl", function () {
    $("#plModal").modal("show");
});

$(document).on("click", "#plSave", function () {
    let form = $("#plForm").serialize();
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=fileSaveMeteringScheme", "mainId=" + mainId + "&mod=metering_pl&type=aid_pl&module=metering_scheme&name=8&" + form, function () {
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
    $.post("/ajax.php?do=delDetailMeteringScheme", { id: id }, function () {
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

        $.post("ajax.php?do=setPositionMeteringScheme", { res: res }, function (data) {

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
    
    $.post("/ajax.php?do=saveElemMeteringScheme", {id: $("#mainId").val(), json:json, saveId:saveId}, function(data){
        notif("success", "Успешно", "Товар успешно обновлены");
    });
    
});

//имя
$(document).on("click", "#title", function(){
    $('#title').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите минимальный расход",
        url: '/ajax.php?do=setMeteringScheme',
    });
    $('#title').editable('show');
});


$(document).on("click", "#rem_aid_scheme", function(){
    $("#rem_aid_scheme_modal").modal("show");
});

$(document).on("click", "#rem_aid_dt", function(){
    $("#rem_aid_dt_modal").modal("show");
	});

$(document).on("click", "#rem_aid_dd", function(){
    $("#rem_aid_dd_modal").modal("show");
});

$(document).on("click", "#rem_aid_auto", function(){
    $("#rem_aid_auto_modal").modal("show");
});

$(document).on("click", "#rem_aid_el", function(){
    $("#rem_aid_el_modal").modal("show");
});

$(document).on("click", "#rem_aid_pd", function(){
    $("#rem_aid_pd_modal").modal("show");
});

$(document).on("click", "#rem_aid_shu", function(){
    $("#rem_aid_shu_modal").modal("show");
});

$(document).on("click", "#rem_aid_pl", function(){
    $("#rem_aid_pl_modal").modal("show");
});

$(document).on("click", "#rem_aid_scheme_btn", function(){
    $.post("/ajax.php?do=removeFileMeteringScheme", {type: "aid_scheme", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#rem_aid_dt_btn", function(){
    $.post("/ajax.php?do=removeFileMeteringScheme", {type: "aid_dt", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#rem_aid_dd_btn", function(){
    $.post("/ajax.php?do=removeFileMeteringScheme", {type: "aid_dd", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#rem_aid_auto_btn", function(){
    $.post("/ajax.php?do=removeFileMeteringScheme", {type: "aid_auto", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#rem_aid_el_btn", function(){
    $.post("/ajax.php?do=removeFileMeteringScheme", {type: "aid_el", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#rem_aid_pd_btn", function(){
    $.post("/ajax.php?do=removeFileMeteringScheme", {type: "aid_pd", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#rem_aid_shu_btn", function(){
    $.post("/ajax.php?do=removeFileMeteringScheme", {type: "aid_shu", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#rem_aid_pl_btn", function(){
    $.post("/ajax.php?do=removeFileMeteringScheme", {type: "aid_pl", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});





$(document).on("click", "#sub", function(){
    $("#delAllModal").modal("show");
});

$(document).on("click", "#delAllModalBtn", function(){
    let data = $("#tablesForm").serialize();
    $.post("/ajax.php?do=delAllMeteringSchemeDetail", data, function(){
        Refresh($("#mainId").val());
    });
});

$(document).on("dblclick", ".title", function(){
    let id = $(this).parents("tr").data("id");
    window.open('https://atri-energo.ru/administration/module/selection/addProductMinMeteringScheme/'+id,'1549627856974','width=1400,height=850,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
    return false;
});

function changeProduct(id, parentId, num, count){
    let mainId = $("#mainId").val();
    $.post("/administration/module/selection/ajax/metering_detail_scheme_tovar_list/" + mainId, {cid:id, parentId:parentId, num:num, count:count}, function (data) {
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