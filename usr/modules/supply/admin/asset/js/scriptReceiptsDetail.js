window.onbeforeunload = function() {
    if(main_active() == 0){
        return "Уйти?";
    }
};

$(function(){
    check_active_btn(false);
    head_editable();
});

function collection_fields(id){
    const elem = $("#tr_"+id);
    const result = {};

    result.price = Price.string_to_number(elem.find(".edit").text());
    result.count = Price.string_to_number(elem.find(".count").text());
    result.total = Price.string_to_number(elem.find(".total").text());
    result.nds = Price.string_to_number(elem.find(".nds").text());
    result.nds_proc = Price.string_to_number(elem.find(".typeNds").text());
    result.nds_includes = $("#includesNds").prop("checked") ? 1 : 0;

    return result;
} 

function get_data_price(id){
	const data = collection_fields(id);
	const price = new Price;
	return price.get_all(data);
} 

function updateReceipts(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/supply/ajax/receipts_detail_list/"+id, function(data){
            $("#dev_list").html(data);
            resolve();
        });
    });
}

function updateHead(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        $.post("/administration/module/supply/ajax/receipts_head/"+id, function(data){
            $("#receipts_head").html(data);
            head_editable();
            resolve();
        });
    });
}

function main_active(value = null){
    const elem = $("#active");
    if(value === null){
        return elem.val();
    } else {
        elem.val(value);
    }
}
    
    //console.log("~");

    $(document).on("keydown", ".rubber", function(event){
        $(this).width(($(this).val().length + 4)*8);
        if(event.which == 13)
        {
            $(this).blur();
        }
    });
    function saveDataToInp(id){
        let count = $('[data-id="'+id+'"]').children(".count").text().replace(/ /g, "");
        let edit = $('[data-id="'+id+'"]').children(".edit").text().replace(" руб.", "").replace(/ /g, "");
        let total = $('[data-id="'+id+'"]').children(".total").text().replace(" руб.", "").replace(/ /g, "");
        let nds = $('[data-id="'+id+'"]').children(".nds").text().replace(" руб.", "").replace(/ /g, "");
        let typeNds = $('[data-id="'+id+'"]').children(".typeNds").text().replace("%", "").replace(/ /g, "");
        
        let json = $("#saveData").val();
        if(json.length > 0){
            json = JSON.parse(json);
            } else {
            json = {};
        }
        
        if((id in json) === false){
            json[id] = {};
        }
        json[id]["count"] = count;
        json[id]["edit"] = edit;
        json[id]["total"] = total;
        json[id]["nds"] = nds;
        json[id]["typeNds"] = typeNds;
        
        json = JSON.stringify(json);
        $("#saveData").val(json);
    }
    
    function getTotal(id){
        let total = $('[data-id="'+id+'"]').children(".total");
        // let count = $('[data-id="'+id+'"]').children(".count").text();
        // let edit = $('[data-id="'+id+'"]').children(".edit").text().replace(" руб.", "").replace(/ /g, "");
        // let nds = $('[data-id="'+id+'"]').children(".nds").text().replace(" руб.", "").replace(/ /g, "");
        // let typeNds = $('[data-id="'+id+'"]').children(".typeNds").text().replace("%", "").replace(/ /g, "");
        
        // let totalCalc = 0;
        // if($("#includesNds").prop("checked")){
        //     totalCalc = count * edit;
        //     getNdsSum(id);
        // } else {
        //     nds = getNdsSum(id);
        //     totalCalc = count * edit + +nds;
        // }
        // totalCalc = totalCalc.toFixed(2);

        const data = get_data_price(id);
        
        $(total).html(Price.priceSet(data.total));
        getNdsSum(id);
    }
    
    function getEdit(id){
        let edit = $('[data-id="'+id+'"]').children(".edit");
        let total = $('[data-id="'+id+'"]').children(".total").text().replace(" руб.", "").replace(/ /g, "");
        let count = $('[data-id="'+id+'"]').children(".count").text().replace(/ /g, "");
        let typeNds = $('[data-id="'+id+'"]').children(".typeNds").text().replace("%", "").replace(/ /g, "");
        let nds = $('[data-id="'+id+'"]').children(".nds");
        
        let editCalc = total / count;

        let ndsCalc = getNds(count, editCalc, typeNds);
        $(nds).html(Price.priceSet(ndsCalc));

        editCalc = editCalc.toFixed(2);
        
        $(edit).html(Price.priceSet(editCalc));
    }
    
    function getNdsSum(id){
        let nds = $('[data-id="'+id+'"]').children(".nds");
        // let total = $('[data-id="'+id+'"]').children(".total").text().replace(" руб.", "").replace(/ /g, "");
        // let typeNds = $('[data-id="'+id+'"]').children(".typeNds").text().replace("%", "").replace(/ /g, "");
        // let edit = $('[data-id="'+id+'"]').children(".edit").text().replace(" руб.", "").replace(/ /g, "");
        // let count = $('[data-id="'+id+'"]').children(".count").text().replace(/ /g, "");
        
        // let ndsCalc = 0;
        // ndsCalc = getNds(count, edit, typeNds);
        const data = get_data_price(id);
        const ndsCalc = data.nds;
        $(nds).html(Price.priceSet(ndsCalc));
        return ndsCalc;
    }

    function getNds(count, edit, typeNds){
        let ndsCalc = 0;
        if($("#includesNds").prop("checked")){
            ndsCalc = (count * edit) * typeNds / (100 + +typeNds);
        } else {
            ndsCalc = edit * count * (typeNds / 100);
        }
        ndsCalc = ndsCalc.toFixed(2)
        return ndsCalc;
    }
    
    function setAllTotal(){
        let total = $(".total");
        let totalAll = 0;
        for(let i=0;i<total.length;i++){
            totalAll += +$(total[i]).text().replace(/ /g, "").replace("руб.", "");
        }
        totalAll = totalAll.toFixed(2);
        // console.log(totalAll);
        $(".allTotal").html("<b>"+totalAll+" руб.</b>");
    }
    
    function setAllNds(){
        let nds = $(".nds");
        let ndsAll = 0;
        for(let i=0;i<nds.length;i++){
            ndsAll += +$(nds[i]).text().replace(/ /g, "").replace("руб.", "");
        }
        ndsAll = ndsAll.toFixed(2);
        $(".allNds").html("<b>"+ndsAll+" руб.</b>");
    }
    
    //количество
    $(document).on("click", ".count", function(){
        //console.log("~");
        $(this).addClass("countInp");
        $(this).removeClass("count");
        $(this).html('<input type="number" id="countInp" class="form-control rubber" value="'+$(this).text().replace(/ /g, "")+'" style="width: ' +(($(this).text().replace(/ /g, "").length + 6) * 8)+ 'px">');
        $("#countInp").focus();
        $("#countInp").select();
    });
    
    $(document).on("blur", "#countInp", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let count = $(this).val();
        $(".countInp").html(count);
        $(".countInp").addClass("count");
        $(".count").removeClass("countInp");
        
        getTotal(id);
        saveDataToInp(id);
        setAllTotal();
        setAllNds();
    });
    
    //цена закупки
    $(document).on("click", ".edit", function(){
        //console.log("~");
        $(this).addClass("editInp");
        $(this).removeClass("edit");
        $(this).html('<input type="number" id="editInp" class="form-control rubber" value="'+$(this).text().replace(" руб.", "").replace(/ /g, "")+'" style="width: ' +(($(this).text().replace(" руб.", "").replace(/ /g, "").length + 6) * 8)+ 'px">');
        $("#editInp").focus();
        $("#editInp").select();
    });
    
    $(document).on("blur", "#editInp", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let edit = $(this).val();
        $(".editInp").html(edit+" руб.");
        $(".editInp").addClass("edit");
        $(".edit").removeClass("editInp");
        
        getTotal(id);
        saveDataToInp(id);
        setAllTotal();
        setAllNds();
    });
    
    
    //сумма
    $(document).on("click", ".total", function(){
        //console.log("~");
        $(this).addClass("totalInp");
        $(this).removeClass("total");
        $(this).html('<input type="number" id="totalInp" class="form-control rubber" value="'+$(this).text().replace(" руб.", "").replace(/ /g, "")+'" style="width: ' +(($(this).text().replace(" руб.", "").replace(/ /g, "").length + 6) * 8)+ 'px">');
        $("#totalInp").focus();
        $("#totalInp").select();
    });
    
    $(document).on("blur", "#totalInp", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let total = $(this).val();
        $(".totalInp").html(total+" руб.");
        $(".totalInp").addClass("total");
        $(".total").removeClass("totalInp");
        
        getEdit(id);
        //getNdsSum(id);
        saveDataToInp(id);
        setAllTotal();
        setAllNds();
    });
    
    //НДС
    $(document).on("click", ".nds", function(){
        //console.log("~");
        $(this).addClass("ndsInp");
        $(this).removeClass("nds");
        $(this).html('<input type="number" id="ndsInp" class="form-control rubber" value="'+$(this).text().replace(" руб.", "").replace(/ /g, "")+'" style="width: ' +(($(this).text().replace(" руб.", "").replace(/ /g, "").length + 6) * 8)+ 'px">');
        $("#ndsInp").focus();
        $("#ndsInp").select();
    });
    
    $(document).on("blur", "#ndsInp", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let nds = $(this).val();
        $(".ndsInp").html(nds+" руб.");
        $(".ndsInp").addClass("nds");
        $(".nds").removeClass("ndsInp");
        
        saveDataToInp(id);
        setAllTotal();
        setAllNds();
    });
    
    //Тип ндс
    let typeNdsData;
    $.post("/ajax.php?do=getNdsType", function(data){ 
        typeNdsData = data;
    });
    $(document).on("click", ".typeNds", function(){
        //console.log("~");
        $(this).addClass("typeNdsInp");
        $(this).removeClass("typeNds");
        let save = $(this).text().replace("%", "");
        $(this).html('<select id="typeNdsInp" class="form-control" style="width: ' +(($(this).text().replace("%", "").replace(/ /g, "").length + 6) * 8)+ 'px"></select>');   
        $("#typeNdsInp").append(typeNdsData);
        let opt = $(".ndsTypeOp");
        for(let i=0;i<opt.length;i++){
            if($(opt[i]).html() == save) $(opt[i]).prop('selected',true);
        }
        $("#typeNdsInp").focus();
        $("#typeNdsInp").select();
    });
    
    $(document).on("blur change", "#typeNdsInp", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let typeNds = $(this).val();
        let typeNdsText = $(this).find('option:selected').text();
        $(".typeNdsInp").html(typeNdsText+"%");
        $(".typeNdsInp").addClass("typeNds");
        $(".typeNds").removeClass("typeNdsInp");
        
        getTotal(id);
        getNdsSum(id);
        saveDataToInp(id);
        setAllTotal();
        setAllNds();
    });
    
    //сохранение
    $(document).on("click", "#saveReceipt", function(){
        let saveData = $("#saveData").val();
        let receiptId = window.location.href.split("/");
        receiptId = receiptId[receiptId.length - 1];
        
        let tr = $("#dev_list").find("tr");
        let totalAll = 0;
        let ndsAll = 0;
        for(let key = 0; key < tr.length; key++){
                let id = $(tr[key]).data("id");
                let edit = $(tr[key]).children(".edit").text().replace(" руб.", "").replace(/ /g, "");
                let count = $(tr[key]).children(".count").text().replace(/ /g, "");
                let nds = $(tr[key]).children(".nds").text().replace(" руб.", "").replace(/ /g, "");
                let total = $(tr[key]).children(".total").text().replace(" руб.", "").replace(/ /g, "");
                totalAll += +total;
                ndsAll += +nds;
            } 
            ndsAll = ndsAll.toFixed(2);
            totalAll = totalAll.toFixed(2);
        $.post("/ajax.php?do=updateProductsReceipt", {saveData:saveData, receiptId:receiptId, totalAll:totalAll, ndsAll:ndsAll}, function(data){
            notif('success', 'Информация', 'Данные успешно сохранены!');
            //console.log(data);
        });
    });
    
    
    //Входящий номер
    /*
    $(document).on("click", ".doc_number", function(){
        //console.log("~");
        $(this).addClass("input");
        $(this).removeClass("doc_number");
        let save = $(this).html();
        $(this).html('<input type="text" value="'+save+'" id="input" class="form-control rubber" style="width: ' +((save.length + 6) * 8)+ 'px">');
        $("#input").focus();
        $("#input").select();
    });
    
    $(document).on("blur", "#input", function(){
        let save = $(this).val();
        $(".input").html(save);
        $(".input").addClass("doc_number");
        $(".input").removeClass("input");
        let id = $("#mainId").val();
        $.post("/ajax.php?do=updateDocNumberSupply", {id:id, doc_number: save}, function(data){
            
        });
    });
    */
    //файлы модалка
    $(document).on("click", "#addFile", function(){
        $("#fileModal").modal("show");
    });
    
    $(document).on("click", "#saveFileBtn", function(){
        let data = $("#fileForm").serialize();
        let id = $("#mainId").val();
        $.post("/ajax.php?do=saveFileSupply", (data+"&id="+id), function(data){
            $("#fileBasket").html(data);
        });
    });
    
    //удаление
    $(document).on("click", ".removeFile", function(){
        let id = $(this).data("id");
        let name = $(this).data("name");
        $("#removeFileBtn").data("id", id);
        $("#removeFileBtn").data("name", name);
        $("#fileName").html(name);
        $("#fileRemoveModal").modal("show");
    });
    
    $(document).on("click", "#removeFileBtn", function(){
        let id = $(this).data("id");
        
        $.post("/ajax.php?do=removeFileSupply", {id:id}, function(data){
            $('[data-id="'+id+'"]').parent().remove();
        });
        
    });
    
    
    $(document).on("click", "#conductBtn", function(){
        let receipt_id = $("#mainId").val();
        qqHelp(receipt_id);
    });
    
    $(document).on("click", "#distributeBtn", function(){
        let receipt_id = $("#mainId").val();
        qqHelp(receipt_id);
    });
    
    function qqHelp(receipt_id){
        const lw = new LW;
        lw.on();
        $.post("/ajax.php?do=receiptsSetActive", {receipt_id}, function(data){
            const job = [];
            switch(data){
                case '1':
                    notif("success", "Информация", "Документ поступления успешно проведен");
    
                    $(".elemHide").hide();
    
                    viewReserv();
                    main_active(1);
                    // job.push(automatic_receipts(receipt_id));
                break;
                case '2':
                    notif("system", "Информация", "Проведение документа поступления успешно отменено");
    
                    $(".elemHide").show();
                    main_active(0);
                    // job.push(automatic_receipts(receipt_id));
                break;
                case '3':
                    notif("danger", "Ошибка", "Что-то пошло не так...");
                break;
                case '4':
                    notif("danger", "Ошибка", "Чтобы провести поступление необходимо приложить оригинал документа поступления!");
                break;
                case "5":
                    notif("danger", "Ошибка", "Количество проводимого превышает допустимое значение");
                break;
            }
            
            job.push(updateReceipts());
            job.push(updateHead());

            Promise.all(job).then(() => {
                lw.off();
            });
            check_active_btn();
        });
    }

    function automatic_receipts(receipt_id){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/supply/automatic_receipts/"+receipt_id, function(){
                resolve();
            });
        });
    }

    function waybillShow(){
        const val = $("#type_doc").text();
        if(+val === 2){
            $("#waybill_number_con").show();
        } else {
            $("#waybill_number_con").hide();
        }
    }

    function stock_source(){
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=get_stock_source", function(data){
                let stock = JSON.parse(data);;
                resolve(stock);
            });
        })
    }

    waybillShow();
    function getArraySource(){
        return [
            {value: 0, text: 'Отсутствует'},
            {value: 1, text: 'УПД'},
            {value: 2, text: 'Счет фактура / товарная накладная'},
        ];
    }
    let arraySource = getArraySource();
    // $("#type_doc").text(arraySource[val]["text"]);

    function doc_number_editable(){
        const pk = $("#mainId").val();
        $('#doc_number').editable({  
            type: "text",
            pk,
            emptytext: 'Отсутствует',
            url: "/ajax.php?do=changeDocReceipts"
        });
    }
    

    function head_editable(){
        type_doc_editable();
        doc_number_editable();
        date_receipts_editable();
        stock_id_editable();
        syncRegister_editable();
    }

    function type_doc_editable(){
        const pk = $("#mainId").val();
        const source = getArraySource();
        $('#type_doc').editable({  
            type: "select",
            pk,
            source,
            url: "/ajax.php?do=changeDocReceipts"
        });
    }

    function syncRegister_editable(){
        const pk = $("#mainId").val();
        $('#syncRegister').editable({  
            type: "select",
            pk,
            source: [
                {value: 0, text: 'Нет'},
                {value: 1, text: 'Да'},
            ],
            url: "/ajax.php?do=changeDocReceipts"
        });
    }
    
    function stock_id_editable(){
        stock_source().then(stock => {
            const pk = $("#mainId").val();
            let stock_id = $("#stock_id").data("stock_id")
            $('#stock_id').editable({  
                type: "select",
                pk,
                defaultValue: stock_id,
                source: stock,
                url: "/ajax.php?do=changeDocReceipts"
            });
        });
    }
    
    

    $("#stock_id").on("save", function(e, params){
        $("#stock_id").data("stock_id", params.newValue);
        updateReceipts();
    });
    
    function date_receipts_editable(){
        const pk = $("#mainId").val(); 
        $("#date_receipts").editable({  
            type: "date",
            pk,
            emptytext: 'Не указано!',
            url: "/ajax.php?do=changeDocReceipts",
            format: 'dd.mm.yyyy',
            viewformat: 'dd.mm.yyyy',
        });
    }
    
    
    $("#type_doc").on("save", function(e, params){
        waybillShow(params.newValue);
    });

    

    function waybill_number_x(){
        const pk = $("#mainId").val();
        $("#waybill_number").editable({
            type: "text",
            pk,
            emptytext: 'Отсутствует',
            url: "/ajax.php?do=changeDocReceipts",
        });
    }

    waybill_number_x();

