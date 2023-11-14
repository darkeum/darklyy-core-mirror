$(function(){
    $(document).on("click", "#submit", function(){
        let data = $("#formMain").serialize();
        let id = $("#mainId").val();
        $.post("/administration/bank/editSave/"+id, data, function(){
            Swal.fire("Успешно!", "Данные успешно сохранены", "success");
        });
    });

    listGen();
});

function get_data(){
    const data_sum = $("#data_sum").html();
    const data_sum_array = $("#data_sum_array").html();
    const sum_array = data_sum_array.length > 0 ? JSON.parse(sum_array) : false;
    const data_module = $("#data_module").html();
    const data_pid = $("#data_pid").html();
    const pid = data_pid.length > 0 ? JSON.parse(data_pid) : false;

    return {
        sum: data_sum,
        sum_array, 
        module: data_module,
        pid,
    };
}

function listGen(){
    // const data = get_data();
    // const div = $("#listGen");
    // if(data.module === "sales"){
    //     if(data.pid){
    //         for(elem of data.pid){
    //             div.append(`<select class="ajaxSelect_getSalesToSelect"><option value="${elem}">${elem}</option></select>`);
    //         }
    //     }
    // }
    // updateSelect2Sales();
}

const pay = {
    bank_pay_detail(){
        return new Promise((resolve, reject) => {
            const mainId = $("#mainId").val();
            $.post("/administration//bank/bank_pay_detail/"+mainId, function(data){
                $("#bank_pay_detail").html(data);
                resolve();
            });
        });
    },
    delete_m(id){
        Swal.fire({
            title: "Вы уверены?",
            text: "Вы точно хотите удалить?",
            icon: "warning",
            showConfirmButton: true,
            showCancelButton: true,
          })
          .then((result) => {
            if (result.isConfirmed) {
                const lw = new LW;
                lw.on();
                this.delete(id).then(() => {
                    this.bank_pay_detail().then(() => {
                        lw.off();
                        Swal.fire("Успешно!");
                    });
                });
            }
          });
    },
    delete(id){
        return new Promise((resolve, reject) => {
            $.post("/administration//bank/pay_delete/"+id, function(){
                resolve();
            });
        });
    },
    form_id: "#pay_modal_form",
    pay_modal_id: "#pay_modal",
    pay_sum_id: "#pay_sum",
    select_sales_id: "#select_sales",
    pay_modal_btn: ".pay_modal_btn",
    pay_edit_id: "#pay_edit_id",
    add_m(){
        $(this.pay_modal_id).modal("show");
        $(this.pay_sum_id).val('');
        $(this.pay_edit_id).val('');
        $(this.select_sales_id).val('').change();
        $(this.pay_modal_btn).hide();
        $("#pay_modal_btn_add").show();
    },
    add_m_end(){
        const lw = new LW;
        lw.on();
        this.add().then(() => {
            this.bank_pay_detail().then(() => {
                lw.off();
                $(this.pay_modal_id).modal("hide");
                Swal.fire("Успешно", "Добавлено", "success");
            });
        });
    },
    prepare_form(form){
        const result = form;
        result.forEach((elem, i) => {
            switch(elem.name){
                case "select_sales":
                    result[ i ].name = "pid";
                break;
            }
        });
        return result;
    },
    add(){
        return new Promise((resolve, reject) => {
            const form = $(this.form_id).serializeArray();
            const p_form = this.prepare_form(form);
            $.post("/administration/bank/pay_add", p_form, function(){
                resolve();
            });
        });
    },
    edit_m(request){
        //id, sum
        $(this.pay_modal_id).modal("show");
        $(this.pay_sum_id).val(request.sum);
        $(this.pay_edit_id).val(request.id);
        $(this.pay_modal_btn).hide();
        const data = request.select;
        const newOption = new Option(data.text, data.id, false, false);
        $(this.select_sales_id).append(newOption).trigger('change');
        $("#pay_modal_btn_edit").show();
    },
    edit_m_end(){
        const lw = new LW;
        lw.on();
        const form = $(this.form_id).serializeArray();
        const p_form = this.prepare_form(form);
        this.edit(p_form).then(() => {
            this.bank_pay_detail().then(() => {
                lw.off();
                $(this.pay_modal_id).modal("hide");
                Swal.fire("Успешно", "Добавлено", "success");
            });
        });
    },
    edit(form){
        return new Promise((resolve, reject) => {
            $.post("/administration/bank/pay_edit/", form, function(){
                resolve();
            });
        });
    },
}