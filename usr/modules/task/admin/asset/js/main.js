function archiveModalFunc(id){
    $("#addArchiveBtn").data("id", id);
    $('#archiveModal').modal("show");
}
$(document).on("click", "#addArchiveBtn", function(){
    let id = $(this).data("id");
    $.post("/ajax.php?do=setArchiveTask", {id:id}, function(data){
        $('#jqTask').trigger('reloadGrid');
        notif('success', 'Успешно', 'Задача была перемещена в архив');
    });
});