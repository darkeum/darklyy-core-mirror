function getMainId() {
    return document.getElementById("mainId").value;
}

window.onload = function () {

    const lw = new UiPreloader;
    const cr = new UiCreator;

    const mainId = getMainId();

    /* Main */

    document.getElementById("main-form").addEventListener("submit", async function (e) {
        e.preventDefault();
        try {
            lw.on();
            const data = new FormData(this);
            data.append("_method", "PUT");
            const result = await (await fetch(`/administration/module/bank/${mainId}`, {
                method: "POST",
                body: data,
            })).json();
            Swal.fire("Успешно", "Данные сохранены", "success");
        } catch (error) {
            Swal.fire("Ошибка", error.message, "error");
        } finally {
            lw.off();
        }
    });

    /* Detail */

    function get_link_detail(id) {
        const result = {};
        result.id_ = id;
        result.tr = document.querySelector(`#tr_${id}`);
        result.id = result.tr.querySelector(".item-id");
        result.document = result.tr.querySelector(".item-document");
        result.sum = result.tr.querySelector(".item-sum");
        result.buttons = result.tr.querySelector(".item-buttons");
        result.buttonsGroup = result.buttons.querySelector(".item-buttons-group");
        return result;
    }

    function get_data_detail(id) {
        const link = get_link_detail(id);
        const result = {};
        result.id_ = id;
        result.id = link.id.innerHTML;
        result.document = link.document.innerHTML;
        result.sum = Price.string_to_number(link.sum.innerHTML);
        return result;
    }

    async function load_detail() {
        document.getElementById("tab-details").innerHTML = await (await fetch(`/administration/module/bank/edit_details/${mainId}`)).text();
        load_detail_init();
    }

    async function load_detail_lw() {
        lw.on();
        await load_detail();
        lw.off();
    }

    let bai = false;
    let bci = false; // Clear
    const bei = {};  // Edit
    const bdi = {};  // Delete

    function load_detail_init() {
        buttonAppendInit();
        buttonClearInit();
        buttonEditInit();
        buttonDeleteInit();
    }

    load_detail_init();

    function buttonAppendInit() {
        const buttonAppend = document.querySelector("#button-append");
        if (buttonAppend) {
            if (bai) buttonAppend.removeEventListener("click", bai);
            bai = function () {
                const detailTbody = document.getElementById("detail-tbody");
                const tr_empty = document.getElementById("tr-empty");
                if (tr_empty) {
                    tr_empty.remove();
                }

                // Создаем теги
                const tr = cr.tag('tr');
                const td = cr.tag('td', 4);
                const select = createSelect({
                    module: detailTbody.dataset.module,
                });

                select.classList.add('form-select');
                td[1].append(select);

                const input = cr.input(1, 'form-control', 'number');
                td[2].append(input);

                const buttons = [
                    cr.button(1, 'btn btn-success', 'Добавить'),
                    cr.button(1, 'btn btn-danger', 'Отмена'),
                ];
                buttons[0].addEventListener('click', async function () {
                    try {
                        const form = new FormData();
                        form.append("bank_detail_id", mainId);
                        form.append("pid", select.value);
                        form.append("sum", input.value);
                        await bankPayCreate(form);
                        await load_detail_lw();
                    } catch (error) {
                        Swal.fire("Ошибка добавления", error.message, "error");
                    }
                });
                buttons[1].addEventListener('click', function () {
                    tr.remove();
                });

                const div = cr.tag('div', 1, 'btn-group');
                div.append(...buttons);
                td[3].append(div);

                tr.append(...td);
                detailTbody.prepend(tr);
                select_init();
            }
            buttonAppend.addEventListener('click', bai);
        }
    }

    function buttonClearInit() {
        const buttonClear = document.querySelector("#button-clear");
        if (buttonClear) {
            if (bci) buttonClear.removeEventListener("click", bci);
            bci = function () {
                const detailTbody = document.getElementById("detail-tbody");
                detailTbody.replaceChildren();
                const tr = document.createElement("tr");
                const td = document.createElement("td");
                const div = document.createElement("div");
                tr.setAttribute('id', 'tr-empty');
                td.classList.add('p-0');
                td.setAttribute('colspan', '4');
                div.classList.add('text-center', 'w-100', 'py-50');
                div.innerHTML = 'Пусто';

                td.append(div);
                tr.append(td);

                detailTbody.prepend(tr);
            }
            buttonClear.addEventListener('click', bci);
        }
    }

    function buttonEditInit() {
        document.querySelectorAll(".button-edit").forEach(item => {
            const id = item.dataset.id;
            if (bei[id]) item.removeEventListener("click", bei[id]);
            bei[id] = function () {

                const link = get_link_detail(id);
                const data = get_data_detail(id);
                const tr_save = link.tr.cloneNode(true);

                //document
                const select = createSelect({
                    module: link.document.dataset.module,
                    options: [
                        {value: link.document.dataset.pid, text: data.document}
                    ]
                });
                link.document.innerHTML = ``;
                link.document.append(select);
                select_init();

                //sum
                const input = cr.input(1, 'form-control', 'number', data.sum);

                link.sum.innerHTML = ``;
                link.sum.append(input);

                //buttons
                link.buttonsGroup.innerHTML = ``;
                const buttonSave = cr.button(1,'btn btn-success', 'Сохранить');
                buttonSave.addEventListener("click", async function () {
                    try {
                        const form = new FormData();
                        form.append("pid", select.value);
                        form.append("sum", input.value);

                        await bankPayUpdate(id, form);
                        await load_detail_lw();
                    } catch (error) {
                        Swal.fire("Ошибка сохранения", error.message, "error");
                    }
                });

                const buttonCancel = cr.button(1,'btn btn-danger', 'Отмена');
                buttonCancel.addEventListener("click", function () {
                    link.tr.replaceWith(tr_save);
                    load_detail_init();
                });

                link.buttonsGroup.append(buttonSave, buttonCancel);
            }
            item.addEventListener('click', bei[id]);
        });
    }

    function buttonDeleteInit() {
        document.querySelectorAll(".button-delete").forEach(item => {
            const id = item.dataset.id;
            if (bdi[id]) item.removeEventListener("click", bdi[id]);
            bdi[id] = function () {
                Swal.fire({
                    title: "Вы уверены?",
                    text: "Удалить",
                    icon: "warning",
                    showConfirmButton: true,
                    showCancelButton: true,
                })
                    .then(async (result) => {
                        if (result.isConfirmed) {
                            await bankPayDelete(id);
                            await load_detail_lw();
                        }
                    });
            }
            item.addEventListener('click', bdi[id]);
        })
    }

    function select_init() {
        select_sales_init();
        select_supply_order_init();
    }

    // Создание ...
    async function bankPayCreate(form) {
        return await (await fetch(`/administration/module/bank/pay`, {
            method: "POST",
            body: form,
        })).json();
    }

    // Изменение
    async function bankPayUpdate(id, form) {
        form.append("_method", "PUT");
        return await (await fetch(`/administration/module/bank/pay/${id}`, {
            method: "POST",
            body: form,
        })).json();
    }

    // Удаление ...
    async function bankPayDelete(id) {
        const form = new FormData();
        form.append("_method", "DELETE");
        return await (await fetch(`/administration/module/bank/pay/${id}`, {
            method: "POST",
            body: form,
        })).json();
    }

    function createSelect(request) {
        if (request?.module === undefined) throw new Error(`Field "module" not found!`);
        const module = request.module;

        const opt = request?.options ? request.options : [];

        const select = document.createElement("select");
        select.classList.add(`${module}_select`);
        const options = [];
        opt.forEach(item => {
            const option = document.createElement("option");
            option.value = item.value;
            option.innerHTML = item.text;
            options.push(option);
        });
        select.append(...options);

        return select;
    }

}

const pay = {
    async pact_load() {
        const id = getMainId();
        document.getElementById("tab-reestr").innerHTML = await (await fetch(`/administration/module/bank/edit_reestr/${id}`)).text();
    }
}