function error_notif(){
    swal("Ошибка!", "Распроведите документ", "error");
}

function check_active_btn(start = true){
    let elems = ["type_doc", "doc_number", "date_receipts", "waybill_number", "stock_id"];
    check_active().then(() => {
        if(start){
            elems.forEach((elem) => {
                $("#"+elem).editable("enable");
                $("#"+elem).unbind('click', error_notif);
            });
        }
    }, () => {
        elems.forEach((elem) => {
            $("#"+elem).editable("disable");
            $("#"+elem).click(error_notif);
        });
    });
}

function viewReserv(open = false){
    let rid = $("#mainId").val();
    if(open){
        $("#viewReservModal").modal("show");
    }
    $.post("/ajax.php?do=getListReservSupplyOrder", {rid:rid}, function(data){
        if(data.length > 0){
            $("#viewReservModal").modal("show");
            $("#viewReservModalBody").html(data);
        } else {
            $("#viewReservModalBody").html("Резерв пуст");
        }
    });
}

$(document).on("change", ".checks, #all", function(){
    const check = $(".checks").is(":checked");
    const transferBtn = $("#transferBtn");
    const sub = $("#sub");
    if(check){
        transferBtn.addClass("holder-active");
        sub.prop("disabled", false);
    } else {
        transferBtn.removeClass("holder-active");
        sub.prop("disabled", true);
    }
});

