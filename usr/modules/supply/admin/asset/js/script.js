$(function(){
    updateHeadFunc();
    update_body({view_load:false});
    
    $(document).on("keydown", ".rubber", function(event){
        $(this).width(($(this).val().length + 4)*8);
        if(event.which == 13)
        {
            $(this).blur();
        }
    });
    
    //количество
    let saveCount = 0;
    $(document).on("click", ".count", function(){
        $(this).addClass("countInp");
        $(this).removeClass("count");
        saveCount = $(this).text().replace(/ /g, "");
        const input = input_rubber_gen({id:"countInp", value: $(this).text()});
        $(this).html(input);
        $("#countInp").focus();
        $("#countInp").select();
    });
    
    $(document).on("blur", "#countInp", function(){
        const id = $(this).parent().parent().data("id");
        const count = $(this).val();

        $.post("/ajax.php?do=checkSendCount", {count, id}, function(data){
            if(data === ""){
                $(".countInp").html(count);
                $(".countInp").addClass("count");
                $(".count").removeClass("countInp");
                
                getTotal(id);
                saveDataToInp(id);
                setAllTotal();
                setAllNds();
            } else {
                swal("Ошибка!", "Количество превышает количество в заявке", "error");
                $(".countInp").html(saveCount);
                $(".countInp").addClass("count");
                $(".count").removeClass("countInp");
            }
        });
    });
    
    //цена закупки
    $(document).on("click", ".edit", function(){
        //console.log("~");
        $(this).addClass("editInp");
        $(this).removeClass("edit");
        const input = input_rubber_gen({id:"editInp", value: $(this).text()});
        $(this).html(input);
        $("#editInp").focus();
        $("#editInp").select();
    });
    
    $(document).on("blur", "#editInp", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let edit = $(this).val();
        $(".editInp").html(Price.priceSet(edit));
        $(".editInp").addClass("edit");
        $(".edit").removeClass("editInp");
        
        getTotal(id);
        saveDataToInp(id);
        setAllTotal();
        setAllNds();
    });
    
    
    //сумма
    $(document).on("click", ".total_no_nds", function(){
        const id = $(this).data("id");
        $(this).addClass("totalInpNoNds");
        $(this).removeClass("total_no_nds");
        const input = input_rubber_gen({id:"totalInpNoNds", value: $(this).text(), data_id: id});
        $(this).html(input);
        $("#totalInpNoNds").focus();
        $("#totalInpNoNds").select();
    });
    
    $(document).on("blur", "#totalInpNoNds", function(){
        const id = $(this).data("id");
        let total = $(this).val();
        $(".totalInpNoNds").html(Price.priceSet(total));
        $(".totalInpNoNds").addClass("total_no_nds");
        $(".total_no_nds").removeClass("totalInpNoNds");
        
        getEditNoNds(id);
        //getNdsSum(id);
        saveDataToInp(id);
        setAllTotal();
        setAllNds();
    });

    $(document).on("click", ".total", function(){
        const id = $(this).data("id");
        $(this).addClass("totalInp");
        $(this).removeClass("total");
        const input = input_rubber_gen({id:"totalInp", value: $(this).text(), data_id: id});
        $(this).html(input);
        $("#totalInp").focus();
        $("#totalInp").select();
    });
    
    $(document).on("blur", "#totalInp", function(){
        const id = $(this).data("id");
        const total = $(this).val();
        $(".totalInp").html(Price.priceSet(total));
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
        const input = input_rubber_gen({id:"ndsInp", value: $(this).text()});
        $(this).html(input);
        $("#ndsInp").focus();
        $("#ndsInp").select();
    });
    
    $(document).on("blur", "#ndsInp", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let nds = $(this).val();
        $(".ndsInp").html(Price.priceSet(nds));
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
        let typeNdsText = $(this).find('option:selected').text();
        $(".typeNdsInp").addClass("typeNds");
        $(".typeNds").removeClass("typeNdsInp");
        changeNdsTypeValue(id, typeNdsText);
    });
    
    //**************************************************
    
    $(document).on("change", ".checks", function(){
        //console.log("~");
        let flag = false;
        $(".checks").each(function(i, elem){
            if($(elem).prop("checked")) flag = true;
        });
        if(flag) {
            $("#myCheck").addClass("holder-active");
            $("#sub").prop("disabled", false);
        } else {
            $("#myCheck").removeClass("holder-active");
            $("#sub").prop("disabled", true);
        }
    });
    
    $(document).on("click", "#myCheck", function(){
        let flag = false;
        $(".checks").each(function(i, elem){
            if($(elem).prop("checked") && $(elem).val() != "on") flag = true;
        });
        if(flag){
            let msg = $("#tablesForm").serialize();
            checkZeroSum(msg).then(() => {
                $.ajax({
                    type: "POST",
                    url: "/administration/module/supply/checkGoods",
                    data: msg,
                    success: function(data) {
                        if(data == "good"){
                            $("#myModal").modal("show");
                            } else {
                            notif("danger", "Предупреждение!", "Проведенные товары: "+data+"");
                        }
                    },
                });
            }, data => {
                notif("danger", "Ошибка!", "Товары с 0 суммой: "+data+"");
            });
        }
        else
        notif("danger", "Предупреждение!", "Выберите товары");    
    });
    
    //**************************************************
    let saveValue = "";
    $(document).on("click", ".changeGoods", function(){
        let id = $(this).attr("id").slice(3);
        saveValue = $(this).html();
        $(this).html('<input type="text" id="ic_'+id+'" class="inputChange form-control" value="'+saveValue+'">');
        $(".inputChange").focus();
        $(".inputChange").select();
        
        let coord = $(".inputChange").offset();
        let width = $(".inputChange").outerWidth();
        
        let div = "<div id=\'ajaxResult\' style=\'display:none; width: "+width+"px; height: 200px; border: 1px black solid; z-index:999; position: absolute; overflow-y: auto; background: white; \'></div>";
        
        let ctop = coord.top;
        let cleft = coord.left;
        $("#ic_"+id).after(div).offset({top: ctop, left: cleft});
        
        $(this).removeClass("changeGoods");
        
        if(ctop + 300 >= $(document).height())
        $("#content").append("<div id=\'helpDiv\' style=\'height: 200px\'></div>");
    });
    
    
    let flagList = true;
    function getSearchList(){
        $.ajax({
            url: "/ajax.php?do=getSearchListSupply",
            type: "POST",
            data: {search: $(".inputChange").val(), counter: $("#counter").val()},
            success: function(data){
                $("#ajaxResult").append(data);
                $("#counter").val(+$("#counter").val() + 50);
                flagList = true;
            }
        });
    }
    
    $(document).on("input cut copy paste", ".inputChange", function(){
        if(flagList){
            if($(this).val().length > 2){
                flagList = false;
                $("#counter").val(0);
                $("#ajaxResult").html("");
                getSearchList();
                $("#ajaxResult").slideDown();
                
                if($("#helpDiv").length > 0){
                    $("html, body").animate({ scrollTop: $(document).height() }, "slow");
                }
            }
        }
    });
    
    $(document).on("click", ".loadMore", function(){
        $(".loadMore").remove();
        getSearchList();
    });
    
    let flagCheck = true;
    
    $(document).on("mouseleave", "#ajaxResult", function(){
        flagCheck = true;
        $(".inputChange").focus();
    });   
    
    $(document).on("mouseenter", "#ajaxResult", function(){
        flagCheck = false;
    });  
    
    $(document).on("click", ".elemList", function(){
        let id = $(this).attr("id").slice(3);
        let parentId = $(".inputChange").attr("id").slice(3);
        
        let child = $("#cg_"+parentId).parent().children();
        let num = $(child[0]).html();
        let count = $(child[3]).html();
        let supply_d = $("#supply_d_"+parentId).val();
        
        $.ajax({
            type: "POST",
            url: "/administration/module/supply/getCatProduct", 
            data: {id: id, parentId: parentId, num: num, count: count, supply_d: supply_d},
            success: function(data){
                $("#cg_"+parentId).parent().after(data);
                $("#cg_"+parentId).parent().remove();
                flagCheck = true;
                
                let json = $("#saveId").val();
                if(json.length > 0){
                    json = JSON.parse(json);
                    } else {
                    json = {};
                }
                json[parentId] = id;
                json = JSON.stringify(json);
                $("#saveId").val(json);
                
                $("#helpDiv").remove();
            }
        });
    });    
    
    $(document).on("blur", ".inputChange", function(){
        if(flagCheck){
            let id = $(".inputChange").attr("id").slice(3);
            $("#cg_"+id).html(saveValue);
            $("#cg_"+id).addClass("changeGoods");
            $("#helpDiv").remove();
        } 
    });
    
    
    $(document).on("click", "#saveSupply", function(){
        const saveId = $("#saveId").val(),
        saveData = $("#saveData").val(),
        includesNds = $("#includesNds").prop("checked"),
        supplyId = $("#mainId").val();
        
        let totalAll = 0;
        $("#tov_list").find("tr").each((i, item) => {
            const id = $(item).data("id");
            const data = get_data_price(id);
            totalAll += data.total;
        });
        
        
        $.ajax({
            type: "POST",
            url: "/ajax.php?do=updateProductsSupply", 
            data: {saveId, saveData, includesNds, supplyId, totalAll},
            success: function(){
                notif('success', 'Информация', 'Данные успешно сохранены!');
            }
        });
    });
    
    $(document).on("change", "#includesNds", function(){
        let json = $("#saveData").val();
        if(json.length > 0){
            json = JSON.parse(json);
        } else {
            json = {};
        }
        $("#tov_list").find("tr").each((i, elem) => {
            const id = $(elem).data("id");
            if(id !== undefined){
                const link = collection_fields_link(id);
                const data = get_data_price(id);
                const total = data.total;
                const nds = data.nds;
                const total_no_nds = total - nds;
                link.total.html(Price.priceSet(total));
                link.total_no_nds.html(Price.priceSet(total_no_nds));
                link.nds.html(Price.priceSet(nds));
                
                if((id in json) === false){
                    json[id] = {};
                }
                json[id]["count"] = data.count;
                json[id]["edit"] = data.price;
                json[id]["total"] = data.total;
                json[id]["nds"] = data.nds;
                json[id]["typeNds"] = data.nds_proc;
            }
        });

        json = JSON.stringify(json);
        $("#saveData").val(json);
        setAllTotal();
        setAllNds();
    });
    
    $(document).on("click", ".removeAttach", function(){
        $("#delFileSupply").modal("show");
        $("#nameFile").html($(this).data("name"));
        $("#delFileBtn").data("id", $(this).data("id"));
    });
    
    $(document).on("click", "#delFileBtn", function(){
        let id = $(this).data("id");
        $.post("/ajax.php?do=removeFileSupply",{id:id}, function(data){
            $("#li_"+id).remove();
        });
    });
    
    $(document).on("click", "#reconcileInvoice", function(){
        check_fcontrol().then(() => {
            $("#reconcileInvoiceModal").modal("show");
        }, error => {
            let div = document.createElement('div');
            div.innerHTML = error;
            swal({
                content: div,
                title: "Ошибка",
                icon: "error",
            });
        });
    });

    $(document).on("dblclick", ".change_goods", async function(){
        const id = this.dataset.id;
        const lw = new LW;
        lw.on();
        const ccd = await check_can_delete(id);
        lw.off();
        if(ccd.status){
            lw.on();
            const ccc = await check_can_change(id);
            lw.off();
            if(ccc.status){
                change_goods_open(id);
            } else {
                error_modal_show_move(ccc.data);
            }
        } else {
            error_modal_show_move(ccd.data);
        }
    });


});

