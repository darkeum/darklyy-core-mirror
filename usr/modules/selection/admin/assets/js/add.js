$(document).on("click", "#sub", function(){
    let code = $("#code").val();
    if(code != ""){
        $.post("/ajax.php?do=checkCodeSelection", {code:code}, function(data){
            if(data == '1'){
                $("#warningCode").show();
            } 
            if(data == '2') {
                $("#admin-form").submit();
            }
        });
    }
});