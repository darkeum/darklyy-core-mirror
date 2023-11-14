$(function(){
    // $("#file-items").sortable({
    //     axis: "y",
    //     handle: ".file-item"
    // });
    $("#file-items").sortable();

    $(".file-item__delete").on("click", function(){
        $(this).parent().remove();
        const fileItems = $("#file-items");
        if(fileItems.html() == ""){
            fileItems.html(`Файлы не приложены`);
        }
    });
});