async function check_can_change(id){
    const form = new FormData;
    form.append("id", id);
	return await( await fetch(`/administration/module/supply/check_can_change`, {
		method: "POST",
		body: form,
	}) ).json();
}

// function change_goods_open(id){
//     window.open(`/administration/module/supply/change_goods_order/${id}`,'1549627856974','width=1400,height=850,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');
// }

function change_goods(id, parentId){
    //id -> cid
    return new Promise((resolve, reject) => {
        const d = collection_fields(parentId);
        const supply_d = $("#supply_d_"+parentId).val();
        $.post("/administration/module/supply/getCatProduct", {id, parentId, supply_d, num: d.num, count: d.count}, function(data){
            $("#tr_"+parentId).after(data);
            $("#tr_"+parentId).remove();
            
            let json = $("#saveId").val();
            if(json.length > 0){
                json = JSON.parse(json);
            } else {
                json = {};
            }
    
            json[parentId] = id;
            json = JSON.stringify(json);
            $("#saveId").val(json);

            saveDataToInp(parentId);
            updateBodyFunc();
            resolve();
        });
    });
}

function changeNdsTypeValue(id, new_value){
    const link = collection_fields_link(id);
    link.nds_proc.html(`${new_value}%`);
    getTotal(id);
    getNdsSum(id);
    saveDataToInp(id);
    setAllTotal();
    setAllNds();
}

