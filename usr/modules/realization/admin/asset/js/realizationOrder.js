// $(function(){
//     $("#deliveryModalForm").on("submit", async function(e){
//         e.preventDefault();
//         $("#deliveryModal").modal("hide");
//         const request = $(this).serialize();
//         const lw = new LW;
//         lw.on();
//         await delivery_add(request).then(data => {
//             const realization_id = $("#mainId").val();
//             updateTovList(realization_id);
//             if(data.count > 0){
//                 swal("Успешно!", "Заявка успешно отправлена", "success");
//             } else {
//                 if(data.error_message){
//                     swal("Внимание!", data.error_message, "warning");
//                 } else {
//                     swal("Внимание!", "Позиции не были отправлены", "warning");
//                 }
//             }
//         }, error => {
//             swal("Ошибка!", error, "error");
//         });
//         lw.off();
//     });
//     $("#delivery_type").on("change", function(){
//         const type = +$(this).val();
//         const delivery_price_div = $("#delivery_price_div");
//         const delivery_price = $("#delivery_price");
//         switch(type){
//             default:
//                 delivery_price_div.show();
//                 delivery_price.val("");
//             break;
//             case 3:
//                 delivery_price_div.hide();
//                 delivery_price.val(0);
//             break;
//         }
//     });
// });

$(function(){
    updateHeadFunc();
    $("#tov_list").sortable({
        update: function( event, ui ) {
          const position = $(".position");
          const json = {};
          position.each(function(index) {
            const i = index + 1;
                $(this).html(i);
            json[ $(this).data("id") ] = i;
          });
          const res = JSON.stringify(json);
          $.post("ajax.php?do=setPositionRealization", {res});
        },
        axis: "y",
        handle: ".position"
      });
});

function updateTovList(realization_id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/realization/ajax/order_tovar_list/"+realization_id, function(data){
            $("#tov_list").html(data);
            resolve();
        });
    });
}

function updateHead(){
    return new Promise((resolve, reject) => {
        let id = $("#mainId").val();
        $.post("/administration/module/realization/ajax/order_data/"+id, function(data){
            $("#orderData").html(data);
            updateHeadFunc();
        });
    });
}

function updateHeadFunc(){
    x_comment();
}

