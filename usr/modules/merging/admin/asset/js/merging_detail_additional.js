$(function(){
    header_func();
    body_func();
});

async function updateHeader(){
    document.getElementById("header_container").innerHTML = await( await fetch(`${document.getElementById("router_show").value}?display=header`) ).text();
    header_func();
}

async function updateBody(){
    document.getElementById("tov_list").innerHTML = await( await fetch(`${document.getElementById("router_show").value}?display=body`) ).text();
    body_func();
}

async function updateButtons(){
    document.getElementById("detail_body_btn").innerHTML = await( await fetch(`${document.getElementById("router_show").value}?display=buttons`) ).text();
    body_func();
}

function remove_item_modal(link){
    swal({
        title: "Удалить товар",
        text: "Вы точно хотите удалить?",
        icon: "warning",
        buttons: {
            confirm: {
                text: "Удалить",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
            cancel: {
                text: "Отмена",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
      })
    .then(async (willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            await remove_item(link);
            await updateBody();
            lw.off();
        }
    });
}

async function remove_item(link){
    return await (await fetch(link)).text();
}

function header_func(){
    editable_date();
}

function body_func(){
    editable_count_detail();
    editable_price_detail();
    total_view()
}

function editable_date(){
    $('#date').editable({
        type: "date",
        viewformat: 'dd.mm.yyyy',
        datepicker: {
            weekStart: 1
        }
    });
}

function editable_count_detail(){
    $('.count').editable({
        name: 'count',
        type: 'text',
        title: 'Введите количество',
        validate: function(value){
            return validate_number(value);
        }
    }).on("save", async function(e, params){
        await params.response
        total_view();
    });
}

function editable_price_detail(){
    $('.price').editable({
        name: "price",
        type: 'text',
        title: "Введите цену",
        validate: function(value){
            return validate_number(value);
        },
        display: function(value) {
            $(this).text(Price.priceSet(value));
        } 
    }).on("save", async function(e, params){
        await params.response
        total_view();
    });
}

function validate_number(value){
    if ($.isNumeric(value) == '') {
        return 'Разрешены только цифры';
    }
}

function get_link(id){
    const result = {};

    const parent = result.tr = $(`#tr_${id}`);
    result.count = parent.find(".count");
    result.price = parent.find(".price");
    result.total = parent.find(".total");

    return result;
}

function get_data(id){
    const result = {};
    const link = get_link(id);
    result.id = id;
    result.cid = link.tr.data("cid");
    result.count = Price.string_to_number(link.count.html());
    result.price = Price.string_to_number(link.price.html());
    result.total = Price.string_to_number(link.total.html());
    return result;
}

function total_view(){
    let total = 0;
    $(".tr_item").each((i, item) => {
        const id = $(item).data("id");
        const link = get_link(id);
        const data = get_data(id);
        const t = data.count * data.price;
        total += t;
        link.total.html(Price.priceSet(t));
    });
    $(".allSum").html(Price.priceSet(total));
}

async function end_quest(active){
    const check = await check_minus_count();
    if(check.status){
        swal({
            title: active == 1 ? "Завершить заявку" : "Отменить завершение заявки",
            text: active == 1 ? "Вы точно хотите завершить заявку?" : "Вы точно хотите отменить заявку?",
            icon: "info",
            buttons: {
                confirm: {
                    text: active == 1 ? "Завершить" : "Выполнить",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                },
                cancel: {
                    text: "Отмена",
                    value: false,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
            },
          })
        .then(async (endBtn) => {
            if (endBtn) {
                const lw = new LW;
                lw.on();
                await end(active);
                await Promise.all([
                    updateHeader(),
                    updateBody(),
                    updateButtons()
                ]);
                lw.off();
            }
        });
    } else {
        swal("Ошибка", `Запрещено докамплектовка в минус (id: ${check.data.id}; номенклатура: ${check.data.name})`, "error");
    }
}

async function check_minus_count(){
    return await ( await fetch( document.getElementById("router_check_minus_count").value ) ).json();
}

async function end(active){
    const form = new FormData();
    form.append("active", active);
    return await( await fetch( document.getElementById("router_additional_end").value, {
        method: "POST",
        body: form,
    } ) ).json();
}