function x_date_ship_detail(){
    const date_ship = $('.date_ship');
    date_ship.editable({
        type: 'date',
        datepicker: {
            weekStart: 1,
        },
        name: "date_ship",
        url: "/ajax.php?do=fastChangeSupplyOrderDetail",
        viewformat: "dd.mm.yyyy",
        emptytext: "Не указано",
        success: function(response) {
            const data = JSON.parse(response);
            changeDateEnd(data.id, data.date_end);
            if(data.date_ship == "" || data.date_ship == null){
                const value = $("#date_ship").editable('getValue', true);
                if(value != null){
                    setTimeout(() => {
                        $(this).editable('setValue', value, true);
                    }, 10);
                }
            }
        }
    });
} 

function x_date_end_detail(){
    const date_end = $('.date_end');
    date_end.editable({
        type: 'date',
        datepicker: {
            weekStart: 1,
        },
        name: "date_end",
        url: "/ajax.php?do=fastChangeSupplyOrderDetail",
        viewformat: "dd.mm.yyyy",
        emptytext: "Не указано",
        success: function(response) {
            const data = JSON.parse(response);
            changeDateEnd(data.id, data.date_end);
        }
    });
}

function changeDateEnd(id, value){
    logistic_status({id, type: "supply_detail"});
    const link = collection_fields_link(id);
    if(value == "" || value == null){
        const main_value = $("#date_end").editable('getValue', true);
        if(main_value != null){
            setTimeout(() => {
                $(link.date_end).editable('setValue', main_value, true);
            }, 10);
        }
    } else {
        setTimeout(() => {
            $(link.date_end).editable('setValue', value, true);
        }, 10);
    }
}

