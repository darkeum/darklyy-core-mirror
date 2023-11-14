$(function(){
    $(document).on("click", ".removeAttach", function(){
        $("#delFileSupply").modal("show");
        $("#nameFile").html($(this).data("name"));
        $("#delFileBtn").data("id", $(this).data("id"));
    });
    
    $(document).on("click", "#delFileBtn", function(){
        let id = $(this).data("id");
        $.post("/ajax.php?do=removeFileSupply",{id:id}, function(data){
            $("#li_"+id).remove();
        });
    });
});