$(function(){
    //console.log("~");
    function updateTable(mainId){
        $.post("/administration/module/fcontrol/storage_detail_list/"+mainId, function(data){
                $("#dev_list").html(data);
        });    
    }
    
    $(document).on("click", ".btnStatus", function(){
        let id = $(this).parent().parent().parent().data("id");
        let status = $(this).data("status");
        let mainId = window.location.href.split("/");
        mainId = mainId[mainId.length - 1];
        //console.log(id);
        json = {};
        json[id] = status;
        json = JSON.stringify(json);
        $.post("/ajax.php?do=saveStatusStorageControl", {json, mainId}, function(){
            updateTable(mainId);
        });
    });
    
    $(document).on("click", "#sub", function(){
        let checks = $(".checks");
        let mainId = window.location.href.split("/");
        mainId = mainId[mainId.length - 1];
        let status = $("#act").val();
        let json = {};
        for(let i=0;i<checks.length;i++){
            if($(checks[i]).prop("checked")){
                let tr = $(checks[i]).parent().parent().parent();
                let id = $(tr).data("id");
                json[id] = status;
            }
        }
        json = JSON.stringify(json);
        $.post("/ajax.php?do=saveStatusStorageControl", {json:json, mainId:mainId}, function(){
            updateTable(mainId);  
        });

    });
});