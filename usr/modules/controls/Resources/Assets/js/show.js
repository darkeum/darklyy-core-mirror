window.onload = () => {
    function head_init(){
        new DarkEditable(document.getElementById("plan_pay_date"), {
            type: "date",
            viewformat: "DD.MM.YYYY",
            url: "/ajax.php?do=setPlanPayDateControl",
            title: "Введите дату оплаты",
        })
    }

    head_init();
}

let show = function() {

    let lw = new LW;

    let prepare_link = (link, id) =>  link.replace("@id", id);

    let getMainId = () => document.getElementById('mainId');

    const mainId = getMainId();

    let update_control  = async (form) => {
        form.append("_method", "PUT");
        let link = prepare_link(document.getElementById('router-update').value, mainId);
        return await fetch(link, {
            method: 'post',
            body: form,
        })
    }

    let buttonModal = (text, func) => {
        Swal.fire({
            title: "Вы уверены?",
            text,
            icon: "info",
            showConfirmButton: true,
            showCancelButton: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                lw.on();
                await func();
                lw.off();
            }
        });
    }

    let showCancel = () => {
        buttonModal("Отменить согласование заявки", async () => {
            window.livewire.emit('update', {status: 1});
        });
    }

    let buttonTextModal = (text, func) => {
        Swal.fire({
            title: "Вы уверены?",
            text,
            icon: "info",
            inputLabel: 'Комментарий',
            input: 'textarea',
            showConfirmButton: true,
            showCancelButton: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                lw.on();
                await func(result.value);
                lw.off();
            }
        });
    }

    let showRevision = () => {
        buttonTextModal("Отправить на доработку", async (comment) => {
            await end(2, comment);
        });
    }

    let showApprove = () => {
        buttonModal("Согласовать заявку и отправить на оплату", async () => {
            end(3);
        });
    }

    let pay_func = async () => {
        const form = new FormData;
        await update_control(form);
        await update_head();
    }

    let buttonPay = () => {
        buttonModal("Оплатить", async () => await pay_func());
    }

    let buttonCancel = () => {
        buttonModal("Отменить оплату", async () => await pay_func())
    }

    let deletePosition = (url) => {
        Swal.fire({
            title: "Вы уверены?",
            text: "Удалить позицию",
            icon: "warning",
            showConfirmButton: true,
            showCancelButton: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                lw.on();
                await delete_item(url);
                await update_body();
                lw.off();
            }
        });
    }

    let delete_item = async (url) => {
        const form = new FormData;
        form.append('_method', 'DELETE');
        return (await fetch(url, {
                method: 'POST',
                body: form,
            })
        );
    }

    let update_head = async () => {
        const link = `${document.getElementById('router-show').value}?view=head`;
        document.getElementById('control-head').innerHTML = await ( await fetch( link ) ).text();
    }

    let update_body = async () => {
        const link = `${document.getElementById("router-show").value}?view=body`;
        document.getElementById("control-tbody").innerHTML = await ( await fetch( link ) ).text();
    }

    return {
        showCancel: showCancel,
        buttonModal: buttonModal,
        buttonTextModal: buttonTextModal,
        showRevision: showRevision,
        showApprove: showApprove,
        buttonPay: buttonPay,
        buttonCancel: buttonCancel,
        deletePosition: deletePosition,
    };
}();
