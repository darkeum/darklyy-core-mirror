function check_minus_count(id = null){
    return new Promise((resolve, reject) => {
        if(id === null){
            id = $("#mainId").val();
        }
        $.post("/ajax.php?do=check_minus_count_realization", {id}, function(data){
            const answer = JSON.parse(data);
            if(answer.status){
                resolve();
            } else {
                reject(answer);
            }
        });
    });
}

function view_errors_minus(data){
    const err = [];
    data.items.forEach(item => {
        err.push(`${item.s_name} арт. ${item.s_sku} (${item.id})`);
    });
    swal('Ошибка!', `Нельзя отгружать в минус! ${err.join('; ')}`, 'error');
}

function conduct_modal(id = null){
    check_minus_count(id).then(() => {
        $('#conductModal').modal('show');
    }, (data) => {
        view_errors_minus(data);
    });
}

function modalPay(id = null){
    check_minus_count(id).then(async () => {
        const data = await get_realization_data();
        $("#modal-pay-cash").html(Price.priceSet(data.total));
        $("#modal-pay-cash").data("value", data.total);
        $("#modal-pay-card").prop("max", data.total);
        $("#modal-pay-total").html(Price.priceSet(data.total));
        $("#modal-pay-total").data("total", data.total);
        $('#modal-pay').modal('show');
    }, (data) => {
        view_errors_minus(data);
    });
}

$("#modal-pay-card").on("input", function(){
    const card = $("#modal-pay-card").val();
    const total = $("#modal-pay-total").data("total");
    const value = total - card;
    $("#modal-pay-cash").html(Price.priceSet(value));
    $("#modal-pay-cash").data("value", value);
});

function modalPayCancel(){
    if($('#modal-pay-cancel').length > 0){
        $('#modal-pay-cancel').modal('show');
    } else {
        alert(`Записи в БД отсутствуют! Обратитесь к администратору`);
    }
}