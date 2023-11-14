$(document).on("click", "#send", function(){
    let data = $("#fomrMain").serialize();
    let id = $("#request_free_id").val();
    $.post("/ajax.php?do=saveFileCommRequestFree", data, function(){
        $.post("/administration/module/selection/fileCommView/"+id, function(data){
            $("#loadForm").html(data);
        });
    });
});