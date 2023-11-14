window.onload = function(){

    const lw = new LW;

    function getMainId(){
        return document.getElementById("mainId").value;
    }

    const mainId = getMainId();

    async function update_control(form){
        form.append("_method", "PUT");
        return await ( await fetch( prepare_link(document.getElementById("router-update").value, mainId), {
            method: "POST",
            body: form,
        } ) ).json();
    }

    function buttonModal(text, func){
        Swal.fire({
            title: "Вы уверены?",
            text,
            icon: "info",
            showConfirmButton: true,
            showCancelButton: true,
          })
          .then(async (result) => {
            if (result.isConfirmed) {
                lw.on();
                await func();
                lw.off();
            }
          });
    }

    function buttonTextModal(text, func){
        Swal.fire({
            title: "Вы уверены?",
            text,
            icon: "info",
            inputLabel: 'Комментарий',
            input: 'textarea',
            showConfirmButton: true,
            showCancelButton: true,
          })
          .then(async (result) => {
            if (result.isConfirmed) {
                lw.on();
                await func(result.text);
                lw.off();
            }
          });
    }

    function head_init(){
        new DarkEditable(document.getElementById("plan_pay_date"), {
            type: "date",
            viewformat: "DD.MM.YYYY",
            url: "/ajax.php?do=setPlanPayDateControl",
            title: "Введите дату оплаты",
        })

        //control
        document.getElementById("button-approve")?.addEventListener('click', function(){
            buttonModal("Согласовать заявку и отправить на оплату", async () => {
                await end(mainId, 3);
                await update_head();
            });
        });
        document.getElementById("button-revision")?.addEventListener('click', function(){
            buttonTextModal("Отправить на доработку", async (comment) => {
                await end(mainId, 2, comment);
                await update_head();
            });
        });
        document.getElementById("button-cancel_approve")?.addEventListener('click', function(){
            buttonModal("Отменить согласование заявки", async () => {
                const form = new FormData;
                form.append("status", 1);
                await update_control(form);
                await update_head();
            });
        });

        //pay control
        const pay_func = async () => {
            const form = new FormData;
            // form.append("status", status);
            await update_control(form);
            await update_head();
        }
        document.getElementById("button-pay")?.addEventListener('click', function(){
            buttonModal("Оплатить", async () => {
                await pay_func();
            });
        })
        document.getElementById("button-cancel-pay")?.addEventListener('click', function(){
            buttonModal("Отменить оплату", async () => {
                await pay_func();
            });
        })
    }
    
    head_init();

    async function update_head(){
        const link = `${document.getElementById("router-show").value}?view=head`;
        document.getElementById("control-head").innerHTML = await ( await fetch( link ) ).text();
        head_init();
    }

    async function update_body(){
        const link = `${document.getElementById("router-show").value}?view=body`;
        document.getElementById("control-tbody").innerHTML = await ( await fetch( link ) ).text();
    }

    document.querySelectorAll(".delete-item").forEach(item => {
        item.addEventListener('click', function(){
            Swal.fire({
                title: "Вы уверены?",
                text: "Удалить позицию",
                icon: "warning",
                showConfirmButton: true,
                showCancelButton: true,
              })
              .then(async (result) => {
                if (result.isConfirmed) {
                    lw.on();
                    await delete_item(this.dataset.id);
                    await update_body();
                    lw.off();
                }
              });
        });
    })

    function prepare_link(link, id){
        return link.replace("@id", id);
    }

    async function delete_item(id){
        const form = new FormData;
        form.append("_method", "DELETE");
        return await ( await fetch( prepare_link(document.getElementById("router-delete-item").value, id), {
            method: "POST",
            body: form,
        } ) ).json();
    }
}