//быстрая смена
    //Номер счета
    function x_bill_code(){
        $('#bill_code').editable({
            type: 'text',
            pk: $("#mainId").val(),
            url: "/ajax.php?do=fastChangeSupplyOrder",
            emptytext: "Не указан!",
        });
    }
    
    //Дата счета
    function x_date_bill(){
        $('#date_bill').editable({
            type: 'date',
            datepicker: {
                weekStart: 1,
                //language: "ru",
            },
            pk: $("#mainId").val(),
            url: "/ajax.php?do=fastChangeSupplyOrder",
            viewformat: "dd.mm.yyyy",
        });
    }
    
    
    //Дата размещения
    function x_date_work(){
        $('#date_work').editable({
            type: 'date',
            datepicker: {
                weekStart: 1,
            },
            pk: $("#mainId").val(),
            url: "/ajax.php?do=fastChangeSupplyOrder",
            viewformat: "dd.mm.yyyy",
        });
    }
    
    
    //Дата отгрузки
    function x_date_ship(){
        const date_ship = $('#date_ship');
        date_ship.editable({
            type: 'date',
            datepicker: {
                weekStart: 1,
            },
            pk: $("#mainId").val(),
            url: "/ajax.php?do=fastChangeSupplyOrder",
            viewformat: "dd.mm.yyyy",
        });
        date_ship.on("save", function(){
            update_head();
            update_body();
        });
    }
    
    
    //Дата поставки
    function x_date_end(){
        const date_end = $('#date_end');
        const id = $("#mainId").val();
        date_end.editable({
            type: 'date',
            datepicker: {
                weekStart: 1,
            },
            pk: id,
            url: "/ajax.php?do=fastChangeSupplyOrder",
            viewformat: "dd.mm.yyyy",
        });
        date_end.on("save", function(){
            update_body();
            logistic_status({id, type: "supply"});
        });
    }

    async function logistic_status(request){
        await $.post("/ajax.php?do=supply_logistic_status", request);
    }
    

    function x_stock_ndr(){
        const stock_ndr = $('#stock_ndr');
        stock_ndr.editable({
            type: 'select',
            source: JSON.parse($("#stock_ndr_data").val()),
        });
        stock_ndr.on('save', function(e, params){
            const data = {};
            data.stock_ndr = params.newValue;
            update_body(data);
        });
    }

