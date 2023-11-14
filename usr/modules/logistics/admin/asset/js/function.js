const $transport = {
    update_list_func: () => {},
    update_info_func: () => {},
    change_driver: (id, data) => {alert(`Функция не указана`)},
}

class DriverModalInit{
    driver = $("#driver");
    btn = $("#driverModalBtn");
    modal = $("#driverModal");
    
    open(){
        this.btn.off();
        const checks = get_checkbox_checked();
        if(checks.length > 0){
            this.modal.modal("show");
            this.btn.on("click", () => {
                const driver_id = this.driver.val();
                if(driver_id != null){
                    this.save({id: checks, driver: driver_id}).then(() => {
                        update_all();
                    });
                    this.modal.modal("hide");
                } else {
                    swal("Внимание!", "Выберите водителя, чтобы продолжить!", "warning");
                }
            });
        } else {
            checkbox_error();
        }
    }
    async save(request){
        swal("Внимание!", "Функция сохранения не установлена", "warning");
    }
}

function checkbox_error(){
    swal("Внимание!", "Выберите позиции, чтобы продолжить!", "warning");
}

const $driverModal = new DriverModalInit;

function checkbox_change(This, id){
    $(".item__checkbox-"+id).prop("checked", $(This).prop("checked"));
}

function get_checkbox_checked(){
    const id = [];
    $(".item__checkbox:checked").each((index, item) => {
        id.push($(item).val());
    });
    return id;
}

function update_info(){
    return update_info_data().then(data => {
        $("#info").html(data);
        $transport.update_info_func();
    });
}

async function update_list(){
    const data = await update_list_data();
    $("#list").html(data);
    $transport.update_list_func();
}

async function update_info_lw(){
    const lw = new LW;
    lw.on();
    await update_info();
    lw.off();
}

async function update_list_lw(){
    const lw = new LW;
    lw.on();
    await update_list();
    lw.off();
}

async function update_all(){
    await Promise.all([update_list(), update_info()]);
}

async function update_all_lw(){
    const lw = new LW;
    lw.on();
    await update_all();
    lw.off();
}

function toggle_min_view(id){
    const min_view_class = ".min_view";
    $(`#toggle_min_view_icon_${id}`).toggleClass("fa-rotate-180");
    const head = $(`#panel_head_${id}`);
    head.find(min_view_class).toggle();
    $(`#panel_body_${id}`).slideToggle();
    $(`#panel_footer_${id}`).slideToggle();
    head.find(".min_view_v").each((i, elem) => {
        const item = $(elem);
        const class_toggle = item.data("class_toggle");
        if(class_toggle != undefined){
            item.toggleClass(class_toggle);
        }
    });
}

function mas_delete_modal(){
    const checks = get_checkbox_checked();
    if(checks.length > 0){
        swal({
            title: "Вы уверены?",
            text: "Удалить выделенные позиции!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then(async (willDelete) => {
            if (willDelete) {
                const lw = new LW;
                lw.on();
                try{
                    await mas_delete(checks);
                    await update_list();
                } catch {
                    alert('Функция не объявлена!');
                } finally{
                    lw.off();
                }
            }
          });
    } else {
        checkbox_error();
    }
}

async function print_modal_ttn(id){
    $("#modal_ttn").modal("show");
    $("#modal_ttn_form").prop(`action`, `/administration/module/logistics/delivery/print/${id}`);
}