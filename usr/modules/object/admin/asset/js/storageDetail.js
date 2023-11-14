$(function(){
    //console.log("~");
    $(document).on("keydown", ".rubber", function(event){
        $(this).width(($(this).val().length + 4)*8);
        if(event.which == 13)
        {
            $(this).blur();
        }
    });
    $(document).on("click", ".count_now", function(){
        $(this).addClass("input");
        $(this).removeClass("count_now");
        if(isNaN($(this).text())){
            $(this).text('0');
        } 
        window.saveValue = $(this).text();
        $(this).html('<input type="number" id="input" class="form-control rubber" value="'+$(this).text().replace(/ /g, "")+'" style="width: ' +(($(this).text().replace(/ /g, "").length + 6) * 8)+ 'px">');
        $("#input").focus();
        $("#input").select();
    });
    
    $(document).on("blur", "#input", function(){
        let id = $(this).parent().parent().data("id");
        let value = $(this).val();
        let count_stable = $(this).parent().parent().children(".availability").text();
        let text = $(this).parents("tr").children(".title").text();

        let statusElem = $(this).parents("tr").children(".status").children();
        statusElem.removeClass("fa-circle-o");
        statusElem.removeClass("text-danger");
        statusElem.addClass("fa-check-circle");
        statusElem.addClass("text-success");
        
        $(".input").html(value);
        $(".input").addClass("count_now");
        $(".input").removeClass("input");
        let mainId = window.location.href.split("/");
        mainId = mainId[mainId.length - 1];

        $.post("/administration/module/storage/ajax/count_send", {id:id, count_now: value, count_stable: count_stable, type: 2}, function(){

        }).fail(function() {
            swal("Ошибка", "Данные товара "+text+" не сохранены!", "error");
        });
    });
    
    //кнопка выполнить
    $(document).on("click", "#sub", function(){
        let checks = $(".checks");
        let select = $("#act").val();
        //console.log(select);
        switch(select){
            case "1":
            $("#confirmModal").modal("show");
            break;
            case "2":
            $("#removeModal").modal("show");
            break;
            case "3":
            $("#delAllModal").modal("show");
            break;
        }
    });

    $(document).on("click", "#delAllModalBtn", function(){
        let data = $("#tablesForm").serialize();
        $.post("/ajax.php?do=delAllStorage", data, function(){
            let mainId = window.location.href.split("/");
            mainId = mainId[mainId.length - 1];
            $.post("/administration/module/storage/ajax/detail_list/"+mainId, function(data){
                $("#dev_list").html(data);
            });
        });
    });
    
    $(document).on("click", "#confirmBtn", function(){
        let checks = $(".checks");
        for(let i=0;i<checks.length;i++){
            if($(checks[i]).prop("checked")){
                let tr = $(checks[i]).parent().parent().parent();
                let id = $(tr).data("id");
                let availability = $(tr).children(".availability").text();
                $.post("/administration/module/storage/ajax/count_send", {id: id, count_now:availability, type: 2}, function(){
                    
                });
            }
        }
        let mainId = window.location.href.split("/");
        mainId = mainId[mainId.length - 1];
        $.post("/administration/module/storage/ajax/detail_list/"+mainId, function(data){
            $("#dev_list").html(data);
        });
    });
    
    $(document).on("click", "#removeBtn", function(){
        let checks = $(".checks");
        for(let i=0;i<checks.length;i++){
            if($(checks[i]).prop("checked")){
                let tr = $(checks[i]).parent().parent().parent();
                let id = $(tr).data("id");
                $.post("/administration/module/storage/ajax/count_send", {id: id, count_now:0, type: 2}, function(){
                    
                });
            }
        }
        let mainId = window.location.href.split("/");
        mainId = mainId[mainId.length - 1];
        $.post("/administration/module/storage/ajax/detail_list/"+mainId, function(data){
            $("#dev_list").html(data);
        });
    });
    
});

$(document).on("click", ".confirmStorage", function(){
    let elem = $(this);
    let mainId = window.location.href.split("/");
    mainId = mainId[mainId.length - 1];
    $.post("/administration/module/storage/ajax/check_storage_status/"+mainId, function(data){
        if(+data === 1){
            elem.parent().remove();
            $(".AddProduct").remove();
            $.post("/administration/module/storage/ajax/storage_confirm_status/"+mainId, function(){
                alert("Успешно отправлено!");
                $.post("/administration/module/storage/ajax/detail_list/"+mainId, function(data){
                    $("#dev_list").html(data);
                });
            });
        } else {
            alert("Не все товары проставлены!");
        }
    })
});

$(document).on("click", ".removeElem", function(){
    let id = $(this).data("id");
    $("#removeElemModalBtn").data("id", id);
    $("#removeElemModal").modal("show");
});

$(document).on("click", "#removeElemModalBtn", function(){
    let id = $(this).data("id");
    let mainId = window.location.href.split("/");
    mainId = mainId[mainId.length - 1];
    $.post("/administration/module/storage/ajax/remove_element/"+id, function(){
        $.post("/administration/module/storage/ajax/detail_list/"+mainId, function(data){
            $("#dev_list").html(data);
        });
    });
});