function collection_fields_link(id){
    const result = {};
    const item = $("#tr_"+id);

    result.item = item;
    result.id = id;
    result.price_now = item.find(".edit");
    result.count = item.find(".count");
    result.total = item.find(".total");
    result.total_no_nds = item.find(".total_no_nds");
    result.nds = item.find(".nds");
    result.nds_proc = item.find(".typeNds");
    result.nds_includes = +$("#includesNds").prop("checked");
    result.num = item.find(".num");
    result.date_ship = item.find(".date_ship");
    result.date_end = item.find(".date_end");

    return result;
}

function collection_fields(id){
    const item = collection_fields_link(id);
    const result = {};

    result.price_now = Price.string_to_number(item.price_now.text());
    result.count = Price.string_to_number(item.count.text());
    result.total = Price.string_to_number(item.total.text());
    result.total_no_nds = Price.string_to_number(item.total_no_nds.text());
    result.nds = Price.string_to_number(item.nds.text());
    result.nds_proc = Price.string_to_number(item.nds_proc.text());
    result.nds_includes = $("#includesNds").prop("checked") ? 1 : 0;
    result.num = Price.string_to_number(item.num.text());

    return result;
} 

function get_data_price(id){
	const data = collection_fields(id);
	const price = new Price;
	return price.get_all({
		price: data.price_now, 
		count: data.count, 
		nds_includes: data.nds_includes, 
		nds_proc: data.nds_proc,
	});
} 

function input_rubber_gen(request){
    //{id:"", value:""}
    const id = request.id;
    const value = Price.string_to_number(request.value);
    const width = (String(value).length + 6) * 8;
    const data_id = "data_id" in request ? request.data_id : "";
    return `<input type="number" id="${id}" class="form-control rubber" value="${value}" data-id="${data_id}" style="width: ${width}px">`;
}

function getTotal(id){
    const link = collection_fields_link(id);
    const data = get_data_price(id);
    $(link.total).html(Price.priceSet(data.total));
    const total_no_nds = data.total - data.nds;
    $(link.total_no_nds).html(Price.priceSet(total_no_nds));
    getNdsSum(id);
}

function getEdit(id){
    const data = collection_fields(id);
    let editCalc = data.total / data.count;
    if(!data.nds_includes){
        editCalc = editCalc / get_nds_factor(data);
    }
    setEdit(id, editCalc);
}

function getEditNoNds(id){
    const data = collection_fields(id);
    let editCalc = data.total_no_nds / data.count;
    if(data.nds_includes){
        editCalc = editCalc * get_nds_factor(data);
    }
    setEdit(id, editCalc);
}

function get_nds_factor(data){
    return (100 + data.nds_proc) / 100;
}

function setEdit(id, editCalc){
    const link = collection_fields_link(id);
    const data = collection_fields(id);
    const ndsCalc = getNds(data.count, editCalc, data.nds_proc);
    link.nds.html(Price.priceSet(ndsCalc));
    link.price_now.html(Price.priceSet(editCalc.toFixed(2)));
    const price = new Price;
    p = price.get_all({price: editCalc, count: data.count, nds_proc:data.nds_proc, nds_includes: data.nds_includes});
    link.total.html(Price.priceSet(p.total));
    link.total_no_nds.html(Price.priceSet(p.total - p.nds));
}

function getNdsSum(id){
    let nds = $('[data-id="'+id+'"]').children(".nds");
    const data = get_data_price(id);
    $(nds).html(Price.priceSet(data.nds));
    return data.nds;
}

function getNds(count, edit, typeNds){
    const nds_includes = +$("#includesNds").prop("checked");
    const price = new Price;
    const p = price.get_all({price: edit, count, nds_proc: typeNds, nds_includes});
    return p.nds;
}

