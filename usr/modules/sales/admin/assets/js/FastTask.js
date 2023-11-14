class FastTask{
    static async get_data(sales_id){
        return await (await fetch(`/administration/module/sales/get_fast_task_data/${sales_id}`)).json(); 
    }

    static async get_data_all(sales_id){
        return await ( await fetch(`/administration/module/sales/get_fast_task_data_all/${sales_id}`) ).json();
    }

    static view_task_draw(data, request = {}){
        const This = this;
        const container = $(`<div></div>`);
        const title = $(`<h5></h5>`);
        title.html(data.task.title);
        const hr = $(`<hr class="mt10 mb10">`);
        const div = $(`<div style="display:flex; justify-content: space-between;"></div>`);
        let div_end_message = ``;
        if(data.task.status == 3){
            let end_message = "";
            data.task.comments.forEach(item => {
                if(item.cat == 3){
                    end_message = item.comment;
                }
            });
            div_end_message = `<div><b>Результат задачи: </b>${end_message}</div>`;
        }
        div.append(`
            <div>
                <div><b>Содержание задачи: </b>${data.task.task}</div>
                <div><b>Дата завершения задачи: </b>${data.task.date_end_view}</div>
                ${div_end_message}
            </div>
        `);
        const div2 = $(`<div class="btn-group" style="display: flex; align-items: center;"></div>`);
        const btnShow = this.btn_show();
        btnShow.on("click", function(){
            window.open(`/administration/module/task/view/${data.task.id}`);
        });
        div2.append(btnShow);
        if(data.task.status != 3){
            const btnEnd = this.btn_end();
            btnEnd.on("click", function(){
                This.end_modal().then(async (end) => {
                    if(end !== null) {
                        try {
                            const form = new FormData();
                            form.append("task_id", data.task.id);
                            form.append("sales_id", data.sales.id);
                            form.append("end_message", end);
                            const result = await This.fast_task_end(form);
                            $(this).remove();
                            if("end_task" in request){
                                request.end_task();
                            }
                            
                            This.open_new_task_modal().then((willDelete) => {
                                if (willDelete) {
                                    if("open" in request){
                                        request.open();
                                    }
                                    This.modal_open(data.sales.id);
                                }
                            });
                        } catch (error) {
                            this.error_modal(error);
                        }
                    }
                });
            });
            div2.append(btnEnd);
        }
        div.append(div2);
        container.append(title, div, hr);
        return container;
    }

    static async fast_task_end(form){
        return await ( await fetch(`/administration/module/sales/fast_task_end`, {
            method: "POST",
            body: form
        }) ).json();
    }

    static async fast_task(form){
        return await (await fetch(`/administration/module/sales/fast_task`, {
            method: "POST",
            body: form,
        })).json();
    }

    static btn_show(){
        return $(`<button type="button" class="btn btn-xs btn-primary">Открыть задачу</button>`);
    }

    static btn_end(){
        return $(`<button type="button" class="btn btn-xs btn-info">Завершить задачу</button>`);
    }

    static async end_modal(){
        return await swal({
            title: "Вы уверены?",
            text: "Завершить задачу",
            icon: "info",
            content: "input",
            buttons: true,
            dangerMode: true,
        });
    }

    static async open_new_task_modal(){
        return await swal({
            title: "Открыть новую задачу?",
            text: "Открыть новую задачу",
            icon: "info",
            buttons: true,
            dangerMode: true,
        })
    }

    static error_modal(error){
        swal("Ошибка!", error.message, "error");
    }

    static modal_(){
        return $(`<div class="modal fade" id="fastTaskModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content admin-form">
              <form id="fastTaskModalForm">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Быстрые задачи</h5>
                </div>
                <div class="modal-body">
                    <label>Дата конца</label>
                    <input type="datetime-local" name="dateend" value="" class="form-control" required>
                    <label>Описание</label>
                    <textarea name="task" placeholder="Введите описание" class="form-control" required></textarea>
                    <input type="hidden" name="id" id="fastTaskModalId" value="">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Добавить</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                </div>
            </form>
          </div>
        </div>
      </div>`);
    }

    static modal(request = {}){
        const This = this;
        const modal = this.modal_();

        modal.find(`#fastTaskModalForm`).on("submit", async function(e){
            e.preventDefault();
            $("#fastTaskModal").modal("hide");
            const form = new FormData(this);
            this.reset();
            const data = await This.fast_task(form);
            if(request?.end){
                request.end();
            }
        })
        return modal;
    }

    static modal_open(id){
        $(`#fastTaskModal`).modal(`show`);
        $("#fastTaskModalId").val(id);
    }
}