$(function(){
    window.onbeforeunload = function() {
        if(changing_()){
            return "Данные не сохранены. Точно перейти?";
        }
    };
    $(".fill_btn").on("click", async function(){
        const lw = new LW;
        lw.on();
        fill_tab_body(0);
        changing_(true);
        lw.off();
    });
    $(".fill_one_btn").on("click", async function(){
        const lw = new LW;
        lw.on();
        fill_tab_body(1);
        changing_(true);
        lw.off();
    });
    $("#write_btn").on("click", data_save);
    $("#print_btn").on("click", function(){
        const act_id = get_act_id();
        const move = act_id => {
            window.open(`/administration/module/economic/act/view/${act_id}`);
        }
        if(act_id && !changing){
            move(act_id);
        } else {
            // error_no_save();
            swal({
                text: "Для выполнения команды \"Акт сверки\" данные будут записаны. Продолжить?",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Записать и продолжить",
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
              .then(async (ff) => {
                if (ff) {
                    await data_save();
                    move(get_act_id());
                }
              });
        }
    });
    $("#spend_btn").on("click", function(){
        spend(true);
    });
    $("#spread_out_btn").on("click", function(){
        spend(false);
    });
    $("#spend_close_btn").on("click", async function(){
        await spend(true);
        window.close();
    });
    $("#org").on("change", function(){
        change_element("организации", () => { $("#data_org_data").val(""); $("#data_org_director").val(""); });
    });
    $("#oid").on("change", function(){
        change_element("контрагента", () => { $("#data_oid_data").val(""); });
    });
    $("#date_start, #date_end").on("blur", function(){
        change_element("периода сверки");
    });
    $("#agreed").on("change", function(){
        changing_(true);
    })
    const fill_type = get_fill_type();
    if(get_act_id()){
        const records = JSON.parse(field_data_records());
        switch(fill_type){
            case 0:
                tab_1_2({records});
            break;
            case 1:
                tab_1({records});
            break;
        }
    }
    changing_(false);
});

function changing_(changing = null){
    if(changing === null) return window.changing;
    window.changing = changing;
}

function field_data_records(value = null){
    const data_records = $("#data_records");
    if(value === null){
        return data_records.val();
    }
    return data_records.val(value);
}

function error_no_save(){
    swal("Ошибка", "Заявка не сохранена!", "error");
}

function get_act_id(){
    const act_id = $("#act_id").val();
    return act_id == "" ? false : act_id;
}

function get_fill_type(){
    const fill_type = $("#fill_type").val();
    return fill_type === "" ? false : +fill_type;
}

function get_active(){
    return $("#active").is(":checked");
}

function get_main_form(){
    return $("#mainForm").serialize();
}

async function fill_tab_body(fill_type){
    let json;
    try{
        const mainForm = get_main_form();
        json = await $.post("/administration/module/economic/act/editor_tab_body", mainForm);
        const data = JSON.parse(json);
        if(data?.error == null || data?.error == undefined){
            clear_board();
            init_tab_body(data);
            $("#fill_type").val(fill_type);
            switch(fill_type){
                default:
                    throw new Error("Неизвестный тип заполнения");
                break;
                case 0:
                    tab_1_2(data);
                    init_balance("org", data.initial_balance, data.final_balance);
                    init_balance("oid", -data.initial_balance, -data.final_balance);
                break;
                case 1:
                    tab_1(data);
                    init_balance("org", data.initial_balance, data.final_balance);
                break;
            }
        } else {
            swal("Внимание", data.error, "warning");
        }
    } catch(error){
        swal("Ошибка!", json, "error");
    }
}

function clear_balance(){
    $("#data_initial_balance_contract").val("");
    init_balance("org", 0, 0);
    init_balance("oid", 0, 0);
}

function init_tab_body(data){
    $(`#org_director_full`).val(data.org_director_full);
    $(`#oid_director_full`).val(data.oid_director_full);
    $(`#data_initial_balance`).val(data.initial_balance);
    $(`#data_final_balance`).val(data.final_balance);
    $(`#data_org_data`).val(JSON.stringify(data.org_data));
    $(`#data_org_director`).val(JSON.stringify(data.org_director));
    $(`#data_oid_data`).val(JSON.stringify(data.oid_data));
    $(`#data_initial_balance_contract`).val(JSON.stringify(data.initial_balance_contract));
}

function get_tab_tbody(){
    return [$("#tab_1_tbody"), $("#tab_2_tbody")];
}

function clear_tab_tbody(){
    const table = get_tab_tbody();
    table.forEach(value => {
        value.html("");
    });
    field_data_records("");
}

function init_tab_1_2(data){
    field_data_records(JSON.stringify(data.records));
}

function tab_1_2(data){
    init_tab_1_2(data);
    const table = get_tab_tbody();
    data.records.forEach((v, i) => {
        append_table({i, data: v, o: "org_data", table: table[0], debit_view: v.debit_view, credit_view: v.credit_view});
        append_table({i, data: v, o: "oid_data", table: table[1], debit_view: v.credit_view, credit_view: v.debit_view});
    }); 
}

function tab_1(data){
    init_tab_1_2(data);
    const table = get_tab_tbody();
    data.records.forEach((v, i) => {
        append_table({i, data: v, o: "org_data", table: table[0], debit_view: v.debit_view, credit_view: v.credit_view});
    }); 
}

function append_table(v){
    const tr = $(`<tr></tr>`);
    const td = [];
    td.push($(`<td>${v.i + 1}</td>`));
    if(get_break_contracts()){
        const contract = $(`<td>${v.data.contract_name}</td>`);
        contract.on("dblclick", () => { window.open(`/administration/module/reestr/view/${v.data.contract_id}`) });
        contract.addClass("cp");
        td.push(contract);
    }
    td.push($(`<td>${v.data.date}</td>`));
    const document_full = $(`<td >${v.data[ v.o ].document_full}</td>`);
    if(typeof(v.data.link) != "undefined" && v.data.link !== null){
        document_full.on("dblclick", () => { window.open(v.data.link); });
        document_full.addClass("cp");
    }
    td.push(document_full);
    td.push($(`<td>${v.data[ v.o ].document}</td>`));
    td.push($(`<td>${v.debit_view ?? ""}</td>`));
    td.push($(`<td>${v.credit_view ?? ""}</td>`));
    td.forEach(t => {
        tr.append(t);
    });
    v.table.append(tr);
}

function init_balance(name, initial, final){
    $(`#${name}_initial_balance`).html(number_format(initial));
    $(`#${name}_final_balance`).html(number_format(final));
    if(name == "org"){
        $(`#data_initial_balance`).val(initial);
        $(`#data_final_balance`).val(final);
    }
}

async function data_save(){
    const mainForm = get_main_form();
    await save_lw(mainForm);
}

async function save_lw(request){
    const lw = new LW;
    lw.on();
    await save(request);
    lw.off();
}

async function save(request){
    let json;
    try{
        // if(!request?.fill_type){
        //     swal("Внимание!", `Заполните акт сверки!`, "warning");
        //     return;
        // }
        const act_id = get_act_id();
        json = await (act_id ? data_update(act_id, request) : data_store(request)).then(j => j);
        const data = JSON.parse(json);
        if(!act_id){
            $("#act_id").val(data.id);
            $("#numb").val(data.numb);
            $("#title").html(data.title);
            history.pushState(null, null, `/administration/module/economic/act/editor/${data.id}`);
        }
        changing_(false);
    } catch(e){
        swal("Ошибка!", `Данные не сохранены (${e.message}, ${json})`, "error");
    }
}

async function data_store(request){
    return await $.post("/administration/module/economic/act/store", request);
}

async function data_update(id, request){
    return await $.post("/administration/module/economic/act/update/"+id, request);
}

function number_format(price){
    return Price.priceSet(price, "").replace(".", ",");
}

async function spend(active){
    $("#active").prop("checked", active);
    // await save_lw({active: +active});
    await data_save();
}

function get_link_break_contracts(){
    return $("#break_contracts");
}

function get_break_contracts(){
    return get_link_break_contracts().prop("checked");
}

function break_contracts_change(){
    const checkbox = get_link_break_contracts();
    const cp = checkbox.prop("checked");
    const table = get_tab_tbody();
    let flag = false;
    table.forEach(item => {
        if(item.html().length > 0) flag = true;
    });
    if(flag){
        change_element("флага \"Разбить по договорам\"", () => { break_contracts_change_view(cp); }, () => { checkbox.prop("checked", !cp); });
    } else {
        break_contracts_change_view(cp);
    }
}

function break_contracts_change_view(action){
    const thead_contracts = $(".thead_contracts");
    if(action){
        thead_contracts.show();
    } else {
        thead_contracts.hide();
    }
}

function clear_board(){
    clear_tab_tbody();
    clear_balance();
    changing_(true);
}

function change_element(name, call1 = () => {}, call2 = () => {}){
    const table = get_tab_tbody()
    if(table[0].html().length > 0){
        swal({
            text: `При изменении ${name} табличные части будут очищены. Продолжить?`,
            icon: "info",
            buttons: {
                confirm: {
                    text: "Да",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: true
                },
                cancel: {
                    text: "Нет",
                    value: false,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
            },
          })
          .then((ff) => {
            if (ff) {
                clear_board();
                call1();
            } else {
                call2();
            }
          });
    }
}