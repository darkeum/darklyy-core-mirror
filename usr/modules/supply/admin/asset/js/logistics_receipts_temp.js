$(function(){
    driver_init();
    $("#btn-receipt-confirm").on("click", async function(){
        const lw = new LW;
        lw.on();
        try {
            const checks = get_checkbox_checked();
            if(checks.length > 0){
                const form = new FormData();
                form.append("checks", JSON.stringify(checks));
                drawFormsInModal(await getDataById(form));
                $("#receipt-confirm-modal").modal("show");
            } else {
                checkbox_error();
            }
        } catch (error) {
            swal("Ошибка", error.message, "error");
        } finally{
            lw.off();
        }
    });
    $("#btn-receipt-cancel").on("click", function(){
        const checks = get_checkbox_checked();
        if(checks.length > 0){
            swal({
                title: "Вы уверены?",
                text: "Вернуть товары в логистику",
                icon: "warning",
                buttons: true,
                dangerMode: true,
              })
              .then(async (willDelete) => {
                if (willDelete) {
                  const lw = new LW;
                  lw.on();
                  const form = new FormData();
                  form.append("checks", JSON.stringify(checks));
                  await(await fetch(`/administration/module/supply/logistics_receipts/receipt_cancel`, {
                      method: "POST",
                      body: form,
                  })).text();
                  await update_all();
                  lw.off();
                }
              });
        } else {
            checkbox_error();
        }
    });
})

async function getDataById(form){
    return await ( await fetch(`/administration/module/supply/logistics_receipts/get_data_by_id`, {
        method: "POST",
        body: form,
    }) ).json();
}

function drawFormsInModal(items){
    const mb = $("#receipt-confirm-modal-body");
    mb.html("");
    const so = [];
    const input_gen = supply_detail_id => {
        return `<input type="hidden" name="supply_detail_id[]" value="${supply_detail_id}">`;
    }
    items.forEach(item => {
        console.log(item);
        const supply_order_detail = item.logistics_detail.supply_order_detail;
        const supply_order = supply_order_detail.supply_order;
        const formName = `receipt-confirm-modal-form-${supply_order.id}`;
        if(so.includes(supply_order.id)){
            $(`#${formName}`).append(input_gen(supply_order_detail.id));
        } else {
            so.push(supply_order.id);
            const form = $(`<form id="${formName}"></form>`);

            form.on("submit", async function(e){
                e.preventDefault();
                const lw = new LW;
                const request = new FormData(this);
                try {
                    lw.on();
                    const data = await (await fetch(`/administration/module/supply/logistics_receipts/receipt_confirm`, {
                        method: "POST",
                        body: request,
                    })).json();
                    console.log(data);
                    $(this).after(`<div><a href="/administration/module/supply/receipts_detail/${data.receipts_id}" target="_blank" class="btn btn-primary btn-xs">Перейти в документ поступления</a></div>`);
                    $(this).remove();
                } catch (error) {
                    swal("Ошибка", error.message, "error");
                } finally{
                    lw.off();
                }
            });

            form.append(`<div class="form-group">
                <label>Номер документа</label>
                <input type="text" class="form-control" name="doc_number" placeholder="Введите номер документа" required>
            </div>`);

            form.append(`<div class="form-group">
                <label>Дата поступления</label>
                <input type="date" class="form-control" name="date_receipts" placeholder="Введите дату поступление" required>
            </div>`);

            form.append(`<div class="form-group">
                <button class="btn btn-primary btn-xs">Создать поступление</button>
            </div>`);

            form.append(`<input type="hidden" name="id" value="${supply_order.id}">`);
            form.append(`<input type="hidden" name="org" value="${supply_order.org}">`);
            form.append(`<input type="hidden" name="stock_id" value="${supply_order.org_.stock_id_default}">`);

            form.append(input_gen(supply_order_detail.id));

            const div = $(`<div></div>`);
            div.append(`<h4>Счёт поставщика №${supply_order.numb}</h4>`);
            div.append(form);
            mb.append(div);
        }
    });
}

$transport.update_info_func = function(){
    driver_init();
}

function driver_init(){
    $(".driver-item").on("click", function(){
        mainId(this.dataset.id);
        update_list_lw();
    });
}

function mainId(id = "get"){
    const mid = $("#mainId");
    if(id === "get"){
        return mid.val();
    } else {
        mid.val(id);
    }
}

async function update_list_data(){
    const id = mainId();
    const end = id === "" ? null : `/${id}`;
    return await ( await fetch(`/administration/module/supply/logistics_receipts/detail_list${end}`) ).text();
}

async function update_info_data(){
    return await ( await fetch(`/administration/module/supply/logistics_receipts/detail_info`) ).text();
}