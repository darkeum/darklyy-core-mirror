$(function(){
    function change_bank_list(selector, selector_list){
        $(document).on("change", selector, function(){
            const oid = $(this).val();
            torg.get_bank(oid).then(option => {
                $(selector_list).html(option);
            });
        });
    }

    change_bank_list("#consignee", "#consignee_bank");
    change_bank_list("#shipper", "#shipper_bank");

    $(document).on("change", "#ship", function(){
        const id = $(this).val();
        const realization_id = $("#mainId").val();
        $.post("/ajax.php?do=getPostOrgTorg", {id, realization_id}, function(data){
            $("#ship_post").val(data);
        });
    });

    $(document).on("change", "#base_date", function(){
        torg.get_procuratory_option();
    });

    torg.get_procuratory_option();
});

const torg = {
    name: "torg",
    procuratory_data: [],
    open(){
        $(`#${this.name}Modal`).modal("show");
    },
    save(){
        const form = $(`#${this.name}Form`).serialize();
        $.post("/ajax.php?do=torgSave", form, error => {
            if(error != ""){
                swal("Ошибка!", "Данные не сохранены! Обратитесь к администратору "+error, "error");
            }
            this.get_procuratory_option();
        });
    },
    get_bank(oid){
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=torgGetBank", {oid}, function(option){
                resolve(option);
            });
        });
    },
    get_procuratory(request){
        //realization_id, date
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=torgGetProcuratory", request, function(option){
                resolve(JSON.parse(option));
            });
        });
    },
    get_procuratory_option(){
        const request = {};
        request.date = $("#base_date").val();
        request.realization_id = $("#mainId").val();

        this.get_procuratory(request).then(option => {
            this.procuratory_data = option;

            const edit_option = option.map((value, index, array) => {
                return {value: value.id, view: `№${value.number} для ${value.issued_to} действует от ${value.date_from_view} до ${value.date_to_view}`, data: value};
            });

            const new_option = [{value: "", view: "Без доверенности", data: {selected: false}}, {value: "-1", view: "Новая доверенность", data: {selected: false}}].concat(edit_option);

            let selected_id = null;
            const result = new_option.map((value, index, array) => {
                const selected = value.data.selected ? "selected" : "";
                if(value.data.selected){
                    selected_id = value.data.id;
                }
                return `<option value="${value.value}" ${selected}>${value.view}</option>`;
            });
            this.update_input_procuratory(selected_id);

            $("#procuratory_id").html(result.join(''));
        });
    },
    update_input_procuratory(selected_id){
        const procuratory = ["number", "date_from", "date_to", "issued_by", "issued_to"];

        procuratory.forEach(selector => {
            $(`#${selector}`).val("");
        });

        this.procuratory_data.forEach(item => {
            if(selected_id == item.id){
                procuratory.forEach(selector => {
                    $(`#${selector}`).val(item[ selector ]);
                });
            }
        });
    },
    check_torg(){
        return new Promise((resolve, reject) => {
            const realization_id = $("#mainId").val();
            $.post("/ajax.php?do=check_torg", {realization_id}, function(answer){
                answer ? resolve() : reject();
            });
        });
    }
}