function x_comment(){
    $('#comment').editable({
        type: 'textarea',
        pk: $("#mainId").val(),
        url: '/ajax.php?do=fastChangeRealization',
        emptytext: "Не указано",
    });
}

    let flagChange = false;
    $(document).on("click", "#torg12", function(){
        //console.log("~");
        flagChange = false;
        let mainId = window.location.href.split("/");
        mainId = mainId[mainId.length - 1];
        $.post("/ajax.php?do=torg12Realization", {id:mainId}, function(data1){
            let json = JSON.parse(data1);
            $("#base_doc").val(json["base"]["doc"]);
            $("#base_numb").val(json["base"]["numb"]);
            $("#base_date").val(json["base"]["date"]);

            $.post("/ajax.php?do=torg12Data", {id:mainId}, function(data0){
                let json0 = JSON.parse(data0);

                let torg12 = localStorage.getItem("torg12");
                if(torg12 === null){
                    torg12 = false;
                } else {
                    torg12 = JSON.parse(torg12);
                }

                $("#sales_id").val(json0["sales_id"]);

                $("#oid_address").html('<option value="'+json0["oid"]["address"]+'">'+json0["oid"]["address"]+'</option>');

                $("#oid_id").val(json0["oid"]["id"]);

                $("#org_id").val(json0["org"]["id"]);

                if(json0["base"]["doc"].length > 0){
                    $("#base_doc").val(json0["base"]["doc"]);
                } else {
                    $("#base_doc").val(json["base"]["doc"]);
                }

                if(json0["base"]["numb"].length > 0){
                    $("#base_numb").val(json0["base"]["numb"]);
                } else {
                    $("#base_numb").val(json["base"]["numb"]);
                }

                let base_date;
                if(json0["base"]["date"].length > 0){
                    base_date = json0["base"]["date"];
                } else {
                    base_date = json["base"]["date"];
                }
                $("#base_date").val(base_date);

                //Счет покупателя:
                $("#oid_bank_id").empty();
                $("#oid_bank_data").empty();
                let selFlag = true;
                for(let key in json0["oid"]["bank"]["id"]){
                    let elem = json0["oid"]["bank"]["id"][key];
                    let selected = "";
                    if(key === json["oid"]["bank"]["id"]){
                        selected = "selected";
                        selFlag = false;
                    }
                    $("#oid_bank_id").append('<option value="'+key+'" '+selected+'>'+elem+'</option>');
                }
                $("#oid_bank_id").prepend('<option value="" '+(selFlag ? 'selected' : '')+' disabled>Выберите счет продавца</option>');
                if(!selFlag){
                    $("#oid_bank_data").val(json["oid"]["bank"]["data"]);
                }
    
                //Счет организации:
                selFlag = true;
                $("#org_bank_id").empty();
                $("#org_bank_data").empty();
                for(let key in json0["org"]["bank"]["id"]){
                    let elem = json0["org"]["bank"]["id"][key];
                    let selected = "";
                    if(key === json["org"]["bank"]["id"]){
                        selected = "selected";
                        selFlag = false;
                    }
                    $("#org_bank_id").append('<option value="'+key+'" '+selected+'>'+elem+'</option>');
                }
                $("#org_bank_id").prepend('<option value="" '+(selFlag ? 'selected' : '')+' disabled>Выберите счет покупателя</option>');
                if(!selFlag){
                    $("#org_bank_data").val(json["org"]["bank"]["data"]);
                }

                //Подпись руководителя
                selFlag = true;
                let torg12Flag = false;
                $("#sign_leader_id").empty();
                for(let key in json0["sign"]["leader"]["data"]){
                    let elem = json0["sign"]["leader"]["data"][key];
                    let selected = "";
                    if(torg12 && json["sign"]["leader"]["id"] == ""){
                        if(key === torg12["sign_leader_id"]){
                            selected = "selected";
                            selFlag = false;
                            torg12Flag = true;
                        }
                    } else {
                        if(key === json["sign"]["leader"]["id"]){
                            selected = "selected";
                            selFlag = false;
                        }
                    }
                    
                    $("#sign_leader_id").append('<option value="'+key+'" '+selected+'>'+elem+'</option>');
                }
                $("#sign_leader_id").prepend('<option value="" '+(selFlag ? 'selected' : '')+' disabled>Выберите руководителя</option>');
                if(torg12Flag){
                    $("#sign_leader_data").val(torg12["sign_leader_data"]);
                } else if(!selFlag){
                    $("#sign_leader_data").val(json["sign"]["leader"]["data"]);
                }

                //Подпись бухгалтера
                selFlag = true;
                torg12Flag = false;
                $("#sign_buch_id").empty();
                for(let key in json0["sign"]["buch"]["data"]){
                    let elem = json0["sign"]["buch"]["data"][key];
                    let selected = "";
                    if(torg12 && json["sign"]["buch"]["id"] == ""){
                        if(key === torg12["sign_buch_id"]){
                            selected = "selected";
                            selFlag = false;
                            torg12Flag = true;
                        }
                    } else {
                        if(key === json["sign"]["buch"]["id"]){
                            selected = "selected";
                            selFlag = false;
                        }
                    }
                    
                    $("#sign_buch_id").append('<option value="'+key+'" '+selected+'>'+elem+'</option>');
                }
                $("#sign_buch_id").prepend('<option value="" '+(selFlag ? 'selected' : '')+' disabled>Выберите главного бухгалтера</option>');
                if(torg12Flag){
                    $("#sign_buch_data").val(torg12["sign_buch_data"]);
                } else if(!selFlag){
                    $("#sign_buch_data").val(json["sign"]["buch"]["data"]);
                }

                //Груз отпустил
                selFlag = false;
                let data = {};
                if(torg12){
                    data = {
                        id: torg12["sign_ship_id"],
                        text: torg12["sign_ship_data"]
                    };
                    selFlag = true;
                }
                if(json["sign"]["ship"]["data"].length > 0){
                    data = {
                        id: json["sign"]["ship"]["id"],
                        text: json["sign"]["ship"]["data"]
                    };
                    selFlag = true;
                }
                if(selFlag){
                    let newOption = new Option(data.text, data.id, false, false);
                    $('#sign_ship_id').append(newOption).trigger('change');
                }
                if(torg12 && json["sign"]["ship"]["post"] == ""){
                    $("#sign_ship_post").val(torg12["sign_ship_post"]);
                } else{
                    $("#sign_ship_post").val(json["sign"]["ship"]["post"]);
                }
                
                flagChange = true;

                $("#torg12ModalBody").removeClass("loadData");
            });
        });
        
        $("#torg12ModalBody").addClass("loadData");
        $("#torg12Modal").modal("show");
    });

    $(document).on("change", "#sign_ship_id", function(){
        if(flagChange){
            $("#sign_ship_post").prop("disabled", true);
            let id = $(this).val();
            let mainId = window.location.href.split("/");
            mainId = mainId[mainId.length - 1];
            $.post("/ajax.php?do=getPostOrgTorg", {id:id, realization_id:mainId}, function(data){
                $("#sign_ship_post").prop("disabled", false);
                $("#sign_ship_post").val(data);
            });
        }
    });

    $(document).on("change", "#sign_leader_id", function(){
        let value = $(this).find("option:selected").text();
        $("#sign_leader_data").val(value);
    });

    $(document).on("change", "#sign_buch_id", function(){
        let value = $(this).find("option:selected").text();
        $("#sign_buch_data").val(value);
    });

    $(document).on("click", "#torg12ModalSaveBtn", function(){
        const data = $("#torg12Form").serialize();
        const saveData = $("#torg12Form").serializeArray();
        let torg12 = {
            sign_leader_id: "",
            sign_leader_data: "",
            sign_buch_id: "",
            sign_buch_data: "",
            sign_ship_id: "",
            sign_ship_data: "",
            sign_ship_post: "",
        };
        saveData.forEach(elem => {
            if(elem.name in torg12){
                torg12[elem.name] = elem.value;
            }
        });
        let torg12json = JSON.stringify(torg12);
        localStorage.setItem("torg12", torg12json);

        $.post("/ajax.php?do=torg12Save", data, function(){

        });
    });

    $(document).on("change", "#oid_bank_id", function(){
        let value = $(this).find("option:selected").text();
        $("#oid_bank_data").val(value);
    });

    $(document).on("change", "#org_bank_id", function(){
        let value = $(this).find("option:selected").text();
        $("#org_bank_data").val(value);
    });

    $(document).on("change", "#sign_ship_id", function(){
        let value = $(this).find("option:selected").text();
        $("#sign_ship_data").val(value);
    });




    
    $(document).on("keydown", ".rubber", function(event){
        $(this).width(($(this).val().length + 4)*8);
        if(event.which == 13)
        {
            $(this).blur();
        }
    });

    function get_link(id){
        const result = {};
        const item = $("#tr_"+id);
        result.item = item;
        result.count = item.children(".count");
        result.edit = item.children(".edit");
        result.total = item.children(".total");
        result.nds = item.children(".nds");
        result.typeNds = item.children(".typeNds");
        result.discount = item.children(".discount");
        result.includesNds = $("#includesNds");
        result.s_name = item.find(".s_name");

        return result;
    }

    function get_data(id){
        const result = {};
        const link = get_link(id);

        result.count = Price.string_to_number(link.count.text());
        result.edit = Price.string_to_number(link.edit.text());
        result.total = Price.string_to_number(link.total.text());
        result.nds = Price.string_to_number(link.nds.text());
        result.typeNds = Price.string_to_number(link.typeNds.text());
        result.discount = Price.string_to_number(link.discount.text());
        result.includesNds = link.includesNds.prop("checked");
        result.s_name = link.s_name.text().trim();

        return result;
    }

    function get_all_price(id){
        const data = get_data(id);
        const price = new Price;
        const result = price.get_all({
            price: data.edit,
            count: data.count,
            discount: data.discount,
            nds_proc: data.typeNds,
            nds_includes: data.includesNds,
        });
        return result;
    }
    
    function saveDataToInp(id){
        const data = get_data(id);
        // let count = $('[data-id="'+id+'"]').children(".count").text().replace(/ /g, "");
        // let edit = $('[data-id="'+id+'"]').children(".edit").text().replace(" руб.", "").replace(/ /g, "");
        // let total = $('[data-id="'+id+'"]').children(".total").text().replace(" руб.", "").replace(/ /g, "");
        // let nds = $('[data-id="'+id+'"]').children(".nds").text().replace(" руб.", "").replace(/ /g, "");
        // let typeNds = $('[data-id="'+id+'"]').children(".typeNds").text().replace("%", "").replace(/ /g, "");
        // let discount = $('[data-id="'+id+'"]').children(".discount").text().replace("%", "").replace(/ /g, "");
        
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
        json[id]["edit"] = data.edit;
        json[id]["total"] = data.total;
        json[id]["nds"] = data.nds;
        json[id]["typeNds"] = data.typeNds;
        json[id]["discount"] = data.discount;
        json[id]["s_name"] = data.s_name;
        
        json = JSON.stringify(json);
        $("#saveData").val(json);
    }
    
    function getTotal(id){
        // let count = $('[data-id="'+id+'"]').children(".count").text();
        // //console.log(count);
        // let edit = $('[data-id="'+id+'"]').children(".edit").text().replace(" руб.", "").replace(/ /g, "");
        // let total = $('[data-id="'+id+'"]').children(".total");
        // let nds = $('[data-id="'+id+'"]').children(".nds").text().replace(" руб.", "").replace(/ /g, "");
        // let typeNds = $('[data-id="'+id+'"]').children(".typeNds").text().replace("%", "").replace(/ /g, "");
        // let discount = $('[data-id="'+id+'"]').children(".discount").text().replace("%", "").replace(/ /g, "");
        // discount /= 100;
        // let totalCalc = 0;
        // if($("#includesNds").prop("checked")){
        //     totalCalc = count * edit - (count * edit * discount);
        //     } else {
        //     totalCalc = (count * edit + +nds) - ((count * edit + +nds) * discount);
        // }
        // totalCalc = totalCalc.toFixed(2);
        const link = get_link(id);
        const price = get_all_price(id);
        
        $(link.total).html(Price.priceSet(price.total).replace(".", ","));
    }
    
    function getEdit(id){
        let total = $('[data-id="'+id+'"]').children(".total").text().replace(" руб.", "").replace(/ /g, "");
        let count = $('[data-id="'+id+'"]').children(".count").text().replace(/ /g, "");
        let edit = $('[data-id="'+id+'"]').children(".edit");
        
        let editCalc = (total / count).toFixed(2);
        
        $(edit).html(Price.priceSet(editCalc));
    }
    
    function getNdsSum(id){
        const link = get_link(id);
        const price = get_all_price(id);
        $(link.nds).html(Price.priceSet(price.nds).replace(".", ","));
    }
    
    function setAllTotal(){
        let total = $(".total");
        let totalAll = 0;
        for(let i=0;i<total.length;i++){
            totalAll += Price.string_to_number($(total[i]).text());
        }
        totalAll = totalAll.toFixed(2);
        $(".allTotal").html(Price.priceSet(totalAll).replace(".", ","));
    }
    
    function setAllNds(){
        let nds = $(".nds");
        let ndsAll = 0;
        for(let i=0;i<nds.length;i++){
            ndsAll += Price.string_to_number($(nds[i]).text());
        }
        ndsAll = ndsAll.toFixed(2);
        $(".allNds").html("<b>"+Price.priceSet(ndsAll).replace(".", ",")+"</b>");
    }

    
    /*
    $(document).on("click", ".UPB", function(){
        $(this).addClass("UPBChange");
        $(this).removeClass("UPB");
        let text = $(this).text().replace(/ /g, "");
        //что убирать
        let rep = $(this).data("replace");
        if(rep != undefined){
            if(rep.length > 0){
                rep = rep.split(",");
                for(let i=0;i<rep.length;i++){
                    if(rep[i] != ''){
                        text = $(this).text().replace(rep[i], "");
                    }
                }
            }
        }
        $(this).html('<input type="number" id="input" class="form-control rubber" value="'+text+'" style="width: ' +((text.length + 6) * 8)+ 'px">');
        $("#input").focus();
        $("#input").select();
    });
    
    $(document).on("blur", "#input", function(){
        let id = $(this).parent().parent().data("id");
        let value = $(this).val();
        $(".UPBChange").html(value);
        
        let func  = $(".UPBChange").data("func");
        if(func != undefined){
            if(func.length > 0){
                func = func.split(",");
                for(let i=0;i<func.length;i++){
                    if(func[i] != ""){
                        switch(func[i]){
                            case "getTotal":
                            getTotal(id);
                            break;
                            case "saveDataToInp":
                            saveDataToInp(id);
                            break;
                            case "setAllTotal":
                            setAllTotal();
                            break;
                            case "setAllNds":
                            setAllNds();
                            break;
                            case "getEdit":
                            getEdit(id);
                            break;
                            case "getNdsSum":
                            getNdsSum(id);
                            break;
                        }   
                    }
                }
            }
        }
        $(".UPBChange").addClass("UPB");
        $(".UPBChange").removeClass("UPBChange");
    });
    */
    
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
        
        getNdsSum(id);
        getTotal(id);
        setAllTotal();
        setAllNds();
        saveDataToInp(id);
    });

    //цена
    $(document).on("click", ".edit", function(){
        //console.log("~");
        $(this).addClass("editInp");
        $(this).removeClass("edit");
        const value = Price.string_to_number($(this).text());
        $(this).html('<input type="number" id="editInp" class="form-control rubber" value="'+value+'" style="width: ' +((value.length + 6) * 8)+ 'px">');
        $("#editInp").focus();
        $("#editInp").select();
    });
    
    $(document).on("blur", "#editInp", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let edit = $(this).val();
        $(".editInp").html(Price.priceSet(edit).replace(".", ","));
        $(".editInp").addClass("edit");
        $(".edit").removeClass("editInp");
        
        getNdsSum(id);
        getTotal(id);
        setAllTotal();
        setAllNds();
        saveDataToInp(id);
    });
    
    
    //сумма
    // $(document).on("click", ".total", function(){
    //     //console.log("~");
    //     $(this).addClass("totalInp");
    //     $(this).removeClass("total");
    //     const value = Price.string_to_number($(this).text());
    //     $(this).html('<input type="number" id="totalInp" class="form-control rubber" value="'+value+'" style="width: ' +((value.length + 6) * 8)+ 'px">');
    //     $("#totalInp").focus();
    //     $("#totalInp").select();
    // });
    
    // $(document).on("blur", "#totalInp", function(){
    //     let id = $(this).parent().parent().data("id");
    //     //console.log(id);
    //     let total = $(this).val();
    //     $(".totalInp").html(Price.priceSet(total).replace(".", ","));
    //     $(".totalInp").addClass("total");
    //     $(".total").removeClass("totalInp");
        
    //     getNdsSum(id);
    //     //getEdit(id);
    //     setAllTotal();
    //     setAllNds();
    //     saveDataToInp(id);
    // });
    
    //НДС
    $(document).on("click", ".nds", function(){
        //console.log("~");
        $(this).addClass("ndsInp");
        $(this).removeClass("nds");
        const value = Price.string_to_number($(this).text());
        $(this).html('<input type="number" id="ndsInp" class="form-control rubber" value="'+value+'" style="width: ' +((value.length + 6) * 8)+ 'px">');
        $("#ndsInp").focus();
        $("#ndsInp").select();
    });
    
    $(document).on("blur", "#ndsInp", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let nds = $(this).val();
        $(".ndsInp").html(Price.priceSet(nds).replace(".", ","));
        $(".ndsInp").addClass("nds");
        $(".nds").removeClass("ndsInp");
        
        
        setAllTotal();
        setAllNds();
        saveDataToInp(id);
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
        
        getNdsSum(id);
        getTotal(id);
        setAllTotal();
        setAllNds();
        saveDataToInp(id);
    });
    
    //скидка
    $(document).on("click", ".discount", function(){
        //console.log("~");
        $(this).addClass("input");
        $(this).removeClass("discount");
        $(this).html('<input type="number" id="input" class="form-control rubber" value="'+$(this).text().replace(" руб.", "").replace(/ /g, "").replace("%", "")+'" style="width: ' +(($(this).text().replace("%", "").replace(/ /g, "").length + 6) * 8)+ 'px">');
        $("#input").focus();
        $("#input").select();
    });
    
    $(document).on("blur", "#input", function(){
        let id = $(this).parent().parent().data("id");
        //console.log(id);
        let value = $(this).val();
        $(".input").html(value+"%");
        $(".input").addClass("discount");
        $(".input").removeClass("input");
        
        getNdsSum(id);
        getTotal(id);
        setAllTotal();
        setAllNds();
        saveDataToInp(id);
    });
   
    
    //сохранение
    $(document).on("click", "#saveReceipt", function(){
        let saveData = $("#saveData").val();
        let mainId = window.location.href.split("/");
        mainId = mainId[mainId.length - 1];
        
        let tr = $("#tov_list").find("tr");
        let totalAll = 0;
        let ndsAll = 0;
        for(let key = 0; key < tr.length; key++){
            let nds = Price.string_to_number($(tr[key]).children(".nds").text());
            let total = Price.string_to_number($(tr[key]).children(".total").text());
            totalAll += +total;
            ndsAll += +nds;
        } 
        totalAll = totalAll.toFixed(2);
        ndsAll = ndsAll.toFixed(2);
        $.post("/ajax.php?do=updateProductsRealization", {saveData, mainId, totalAll, ndsAll}, function(data){
            notif('success', 'Информация', 'Данные успешно сохранены!');
            console.log(data);
        });
    });
    
    
    //Склад
    function getSource(realization_id) {
        var url = "/ajax.php?do=getStockList";
        return $.ajax({
            type: 'POST',
            url: url,
            data: {id:realization_id},
        });
    }
    
    $(document).on('click', '#stockname', function(){
        const realization_id = $("#mainId").val();
        getSource(realization_id).done(function(result){
            $('#stockname').editable({
                type: 'select',
                name: "stock_id",
                pk: $("#mainId").val(),
                url: '/ajax.php?do=fastChangeRealization',
                ajaxOptions:{
                    success: function(){
                        updateHead();
                        updateTovList($("#mainId").val());
                    }
                },
                source: result,
            });
            $('#stockname').editable('show');
        });
    });

    //провести/распровести
    async function realization_end(action = "", realization_id = ""){
        if(realization_id == ""){
            realization_id = $("#mainId").val();
        }
        return await qqHelp(realization_id, action);
    }

    async function qqHelp(realization_id){
        const lw  = new LW;
        lw.on();
        try {
            const form = new FormData;
            form.append("realization_id", realization_id);
            const dd = await fetch("/ajax.php?do=realization_active_ajax", {
                method: "POST",
                body: form,
            });
            if(!dd.ok){
                throw new Error(await dd.text())
            }
            switch(await dd.text()){
                case '1':
                    notif("success", "Информация", "Документ поступления успешно проведен");
                    updateHead();
    
                    $(".elemHide").hide();
                break;
                case '2':
                    notif("system", "Информация", "Проведение документа поступления успешно отменено");
                    updateHead();
    
                    $(".elemHide").show();
                break;
                case '3':
                    notif("danger", "Ошибка", "Что-то пошло не так...");
                break;
                case '4':
                    notif("danger", "Ошибка", "Чтобы провести поступление необходимо приложить оригинал документа поступления!");
                break;
                case "5":
                    notif("danger", "Ошибка", "Количество проводимого превышает допустимое значение");
                break
            }
            await updateTovList(realization_id);
        } catch (error) {
            notif("danger", "Ошибка", error.message);
        } finally{
            lw.off();
        }
    }
    
    $(document).on("click", ".delete", function(){
        let id = $(this).data("id");
        let mainId = location.href.split("/");
        mainId = mainId[mainId.length-1];
        $.post("/ajax.php?do=delRealizationOrder", {id:id, mainId:mainId}, function(){
            $.post("/administration/module/realization/ajax/order_tovar_list/"+mainId, function(data){
                $("#tov_list").html(data);
            });
        });
    });

    $(document).on("click", "#dateMain", async function(){
        let mainId = location.href.split("/");
        mainId = mainId[mainId.length-1];

        const data = await get_realization_data();
        const dateMain = $("#dateMain");

        if(data.active == 0){
            dateMain.editable({
                type: "date",
                format: 'yyyy-mm-dd',    
                viewformat: 'dd.mm.yyyy',    
                datepicker: {
                        weekStart: 1
                },
                pk: mainId,
                url: "/ajax.php?do=changeRealizationOrder",
                name: "date",
            });
    
            dateMain.editable("show");
        } else {
            dateMain.editable('disable');
            swal("Ошибка!", "Распроведите реализацию", "error");
        }
    });
    
    $(document).on("click", "#syncSales", function(){
        let status = +$(this).data("status");
        if(status === 1){
            swal("Ошибка!", "Прежде чем синхронизировать реализацию, распроведите заявку", "error");
        } else {
            $("#syncSalesModal").modal("show");
        }
    });

    $(document).on("click", ".syncSalesModalBtn", function(){
        swal("Успешно!", "Синхронизировано", "success");
        let type = $(this).data("type");
        let id = $("#mainId").val();
        $.post("/ajax.php?do=syncSales", {id:id, type:type}, function(){
            $.post("/administration/module/realization/ajax/order_tovar_list/"+id, function(data){
                $("#tov_list").html(data);
            });
        });
    });