function saveDataToInp(id){
    const data = collection_fields(id);
    
    let json = $("#saveData").val();
    if(json.length > 0){
        json = JSON.parse(json);
    } else {
        json = {};
    }
    
    if((id in json) === false){
        json[id] = {};
    }
    json[id]["count"] = data.count;
    json[id]["edit"] = data.price_now;
    json[id]["total"] = data.total;
    json[id]["nds"] = data.nds;
    json[id]["typeNds"] = data.nds_proc;
    
    json = JSON.stringify(json);
    $("#saveData").val(json);
}

function setAllTotal(){
    let total = $(".total");
    let totalAll = 0;
    for(let i=0;i<total.length;i++){
        totalAll += +$(total[i]).text().replace(/ /g, "").replace("руб.", "");
    }
    totalAll = totalAll.toFixed(2);
    console.log(totalAll)
    $(".allTotal").html("<b>"+Price.priceSet(totalAll)+"</b>");
}

function setAllNds(){
    let nds = $(".nds");
    let ndsAll = 0;
    for(let i=0;i<nds.length;i++){
        ndsAll += +$(nds[i]).text().replace(/ /g, "").replace("руб.", "");
    }
    ndsAll = ndsAll.toFixed(2);
    $(".allNds").html("<b>"+Price.priceSet(ndsAll)+"</b>");
}


function check_fcontrol(){
    return new Promise((resolve, reject) => {
        let id = $("#mainId").val();
        $.post("/ajax.php?do=check_supply_order_for_fcontrol", {id}, function(data){
            if(data === ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function send_fcontrol(){
    const data = $("#reconcileInvoiceModalForm").serialize();
    $.post("/ajax.php?do=send_supply_order_for_control", data, function(result){
        if(result == ""){
            swal("Успешно", "Данные успешно отправлены", "success");
        } else {
            swal("Ошибка", result, "error");
        }
    });
}

function viewReserv(){
    $("#viewReservModalBody").html("Загрузка данных...");
    $("#viewReservModal").modal("show");
    let id = $("#mainId").val();
    $.post("/ajax.php?do=getListReservSupplyOrder", {id:id}, function(data){
        $("#viewReservModalBody").html(data);
    });
}

function addFile(){
    $("#addFileModal").modal("show");
}

function addFileModalBtn(){
    let id = $("#mainId").val();
    let data = $("#addFileModalForm").serialize();
    $("#files3").html(`<li class="text-muted text-center empty">Файлы не загружены.</li>`);
    $("#debug3").empty();
    $.post("/ajax.php?do=loadFileSupplyOrder", "supply_id="+id+"&"+data, function(){
        updateFile();
    });
}

function updateFile(){
    let id = $("#mainId").val();
    $.post("/administration/module/supply/order_detail_load_file/"+id, function(data){
        $("#conToFile").html(data);
    });
}

function checkZeroSum(msg){
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=checkZeroSumSupplyOrder", msg, function(data){
            if(data === ""){
                resolve();
            } else {
                reject(data);
            }
        });
    });
}

function price_active_change(price_id){
    $.post("/ajax.php?do=price_active_change_supply_order", {price_id}, function(){
        
    });
}

async function check_can_delete(id){
    const form = new FormData;
	const id_array = Array.isArray(id) ? id : [id];
	id_array.forEach(id => {
		form.append("id[]", id);
	})
    form.append("module", "supply_order");
	return await( await fetch(`/administration/module/supply/check_can_delete`, {
		method: "POST",
		body: form,
	}) ).json();
}

async function deleteSubModal(id){
    const lw = new LW;
    lw.on();
    const ccd = await check_can_delete(id);
    lw.off();
    if(ccd.status){
        $('#modal-form-'+id).modal('show');
    } else {
        error_modal_show_move(ccd.data);
    }
}

function error_modal_show_move(data){
    swal({
        title: "Внимание!",
          text: data.error,
          icon: "warning",
        buttons : {
            go: {
                text: "Перейти",
                value: true,
                visible: true,
                className: "",
                closeModal: true,
            },
            cancel: {
                text: "Закрыть",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        }
    }).then((go) => {
        if(go){
            window.open(data.link);
        }
    });
}

async function deleteSub(This){
    const id = $(This).data("id");
    await delete_tov(id);
    $("body").removeClass("modal-open");
}

async function delete_tov(id){
    const data = await del_tov(id);
    if(data.error.length > 0){
        const p = [];
        data.error.forEach((value, index, array) => {
            p.push(`<div>${value}</div>`);
        });
        const error = p.join("");
        const content = document.createElement("div");
        content.innerHTML = error
        swal({
            title: "Ошибка!", 
            icon: "error",
            content,
        });
    }
    await update_body();
}

async function del_tov(id){
    const data = await $.post("/administration/module/supply/ajax/order_divece_del", {id});
    return JSON.parse(data);
}

function update_body(data = {}){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        const stock_ndr = 'stock_ndr' in data ? data.stock_ndr : $("#stock_ndr").editable('getValue', true);
        const view_load = "view_load" in data ? data.view_load : true;
        
        const lw = new LW;
        if(view_load) lw.on();
        
        $.post("/administration/module/supply/ajax/order_detail_list/"+id, {stock_ndr}, function(data){
            $("#tov_list").html(data);
            updateBodyFunc();
            if(view_load) lw.off();
            resolve();
        });
    });
}

function update_head(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        const logistic = get_logistic();
        $.post("/administration/module/supply/order_detail_head/"+id, {logistic}, function(data){
            $("#admin-form").html(data);
            updateHeadFunc();
            resolve();
        });
    });
}

