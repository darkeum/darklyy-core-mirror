$(function(){ 
    updateHeadFunc();

    $logisticsDelivery.after_cancel_delivery = async function(){
        await updateHead();
    };

    const uid_gave = new UidGoodsModal("uid_gave");
    const uid_accepted = new UidGoodsModal("uid_accepted");
    uid_gave.init();
    uid_accepted.init();
    
    $(document).on("click", ".transferBtn", function(){
        transfer_toggle_active_lw();
    });
    
    $(document).on("dblclick", ".changeGoods", function(){
        const id = $(this).data("id");
        change_goods_open(id);
    });     
    
    $(document).on("click", "#saveSupply", function(){
        const saveId = $("#saveId").val();
        const saveCount = $("#saveCount").val();
        const savePrice = $("#savePrice").val();
        $.ajax({
            type: "POST",
            url: "/ajax.php?do=storageUpdateProducts", 
            data: {saveId, saveCount, savePrice},
            success: function(data){
                notif('success', 'Информация', 'Данные успешно сохранены!');
            }
        });
    });
    
    $(document).on("keydown", ".rubber", function(event){
        $(this).width(($(this).val().length + 4)*8);
        if(event.which == 13)
        {
            $(this).blur();
        }
    });

    function gen_click_number_edit(main_class, field_save, view_value = (value) => value){
        const class_input = `${main_class}Inp`;
        $(document).on("click", `.${main_class}`, function(){
            const id = $(this).parent().data("id");
            $(this).addClass(class_input);
            $(this).removeClass("count");
            const inp_value = Price.string_to_number($(this).text());
            $(this).html('<input type="number" id="'+class_input+'" class="form-control rubber" value="'+inp_value+'" style="width: ' +(($(this).text().replace(/ /g, "").length + 6) * 8)+ 'px">');
            const input = $(`#${class_input}`);
            input.focus();
            input.select();
            input.on("blur", function(){
                const value = $(this).val();
                $(`.${class_input}`).html(view_value(value));
                $(`.${class_input}`).addClass(main_class);
                $(`.${main_class}`).removeClass(class_input);
                let json = $(`#${field_save}`).val();
                if(json.length > 0){
                    json = JSON.parse(json);
                } else {
                    json = {};
                }
                json[ id ] = value;
                json = JSON.stringify(json);
                $(`#${field_save}`).val(json);
                getAllTotal();
            });
        });
    }

    gen_click_number_edit("count", "saveCount");
    gen_click_number_edit("price", "savePrice", function(value){
        return Price.priceSet(value);
    });
    
    //удаление
    $(document).on("click", ".removeDetail", function(){
        let id = $(this).data("id");
        $("#removeBtn").data("id", id);
        $("#removeModal").modal("show");
    });
    
    $(document).on("click", "#removeBtn", function(){
        let id = $(this).data("id");
        delItem(id).then(() => {
            updateBody();
        });
    });
});

function get_links(id){
    const data = {};
    const main = $(`#transfer_item_${id}`);
    data.count = main.find(".count");
    data.price = main.find(".price");
    return data;
}

function get_data(id){
    const data = {};
    const links = get_links(id);
    data.count = Price.string_to_number( links.count.text() );
    data.price = Price.string_to_number( links.price.text() );
    return data;
}

function replace_transfer_item(id, cid){
    const child = $("#cg_"+id).parent().children();
    const num = $(child[0]).html();

    $.post("/administration/module/storage/transfer/detail_once", {id, cid, num}, function(data){
        $("#cg_"+id).parent().after(data);
        $("#cg_"+id).parent().remove();
        
        let json = $("#saveId").val();
        if(json.length > 0){
            json = JSON.parse(json);
        } else {
            json = {};
        }
        json[ id ] = cid;
        json = JSON.stringify(json);
        $("#saveId").val(json);
    });
}

function transfer_toggle_active(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/ajax.php?do=transferSendStorage", {id}, function(data){
            resolve(data);
        });
    });
}

async function transfer_toggle_active_lw(){
    const lw = new LW;
    lw.on();
    await transfer_toggle_active().then(data => {
        switch(data){
            case '1':
                notif("success", "Информация", "Перемещение успешно проведено");
                $("#saveSupply").prop("disabled", true);
                $(".AddProduct").hide();

                $("#active").val("1");
                copy_disabled(true);
            break;
            case '2':
                notif("system", "Информация", "Перемещение успешно отменено");
                $("#saveSupply").prop("disabled", false);
                $(".AddProduct").show();

                $("#active").val("0");
                copy_disabled(false);
            break;
            case '3':
                notif("danger", "Ошибка", "Что-то пошло не так...");
            break;
            case '4':
                notif("danger", "Ошибка", "Ошибка");
            break;
        }
    });
    await Promise.all([updateHead(), updateBody()]);
    lw.off();
}