$(document).on("click", "#transferBtn", function(){
    if($(this).hasClass("holder-active")){
        $("#transferModal").modal("show");
    } else {
        alert("Выберите нужные позиции");
    }
});

$(document).on("click", "#transferModalBtn", function(){
    let data = $("#tablesForm").serialize();
    let stock_in = $("#stock_in").val();
    let oid = $("#oid").val();
    let receiptId = $("#mainId").val();
    if(stock_in !== null && oid !== null){
        $("#transferModal").modal("hide");
        $.post("/ajax.php?do=transferReceipt", "receiptId="+receiptId+"&stock_in="+stock_in+"&oid="+oid+"&"+data, function(id){
            if(id == ""){
                swal("Ошибка!", "Неизвестная ошибка, позовите программиста", "error");
            } else {
                swal({
                    title: "Успешно!",
                    text: "Переменщение создано, нажмите чтобы перейти к перемещению",
                    icon: "success",
                    buttons: {
                        cancel: {
                            text: "Закрыть",
                            value: false,
                            visible: true,
                            className: "",
                            closeModal: true,
                        },
                        confirm: {
                            text: "Перейти",
                            value: true,
                            visible: true,
                            className: "",
                            closeModal: true
                        }
                    },
                  })
                  .then((willLocation) => {
                    if (willLocation) {
                      window.open('/administration/module/storage/transfer/detail/'+id);
                    }
                  });
            }
        });
    } else {
        swal("Ошибка!", "Заполните все поля!", "error");
    }
});