function get_logistic(){
    const value = $("#logistic").val();
    if(value){
        return true;
    } else {
        return false;
    }
}

function updateHeadFunc(){
    x_bill_code();
    x_stock_ndr();
    x_date_bill();
    x_date_work();
    x_date_ship();
    x_date_end();
}

function updateBodyFunc(){
    x_date_ship_detail();
    x_date_end_detail();
}

function autoPriceModal(id){
    swal({
        title: "Вы уверены?",
        text: "Автоматическая установка цены на такую же номенклатуру",
        icon: "info",
        buttons: {
            confirm: {
                text: "Выполнить",
                value: true,
                visible: true,
                className: "",
                closeModal: true
            },
            cancel: {
                text: "Закрыть",
                value: false,
                visible: true,
                className: "",
                closeModal: true,
            },
        },
      })
      .then((willAuto) => {
        if (willAuto) {
            const count = autoPrice(id);
            swal("Успешно", `Цены проставлены на ${count} поз.!`, "success");
        }
      });
}

function get_data(id){
    const data = {};
    const elem = $("#tr_"+id);

    data.id = id;
    data.cid = elem.data("cid");
    data.supply_d = elem.data("supply_d");

    data.total = string_to_number(elem.find(".total").text());
    data.count = string_to_number(elem.find(".count").text());
    data.edit = string_to_number(elem.find(".edit").text());
    data.typeNds = string_to_number(elem.find(".typeNds").text());
    data.nds = string_to_number(elem.find(".nds").text());

    return data;
}

function set_data(id, selector, value){
    const elem = $("#tr_"+id);
    elem.find(selector).html(value);
}

function change_price(id, value){
    set_data(id, ".edit", priceSet(value));
    getTotal(id);
    saveDataToInp(id);
}

function autoPrice(id){
    let count = 0;
    const mainElem = get_data(id);
    $("#tov_list").find(".tov_item").each((i, el) => {
        const childId = $(el).data("id");
        const childElem = get_data(childId);
        if(childElem.cid == mainElem.cid && childElem.id != mainElem.id){
            count++;
            change_price(childElem.id, mainElem.edit);
        }
    });
    setAllTotal();
    setAllNds();
    return count;
}

async function universl_sub(){
    const type = $("#act").val();
    const id = get_checks();
    const lw = new LW;
    switch(type){
        case "del":
            lw.on();
            const ccd = await check_can_delete(id);
            lw.off();
            if(ccd.status){
                universl_sub_del(id);
            } else {
                error_modal_show_move(ccd.data);
            }
        break;
        case "mas_date_ship":
            mas_date_change_modal.open("date_ship");
        break;
        case "mas_date_end":
            mas_date_change_modal.open("date_end");
        break;
        case "change_type_nds":
            change_type_nds(id);
        break;
    }
}

