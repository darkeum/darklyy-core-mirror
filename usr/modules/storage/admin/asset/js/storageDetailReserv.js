$(function(){
    //console.log("~");
    $(document).on("keydown", ".rubber", function(event){
        $(this).width(($(this).val().length + 4)*8);
        if(event.which == 13)
        {
            $(this).blur();
        }
    });
    
    //кнопка выполнить
    $(document).on("click", "#sub", function(){

    });

    $(document).on("click", ".reserv", function(){
        let id = $(this).data("id");
        $(".reservBtn").data("id", id);
        $("#reservModal").modal("show");
    });

    $(document).on("click", ".reserv1", function(){
        let id = $(this).data("id");
        $(".reservBtn").data("id", id);
        $("#reservModal1").modal("show");
    });

    $(document).on("click", ".reservBtn", function(){
        let id = $(this).data("id");
        let mainId = window.location.href.split("/");
        mainId = mainId[mainId.length - 1];
        $.post("/ajax.php?do=setReservStatusStorage", {id:id, mainId:mainId}, function(){
            $.post("/administration/module/storage/reserv/detail_list/"+mainId, function(data){
                $("#dev_list").html(data);
            });
        });
    });

    $(document).on("click", "#sub", function(){
        let checks = $(".checks:checked");
        let id = [];
        for(let i=0;i<checks.length;i++){
            id[i] = $(checks[i]).val();
        }
        let mainId = window.location.href.split("/");
        mainId = mainId[mainId.length - 1];
        $.post("/ajax.php?do=setReservStatusStorage", {id:id, mainId:mainId}, function(){
            $.post("/administration/module/storage/reserv/detail_list/"+mainId, function(data){
                $("#dev_list").html(data);
            });
        });
    });

    
});