function printModal(){
    if($("#distributeBlock").length > 0){
        modal_o('#modal-print');
    } else {
        swal("Ошибка", "Прежде чем распечатать, проведите заявку!", "error");
    }
}

function payModal(){
    $('#modal-pay').modal('show');
}

async function checkDelItem(request){
    return await ( await fetch(`/administration/module/realization/checkDelItem`, {
        method: "POST",
        body: request,
    }) ).json();
}

async function delModal(id){
    try {
        const request = new FormData;
        request.append("id", id);
        const check = await checkDelItem(request);
        if(check.status){
            $('#modal-form-'+id).modal('show');
            $('body').css('padding', 0);
        } else {
            swal("Ошибка", check.error[0], "warning");
        }
    } catch (error) {
        swal("Ошибка", error.message, "error");
    }
}

$(document).on("click", "#addFile", function(){
    $("#addFileModal").modal("show");
});

$(document).on("click", "#addFileModalBtn", function(){
    let data = $("#addFileModalForm").serialize();
    let id = $("#mainId").val();
    $.post("/ajax.php?do=saveFileRealization", "id="+id+"&"+data, function(){
        checkFileRealization();
        updateHead();
    });
});

$(document).on("click", ".delFile", function(){
    let id = $(this).data("id");
    $("#delFileModal").modal("show");
    $("#delFileModalBtn").data("id", id);
});

