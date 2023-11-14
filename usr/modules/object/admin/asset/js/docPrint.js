//console.log("~");

$(document).on("click", "#search", function(){
    //console.log("~");
    getDocShipStorage();
});

$(document).on("keyup", "#docNum", function(e){
    //console.log(e.keyCode);
    if(e.keyCode == 13){
        getDocShipStorage();
    }
});

function getDocShipStorage(){
    let docNum = $("#docNum").val();
    if(docNum.length > 0){
        $.post("/ajax.php?do=getDocShipStorage", {docNum: docNum}, function(data){
            $("#docPrint").html(data);
        });
    }
}