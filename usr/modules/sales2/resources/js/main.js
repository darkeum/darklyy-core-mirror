$(function(){
    $(`body`).append(FastTask.modal({
        end(){
            reload_grid();
        }
    }));
});

function reload_grid(){
    $grid.trigger('reloadGrid');
}

function showFastTaskModal(id){
    FastTask5.modal_open(id);
}

async function showViewTaskModal(id){
    $("#viewTaskModal").modal("show");
    const viewTaskModalHeader = $("#viewTaskModalHeader");
    const viewTaskModalBody = $("#viewTaskModalBody");

    viewTaskModalHeader.html("");
    viewTaskModalBody.html("");

    const data = await FastTask5.get_data(id);

    viewTaskModalHeader.html(`Текущая задача по продаже №${data.sales.numb}`);
    if(data.task?.id){
        viewTaskModalBody.append(viewTaskModalDraw(data));
        if(data.count > 1){
            const div = $(`<div style="cursor: pointer;">Показать еще</div>`);
            div.on("click", async function(){
                this.remove();
                const items = await FastTask5.get_data_all(id);
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
    return FastTask5.view_task_draw(data, {
        async end_task(){
            reload_grid();
        },
        open(){
            $("#viewTaskModal").modal("hide");
        }
    })
}