function change_type_nds(id){
    const modal = $("#change_type_nds_modal");
    modal.modal("show");
    const btn = $("#change_type_nds_modal_button");
    btn.off();
    btn.on("click", function(){
        const new_value = $("#change_type_nds_modal_select option:selected").text();
        id.forEach(value => {
            changeNdsTypeValue(value, new_value);
        })
        modal.hide();
        $("body").removeClass("modal-open");
    });
}

function get_checks(){
    const checks = $(".checks:checked");
    const id = [];
    checks.each((i, elem) => {
        const id_ = $(elem).val();
        if(id_ != "on"){
            id.push(id_);
        }
    });
    return id;
}

function universl_sub_del(id){
    swal({
        title: "Вы уверены?",
        text: "Удалить выбранные позиции",
        icon: "info",
        buttons: {
            confirm: {
                text: "Удалить",
                value: true,
                visible: true,
                className: "",
                closeModal: true
              },
            cancel: {
                text: "Отмена",
                value: null,
                visible: true,
                className: "",
                closeModal: true,
              },
        },
      })
      .then(async (willDelete) => {
        if (willDelete) {
            await delete_tov(id);
        } 
      });
}

const mas_date_change_modal = {
    name: "mas_date_change_modal",
    input: "mas_date_change_modal_date",
    btn: "mas_date_change_modal_btn",
    open(field){
        $(`#${this.name}`).modal("show");
        const inp = $(`#${this.input}`);
        inp.val("");
        const btn = $(`#${this.btn}`);
        btn.off("click");
        btn.on("click", () => {
            this.close();
            const id = get_checks();
            update_supply_order_detail(id, field, inp.val()).then(() => {
                update_body();
            });
        });
    },
    close(){
        $(`#${this.name}`).modal("hide");
    },

}

function view_count_receipts(id){
    const body = $("#receipts_view_modal_body");
	body.html("Загрузка поступлений");
	$("#receipts_view_modal").modal("show");
	$.post("/ajax.php?do=get_receipts_supply", {id}, function(data){
		body.html(data);
	});
}

function show_stock(){
    const org = $("#org").val();
    $(".org_opt").hide();
    const org_opt = $(".org_"+org);
    org_opt.show();
    const value = org_opt.eq(0).val();
    console.log(value);
    $("#stock").val(value);
    const btn = $("#confirmRes");
    if(value == undefined){
        btn.prop("disabled", true);
    } else {
        btn.prop("disabled", false);
    }
}

function remove_control_send_modal(){
    swal({
        title: "Вы уверены?",
        text: "Удалить уведомление",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            const id = $("#mainId").val();
            update_supply_order(id, "control_send", null).then(() => {
                update_head().then(() => {
                    lw.off();
                    swal("Успешно!", "Вы свободны!", "success");
                });
            });
          
        }
      });
}

function update_supply_order(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/supply/update_supply_order", {id, field, value}, function(){
            resolve();
        });
    });
}

function update_supply_order_detail(id, field, value){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/supply/update_supply_order_detail", {id, field, value}, function(){
            resolve();
        });
    });
}

function closeOrder(){
    swal({
        title: "Вы уверены?",
        text: "Закрыть счёт",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then(async (willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            await update_supply_order($("#mainId").val(), "close", 1);
            await update_head();
            lw.off();
        }
      });
}
function restoreOrder(){
    swal({
        title: "Вы уверены?",
        text: "Открыть счёт",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then(async (willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            await update_supply_order($("#mainId").val(), "close", 0);
            await update_head();
            lw.off();
        }
      });
}

async function detachFromSale(element, id){
    const result = await swal({
        title: "Вы уверены?",
        text: "Отвязать от продажи",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    if(result){
        const lw = new LW;
        try {
            lw.on();
            const form = new FormData;
            form.append("id", id);
            const data = await ( await fetch("/administration/module/supply/detach_from_sale", {
                method: "POST",
                body: form,
            }) ).json();
            element.remove();
        } catch (error) {
            alert(error.message);
        } finally{
            lw.off();
        }
    }
}