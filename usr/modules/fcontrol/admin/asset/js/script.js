$(function(){
    //console.log("~");
    $(document).on("click", "#payBtn", function(){
        $("#payModal").modal("show");
    });
    $("#approveBtn").on("click", function(){
        $("#approve").modal("show");
    });
    $(document).on("click", "#payBtnAdd", function(){
        $("#payAddModal").modal("show");
    });
    $("#approveConfirm").on("click", async function(){
        const id = $(this).data("id");
        const lw = new LW;
        lw.on();
        $("#approve").modal("hide");
        $(".hideBtn").hide();
        await sendApprove(id, 3, "");
        lw.off();
        notif("success", "Успешно", "");
    });
    
    $("#noApproveBtn").on("click", function(){
        $("#noApprove").modal("show");
    });
    
    $("#noApproveConfirm").on("click", async function(){
        const id = $(this).data("id");
        const comment = $("#commentText").val();
        const lw = new LW;
        lw.on();
        $("#noApprove").modal("hide");
        $(".hideBtn").hide();
        await sendApprove(id, 2, comment);
        lw.off();
        notif("success", "Успешно", "");
    });

    async function sendApprove(id, status, comment){
        await $.post("/ajax.php?do=funcApprove", {id, status, comment});
    }

    $(document).on("click", "#payBtnCancel", function(){
        swal({
            title: "Вы уверены?",
            text: "Отменить оплату",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then(async (willDelete) => {
            if (willDelete) {
                const lw = new LW;
                lw.on();
                const id = $("#mainId").val();
                await updateControl(id, "pay", 0);
                await update_head();
                lw.off();
            }
          });
    });

    
    func_update_head();
});

function payCancleModal(){
    $("#payCancleModal").modal("show");
}

function x_plan_pay_date(){
    $('#plan_pay_date').editable({
        viewformat: 'dd.mm.yyyy'
    });
}

function x_pay_date(){
    $('#pay_date').editable({
        viewformat: 'dd.mm.yyyy'
    });
}

function func_update_head(){
    x_plan_pay_date();
    x_pay_date();
}

function update_head(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        const pay_detail = $('#pay_detail').val();
        $.post("/administration/module/fcontrol/ajax/order_detail_head/"+id, {pay_detail}, function(data){
            $("#order_head").html(data);
            func_update_head();
            resolve();
        });
    });
}

function update_body(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/fcontrol/ajax/order_detail_list/"+id, function(data){
            $("#tov_list").html(data);
            resolve();
        });
    });
}

function addDateModal(){
    $("#dateModal").modal("show");
}

function saveDateModal(){
    const lw = new LW;
    lw.on();
    const form = $("#dateModalForm").serialize();
    saveDate(form).then(() => {
        update_head().then(() => {
            lw.off();
        });
    }, error => {
        lw.off();
        swal("Ошибка", error, "error");
    });
}

function saveDate(form){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/fcontrol/save_date_payment", form, function(error){
            if(error == ""){
                resolve();
            } else {
                reject(error);
            }
        });
    });
}

function delDateModal(id){
    swal({
        title: "Вы уверены?",
        text: "Удалить дату",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            delDate(id).then(() => {
                update_head().then(() => {
                    lw.off();
                });
            });
        }
      });
}

function delDate(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/fcontrol/del_date_payment", {id}, function(){
            resolve();
        });
    });
}

async function updateControl(id, field, value){
    const form = new FormData;
    form.append("pk", id);
    form.append("name", field);
    form.append("value", value);
    await (await fetch(`/ajax.php?do=setPlanPayDateControl`, {
        method: "POST",
        body: form,
    })).text();
}