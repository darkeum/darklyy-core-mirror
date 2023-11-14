window.onbeforeunload = function (evt) {
    const message = "Вы уверены что хотите выполнить это действие?";
    if(cg_changed()){
        return message;
    }
}

$("#objectSelect").on("change", function(){
    cg_changed(true);
});
$("#orgSelect").on("change", function(){
    cg_changed(true);
});
$(".project_data").on("change", function(){
    cg_changed(true);
});

function saveSpec(id){
    const form = $("#mainForm").serialize();
    $("#save_btn").prop("disabled", true);
    grid_load_toggle("on");
    cg_changed(false);
    $.post("/profile/specification/save/"+id, form, function(error){
        if(error == ""){
            location.href = "/profile/specification/view/"+id;
        } else {
            Swal.fire(
                'Ошибка',
                'Данные не сохранены! Проверьте введеные данные',
                'error'
            );
            grid_load_toggle("off");
            console.log(error);
        }
    });
}