$(document).on("click", "#delFileModalBtn", function(){
    let id = $(this).data("id");
    let mainId = $("#mainId").val();
    $.post("/ajax.php?do=delFileRealization", {id:id, mainId:mainId}, function(){
        checkFileRealization();
        updateHead();
    });
});

function checkFileRealization(){
    let id = $("#mainId").val();
    $.post("/ajax.php?do=checkFileRealization", {id:id}, function(){

    });
}

function string_to_number(value){
    const new_value = Number(value.replace(/ /g, "").replace(/	/g, "").replace("руб.", "").replace("%", ""));
    return new_value;
}

function priceSet(data){
	return Number.prototype.toFixed.call(parseFloat(data) || 0, 2).replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ') + ' руб.';
}

function change_purchase_price(){
    const step_name = [
        "","Цена закупки","Сумма закупки","Разница",
    ];
    const name = $("#purchase_price_title");
    let step = name.data("step");
    step++;
    if(step > 3){
        step = 1;
    }
    name.data("step", step);
    name.html(step_name[step]);

    const tov_list = $("#tov_list");
    const tov_item = tov_list.find(".tov_item");

    let all_supply_total = 0;
    tov_item.each((i, elem) => {
        const count = $(elem).find(".count");
        const count_value = string_to_number(count.text());
        const purchase_price = $(elem).find(".purchasePrice");
        const purchase_price_value = purchase_price.data("price");
        const total = $(elem).find(".total");
        const total_value = string_to_number(total.text());
        switch(+step){
            case 1:
                purchase_price.html(priceSet(purchase_price_value));
                all_supply_total += +purchase_price_value * count_value;
            break;
            case 2:
                const new_value_2 = purchase_price_value * count_value;
                purchase_price.html(priceSet(new_value_2));
                all_supply_total += +new_value_2;
            break;
            case 3:
                const new_value_3 = purchase_price_value * count_value;
                const new_value_4 = total_value - new_value_3;
                purchase_price.html(priceSet(new_value_4));
                all_supply_total += +new_value_4;
            break;
        }
        $(".allTotalSupply").html(priceSet(all_supply_total));
    }); 
}

function end_modal(){
    $('#distributeModal').modal('show');
}

function correctRealizationModalError(){
    swal("Ошибка", "Сначала проведите текущую реализацию", "error");
}

