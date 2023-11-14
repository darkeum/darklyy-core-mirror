function request_god(request){
    //module, pid
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=request_god", request, function(){
            resolve();
        });
    });
}