function checkStock(id = null){
    return new Promise((resolve, reject) => {
        if(id === null){
            id = $("#mainId").val();
        }
        
        $.post("/ajax.php?do=checkStockTransfer", {id:id}, function(data){
            if(data === ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function check_count(id = null){
    return new Promise((resolve, reject) => {
        if(id === null){
            id = $("#mainId").val();
        }
        $.post("/ajax.php?do=check_transfer_count", {id}, function(data){
            if(data === ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function error_minus(data){
    swal('Ошибка!', `Нельзя отгружать в минус! (${data})`, 'error');
}

function check_minus(id = null){
    return new Promise((resolve, reject) => {
        if(id === null){
            id = $("#mainId").val();
        }
        $.post("/ajax.php?do=check_minus_count_transfer", {id}, function(data){
            if(data === ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function error_stock(data = null){
    let error;
    if(data === null){
        error = "Склад отправления равен складу назначения!";
    } else {
        error = data;
    }
    swal("Ошибка!", error, "error");
}

function mergingCreate(request){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/storage/transfer/mergingCreate", request, function(merging_id){
            resolve(merging_id);
        });
    });
}