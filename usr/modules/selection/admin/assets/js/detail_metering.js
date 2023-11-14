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
    $.post("/administration/module/selection/ajax/metering_detail_header/" + Id, function (data) {
        $("#admin-form").html(data);
    });
}

function Refresh(mainId, notifCheck = false) {
    $.post("/administration/module/selection/ajax/metering_detail_tovar_list/" + mainId, function (data) {
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
    $.post("/ajax.php?do=fileSaveMetering", "mainId=" + mainId + "&mod=metering&type=aid_scheme&module=metering&name=1&" + form, function () {
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
    $.post("/ajax.php?do=delDetailMetering", { id: id }, function () {
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

        $.post("ajax.php?do=setPositionMetering", { res: res }, function (data) {

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
    
    $.post("/ajax.php?do=saveElemMetering", {id: $("#mainId").val(), json:json, saveId:saveId}, function(data){
        notif("success", "Успешно", "Товар успешно обновлены");
    });
    
});

//код
$(document).on("click", "#r_min", function(){
    $('#r_min').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите минимальный расход",
        url: '/ajax.php?do=setMetering',
    });
    $('#r_min').editable('show');
});

$(document).on("click", "#r_max", function(){
    $('#r_max').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите максимальный расход",
        url: '/ajax.php?do=setMetering',
    });
    $('#r_max').editable('show');
});

$(document).on('click', '#type', function(){
    getTypeBlock().done(function(result){
        $('#type').editable({
            type: 'select',
            pk: $("#mainId").val(),
            url: '/ajax.php?do=setMetering',
            source: result,
        });
        $('#type').editable('show');
    });
});

$(document).on('click', '#scheme', function(){
    getTypeBlockScheme().done(function(result){
        $('#scheme').editable({
            type: 'select',
            pk: $("#mainId").val(),
            url: '/ajax.php?do=setMetering',
            source: result,
        });
        $('#scheme').editable('show');
    });
});

function getTypeBlock() {
    var url = "/ajax.php?do=getTypeBlockMetering";
    return $.ajax({
        type: 'POST',
        url: url,
    });
}

function getTypeBlockScheme() {
    var url = "/ajax.php?do=getTypeBlockMeteringScheme";
    return $.ajax({
        type: 'POST',
        url: url,
    });
}

$(document).on("click", "#rs1_min", function(){
    $('#rs1_min').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите мин. расход у класса А",
        url: '/ajax.php?do=setMetering',
    });
    $('#rs1_min').editable('show');
});

$(document).on("click", "#rs2_min", function(){
    $('#rs2_min').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите мин. расход у класса B",
        url: '/ajax.php?do=setMetering',
    });
    $('#rs2_min').editable('show');
});

$(document).on("click", "#rs3_min", function(){
    $('#rs3_min').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите мин. расход у класса C",
        url: '/ajax.php?do=setMetering',
    });
    $('#rs3_min').editable('show');
});

$(document).on("click", "#d1", function(){
    $('#d1').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите D1",
        url: '/ajax.php?do=setMetering',
    });
    $('#d1').editable('show');
});

$(document).on("click", "#d2", function(){
    $('#d2').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите D2",
        url: '/ajax.php?do=setMetering',
    });
    $('#d2').editable('show');
});

$(document).on("click", "#dn94", function(){
    $('#dn94').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите dn94",
        url: '/ajax.php?do=setMetering',
    });
    $('#dn94').editable('show');
});

$(document).on("click", "#ddt94", function(){
    $('#ddt94').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите ddt94",
        url: '/ajax.php?do=setMetering',
    });
    $('#ddt94').editable('show');
});

$(document).on("click", "#ldt94", function(){
    $('#ldt94').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите ldt94",
        url: '/ajax.php?do=setMetering',
    });
    $('#ldt94').editable('show');
});

$(document).on("click", "#ddt", function(){
    $('#ddt').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите DDT",
        url: '/ajax.php?do=setMetering',
    });
    $('#ddt').editable('show');
});

$(document).on("click", "#ldt", function(){
    $('#ldt').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите LDT",
        url: '/ajax.php?do=setMetering',
    });
    $('#ldt').editable('show');
});

$(document).on("click", "#lbob", function(){
    $('#lbob').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите L боб.",
        url: '/ajax.php?do=setMetering',
    });
    $('#lbob').editable('show');
});

$(document).on("click", "#losn", function(){
    $('#losn').editable({
        type: 'text',
        pk: $("#mainId").val(),
        title: "Введите L осн.",
        url: '/ajax.php?do=setMetering',
    });
    $('#losn').editable('show');
});

$(document).on("click", "#rem_aid_scheme", function(){
    $("#rem_aid_scheme_modal").modal("show");
});

$(document).on("click", "#rem_aid_scheme_btn", function(){
    $.post("/ajax.php?do=removeFileMetering", {type: "aid_scheme", id:$("#mainId").val()}, function(){
        updateHeader($("#mainId").val());
    });
});

$(document).on("click", "#sub", function(){
    $("#delAllModal").modal("show");
});

$(document).on("click", "#delAllModalBtn", function(){
    let data = $("#tablesForm").serialize();
    $.post("/ajax.php?do=delAllMeteringDetail", data, function(){
        Refresh($("#mainId").val());
    });
});

$(document).on("dblclick", ".title", function(){
    let id = $(this).parents("tr").data("id");
    window.open('https://atri-energo.ru/administration/module/selection/addProductMinMetering/'+id,'1549627856974','width=1400,height=850,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
    return false;
});

function callAlice(rsType){	
    window.open('https://atri-energo.ru/administration/module/selection/addProductMinMeteringRs/'+rsType,'1549627856974','width=1400,height=850,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
    return false;
}

function changeProduct(id, parentId, num, count){
    let mainId = $("#mainId").val();
    $.post("/administration/module/selection/ajax/metering_detail_tovar_list/" + mainId, {cid:id, parentId:parentId, num:num, count:count}, function (data) {
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