function x_stock(elem){
    check_active_swal(elem);
    const pk = $("#mainId").val();
    $.post("/ajax.php?do=getListStorageTransfer", {id:pk}, function(source){
        elem.editable({
            type: "select",  
            source,
            pk,
            url: "/ajax.php?do=changeTransferDetail",
            emptytext: "Не указано",
        });
        elem.on("save", function(){
            updateBody();
        })
    });
}

$('#title').editable({
    type: 'text',
    pk: $("#mainId").val(),
    url: '/ajax.php?do=changeTransferDetail',
    title: 'Введите наименование',
    emptytext: 'Отсутствует',
});

function delItem(id){
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=storageTransferRemove", {id}, function(){
            resolve();
        });
    });
}

function getAllTotal(){
    let totalAll = 0;
    $(".transfer_item").each(function(i, elem){
        const data = get_data($(this).data("id"));
        totalAll += data.price * data.count;
    });
    
    $(".totalAll").html(priceSet(totalAll));
}

function priceSet(data){
    return Price.priceSet(data);
};

function updateBody(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        if(id != undefined && id != null){
            $.post("/administration/module/storage/ajax/transfer_detail_list/"+id, function(data){
                $("#tov_list").html(data);
                resolve();
            });
        } 
    });
}

function endTransfer(){
    checkElectronicSign().then(() => {
        checkStock().then(() => {
            check_minus().then(() => {
                check_count().then(() => {
                    transferModal();
                }, data => {
                    swal('Ошибка!', `Количество превышает допустимое! (id: ${data})`, 'error');
                });
            }, data => {
                error_minus(data);
            });
        }, data => {
            error_stock(data);
        });
    }, data => {
        swal('Ошибка!', data.error, 'error');
    });
}

function transferModal(){
    $(".transferModalStep").hide();
    $("#transferModalStep_1").show();
    $('#transferModal').modal('show');
}

function transferModalElectronicSign(){
    $("#transferModalStep_1").hide();
    $("#transferModalStep_2").show();
    let timer;

    const transferModalStep_2Form = $("#transferModalStep_2Form");
    transferModalStep_2Form.off().on("submit", function(e){
        e.preventDefault();
        const form = $(this).serialize();
        confirmElectronicSign(form).then(async () => {
            clearTimeout(timer);
            $("#transferModal").modal("hide");
            // alert("Проведение!");
            await transfer_doc_create();
            transfer_toggle_active_lw();
        }, data => {
            const error = [];
            if(data.uid_gave_id == data.uid_accepted_id){
                if(!data.uid_gave) error.push(`Неправильный код у ${data.uid_gave_name}`);
            } else {
                if(!data.uid_gave) error.push(`Неправильный код у ${data.uid_gave_name}`);
                if(!data.uid_accepted) error.push(`Неправильный код у ${data.uid_accepted_name}`);
            }
        
            alert(`Неверный код! `+error.join('; '));
        });
    });
    transferModalStep_2Form.html("Загрузка данных...");
    getElectronicSign().then(data => {
        transferModalStep_2Form.html(data.template);

        timer = setInterval(function(){
            checkActiveElectronicSign().then(data => {
                if(data.length){
                    data.forEach(item => {
                        const inp = $(`#input_${item.type}`);
                        if(inp.val() == ""){
                            inp.val(item.code).css({
                                border: "2px green solid",
                            });
                        }
                    });
                }
            });
        }, 5000);
    });
}

function checkActiveElectronicSign(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/storage/transfer/checkActiveElectronicSign/"+id, function(json){
            resolve(JSON.parse(json));
        });
    });
}

function checkElectronicSign(){
    return new Promise((resolve, reject) => {
        getTransferData().then(data => {
            const error = [];
            if(data.uid_gave == null) error.push("Не указан кто отдал груз");
            if(data.uid_accepted == null) error.push("Не указан кто принял груз");
            if(error.length){
                reject({error: error.join('; ')});
            } else {
                resolve(data);
            }
        });
    });
}

function printDoc(){
    const id = $("#mainId").val();
    const link = `/administration/module/storage/print/transfer/${id}`;
    checkElectronicSign().then(() => {
        window.open(link);
    }, data => {
        swal('Ошибка!', data.error, 'error');
    });
}

function transfer_doc_create(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/storage/transfer/doc_create/"+id, function(){
            resolve();
        });
    });
}

function getElectronicSign(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/storage/transfer/getElectronicSign/"+id, function(json){
            resolve(JSON.parse(json));
        });
    });
}

function confirmElectronicSign(request){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/storage/transfer/confirmElectronicSign/"+id, request, function(json){
            const data = JSON.parse(json);
            if(data.confirm){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function updateHead(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        if(id != undefined && id != null){
            $.post("/administration/module/storage/transfer/head/"+id, function(data){
                $("#transfer_head").html(data);
                updateHeadFunc();
                resolve();
            });
        } 
    });
}