function check_active(){
    let id = $("#mainId").val();
    return new Promise(function(resolve, reject){
        $.post("/ajax.php?do=check_active_receipts", {id:id}, function(data){
            if(+data === 0){
                resolve();
            } else {
                reject();
            }
        });
    });
}

function delTovModal(id){
    check_active().then(function(){
        $("#delTovModal").modal("show");
        $("#delTovModalBtn").data("id", id);
    }, function(){
        swal("Ошибка!", "Распроведите заявку", "error");
    });
    
}

function delTovBtn(This){
    let id = $(This).data("id");
    delTov(id).then(() => {
        updateReceipts();
    });
}

function delTov(id){
    return new Promise((resolve, reject) => {
        const receipts_id = $("#mainId").val();
        $.post("/ajax.php?do=delTovReceipt", {id:id, receipts_id:receipts_id}, function(){
            resolve();
        });
    });
}

function convertModal(data){
    $("#convertModal").modal("show");
    $("#")
}

function subAct(){
    const type = $("#act").val();
    switch(type){
        default:
            swal("Ошибка!", "Такого пункта нет! Обратитесь к администратору", "error");
        break;
        case "delete":
            delMasModal();
        break;
    }
}

function delMasModal(){
    swal({
        title: "Вы уверены?",
        text: "После удаления вы не сможете восстановить этот товар!",
        icon: "warning",
        buttons: {
            confirm: {
                text: "Выполнить",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
            cancel: {
                text: "Отмена",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
      })
      .then((willDelete) => {
        if (willDelete) {
            const check = [];
            $(".checks:checked").each((i, elem) => {
                check.push($(elem).val());
            });
            delTov(check).then(() => {
                updateReceipts();
            });
        }
      });
}