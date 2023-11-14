async function reload_grid(){
    await $('#jqSales').trigger('reloadGrid');
}
function copySalesModal(This){
    let id = $(This).data("id");
    $("#copySalesModal").modal("show");
    $("#number_sales").html(id);
    $("#copySalesBtn").data("id", id);
}
function copySalesBtn(This){
    let id = $(This).data("id");
    $.post("/ajax.php?do=copySales", {id:id}, function(data){
        reload_grid();
        notif('success', 'Успешно', 'Продажа успешно скопирована');
    });
}


$(function(){
    $(document).on("click", ".syncBtn", function(){
        let sales_id = $(this).data("id");
        $("#modal-active-"+sales_id).magnificPopup("close");
        $.post("/ajax.php?do=sales_sync", {sales_id:sales_id}, function(data){
            reload_grid();
            switch(data){
                case '1':
                    notif("success", "Информация", "Документ успешно зарегистрирован");
                break;
                case '2':
					notif("danger", "Ошибка", "Что-то пошло не так...");
                break;
            }
        });
    });

    $(`#fastTaskModalDiv`).html(FastTask.modal({
        end(){
            $('#jqSales').trigger('reloadGrid');
        }
    }));
});

function showFastTaskModal(id){
    FastTask.modal_open(id);
}

async function showViewTaskModal(id){
    $("#viewTaskModal").modal("show");
    const viewTaskModalHeader = $("#viewTaskModalHeader");
    const viewTaskModalBody = $("#viewTaskModalBody");

    viewTaskModalHeader.html("");
    viewTaskModalBody.html("");

    const data = await FastTask.get_data(id);

    viewTaskModalHeader.html(`Текущая задача по продаже №${data.sales.numb}`);
    if(data.task?.id){
        viewTaskModalBody.append(viewTaskModalDraw(data));
        if(data.count > 1){
            const div = $(`<div style="cursor: pointer;">Показать еще</div>`);
            div.on("click", async function(){
                this.remove();
                const items = await FastTask.get_data_all(id);
                viewTaskModalBody.html("");
                items.forEach(item => {
                    viewTaskModalBody.append(viewTaskModalDraw(item));
                });
            });
            viewTaskModalBody.append(div);
        }
    } else {
        viewTaskModalBody.append($(`<div>Задачи не найдены!</div>`));
    }
}

function viewTaskModalDraw(data){
    return FastTask.view_task_draw(data, {
        async end_task(){
            reload_grid();
        },
        open(){
            $("#viewTaskModal").modal("hide");
        }
    })
}

function del_sales(id){
    swal({
        title: "Вы уверены?",
        text: "Удалить продажу",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            $.post("ajax.php?do=change_sales_field_value", {id, field: 'del', value: 1}, function(){
                reload_grid();
                swal("Успешно", "Продажа успешно удалена", "success");
            });
        }
      });
}

function hidden_site_sales(data){

    swal({
        title: "Вы уверены?",
        text: data.status_text,
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            $.post("ajax.php?do=change_sales_field_value", {id:data.id, field: 'hidden_site', value: data.status}, function(){
                reload_grid();
                swal("Успешно", "", "success");
            });
        }
      });
}