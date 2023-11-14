class FastTask5 extends FastTask{
    static async end_modal(){
        const result = await Swal.fire({
          title: 'Завершить задачу',
          input: 'text',
          showCancelButton: true,
          inputValidator: (value) => {
            if (!value) {
              return 'Вам нужно что-то написать'
            }
          }
        });
        return result.value ?? null;
    }

    static async open_new_task_modal(){
        const result = await Swal.fire({
            title: 'Открыть новую задачу?',
            text: "Открыть новую задачу",
            icon: 'info',
            showCancelButton: true,
        })
        return result.isConfirmed;
    }

    static error_modal(error){
        Swal.fire(
            'Ошибка!',
            error.message,
            'error'
        );
    }

    static btn_show(){
        const btn = super.btn_show();
        btn.addClass("btn-sm");
        btn.removeClass("btn-xs");
        return btn;
    }

    static btn_end(){
        const btn = super.btn_end();
        btn.addClass("btn-sm");
        btn.removeClass("btn-xs");
        return btn;
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </form>
          </div>
        </div>
      </div>`);
    }

    static modal_open(id){
        const modal = new bootstrap.Modal(document.getElementById("fastTaskModal"));
        modal.show();
        $("#fastTaskModalId").val(id);
    }
}