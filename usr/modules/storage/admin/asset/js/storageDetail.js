$(function(){
    func_update_head();
    func_update_body();
    position_init();
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
        $(this).html('<input type="text" id="input" class="form-control rubber" value="'+$(this).text().replace(/ /g, "")+'" style="width: ' +(($(this).text().replace(/ /g, "").length + 6) * 8)+ 'px">');
        $("#input").focus();
        $("#input").select();
    });
    
    $(document).on("blur", "#input", function(){
        let id = $(this).parent().parent().data("id");
        let value = $(this).val();
        // let count_stable = $(this).parent().parent().children(".availability").text();
        let text = $(this).parents("tr").children(".title").text();

        let statusElem = $(this).parents("tr").children(".status").children();
        statusElem.removeClass("far").removeClass("fa-circle").removeClass("text-danger");;
        statusElem.addClass("fa-check-circle").addClass("text-success").addClass("fas");
        
        $(".input").html(value);
        $(".input").addClass("count_now");
        $(".input").removeClass("input");

        $.post("/administration/module/storage/ajax/count_send", {id:id, count_now: value, type: 2}, function(){

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
        const data = $("#tablesForm").serialize();
        $.post("/ajax.php?do=delAllStorage", data, function(){
            update_body();
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
        update_body();
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
        update_body();
    });
    
});

function position_init(){
    $("#dev_list").sortable({
        update: function( event, ui ) {
          //console.log($(this).children());
          const position = $(".position");
          const json = {};
          position.each(function(index) {
                const i = index + 1;
                $(this).html(i);
                json[ $(this).data("id") ] = i;
          });
          const data = JSON.stringify(json);
          $.post("/administration/module/storage/detail_position_update", {data});
        },
        axis: "y",
        handle: ".position"
      });
}

function getMainId(){
    return $("#mainId").val();
}

function confirmStorageModal(){
    $("#confirmStorageModal").modal("show");
}

$(document).on("click", "#confirmStorage", function(){
    const mainId = getMainId();
    $.post("/administration/module/storage/ajax/check_storage_status/"+mainId, function(data){
        if(+data === 1){
            $.post("/administration/module/storage/ajax/storage_confirm_status/"+mainId, function(){
                alert("Успешно отправлено!");
                $("#confirmStorageModal_div").remove();
                $(".AddProduct").remove();
                update_body();
            });
        } else {
            alert("Не все товары проставлены!");
        }
    })
});

function removeElem(id){
    $("#removeElemModalBtn").data("id", id);
    $("#removeElemModal").modal("show");
}

$(document).on("click", "#removeElemModalBtn", function(){
    let id = $(this).data("id");
    $.post("/administration/module/storage/ajax/remove_element/"+id, function(){
        update_body();
    });
});

$(document).on("click", ".sendNotif", function(){
    $("#sendNotifModal").modal("show");
});

$(document).on("click", "#sendNotifBtn", function(){
    const id = getMainId();
    $.post("/ajax.php?do=sendNotifStorage", {id}, function(){
        $(".sendNotif").parent().remove();
    });
});

function modal_print(){
    $("#modal_print").modal("show");
}

function update_body(){
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/storage/ajax/detail_list/"+id, function(data){
            $("#dev_list").html(data);
            func_update_body();
            resolve();
        });
    })
}

function func_update_body(){
    x_comment_detail();
}

function x_comment_detail(){
    $(".comment").editable({
        type: 'textarea',
        url: '/administration/module/storage/x_detail_detail_update',
        title: 'Введите комментарий',
        emptytext: "Не указано",
        name: "comment",
    });
}

async function update_head(){
    const id = getMainId();
    const data = await $.post("/administration/module/storage/ajax/detail_head/"+id);
    $("#admin-form").html(data);
    func_update_head();
}

function func_update_head(){
    x_comment();
}

function x_comment(){
    $("#comment").editable({
        type: 'textarea',
        pk: getMainId(),
        url: '/administration/module/storage/x_detail_update',
        title: 'Введите комментарий',
        emptytext: "Не указано",
    });
}

function storage_detail_sort(value){
    Cookies.set('storage_detail_sort', value);
    update_body();
}

$(function(){
    load_excel.load_init();
});

const load_excel = {
    name: "load_excel_modal",
    open(){
        $(`#${this.name}`).modal("show");
    },
    close(){
        $(`#${this.name}`).modal("hide");
    },
    load_init(){
        const This = this;
        $(document).on("submit", `#${this.name}_form`, function(e){
            e.preventDefault();
            This.close();
            const form_data = new FormData(this);
            $(this)[0].reset();
            This.load(form_data);
        });
    },
    load(data){
        const id = getMainId();
        const lw = new LW;
        lw.on();
        $.ajax({
            url: `/administration/module/storage/storage_load_excel/${id}`,
            cache: false,
            contentType: false,
            processData: false,
            data,
            type: 'post',
            success: function(response){
                try{
                    const data = JSON.parse(response);
                    update_body().then(() => {
                        lw.off();
                        if(data.error.length > 0){
                            swal("Успешно!", "Следующие позиции не были загружены: "+data.error.join(', '), "warning");
                        } else {
                            swal("Успешно!", "Все данные загружены", "success");
                        }
                    });
                } catch(e){
                    lw.off();
                    swal("Ошибка!", e, "error");
                }
            },
            error: function(){
                lw.off();
                swal("Ошибка!", "", "error");
            }
        }); 
    }
}

async function loadFile(){
    const id = getMainId();
    const form = $("#loadFileForm").serialize();
    $("#loadFile").modal("hide");
    clearAll_();
    const lw = new LW;
    lw.on();
    await $.post(`/administration/module/storage/detail_save_file/${id}`, form);
    await update_head();
    lw.off();
}

function delete_file(id){
    swal({
        title: "Вы уверены?",
        text: "Удалить файл!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then(async (willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            await $.post(`/administration/module/storage/detail_delete_file/${id}`);
            await update_head();
            lw.off();
        }
      });
}