function correctRealizationModal(){
    swal({
        title: "Создание корректировки реализации",
        text: "Вы точно хотите создать корректировку реализации?",
        icon: "info",
        buttons: {
            confirm: {
                text: "Создать",
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
    }).then(isCreate => {
        if(isCreate){
            correctRealization().then(id => {
                window.open('/administration/module/realization/correct_detail/'+id);
            });
        }
    });
}

function correctRealization(){
    return new Promise((resolve, reject) => {
        const id = $("#mainId").val();
        const request = $("#tablesForm").serialize();
        $.post("/administration/module/realization/correctRealization/"+id, request, function(data){
            resolve(data);
        });
    });
}

function request_send(){
    swal({
        title: "Вы уверены?",
        text: "Запрос реализации в минус",
        icon: "info",
        buttons: true,
      })
      .then((willSend) => {
        if (willSend) {
            const pid = $("#mainId").val();
            const lw = new LW;
            lw.on();
            request_god({module:"realization",pid}).then(() => {
                lw.off();
                swal("Успешно", "Запрос отправлен", "success");
            });
        }
      });
}

function get_checks_id(){
    const id = [];
    $(".checks:checked").each((i, item) => {
        const value = $(item).val();
        if(value != "on") id.push(value);
    });
    return id;
}

// async function deliveryModal(){
//     const data = await get_realization_data();
//     if(+data.active){
//         swal("Внимание!", "Распроведите реализацию для создания доставки!", "warning");
//     } else {
//         $("#deliveryModal").modal("show");
//     }
    
// }

$logisticsDelivery._open = async function(){
    const data = await get_realization_data();
    if(+data.active){
        swal("Внимание!", "Распроведите реализацию для создания доставки!", "warning");
    } else {
        this.modal.modal("show");
    }
}

// function delivery_add(request){
//     return new Promise((resolve, reject) => {
//         $.post("/administration/module/logistics/delivery/add", request, function(json){
//             try{
//                 const data = JSON.parse(json);
//                 resolve(data);
//             }catch(e){
//                 reject(`${e} (${json})`);
//             }
//         });
//     });
// }

async function get_realization_data(){
    const id = $("#mainId").val();
    const json = await $.post("/administration/module/realization/get_data/"+id);
    return JSON.parse(json);
}

$logisticsDelivery.after_send = async () => {
    const realization_id = $("#mainId").val();
    await updateTovList(realization_id);
}

$logisticsDelivery.after_cancel_delivery = async function(){
    await updateHead();
}

function hide_supply_price(t){
    const item = $(t);
    const name = "realization_supply_price";
    const realization_supply_price = $(`.${name}`);
    if(item.prop("checked")){
        Cookies.set(name, true);
        realization_supply_price.hide();
    } else {
        Cookies.remove(name);
        realization_supply_price.show();
    }
}

function RegisterCheck() {
    // Подготовка данных команды, параметры (TypeCheck = 0, NumDevice = 0, InnKkm = "", CashierName = "")
    var Data = KkmServer.GetDataCheck(0, 0, "4257896515", "Kазакова Н.А.", "430601071197");
    KkmServer_AddTextString(Data, "ООО Рога и копыта", 1);
    KkmServer_AddRegisterString(Data, "Сапоги женские DF-3099-1", 3, 100, 0.01, 0, 18, "1254789547853");
    KkmServer_AddBarcodeString(Data, "EAN13", "1254789547853");
    Data.Cash = 800;
    // Вызов команды
    KkmServer_Execute(Data, ExecuteSuccess, ErrorSuccess);
}
// Функция вызываемая после обработки команды - обработка возвращаемых данных
function ExecuteSuccess(Rezult, textStatus, jqXHR) {

    //----------------------------------------------------------------------
    // ОБЩЕЕ
    //----------------------------------------------------------------------

    var Responce = "";
    document.getElementById('Slip').textContent = "";

    if (Rezult.Status == 0) {
        Responce = Responce + "Статус: " + "Ok" + "\r\n";
    } else if (Rezult.Status == 1) {
        Responce = Responce + "Статус: " + "Выполняется" + "\r\n";
    } else if (Rezult.Status == 2) {
        Responce = Responce + "Статус: " + "Ошибка!" + "\r\n";
    } else if (Rezult.Status == 3) {
        Responce = Responce + "Статус: " + "Данные не найдены!" + "\r\n";
    };

    // Текст ошибки
    if (Rezult.Error != undefined && Rezult.Error != "") {
        Responce = Responce + "Ошибка: " + Rezult.Error + "\r\n";
    }

    if (Rezult != undefined) {
        var JSon = JSON.stringify(Rezult, "", 4);
        Responce = Responce + "JSON ответа: \r\n" + JSon + "\r\n";
        if (Rezult.Slip != undefined) {
            document.getElementById('Slip').textContent = Rezult.Slip;
        }
    }

    document.getElementById('Responce').textContent = Responce;
    console.log(Responce);
    //$(".Responce").text(Responce);
}

// Функция вызываемая при ошибке передачи данных
function ErrorSuccess(jqXHR, textStatus, errorThrown) {
    document.getElementById('Responce').innerHTML = "Ошибка передачи данных по HTTP протоколу: " + errorThrown;
    //$('.Responce').html("Ошибка передачи данных по HTTP протоколу");
}

async function get_realization_list(){
    const id = $("#mainId").val();
    return await( await fetch(`/administration/module/realization/get_realization_list/${id}`) ).json();
}

const kassa = {
        CashierName: "Ефремов А.С.",
        CashierVATIN: "183402781956",
        NumDevice: "1",
        openShift() {
            OldIdCommand = KkmServer.OpenShift(this.NumDevice, this.CashierNam, this.CashierVATIN).Execute(ExecuteSuccess).IdCommand;
        },
        closeShift() {
            OldIdCommand = KkmServer.CloseShift(this.NumDevice, this.CashierName, this.CashierVATIN).Execute(ExecuteSuccess).IdCommand;
        },
        XReport() {
            OldIdCommand = KkmServer.XReport(this.NumDevice).Execute(ExecuteSuccess).IdCommand;
        },
        async RegisterCheck(TypeCheck) {

            // Подготовка данных команды, параметры (TypeCheck = 0, NumDevice = 0, InnKkm = "", CashierName = "")
            var Data = KkmServer.GetDataCheck(TypeCheck, this.NumDevice, "", this.CashierName, this.CashierVATIN);
            

            //***********************************************************************************************************
            // ПОЛЯ ПОИСКА УСТРОЙСТВА
            //***********************************************************************************************************
            // Номер устройства. Если 0 то первое не блокированное на сервере
            Data.NumDevice = this.NumDevice;
            // ИНН ККМ для поиска. Если "" то ККМ ищется только по NumDevice,
            // Если NumDevice = 0 а InnKkm заполнено то ККМ ищется только по InnKkm
            Data.InnKkm = "";
            //---------------------------------------------
            // Заводской номер ККМ для поиска. Если "" то ККМ ищется только по NumDevice,
            Data.KktNumber = "";
            // **********************************************************************************************************

            // Время (сек) ожидания выполнения команды.
            //Если За это время команда не выполнилась в статусе вернется результат "NotRun" или "Run"
            //Проверить результат еще не выполненной команды можно командой "GetRezult"
            //Если не указано или 0 - то значение по умолчанию 60 сек.
            // Поле не обязательно. Это поле можно указывать во всех командах
            Data.Timeout = 30;
            // Это фискальный или не фискальный чек
            Data.IsFiscalCheck = true;
            // Тип чека, Тег 1054;
            // 0 – продажа/приход;                      10 – покупка/расход;
            // 1 – возврат продажи/прихода;             11 - возврат покупки/расхода;
            // 2 – корректировка продажи/прихода;       12 – корректировка покупки/расхода;
            Data.TypeCheck = TypeCheck;
            // Не печатать чек на бумагу
            Data.NotPrint = false; //true,
            // Количество копий документа
            Data.NumberCopies = 0;
            // Продавец, тег ОФД 1021
            Data.CashierName = this.CashierName;
            // ИНН продавца тег ОФД 1203
            Data.CashierVATIN = this.CashierVATIN;
            // Телефон или е-Майл покупателя, тег ОФД 1008
            // Если чек не печатается (NotPrint = true) то указывать обязательно
            // Формат: Телефон +{Ц} Email {С}@{C}
            // Data.ClientAddress = "sochi@papa.com";
            // Покупатель (клиент) - наименование организации или фамилия, имя, отчество (при наличии), серия и номер паспорта покупателя(клиента). Тег 1227
            // Только с использованием наличных / электронных денежных средств и при выплате выигрыша, получении страховой премии или при страховой выплате.
            // Data.ClientInfo = "Везучий В.В. РЕ-125486";
            // ИНН Организации или покупателя(клиента). Тег 1228
            // Только с использованием наличных / электронных денежных средств и при выплате выигрыша, получении страховой премии или при страховой выплате.
            // Data.ClientINN = "502906602876";
            // Aдрес электронной почты отправителя чека тег ОФД 1117 (если задан при регистрации можно не указывать)
            // Формат: Email {С}@{C}
            // Data.SenderEmail = "sochi@mama.com";
            // Место расчетов, Тег ОФД 1187 (если не задано - берется из регистрационных данных ККТ)
            Data.PlaceMarket = "";
            // Система налогообложения (СНО) применяемая для чека
            // Если не указанно - система СНО настроенная в ККМ по умолчанию
            // 0: Общая ОСН
            // 1: Упрощенная УСН (Доход)
            // 2: Упрощенная УСН (Доход минус Расход)
            // 3: Единый налог на вмененный доход ЕНВД
            // 4: Единый сельскохозяйственный налог ЕСН
            // 5: Патентная система налогообложения
            // Комбинация разных СНО не возможна
            // Надо указывать если ККМ настроена на несколько систем СНО
            Data.TaxVariant = "";
            // Дополнительный реквизит пользователя тег ОФД 1084
            // Data.UserAttribute = {
            // 	// Наименование дополнительного реквизита пользователя тег ОД 1085
            // 	Name: "Поле-тест",
            // 	// Значение дополнительного реквизита пользователя тег ОФД 1086
            // 	Value: "Тестовое значение"
            // };
            // Дополнительный реквизит чека тег 1192
            // Data.AdditionalAttribute = "Тест";

            // Это только для тестов:
            //Data.ClientId = "e1e0c5dbb395acecda9e3ed86a798755b21a53de"; //"541a9db930c2e90670898943",
            // Это только для тестов: Получение ключа суб-лицензии : ВНИМАНИЕ: ключ суб-лицензии вы должны генерить у себя на сервере!!!!
            //Data.KeySubLicensing = "";

            // Только для чеков коррекции:
            //Тип коррекции 0 - самостоятельно 1 - по предписанию, Тег 1173, Только для чеков коррекции!
            // Data.CorrectionType = 1;
            // Дата документа основания для коррекции, Тег ОФД 1178, Только для чеков коррекции!
            // Data.CorrectionBaseDate = '2017-06-21T15:30:45';
            // Номер документа основания для коррекции, Тег ОФД 1179, Только для чеков коррекции!
            // Data.CorrectionBaseNumber = "MOS-4516";


            // Если надо одновременно автоматически провести транзакцию через эквайринг 
            // Эквайринг будет задействован если: 1. чек фискальный, 2. оплата по "ElectronicPayment" не равна 0, 3. PayByProcessing = true
            // Использовать эквайринг: Null - из настроек на сервере, false - не будет, true - будет
            Data.PayByProcessing = false; //В тестовом чеке автоматический эквайринг выключен
            // Номер устройства для эквайринга - Null - из настроек на сервере, 0 - любое, число - номер конкретного устройства
            Data.NumDeviceByProcessing = null;
            // Номер чека для эквайринга
            Data.ReceiptNumber = "TEST-1";
            // Печатать Слип-чек после чека (а не в чеке)
            PrintSlipAfterCheck = false;
            // Печатать Слип-чек дополнительно для кассира (основной слип-чек уже будет печататся в составе чека)
            Data.PrintSlipForCashier = true;
            //Если это чек возврата то возможны два поля для отмены транзакции (если не указано то по эквайрингу будет не отмена а возврат оплаты)
            Data.RRNCode = ""; // RRNCode из операции эквайринга. Только для отмены оплаты! Для Оплаты или возврата оплаты не заполнять!
            Data.AuthorizationCode = ""; // AuthorizationCode из операции эквайринга. Только для отмены оплаты! Для Оплаты или возврата оплаты не заполнять!

            // Добавление признаков агенте и поставщика 
            // Data.AddDataAgent(
            // 	// Признак агента (не обязательно), Тег 1057
            // 	// 0: "Банковский платежный агент:" Оказание услуг пользователем, являющимся банковским платежным агентом
            // 	// 1: "Банковский платежный субагент:" Оказание услуг пользователем, являющимся банковским платежным субагентом
            // 	// 2: "Платежный агент:" Оказание услуг пользователем, являющимся платежным агентом
            // 	// 3: "Платежный субагент:" Оказание услуг пользователем, являющимся платежным субагентом
            // 	// 4: "Поверенный:" Оказание услуг пользователем, являющимся поверенным
            // 	// 5: "Комиссионер:" Оказание услуг пользователем, являющимся комиссионером
            // 	// 6: "Агент:" Оказание услуг пользователем, являющимся агентом и не являющимся банковским платежным агентом (субагентом), платежным агентом (субагентом), поверенным, комиссионером
            // 	2,
            // 	// Операция платежного агента тег 1044
            // 	"95315",
            // 	// Телефон платежного агента тег 1073
            // 	"+79995554422",
            // 	// Телефон оператора по приему платежей  тег 1074
            // 	"+72223334455",
            // 	// Телефон оператора перевода тег 1075
            // 	"+74447776655",
            // 	// Наименование оператора перевода тег 1026
            // 	"ООО Тестовая организация",
            // 	// Адрес оператора перевода тег 1005
            // 	"Москва, зубовский бульвар 44",
            // 	// ИНН оператора перевода тег 1016
            // 	"430601071197",
            // 	// Телефон поставщика тег 1171
            // 	"+76662229955"
            // );

           

            // Добавление печати текста по полям

            //При вставке в текст символов ">#10#<" строка при печати выровнеется по центру, где 10 - это на сколько меньше станет строка ККТ
            Data.AddTextString(">#2#<ООО \"ТТ-ЭНЕРГО\"", 1);
            // При вставке в текст в середину строки символов "<#10#>" Левая часть строки будет выравнена по левому краю, правая по правому, где 10 - это на сколько меньше станет строка ККТ
            // При вставке в текст в середину строки символов "<#10#>>" Левая часть строки будет выравнена по правому краю, правая по правому, где 10 - отступ от правого клая
            // Data.AddTextString("<<->>");
            // Data.AddTextString("Пример №1:<#10#>154,41");
            // Data.AddTextString("2-рое поле:<#10#>4,00");
            // Data.AddTextString("<<->>");
            // Data.AddTextString("Пример №2:<#8#>>4,00");
            // Data.AddTextString("2-рое поле:<#8#>>1544,00");

            // // Добавление печати текста шрифтом и интенсивностью
            // // Data.AddTextString(Data, Текст, Font(1-4, 0 - по настройкам ККМ), Intensity(1-15, 0 - по настройкам ККМ);
            // Data.AddTextString("<<->>");
            // Data.AddTextString("Шрифт № 1", 1, 15);
            // Data.AddTextString("Шрифт № 2", 2, 10);
            // Data.AddTextString("Шрифт № 3", 3, 5);
            // Data.AddTextString("Шрифт № 4", 4, 15);
            // Data.AddTextString("<<->>");

                // // Добавление печати фискальной строки
                // var DataStr = Data.AddRegisterString(
                //     // НаименованиеТовара(64 символа)
                //     "Гайка",
                //     // Количество (3 знака после запятой)
                //     1,
                //     // ЦенаБезСкидки (2 знака после запятой)
                //     1,
                //     // СуммаСтроки (2 знака после запятой)
                //     1,
                //     // СтавкаНДС(0(НДС 0%), 10(НДС 10%), 20(НДС 20%), -1(НДС не облагается), 120 (НДС 20/120), 110 (НДС 10/110))
                //     -1,
                //     // Отдел
                //     0,
                //     // Код товара EAN13 - не обязательно
                //     '',
                //     // Признак способа расчета. тег ОФД 1214. Для ФФД.1.05 и выше обязательное поле
                //     // 1: "ПРЕДОПЛАТА 100% (Полная предварительная оплата до момента передачи предмета расчета)"
                //     // 2: "ПРЕДОПЛАТА (Частичная предварительная оплата до момента передачи предмета расчета)"
                //     // 3: "АВАНС"
                //     // 4: "ПОЛНЫЙ РАСЧЕТ (Полная оплата, в том числе с учетом аванса в момент передачи предмета расчета)"
                //     // 5: "ЧАСТИЧНЫЙ РАСЧЕТ И КРЕДИТ (Частичная оплата предмета расчета в момент его передачи с последующей оплатой в кредит )"
                //     // 6: "ПЕРЕДАЧА В КРЕДИТ (Передача предмета расчета без его оплаты в момент его передачи с последующей оплатой в кредит)"
                //     // 7: "ОПЛАТА КРЕДИТА (Оплата предмета расчета после его передачи с оплатой в кредит )"
                //     4,
                //     // Признак предмета расчета. тег ОФД 1212. Для ФФД.1.05 и выше обязательное поле
                //     // 1: "ТОВАР (наименование и иные сведения, описывающие товар)"
                //     // 2: "ПОДАКЦИЗНЫЙ ТОВАР (наименование и иные сведения, описывающие товар)"
                //     // 3: "РАБОТА (наименование и иные сведения, описывающие работу)"
                //     // 4: "УСЛУГА (наименование и иные сведения, описывающие услугу)"
                //     // 5: "СТАВКА АЗАРТНОЙ ИГРЫ (при осуществлении деятельности по проведению азартных игр)"
                //     // 6: "ВЫИГРЫШ АЗАРТНОЙ ИГРЫ (при осуществлении деятельности по проведению азартных игр)"
                //     // 7: "ЛОТЕРЕЙНЫЙ БИЛЕТ (при осуществлении деятельности по проведению лотерей)"
                //     // 8: "ВЫИГРЫШ ЛОТЕРЕИ (при осуществлении деятельности по проведению лотерей)"
                //     // 9: "ПРЕДОСТАВЛЕНИЕ РИД (предоставлении прав на использование результатов интеллектуальной деятельности или средств индивидуализации)"
                //     // 10: "ПЛАТЕЖ (аванс, задаток, предоплата, кредит, взнос в счет оплаты, пени, штраф, вознаграждение, бонус и иной аналогичный предмет расчета)"
                //     // 11: "АГЕНТСКОЕ ВОЗНАГРАЖДЕНИЕ (вознаграждение (банковского)платежного агента/субагента, комиссионера, поверенного или иным агентом)"
                //     // 12: "СОСТАВНОЙ ПРЕДМЕТ РАСЧЕТА (предмет расчета, состоящем из предметов, каждому из которых может быть присвоено вышестоящее значение"
                //     // 13: "ИНОЙ ПРЕДМЕТ РАСЧЕТА (предмет расчета, не относящемуся к предметам расчета, которым может быть присвоено вышестоящее значение"
                //     // 14: "ИМУЩЕСТВЕННОЕ ПРАВО" (передача имущественных прав)
                //     // 15: "ВНЕРЕАЛИЗАЦИОННЫЙ ДОХОД"
                //     // 16: "СТРАХОВЫЕ ВЗНОСЫ" (суммы расходов, уменьшающих сумму налога (авансовых платежей) в соответствии с пунктом 3.1 статьи 346.21 Налогового кодекса Российской Федерации)
                //     // 17: "ТОРГОВЫЙ СБОР" (суммы уплаченного торгового сбора)
                //     // 18: "КУРОРТНЫЙ СБОР"
                //     // 19: "ЗАЛОГ"
                //     1,
                //     //штрих - код маркировки товара со сканера(нужно настроить сканер так чтобы не проглатывал управляющие символы)
                //     //КИЗ(контрольный идентификационный знак) товарной номенклатуры Тег ОФД 1162(честный знак), можно не указывать
                //     // Поддерживаются ШК: 
                //     // Без идентификатора экземпляра товара: EAN8, EAN13, ITF14
                //     // С идентификатором экземпляра товара: GS1, ШК шуб, ШК табачной продукции., ЕГАИС-2, ЕГАИС-3
                //     //Описание применимых ШК
                //     "",
                //     // Единица измерения предмета расчета. Можно не указывать
                //     "шт.",
                //     // Цифровой код страны происхождения товара (CountryOfOrigin) в соответствии с Общероссийским классификатором стран мира 3 симв. Тег 1230
                //     "",
                //     // Регистрационный номер таможенной декларации (CustomsDeclaration) 32 симв. Тег 1231
                //     "",
                //     // Сумма акциза (ExciseAmount) с учетом копеек, включенная в стоимость предмета расчета Тег 1229
                //     null, //иначе сумма акциза
                //     // Дополнительный реквизит предмета расчета тег 1191, Только для ФФД 1.1 !
                //     ""
                // );

                // Добавляет в последнею строку Код товарной номенклатуры
                // КИЗ (контрольный идентификационный знак) товарной номенклатуры Тег ОФД 1162 (честный знак), можно не указывать
                // !!!!Команда нужна если в команде  Data.AddRegisterString не добавили КИЗ
                //Data.MarkingCode(
                //    // штрих-код маркировки товара со сканера (нужно настроить сканер так чтобы не проглатывал управляющие символы)
                //    // Поддерживаются ШК: 
                //    // Без идентификатора экземпляра товара: EAN8, EAN13, ITF14
                //    // С идентификатором экземпляра товара: GS1, ШК шуб, ШК табачной продукции., ЕГАИС-2, ЕГАИС-3
                //    "0104300943734342212413195240818240640291ffd092MDEwNDMwMDk0MzczNDM",
                //    // Проверять содержит ли ШК кода маркировки идентификатор экземпляра товара (если вообше не указать - true)
                //    // Для некоторых товаров нужно передавать ШК EAN-13, тогда это поле устанавливайте в 'false'
                //    true
                //);

                // DataStr.EGAIS = {
                // 	Barcode: "22N0000154NUCPRZ3R8381461004001003499NKAQ0ZBUVDNV62JQAR69PEV878RO93V",
                // 	Ean: "3423290167937",
                // 	Volume: 0.7500,
                // };

                // Добавляет в последнею строку признак агента (не обязательно), Тег 1222.
                // Поле не обязательное. Можно вообще не указывать.
                // 0: "Банковский платежный агент:" Оказание услуг пользователем, являющимся банковским платежным агентом
                // 1: "Банковский платежный субагент:" Оказание услуг пользователем, являющимся банковским платежным субагентом
                // 2: "Платежный агент:" Оказание услуг пользователем, являющимся платежным агентом
                // 3: "Платежный субагент:" Оказание услуг пользователем, являющимся платежным субагентом
                // 4: "Поверенный:" Оказание услуг пользователем, являющимся поверенным
                // 5: "Комиссионер:" Оказание услуг пользователем, являющимся комиссионером
                // 6: "Агент:" Оказание услуг пользователем, являющимся агентом и не являющимся банковским платежным агентом (субагентом), платежным агентом (субагентом), поверенным, комиссионером
                // Data.AddAgentSign(2);
                // Добавляет в последнею строку данные агента (не обязательно)
                // Data.AddAgentData(
                // 	// Операция платежного агента тег 1044
                // 	"95315",
                // 	// Телефон платежного агента тег 1073
                // 	"+79995554422",
                // 	// Телефон оператора по приему платежей  тег 1074
                // 	"+72223334455",
                // 	// Телефон оператора перевода тег 1075
                // 	"+74447776655",
                // 	// Наименование оператора перевода тег 1026
                // 	"ООО Рога и копыта",
                // 	// Адрес оператора перевода тег 1005
                // 	"Москва, зубовский бульвар 44",
                // 	// ИНН оператора перевода тег 1016
                // 	"430601071197"
                // );
                // Добавляет в последнею строку данные поставщика (не обязательно)
                // Data.AddPurveyorData(
                // 	// Телефон поставщика тег 1171
                // 	"+76662229955",
                // 	// Наименование поставщика ОФД 1225
                // 	"ООО 'Гаражик в деревне'",
                // 	// ИНН поставщика тег 1226
                // 	"430601071197"
                // );

                // Добавление печати штрихкода
                // Data.AddBarcodeString(ТипШтрихКода ("EAN13", "CODE39", "CODE128", "QR", "PDF417", "1254789547853"), ШтрихКод);
                // Data.AddBarcodeString("EAN13", "1254789547853"); Data.AddTextString("<<->>");

                // Data.AddTextString("Это мега крутой товар. Продается во всех магазинах страны. Покупайте только у нас", 4, 0);

                // Добавление печати еще одной фискальной строки
                // Data.AddRegisterString("Шаровары мужские красные: НИМБУС-2000", 1, 1500, 0.01, 18, 1, "1254789547853", 1, 1);

                const list = await get_realization_list();
                let total = 0;
                list.forEach(item => {
                    console.log(item.s_name);
                    Data.AddRegisterString(item.s_name, item.count, item.s_price, item.total, -1, 0, "", 1, 1, '', item.unit_name, '', '', null, '');
                    total += +item.total;
                });
                // Data.AddRegisterString("Шаровары мужские красные: НИМБУС-2000", 1, 1500, 0.01, 18, 1, "1254789547853", 1, 1);

                // Добавляет в последнею строку Код товарной номенклатуры
                // Data.AddGoodCodeData("02", "04630037591316", "sgEdK2y5");

                // Добавление печати штрихкода
                // Data.AddBarcodeString(ТипШтрихКода ("EAN13", "CODE39", "CODE128", "QR", "PDF417", "1254789547853"), ШтрихКод);
                // Data.AddBarcodeString("QR", "12345DFG Proba pera, Print barcode 1234567890."); Data.AddBarcodeString("PDF417", "12345DFG Proba pera, Print barcode 1234567890.");

                // Наличная оплата (2 знака после запятой)
                Data.Cash = 0;

                // Сумма электронной оплаты (2 знака после запятой)
                Data.ElectronicPayment = total; 
                // Сумма из предоплаты (зачетом аванса) (2 знака после запятой)
                Data.AdvancePayment = 0;
                // Сумма постоплатой(в кредит) (2 знака после запятой)
                Data.Credit = 0;
                // Сумма оплаты встречным предоставлением (сертификаты, др. мат.ценности) (2 знака после запятой)
                Data.CashProvision = 0;


                //#if Relese
                // Скидываем данные об агенте - т.к.у Вас неверняка ККТ не зарегистрирована как Агент.
                Data.AgentSign = null; Data.AgentData = null; Data.PurveyorData = null;
                //#endif
                for (var i = 0; i < Data.CheckStrings.length; i++) {
                    if (Data.CheckStrings[i] != undefined && Data.CheckStrings[i].Register != undefined) {
                        Data.CheckStrings[i].Register.AgentSign = null;
                        Data.CheckStrings[i].Register.AgentData = null;
                        Data.CheckStrings[i].Register.PurveyorData = null;
                    };
                };

                console.log(Data);

                // Вызов команды
               OldIdCommand = Data.Execute(ExecuteSuccess).IdCommand;

               console.log(OldIdCommand);

                // Возвращается JSON:
                //{
                //    "CheckNumber": 3,           // Номер документа
                //    "SessionNumber": 1,         // Номер смены
                //    "SessionCheckNumber": 1,    // Номер чека в смене
                //    "URL": "https://ofd-ya.ru/getFiscalDoc?kktRegId=0000000000061716&fiscalSign=839499349",
                //    "QRCode": "t=20190101T195300&s=0.03&fn=9999078900002838&i=3&fp=839499349&n=1",
                //    "Command": "RegisterCheck",
                //    "Cash": 0, // Оплачено наличными
                //    "ElectronicPayment": 3.02, // Оплачено электронноо
                //    "AdvancePayment": 0, // Оплачено предоплатой (зачетом аванса)
                //    "Credit": 0, // постоплатой(в кредит)
                //    "CashProvision": 0, // встречным предоставлением (сертификаты, др. мат.ценности)
                //    "Error": "", // Текст ошибки если была - обязательно показать пользователю - по содержанию ошибки можно в 90% случаях понять как ее устранять
                //    "Message": "", // Сообщение пользователю - Если строка не пустая - ее нужно отобразить пользователю
                //    "Status": 0, // Ok = 0, Run(Запущено на выполнение) = 1, Error = 2, NotFound(устройство не найдено) = 3, NotRun = 4
                //    "IdCommand": "dd261969-4190-1125-26cd-aaf5c213c0e3",
                //    "NumDevice": 2
                //}


            }
        }

$(document).on("click", ".s_name", function(){
    const This = $(this);
    const id = This.data("id");
    const cn = "s_name";
    This.removeClass(cn);
    const text = This.text();
    const width = (text.length + 6) * 8;
    const input = $(`<input type="text" value="${text}" class="form-control rubber" style="width: ${width}px">`);
    input.on("blur", function(){
        This.addClass(cn);
        const text = this.value;
        This.html(text);
        saveDataToInp(id);
    });
    This.html(input);
    input.focus();
    input.select();
})

$(document).on("change", ".checks", function(){
    const id = get_checks_id();
    const hi = "holder-active";
    const resale = $("#resale");
    if(id.length > 0){
        resale.addClass(hi);
    } else {
        resale.removeClass(hi);
    }
});

$(document).on("click", "#resale", async function(){
    const id = get_checks_id();
    if(id.length > 0){
        const resaleModal = $("#resaleModal");
        resaleModal.modal("show");
        const resaleFormModal = $("#resaleFormModal");
        resaleFormModal.off();
        resaleFormModal.on("submit", async function(e){
            e.preventDefault();
            resaleModal.modal("hide");
            const lw = new LW;
            lw.on();
            const form = new FormData(this);
            form.append("realization_id", $("#mainId").val());
            id.forEach(item => {
                form.append("realization_detail_id[]", item);
            })
            try {
                const data = await ( await fetch(`/administration/module/realization/check_resale`, {
                    method: "POST",
                    body: form,
                }) ).json();
                if(data.error){
                    throw new Error(data.error);
                }
                resale_modal(form);
            } catch (error) {
                swal("Внимание!", error.message, "error");
            } finally{
                lw.off();
            }
        });
    } else {
        notif("danger", "Внимание", "Позиции не выбраны");
    }
});

$(document).on("change", "#resale_org", async function(){
    const resale_stock_id = $("#resale_stock_id");
    resale_stock_id.html("");
    const form = new FormData;
    form.append("org", this.value);
    const stock = await (await fetch(`/administration/module/realization/get_resale_stock`, {
        method: "POST",
        body: form
    })).json();
    stock.forEach(item => {
        resale_stock_id.append(`<option value="${item.id}">${item.name}</option>`);
    })
});

async function resale_modal(form){
    swal({
        title: "Вы уверены?",
        text: "Будут созданы ряд документов для перепродажи с склада партнера на ваш!",
        icon: "info",
        buttons: true,
        dangerMode: true,
      })
      .then(async (willSend) => {
        if (willSend) {
            const lw = new LW;
            lw.on();
            try {
                const data = await (await fetch(`/administration/module/realization/resale`, {
                    method: "POST",
                    body: form,
                })).json();
                if(data.error){
                    throw new Error(data.error);
                }
                await updateTovList($("#mainId").val());
            } catch (error) {
                swal("Внимание!", error.message, "error");
            } finally{
                lw.off();
            }
        }
      });
}