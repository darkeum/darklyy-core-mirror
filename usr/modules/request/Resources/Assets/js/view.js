const urlAjax = '/administration/module/request/';

//загрузка комментариев
function loadComment(){
    let id = $("#mainId").val();
    $.post(urlAjax+"loadComment/"+id, function(data){
        let json = JSON.parse(data);
        $("#commentCon").empty();
        if(json["comment"].length > 0){
            json["comment"].forEach(function(value, i){
                //console.log(value);
                let ss = $("#commentTemplate").html();
                ss = $(ss);
                ss.find(".avatar").attr("src", value["avatar"]);
                ss.find(".uidComm").html(value["uidFull"]);
                ss.find(".dateComm").html(value["date"]);
                ss.find(".commComm").html(value["comment"]);
                if(value["file"].length > 0){
                    value["file"].forEach(function(val, j){
                        let finalFile = "far cp";
                        let finalFileFunc = 'finalFileModal('+val["id"]+');';
                        if(val["finalFile"] == "true"){
                            finalFile = "fas activeFile";
                            finalFileFunc = "";
                        }
                        ss.find(".fileComm").append('<li><a href="/download/'+val["id"]+'">'+val["name"]+'</a><i class="'+finalFile+' fa-star finalFileStar ml10" aria-hidden="true" onclick="'+finalFileFunc+'"></i></li>');
                    });
                }
                $("#commentCon").append(ss);
            });
            $("#commentCon").scrollTop($("#commentCon")[0].scrollHeight);
        } else {
            $("#commentCon").html("Комментарии отсутствуют");
        }
    });
}

loadComment();

//обновление боковой панели
function updatePanel(){
    let id = $("#mainId").val();
    $.post(urlAjax+"viewPanel/"+id, function(data){
        $("#mainPanel").html(data);
    });
}

updatePanel();

//загрузка ввода
tinymce.init({
    selector: '#mytextarea',
    language: 'ru',
    gecko_spellcheck:true
});

//Взятие заявки
function takeBtn(){
    $("#take_modal").modal("show");
}
function take_modal_btn(){
    let id = $("#mainId").val();
    $.post(urlAjax+"takeRequest/"+id, function(data){
        if(data === "error"){
            swal("Ошибка!", "Заявка уже взята!", "error");
        }
        updatePanel();
    });
}

//отправка коммента
function sendComment(){
    let id = $("#mainId").val();
    tinyMCE.triggerSave();
    let message = $("#formTextarea").serialize();
    let file = $("#fileForm").serialize();
    if(message.replace("message=", "") != "" || $("#fileBody").html() != ""){
        tinyMCE.get("mytextarea").setContent("");
        $("#debug1").html("");
        $("#files1").find(".media").remove();
        $.post(urlAjax+"saveComment/"+id, message+"&"+file, function(data){
            loadComment();
        });
    }
}

function modalFile(){
    $("#load_file").modal("show");
}

$(document).on("DOMSubtreeModified", "#debug1", function(){
    let count = $(this).children().length;
    $(".circleLoad").html(count);
    if(count > 0){
        $(".circleLoad").show();
    } else {
        $(".circleLoad").hide();
    }
});

function finalFileModal(id){
    $("#finalModalBtn").data("id", id);
    $("#finalModal").modal("show");
}

function finalModalBtn(This){
    let id = $(This).data("id");
    let mainId = $("#mainId").val();
    $.post(urlAjax+"finalFile", {id:id, mainId:mainId}, function(){
        loadComment();
        updatePanel();
    });
}

function endQuest(){
    $("#endQuestModal").modal("show");
}

function endQuestBtn(){
    let mainId = $("#mainId").val();
    $.post(urlAjax+"endQuest/"+mainId, function(){
        updatePanel();
    });
}

function addFileModal(){
    $("#addFileModal").modal("show");
}

function loadFile(){
    let data = $("#fileForm2").serialize();
    let id = $("#mainId").val();
    $.post(urlAjax+"loadFile/"+id, data, function(){
        updatePanel();
    });
    $("#files2").html(`<li class="text-muted text-center empty">Файлы не загружены.</li>`);
    $("#debug2").empty();
}

function change_nds_price(This){
    let nds_text_1 = $(This).text();
    let nds_text_2 = $(This).data("text");
    let nds = $(This).data("nds");
    if(+nds === 0){
        nds = 1;
    } else {
        nds = 0;
    }
    let id = $("#mainId").val();

    $(This).data("text", nds_text_1);
    $(This).html(nds_text_2);
    $(This).data("nds", nds);
    saveSpecDataAjax(id, nds, "nds");
}

function toggleAssigment2(This){
    let value = $(This).val();
    if(value === "ГВС"){
        $("#assigment2block").show();
        $("#assigment2").prop("disabled", false);
    } else {
        $("#assigment2block").hide();
        $("#assigment2").prop("disabled", true);
    }
}

$(document).on("submit", "#mainPanel", function(e){
    e.preventDefault();
    endQuest();
});

function saveSpecData(This){
    let field = $(This).attr("id");
    let value = $(This).val();
    switch(field){
        default:
            value = value.trim().replace(/ /g, "").replace(/\r?\n/g, "");
        break;
        case "title":
        case "assigment2":
            //пропуск
        break;
    }
    let id = $("#mainId").val();
    saveSpecDataAjax(id, value, field);
}

function saveSpecDataAjax(id, value, field){
    $.post(urlAjax+"saveSpecData/"+id, {field:field, value:value, id:id}, function(){

    })
    .done(function(){
        notif("success","Успешно", "Данные успешно сохранены");
    })  
    .fail(function(){
        notif("error","Ошибка", "Данные не сохранены");
    });
}

function saveData(field, value){
    return new Promise((resolve, reject) => {
        let id = $("#mainId").val();
        $.post(urlAjax+"saveData/"+id, {field:field, value:value}, function(){
            resolve();
        });
    });
}

function delFileModal(id){
    $("#delFileBtn").data("id", id);
    $("#delFileModal").modal("show");
}

function delFileRes(This){
    let id = $(This).data("id");
    $.post(urlAjax+"delFileRes/"+id, function(){
        updatePanel();
    });
}

function convert_for_andrew(This){
    let old_value = $(This).val();
    let new_value = old_value.replace(".", ","); 
    $(This).val(new_value);
}

function edit(){
    let mainId = $("#mainId").val();
    get_sales().then(() => {
        saveData('status', 2).then(() => {
            updatePanel();
        });
    }, data => {
        let div = document.createElement('div');
        div.innerHTML = data;
        swal({
            content: div,
            title: "Внимание!",
            text: "В продажах, в которых присутствует данный товар, не будет изменен",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Отмена",
                    value: false,
                    visible: true,
                    className: "",
                    closeModal: true,
                  },
                  confirm: {
                    text: "Продолжить",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                  }
            },
          }).then(doit => {
            if(doit){
                saveData('status', 2).then(() => {
                    updatePanel();
                });
            }
          });
    });
}

function get_sales(){
    return new Promise((resolve, reject) => {
        let mainId = $("#mainId").val();
        $.post(urlAjax+"get_sales/"+mainId, function(data){
            if(data === ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}