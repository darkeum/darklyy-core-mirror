$(function(){
    $(".select2").select2({
        placeholder: "Нажмите чтобы выбрать элемент",
    });
});

function check_numb(numb){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/engineering/register/check_numb", {numb}, function(data){
            if(data === ""){
                resolve();
            } else {
                reject();
            }
        });
    });
}

function save(This){
    event.preventDefault();
    let form = $(This).serialize();
    let form_array = $(This).serializeArray(); 
    let numb = form_array.forEach(element => {
        if(element.name === "numb"){
            return element.name;
        }
    });
    check_numb(numb).then(() => {
        $.post("/administration/module/engineering/register/save", form, function(id){
            if(id !== ""){
                window.location.href = `/administration/module/engineering/register/edit/${id}`;
            } else {
                notif('success', 'Успешно', 'Данные сохранены');
            }
        });
    }, 
    () => {
        notif('error', 'Ошибка', 'Данные шифр уже был');
    });
}

function file_modal_open(){
    $("#file_modal").modal("show");
}

function load_file_register(){
    let id = $("#mainId").val();
    $.post("/administration/module/engineering/register/file/"+id, function(data){
        $("#div_files").html(data);
    })
}

function save_file(){
    let form = $("#file_modal_form").serialize();
    let id = $("#mainId").val();
    $("#debug1").html("");
    $("#files1").html(`<li class="text-muted text-center empty">Файлы не загружены.</li>`);
    $.post("/administration/module/engineering/register/file_save", "id="+id+"&"+form, function(){
        load_file_register();
    });
}

function del_file_register_modal(id){
    $("#del_file_register_modal_btn").data("id", id);
    $("#del_file_register_modal").modal("show");
}

function del_file_register(id){
    $.post("/administration/module/engineering/register/file_del/"+id, function(){
        load_file_register();
    });
}