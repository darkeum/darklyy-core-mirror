$(function() {

    let ghost = $("#ghost").val();
    if(ghost === "true"){
        swal({
            title: "Ошибка!",
            text: "Вас нет в этой задаче. Хотите запросить доступ?",
            icon: "error",
            buttons: {
                cancel: "Отмена",
                confirm: {
                    text: "Запросить",
                    value: true,
                },
            },
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              swal("Ваша заявка принята. Ожидайте подтверждения", {
                icon: "success",
              }).then((cc) => {
                  $.post("/ajax.php?do=requestViewTask", {id:$("#mainId").val()}, function(){
                    window.location.href = "https://atri-energo.ru/administration/module/task/";
                  });
              });
            } else {
                window.location.href = "https://atri-energo.ru/administration/module/task/";
            }
          });
    }
    
    tinymce.init({
        selector: '#mytextarea',
        language: 'ru',
        gecko_spellcheck:true
    });
    
    function updateMain(id){
        $.post("/ajax.php?do=taskMain", {id:id}, function(data){
            $("#taskMain").html(data);
        });
    }
    
    updateMain($("#mainId").val());
    
    function updatePanel(id){
        $.post("/ajax.php?do=taskPanel", {id:id}, function(data){
            $("#taskPanel").html(data);
        });
    }
    
    updatePanel($("#mainId").val());
    
    
    $("#fileBody").html("");
    
    $.post("/ajax.php?do=getCommentTask", {id:$("#mainId").val()}, function(data){
        $("#commentBody").html(data);
        $("#commentBody").scrollTop($("#commentBody")[0].scrollHeight);
    });

    $.post("/ajax.php?do=getAllFileTask", {id:$("#mainId").val()}, function(data){
        $("#allFile").html(data)
    });
    
    $('.select2').select2();


    $("#listAttach").select2({
        ajax: {
            url: "/ajax.php?do=getRelationTask",
            dataType: 'json',
            delay: 250,
            method: 'POST',
            data: function (params) {
              return {
                q: params.term, // search term
                module: $("#typeAttach").val(),
              };
            },
            processResults: function (data, params) {
              return {
                results: data.items,
              };
            },
            cache: true
          },
          minimumInputLength: 1,
          escapeMarkup: function (text) { return text; },
    });
    
    //WebSocket
    ws = new WebSocket("wss://atri-energo.ru/websocket?task="+$("#mainId").val()+"&user="+$("#whoAmI").val()+"");
    ws.onopen = function() {
        console.log("connection success");
    };
    ws.onmessage = function(e) {
        let json = JSON.parse(e.data);
        //console.log(json);
        //console.log(e.data);
        
        append_message(json);
        comment_scroll_top();
        
    };
    ws.onclose = function() {
        console.log("connection close");
    };

    function append_message(json){
        let str = "";
        if(json.attach != ""){
            for(let i=0;i<json.attach.length;i++){
                str += '<div><a target="_blank" href="/download/'+json["attach"][i]+'">'+json["attachName"][i]+'</a></div>'
            }
        }
        if(str.length > 0){
            str = '<div style="margin-bottom: 10px;"><a href="/ajax.php?do=loadAllFileCommentTask&pub_id='+json["comm_id"]+'" class="allFileComment">Скачать все файлы</a></div>'+str;
        }
        
        let status = '';
        switch (json.cat){
            case "1":
            status = '';
            break;
            case "2":
            status = '<code class="mr10 bg-warning dark p3 ph5">Задача отправлена на проверку</code><br><br>';
            break;
            case "3":
            status = '<code class="mr10 bg-success dark p3 ph5">Задача выполнена</code><br><br>';
            break;
            case "4":
            status = '<code class="mr10 bg-danger dark p3 ph5">Отправлена на доработку</code><br><br>';
            break;
            case "6":
            status = '<code class="mr10 bg-success dark p3 ph5">Задача возобновлена</code><br><br>';
            break;
            case "7":
            status = '<code class="mr10 bg-success dark p3 ph5">Отменена</code><br><br>';
            break;
        }
        $("#commentBody").append(`
        <div class="row blockComment">
            <div class="col-sm-5">
                <div class="col-md-5 col-lg-2">
                    <img class="avatar" src="${json.avatar}">
                </div>
                <div class="col-sm-10 bodyComment">
                    <div>${status}</div>
                    <div class="fullCommentData">
                        <span class="nameComment">${json.fullUserName}</span> 
                        <span class="dataComment">${json.date}</span>
                    </div>
                    <div class="textComment">${json.message}</div>
                    ${str}
                </div>
            </div>
        </div>`);
    }
    
    function comment_scroll_top(){
        $("#commentBody").scrollTop($("#commentBody")[0].scrollHeight);
    }

    //подтверждение задачи 
    $(document).on("click", "#performBtn", function(){
        $("#perform").modal("show");
    });
    
    $(document).on("click", "#performConfirm", function(){
        let id = $("#mainId").val(); 
        let fromid = $("#fromid").val();
        let toid = $("#toid").val();
        //let uid = $("#whoAmI").val();
        let coperformerid = $("#coperformerid").val();
        let observerid = $("#observerid").val();
        let status = 2;
        
        $.post("/ajax.php?do=taskSave", {id:id, fromid:fromid, toid:toid, coperformerid:coperformerid, observerid:observerid, status:status}, function(data){
            $.post("/ajax.php?do=commentSendTask", "message=&task="+id+"&cat=2&file=", function(data1){
                let json = JSON.parse(data1);
                append_message(json);
                comment_scroll_top();
            });
            
            updateMain(id);
            updatePanel(id);
        });
    });
    
    //ответственные
    $(document).on("click", "#responsibleChange", function(){
        $("#responsible").modal("show");
    });
    
    $(document).on("click", "#responsibleConfirm", function(){
        let id = $("#mainId").val(); 
        let toid = $("#toidNew").val();
        let oldtoid = $("#toid").val();
        let coperformerid = $("#coperformerid").val();
        let observerid = $("#observerid").val();
        
        $.post("/ajax.php?do=taskChangeResponsible", {id:id, toid:toid, oldtoid:oldtoid, coperformerid:coperformerid, observerid:observerid}, function(data){
            updatePanel(id);
        });
    });
    
    //подтверждение или доделать модалка
    $(document).on("click", ".solverOfFate", function(){
        let status = $(this).data("status");
        let id = $("#mainId").val(); 
        switch(status){
            case 3:
            $("#titleConfirmTaskModal").html("Подтвердить выполнение?");
            $("#bodyConfirmTaskModal").html("Вы уверены что хотите подтвердить выполнение задачи №"+id);
            $("#confirmTask").data("status", status);
            break;
            case 4:
            $("#titleConfirmTaskModal").html("Отправить на доработку");
            $("#bodyConfirmTaskModal").html("Вы уверены что хотите отправить на доработку задачу №"+id);
            $("#confirmTask").data("status", status);
            break;
        }
        $("#confirmTaskModal").modal("show");
    });
    
    $(document).on("click", "#confirmTask", function(){ 
        let fromid = $("#fromid").val();
        let toid = $("#toid").val();
        let coperformerid = $("#coperformerid").val();
        let observerid = $("#observerid").val();
        let uid = $("#whoAmI").val();
        
        let status = $(this).data("status");
        
        let id = $("#mainId").val();
        $.post("/ajax.php?do=taskSave", {id:id, fromid:fromid, toid:toid, coperformerid:coperformerid, observerid:observerid, status:status}, function(data){
        if(data == "delete"){
            document.location.href = "https://atri-energo.ru/administration/module/task";
        }
            $.post("/ajax.php?do=commentSendTask", "uid="+uid+"&message=&task="+id+"&cat="+status+"&file=", function(data1){
                let json = JSON.parse(data1);
                append_message(json);
                comment_scroll_top();
            });
            updatePanel(id);
            updateMain(id);
            /*
            switch(status){
                case 3:
                $("#status_aside").html('Подтверждена');
                $("#reloadTask").removeClass("hideBlock");
                break;
                case 4:
                $("#status_aside").html('Доделать');
                $("#performBtn").removeClass("hideBlock");
                break;
            }
            $(".solverOfFate ").addClass("hideBlock");
            */
        });
    });
    
    //добавить соисполнителей - 1 / наблюдателей - 2
    $(document).on("click", "#addCoperformer", function(){
        $("#COTitle").html("Выберите соисполнителей");
        $("#COBody").html("Выберите соисполнителей:");
        $("#COBtn").data("type", 1);
        $("#COModal").modal("show");
    });
    
    $(document).on("click", "#addObserverid", function(){
        $("#COTitle").html("Выберите наблюдателей");
        $("#COBody").html("Выберите наблюдателей:");
        $("#COBtn").data("type", 2);
        $("#COModal").modal("show");
    });
    
    $(document).on("click", "#COBtn", function(){
        let type = $(this).data("type");
        let id = $("#mainId").val();
        let val = $("#toidCO").val();
        let allCO = [];
        let mas = [];
        switch(type){
            case 1:
            mas = $(".allCoperformerid");
            for(let i = 0;i<mas.length;i++){
                allCO[i] = $(mas[i]).val();
            }
            for(let i = 0;i<val.length;i++){
                allCO.push(val[i]);
            }
            break;
            case 2:
            mas = $(".allObserverid");
            for(let i = 0;i<mas.length;i++){
                allCO[i] = $(mas[i]).val();
            }
            for(let i = 0;i<val.length;i++){
                allCO.push(val[i]);
            }
            break;
        }
        
        allCO = JSON.stringify(allCO);
        
        $.post("/ajax.php?do=addCOTask", {id:id, allCO:allCO, type:type, act: "add"}, function(){
            $("#toidCO").select2("val", "");
            updatePanel(id);
        });
    });
    
    //удалить соисполнителей - 1 / наблюдателей - 2
    $(document).on("click", ".removeCoperformerid", function(){
        let idDel = $(this).data("id");
        $("#CODelBody").html("Удалить соисполнителя "+$(this).data("name")+"?");
        $("#COTitleDel").html("Удалить соисполнителя?");
        $("#CODelBtn").data("id", idDel);
        $("#CODelBtn").data("type", 1);
        $("#COModalDel").modal("show");
    });
    
    $(document).on("click", ".removeObserverid", function(){
        let idDel = $(this).data("id");
        $("#CODelBody").html("Удалить наблюдателя "+$(this).data("name")+"?");
        $("#COTitleDel").html("Удалить наблюдателя?");
        $("#CODelBtn").data("id", idDel);
        $("#CODelBtn").data("type", 2);
        $("#COModalDel").modal("show");
    });
    
    $(document).on("click", "#CODelBtn", function(){
        let id = $("#mainId").val();
        let idDel = $(this).data("id");
        let type = $(this).data("type");
        let mas = [];
        let allCO = [];
        switch(type){
            case 1:
            mas = $(".allCoperformerid");
            for(let i = 0;i<mas.length;i++){
                if(idDel != $(mas[i]).val()){
                    allCO.push($(mas[i]).val());
                }
            }
            break;
            case 2:
            mas = $(".allObserverid");
            for(let i = 0;i<mas.length;i++){
                if(idDel != $(mas[i]).val()){
                    allCO.push($(mas[i]).val());
                }
            }
            break;
        }
        
        allCO = JSON.stringify(allCO);
        $.post("/ajax.php?do=addCOTask", {id:id, allCO:allCO, type:type, act: "delete"}, function(){
            $("#toidCO").select2("val", "");
            updatePanel(id);
        });
    });
    
    
    //отправка коммента
    $(document).on("click", "#send-comment", function(){
        let task = $("#mainId").val();
        let uid = $("#whoAmI").val();
        tinyMCE.triggerSave();
        let message = $("#formTextarea").serialize();
        let file = $("#fileForm").serialize();
        if(message.replace("message=", "") != "" || $("#fileBody").html() != ""){
            tinyMCE.activeEditor.setContent("");
            $("#fileBody").html("");
            $("#debug").html("");
            $.post("/ajax.php?do=commentSendTask", "uid="+uid+"&"+message+"&task="+task+"&cat=1&"+file, function(data1){
                let json = JSON.parse(data1);
                append_message(json);
                comment_scroll_top();
            });
        }
    });
    
    //файлы
    $(document).on("click", "#add-file", function(){
        $("#fileModal").modal("show");
    });
    
    $(document).on("DOMSubtreeModified", "#debug", function(){
        $("#fileModal").modal("hide");
        $("#fileBody").html("");
        let file = $("#debug").children();
        for(let i=0;i<file.length;i++){
            let val = $(file[i]).val();
            let text = val.split("/");
            text = text[text.length - 1];
            text = text.substr(text.indexOf("_") + 1);
            $("#fileBody").append('<tr><td>'+text+'</td><td><span title="Удалить" data-id="'+i+'" data-value="'+val+'" class="del-link delFile"><i class="fa fa-times" aria-hidden="true"></i></span></td></tr>'); 
        }
    });
    
    $(document).on("click", ".delFile", function(){
        let num = $(this).data("id");
        let value = $(this).data("value");
        $("#fileBody").html("");
        let file = $("#debug").children();
        for(let i=0;i<file.length;i++){
            let val = $(file[i]).val();
            if(val == value){
                $(file[i]).remove();
                } else {
                let text = val.split("/");
                text = text[text.length - 1];
                text = text.substr(text.indexOf("_") + 1);
                $("#fileBody").append('<tr><td>'+text+'</td><td><span title="Удалить" data-id="'+i+'" data-value="'+val+'" class="del-link"><i class="fa fa-times" aria-hidden="true"></i></span></td></tr>');
            }
        }
    });
    
    //крайний срок
    let flagTest = false;
    $(document).on("click", ".deadline", function(){
        $(this).html($(this).html()+'<div id="calend"></div>');
        $('#calend').datetimepicker({
            inline: true,
            locale: 'ru',
            sideBySide: true
        });
        $(".datepicker").removeClass("col-md-6");
        $(".datepicker").addClass("col-md-12");
        $(".timepicker").removeClass("col-md-6");
        $(".timepicker").addClass("col-md-12");
        $(this).removeClass("deadline");
        $(this).addClass("input");
        $("#calend").append('<div class="buttonCenter"><button id="saveDate" class="btn btn-sm btn-success mr10"><i class="fa fa-check" aria-hidden="true"></i></button><button class="btn btn-sm btn-danger" id="closeDate"><i class="fa fa-times" aria-hidden="true"></i></button></div>');
        setTimeout(function(){flagTest = true;}, 200);
    });
    
    $(document).on("click", "#saveDate", function(){
        let date = $('#calend').data("DateTimePicker").date();
        let id = $("#mainId").val();
        
        var d = new Date(date);
        var curr_date = ('0'+d.getDate()).slice(-2);
        var curr_month = ('0'+ (d.getMonth() + +1)).slice(-2);
        var curr_year = d.getFullYear();
        var curr_hours = ('0'+d.getHours()).slice(-2);
        var curr_minute = ('0'+d.getMinutes()).slice(-2);
        
        date = curr_year+"-"+curr_month+"-"+curr_date+" "+curr_hours+":"+curr_minute;
        $("#calend").remove(); 
        $(".input").addClass("deadline");
        $(".input").removeClass("input");
        flagTest = false;
        $.post("/ajax.php?do=setDeadlineTask", {id:id, date:date}, function(data){
            updatePanel(id);
        });
    });
    
    $(document).on("mouseout", "#calend", function(){
        flagTest = true;
    });
    
    $(document).on("mouseover", "#calend", function(){
        flagTest = false;
    });
    
    $(document).on("click", function(){
        if(flagTest){
            $("#calend").remove(); 
            $(".input").addClass("deadline");
            $(".input").removeClass("input");
            flagTest = false;
        }
    });
    
    $(document).on("click", "#closeDate", function(){
        $("#calend").remove(); 
        $(".input").addClass("deadline");
        $(".input").removeClass("input");
        flagTest = false;
    });
    
    //возобновить задачу
    $(document).on("click", "#reloadTask", function(){
        $("#reloadModal").modal("show");
    });
    
    $(document).on("click", "#reloadBtn", function(){
        let id = $("#mainId").val();
        let uid = $("#whoAmI").val();
        $.post("/ajax.php?do=reloadTask", {id: id}, function(){
            updateMain(id);
            updatePanel(id);
            $("#panelComment").removeClass("hideBlock");
            //если постановщик или админ
            if($("#hideAdmim").val() == 0 || $("#hideFrom").val() == 0){
                $("#editTask").removeClass("hideBlock");
            }
            $.post("/ajax.php?do=commentSendTask", "uid="+uid+"&message=&task="+id+"&cat=6&file=", function(data1){
                let json = JSON.parse(data1);
                append_message(json);
                comment_scroll_top();
            });
        });
    });
    
    //привязка
    $(document).on("click", "#addObject", function(){
        $("#objectModal").modal("show");
    });
    
    $(document).on("click", "#addObjectBtn", function(){
        let id = $("#mainId").val();
        let object = $("#listAttach").val();
        let module = $("#typeAttach").val();
        if(module != null && object != null){
            $.post("/ajax.php?do=reObjectTask", {id: id, object: object, module:module}, function(data){
                updatePanel(id);
            });
        }
    });
    
    $(document).on("click", ".delObject", function(){
        $("#objectDelModal").modal("show");
        let id = $(this).data("id");
        let module = $(this).data("module");
        $("#delObjectBtn").data("id", id);
        $("#delObjectBtn").data("module", module);
    });
    
    $(document).on("click", "#delObjectBtn", function(){
        let object = $(this).data("id");
        let module = $(this).data("module");
        let id = $("#mainId").val();
        $.post("/ajax.php?do=removeObjectTask", {object:object, id:id, module:module}, function(){
            updatePanel(id);
        });
    });

    /*
    $(document).on("change", "#typeAttach", function(){
        let module = $(this).val();
        
        $.post("/ajax.php?do=getRelationTask", {module: module}, function(data){
            $("#listAttach").html(data);
        });
        
    });
    */
    
    //редактировать задачу
    $(document).on("click", "#editTask", function(){
        if($("#editTask").data("status") == 1){
            $("#editTask").data("status", 0);
            $("#editTask").addClass("editActive");
            let title = $("#titleTask").text();
            $("#titleTask").html('<input type="text" class="form-control" id="titleInput" placeholder="Введите заголовок задачи" value="'+title+'">');
            let textTask = $("#textTask").text();
            $("#textTask").html('<textarea id="taskText" class="form-control" placeholder="Введите текст задачи">'+textTask+'</textarea>');
            tinymce.init({
                selector: '#taskText',
                language: 'ru'
            });
            if($("#fileBlock").html().replace(/\s/g, "") == "Отсутствуют"){
                $("#fileBlock").html("");
                }else {
                $(".liFile").append('<a href="javascript:void(0);" class="removeFileTaskEdit del-link ml20"><i class="fa fa-times" aria-hidden="true"></i></a>');
            }
            $("#fileBlock").append('<input type="button" class="btn btn-info" value="Добавить файл" id="addFileTaskEdit">');
            $("#fileBlock").append('<input type="hidden" value="" id="fileBoxDel">');
            $("#btnGroupChoice").addClass("hideBlock");
            $(this).remove();
            $("#fileBlock").append('<button style="margin-left: 20px; padding: 10px 15px;" type="button" class="btn btn-success" id="editTask" title="Сохранить изменения"><i style="font-size: 22px;" class="fa fa-floppy-o" aria-hidden="true"></i></button>');
            } else {
            $("#editTask").data("status", 1);
            $("#editTask").removeClass("editActive");
            let id = $("#mainId").val();
            title = $("#titleInput").val();
            $("#titleTask").html(title);
            //textTask = $("#taskText").val();
            textTask = tinyMCE.get('taskText').getContent();
            let fileBoxDel = $("#fileBoxDel").val();
            let file = $("#fileEditForm").serialize();

            $.post("/ajax.php?do=editTask", "id="+id+"&title="+title+"&textTask="+textTask+"&fileBoxDel="+fileBoxDel+"&"+file, function(data){
                updateMain(id);
            });
        }
        
    });
    
    //удаление файлов в редактировании
    $(document).on("click", ".removeFileTaskEdit", function(){
        if($(this).parent().data("id") != undefined){
            let json = [];
            if($("#fileBoxDel").val().length > 0) json = JSON.parse($("#fileBoxDel").val());
            json.push($(this).parent().data("id"));
            json = JSON.stringify(json);
            $("#fileBoxDel").val(json);
            $(this).parent().remove();
            } else {
            let value = $(this).parent().data("value");
            $("#newFileEditTask").html("");
            let file = $("#debug1").children();
            for(let i=0;i<file.length;i++){
                let val = $(file[i]).val();
                if(val == value){
                    $(file[i]).remove();
                    } else {
                    let text = val.split("/");
                    text = text[text.length - 1];
                    text = text.substr(text.indexOf("_") + 1);
                    $("#newFileEditTask").append('<li class="liFile" data-value="'+val+'"><a href="javascript:void(0);" class="">'+text+'</a><a href="javascript:void(0);" class="removeFileTaskEdit del-link ml20"><i class="fa fa-times" aria-hidden="true"></i></a></li>');
                }
            }
        }
    });
    
    //добавление файлов
    $(document).on("click", "#addFileTaskEdit", function(){
        $("#fileEditModal").modal("show");
    });
    
    $(document).on("DOMSubtreeModified", "#debug1", function(){
        // $("#fileEditModal").modal("hide");
        $("#newFileEditTask").html("");
        let file = $("#debug1").children();
        for(let i=0;i<file.length;i++){
            let val = $(file[i]).val();
            let text = val.split("/");
            text = text[text.length - 1];
            text = text.substr(text.indexOf("_") + 1);
            $("#newFileEditTask").append('<li class="liFile" data-value="'+val+'"><a href="javascript:void(0);" class="">'+text+'</a><a href="javascript:void(0);" class="removeFileTaskEdit del-link ml20"><i class="fa fa-times" aria-hidden="true"></i></a></li>'); 
        }
    });

    //Основной объект
    $(document).on("click", ".myStarO", function(){
        let id = $("#mainId").val();
        let oid = $(this).data("oid");
        $.post("/ajax.php?do=setObjectDefTask", {id:id, oid:oid}, function(){
            updatePanel(id);
        });
    });

    //установить/снять все checkbox
    $(document).on("change", "#checkChangeAll", function(){
        let status = $(this).prop("checked");
        $(".checkFileLoad").each(function(){
            $(this).prop("checked", status);
        });
    });

    //одобрение/не одобрение заявки на просмотр
    $(document).on("click", ".addRequestid", function(){
        let id = $(this).data("id");
        $(".requestConfirm").data("id", id);
        $("#requestModal").modal("show");
    });

    $(document).on("click", ".requestConfirm", function(){
        let id = $(this).data("id");
        let mainId = $("#mainId").val();
        let request = $(this).data("request");
        $.post("/ajax.php?do=requestResTask", {id:id, mainId:mainId, request:request}, function(){
            updatePanel(mainId);
        });
    });

    //отмена задачи
    $(document).on("click", "#cancleTask", function(){
        $("#cancleTaskModal").modal("show");
    });

    $(document).on("click", "#cancleTaskModalBtn", function(){
        let mainId = $("#mainId").val();
        $.post("/ajax.php?do=cancleTask", {id:mainId}, function(){
            $.post("/ajax.php?do=commentSendTask", "message=&task="+mainId+"&cat=7&file=", function(data1){
                let json = JSON.parse(data1);
                append_message(json);
                comment_scroll_top();
            });
            updateMain(mainId);
            updatePanel(mainId);
        });
    });
    
});