function updateHeadFunc(){
    x_org();
    x_stock($("#stock_out"));
    x_stock($("#stock_in"));
    x_comment();
}

function x_comment(){
    $("#comment").editable({
        type: 'textarea',
        pk: $("#mainId").val(),
        url: '/ajax.php?do=changeTransferDetail',
        title: 'Введите комментарий',
        emptytext: "Не указано",
    });
}

function add_file_modal(){
    $("#addFileModal").modal("show");
}

function add_file(){
    let data = $("#addFileModalForm").serialize();
    let id = $("#mainId").val();
    $.post("/ajax.php?do=saveFileTransfer", "id="+id+"&"+data, function(){
        checkFile();
        updateHead();
    });
}

function del_file_modal(id){
    $("#delFileModal").modal("show");
    $("#delFileModalBtn").data("id", id);
}

function del_file(id){
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=delFileTransfer", {id:id, mainId:mainId}, function(){
        checkFile();
        updateHead();
    });
}

function checkFile(){
    let id = $("#mainId").val();
    $.post("/ajax.php?do=checkFileTransfer", {id}, function(){

    });
}

function startTransfer(){
    $('#transferModal2').modal('show');
}

function mergingModal(){
    const checks = $(".checks:checked");
    if(checks.length > 0){
        $("#merging_modal").modal("show");
    } else {
        swal("Ошибка!", "Вы не выбрали позиции", "error");
    }
}

function get_checks(){
    const id = [];
    const checks = $(".checks:checked");
    checks.each((i, elem) => {
        const check_id = $(elem).val();
        if(check_id != "on"){
          id.push(check_id);
        }
    });
    return id;
}

function mergingModalCreate(){
    const checks = $(".checks:checked");
    const merging_id = $("#merging_id").val();
    const transfer_id = $("#mainId").val();
    const id = get_checks();
    mergingCreate({transfer_id, id, merging_id}).then(merging_id => {
        window.open('/administration/module/merging/detail/'+merging_id);
    });
}

$(document).on("change", ".checks", function(){
    const checks = $(".checks:checked");
    const btn = $("#sub");
    btn.prop("disabled", true);
    checks.each((i, elem) => {
        const value = $(elem).val();
        if(value !== "on"){
            btn.prop("disabled", false);
            return false;
        }
    });
});

function act_rout(){
    const value = $("#act").val();
    const id = get_checks();
    switch(value){
        case "del":
            check_active().then(() => {
                mas_del_modal(id);
            }, () => {
                error_modal();
            });
        break;
    }
}

function error_modal(){
    swal("Ошибка", "Распроведите заявку", "error");
}

function check_active(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/ajax.php?do=check_active_transfer", {id}, function(data){
            if(data == 0){
                resolve();
            } else {
                reject();
            }
        })
    });
}

function mas_del_modal(id){
    swal({
        title: "Вы уверены?",
        text: "Удалить выбранные позиции?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            const prom = [];
            id.forEach((value, index, array) => {
                prom.push(delItem(value));
            });
            Promise.all(prom).then(() => {
                updateBody().then(() => {
                    lw.off();
                });
            });
        }
      });
}

function x_org(){
    const org = $('#org');
    check_active_swal(org);
    $.post("/ajax.php?do=get_org_data_ajax", function(source){
        org.editable({
            type: 'select',
            source,
            pk: $("#mainId").val(),
            url: '/ajax.php?do=changeTransferDetail',
        });
    });

    org.on('save', function(e, params) {             
        getTransferData().then(data => {
            $("#numb").html(data.numb);
        });
    }); 
    org.on("save", function(){
        updateHead();
    });
}

function check_active_swal(elem){
    check_active().then(() => {}, () => {
        elem.editable("disable"); 
        elem.on("click", function(){
            swal("Ошибка", "Прежде чем поменять склад, распроведите заявку", "error");
        });
    });
}

function getTransferData(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/storage/transfer/getTransferData/"+id, function(data){
            resolve(JSON.parse(data));
        });
    });
}

function transferUpdate(request){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/storage/transfer/transferUpdate/"+id, request, function(){
            resolve();
        });
    });
}

class UidGoodsModal{
    constructor(name){
        const m_name = "uid_goods_modal";
        this.name = name;
        this.modal = $(`#${m_name}_${this.name}`);
        this.form = $(`#${m_name}_form_${this.name}`);
    }
    init(){
        const This = this;
        $(this.form).on("submit", async function(e){
            e.preventDefault();
            const form = $(This.form).serialize();
            $(This.modal).modal("hide");
            await transferUpdate(form)
            updateHead();
        });
    }
}

$(document).on("submit", "#object_modal_form", async function(e){
    e.preventDefault();
    $("#object_modal").modal("hide");
    const lw = new LW;
    lw.on();
    await transferUpdate($(this).serialize());
    await updateHead();
    lw.off();
})