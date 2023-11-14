async function save(){
    const form = $("#load_cat_form").serialize();
    const lw = new LW;
    lw.on();
    await clear_tf();
    await save_infinity(form).then(data => {
        lw.off();
        success_save(data);
    }, async error => {
        await error_save(error);
        lw.off();
    });
}

async function save2(){
    const form = $("#load_cat_form").serialize();
    const lw = new LW;
    lw.on();
    save_univ(form).then(data => {
        lw.off();
        success_save(data);
    }, async error => {
        await error_save(error);
        lw.off();
    });
}

async function error_save(error){
    await clear_tf();
    swal("Ошибка!", error, "error");
}

function success_save(data){
    // if(data.empty){
    //     swal("Внимание!", "Товары в этой категории отсутствуют", "warning");
    // } else {
    //     location.href = `/administration/module/catalog_v2/loadCatFile?link=${data.link}`;
    // }
    location.href = `/administration/module/catalog_v2/loadCatFile?link=${data.link}`;
}

async function save_infinity(form, limit = 1){
    return new Promise((resolve ,reject) => {
        save_univ(form, limit).then(async data => {
            if(data.end){
                resolve(data);
            } else {
                const new_limit = limit + 1;
                save_infinity(form, new_limit).then(data => resolve(data), error => reject(error));
            }
        }, error => {
            reject(error);
        });
    });
}

function save_univ(form, limit = 0){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/catalog_v2/loadCat", "limit="+limit+"&"+form, function(json){
            try{
                const data = JSON.parse(json);
                resolve(data);
            }catch(e){
                reject(`${e} (${json})`);
            }
        });
    });
}

function toggle_btn(){
    $("#submit").toggle();
    $("#submit_limit").toggle();
}

function clear_temp_file(){
    const lw = new LW;
    lw.on();
    clear_tf().then(() => {
        lw.off();
        swal("Успешно!", "Файлы удалены!", "success");
    });
}

function clear_tf(){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/catalog_v2/clear_temp_file", function(){
            resolve();
        });
    });
}

function getFormData(){
    return new FormData($("#loadFileForm")[0]);
}

function check_file(){
    const lw = new LW;
    lw.on();
    const form = getFormData();
    loadFile(form, '/administration/module/catalog_v2/loadCatCheck').then(data => {
        lw.off();
        $("#countRow").html(`Количество строк: ${data.countRow}`);
        $('#save').prop('disabled', false);
    }, error => {
        swal("Ошибка!", error, "error");
    });
}

function loadFile(form, url){
    return new Promise((resolve, reject) => {
        $.ajax({
            url,
            dataType: 'text',
            cache: false,
            contentType: false,
            processData: false,
            data: form,
            type: 'post',
            success: function(data){
                try{
                    const j_data = JSON.parse(data);
                    resolve(j_data);
                } catch(e){
                    reject(`${e.message} (${data})`);
                }
            }
        });
    });
}

$(document).on("submit", "#loadFileForm", function(e){
    e.preventDefault();
    const form = getFormData();
    const lw = new LW;
    lw.on();
    form.append("number", 2);
    $("#items_list_table").show();
    load_infinity(form).then(() => {
        lw.off();
        change_button();
    }, error => {
        lw.off();
        change_button();
        swal("Ошибка!", error, "error");
    });
});

function change_button(){
    $("#save").hide();
    $("#clear").show();
}

function reset_send_form(){
    $("#loadFileForm")[0].reset();
    $("#items_list_table_body").html("");
    $("#countRow").html("");
    $("#save").show().prop("disabled", true);
    $("#clear").hide();
    $("#items_list_table").hide();
}

function load_infinity(form){
    return new Promise((resolve, reject) => {
        loadFile(form, '/administration/module/catalog_v2/loadCatSave').then(data => {
            draw_items(data.items);
            if(+data.last <= +data.countRow){
                form.set("number", data.last);
                load_infinity(form).then(() => {
                    resolve();
                }, error => {
                    reject(error);
                });
            } else {
                resolve();
            }
        }, error => {
            reject(error);
        });
    });
}

function draw_items(items){
    const list = $("#items_list_table_body");
    items.forEach(value => {
        list.append(`<tr>
            <td>${value.number}</td>
            <td style="background-color: ${value.color}; color: white;">${value.id}</td>
            <td>${value.sku}</td>
            <td>${value.title}</td>
            <td>${value.res}</td>
        </tr>`);
    });
}