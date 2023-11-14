    /* ----------------------------------------------------------------------------- */
    getBillList();

    $(document).on("keydown", ".enterBlur", function(){
        if(event.keyCode === 13){
            $(this).blur();
        }
    });

    let save_date_post;
    $(document).on("click", ".date_post, .date_ship", function(){
        const value = $(this).text();
        const field = $(this).data("field");
        let date = value;
        if(value !== "Отсутствует"){
            date = date.split(".");
            date = date[2]+"-"+date[1]+"-"+date[0];
        } else {
            date = "";
        }
        save_date_post = date;
        $(this).html('<input type="date" value="'+date+'" class="form-control enterBlur" id="input_date_post" data-field="'+field+'">');
        $(this).removeClass(field);
        $("#input_date_post").select();
        $("#input_date_post").focus();
    });

    $(document).on("blur", "#input_date_post", function(){
        const value = $(this).val();
        const field = $(this).data("field");
        const id = $(this).parents("tr").data("id");
        let date = value;
        let date_ship;
        let value_ship;
        const work_day = 9;
        if(value !== ""){
            date = date.split("-");
            date = date[2]+"."+date[1]+"."+date[0];
            let vs = date_up(value, work_day);
            value_ship = parse_date_en(vs);
            date_ship = parse_date_rus(vs);
        } else {
            date = 'Отсутствует';
            date_ship = 'Отсутствует';
            value_ship = "";
        }
        $(this).parent().addClass(field);
        $(this).parent().html(date);
        if(save_date_post !== value){
            const status = checkStatus(id);
            send(field, value, id, status);
            if(field == "date_ship"){
                change_field_view(id, ".date_post", date_ship);
                send("date_post", value_ship, id, status);
            }
        }
    });

    function date_up(date, day){
        const time = Date.parse(date);
        const new_date = new Date(time);
        new_date.setDate(new_date.getDate() + day);
        return new_date;
    }

    function change_field_view(id, selector, value){
        const elem = $("#tr_"+id);
        elem.find(selector).html(value);
    }

    function send(type, value, id, status){
        const supply_id = $("#mainId").val();
        $.post("/ajax.php?do=changeTwoDetail", {type:type, value:value, id:id, supply_id:supply_id, status:status}, function(){
            //updateList(false);
        });
    }

    function checkStatus(id = null){
        let tr_elem;
        if(id === null){
            tr_elem = $(".tr_elem");
        } else {
            tr_elem = $("#tr_"+id);
        }

        let len = tr_elem.length;

        let countStatusMin = 1;
        let countStatusMax = 3;

        for(let i=0;i<len;i++){
            let countStatus = 1;
            let date_post = $(tr_elem[i]).children(".date_post");
            let sos_price = $(tr_elem[i]).children(".sos_price");

            if(date_post.text() !== "Отсутствует"){
                countStatus++;
            }
            if(sos_price.text() !== "Отсутствует"){
                countStatus++;
            }

            if(id !== null){
                if(countStatus === countStatusMin){
                    return 1;
                } else if(countStatus === countStatusMax){
                    return 3;
                } else {
                    return 2;
                }
            }
        }
    }

    let save_sos_price;
    $(document).on("click", ".sos_price", function(){
        let value = $(this).text().replace(/ /g, "").replace("руб.", "");
        let price = value;
        if(value === "Отсутствует"){
            price = 0;
        }
        save_sos_price = price;
        $(this).html('<input type="number" value="'+price+'" class="form-control enterBlur" id="input_sos_price">');
        $(this).removeClass("sos_price");
        $("#input_sos_price").select();
        $("#input_sos_price").focus();
    });

    $(document).on("blur", "#input_sos_price", function(){
        let value = $(this).val();
        let id = $(this).parents("tr").data("id");
        let price = value;
        if(value !== ""){
            price = priceSet(price);
        } else {
            price = 'Отсутствует';
        }
        let count = $("#tr_"+id).children(".count").html();
        let total = +value * count;
        $("#tr_"+id).children(".sos_total").html(priceSet(total));
        $(this).parent().addClass("sos_price");
        $(this).parent().html(price);
        if(save_sos_price !== value){
            let status = checkStatus(id);
            send("sos_price", value, id, status);
        }
    }); 

    function sos_total_inp(This, id){
        if($(This).data("inp") !== "on"){
            let value = $(This).text().replace(/ /g, "").replace("руб.", "");
            let price = value;
            if(value === "Отсутствует"){
                price = 0;
            }
            $(This).html(`<input type="number" value="${price}" class="form-control enterBlur" id="input_sos_total" onblur="sos_total(this, ${id});">`);
            $("#input_sos_total").select();
            $("#input_sos_total").focus();
        }
    }

    function sos_total(This, id){
        let value = $(This).val();
        let total;
        if(value !== ""){
            total = priceSet(value);
        } else {
            total = 'Отсутствует';
        }
        let count = $("#tr_"+id).children(".count").html();
        let price = value / count;
        price = price.toFixed(2);
        let price_view = priceSet(price);
        $(`#tr_${id}`).children(".sos_price").html(price_view);
        $(This).parent().html(total);
        let status = checkStatus(id);
        send("sos_price", price, id, status);
    }

    function confirmExecutionModal(){
        let tr_elem = $(".tr_elem");

        let len = tr_elem.length;
        let flag = true;

        for(let i=0;i<len;i++){
            let countStatus = 0;
            const date_post = $(tr_elem[i]).find(".date_post");
            const sos_price = $(tr_elem[i]).find(".sos_price");
            const oid = $(tr_elem[i]).find(".oid");

            if(date_post.text() !== "Отсутствует"){
                countStatus++;
            }
            if(sos_price.text() !== "Отсутствует"){
                countStatus++;
            }
            if(oid.data("oid") !== ""){
                countStatus++;
            }

            if(countStatus !== 3){
                flag = false; 
                break;
            }
        }

        if(flag){
            $("#confirmExecutionModal").modal("show");
        } else {
            swal("Ошибка", "Заполните все поля!", "error");
        }
    }

    function confirmExecution(){
        const id = $("#mainId").val();
        $.post("/ajax.php?do=confirmExecutionTwoDetail", {id:id}, function(){
            update_head();
            updateList(false);
            swal("Успешно", "Данные отправлены", "success");
        });
    }

    function update_head(){
        return new Promise((resolve, reject) => {
            const id = $("#mainId").val();
            $.post("/administration/module/supply/two_ajax/detail_head/"+id, function(data){
                $("#dev_head").html(data);
                getBillList();
                resolve();
            });
        });
    }

    function updateList(flagNotif = true){
        return new Promise((resolve, reject) => {
            const id = $("#mainId").val();
            $.post("/administration/module/supply/two_ajax/detail_list/"+id, function(data){
                $("#dev_list").html(data);
                if(flagNotif){
                    notif('primary', 'Информация', 'Данные успешно получены');
                }
                resolve();
            });
        });
    }

    $(document).on("click", ".refresh", function(){
        updateList();
    });

    $(document).on("click", ".addBill", function(){
        let id = $("#mainId").val();
        $("#billModalList").html("Загрузка данных...");
        $("#billModalAdd").prop("disabled", true);
        $("#billModal").modal("show");
        $.post("/ajax.php?do=getListNoFileTwoDetail", {id:id}, function(data){
            $("#billModalList").html(data);
        });
    });

    $(document).on("change", "#checkAll", function(){
        if($(this).is(":checked")){
            $(".local_check").prop("checked", true);
        } else {
            $(".local_check").prop("checked", false);
        }
    });

    $(document).on("change", ".local_check, #checkAll", function(){
        if($(".local_check").is(":checked")){
            $("#billModalAdd").prop("disabled", false);
            $("#removeBillModalBtn").prop("disabled", false);
        } else {
            $("#billModalAdd").prop("disabled", true);
            $("#removeBillModalBtn").prop("disabled", true);
        }
    });

    $(document).on("click", "#billModalAdd", function(){
        let data = $("#billModalForm").serialize();
        let id = $("#mainId").val();
        $("#files1").html('<li class="text-muted text-center empty">Файлы не загружены.</li>');
        $("#debug1").empty();
        $.post("/ajax.php?do=saveBillTwoDetail", "id="+id+"&"+data, function(){
            updateList(false);
            getBillList();
        });
    });

    function getBillList(){
        let id = $("#mainId").val();
        $.post("/ajax.php?do=getBillListTwoDetail", {id:id}, function(data){
            $("#billList").html(data);
        });
    }

    $(document).on("click", ".removeBillAll", function(){
        let id = $(this).parent().data("id");
        let text = $(this).parent().children("a").text();
        $("#removeBillAllModalBtn").data("id", id);
        $("#removeBillAllModalText").html(text);
        $("#removeBillAllModal").modal("show");
    });

    $(document).on("click", "#removeBillAllModalBtn", function(){
        let id = $(this).data("id");
        let supply_id = $("#mainId").val();
        $.post("/ajax.php?do=removeBillAllTwoDetail", {supply_id:supply_id, id:id}, function(){
            updateList(false);
            getBillList();
        });
    });

    $(document).on("click", ".editBill", function(){
        let id = $(this).parent().data("id");
        let supply_id = $("#mainId").val();
        $("#removeBillModalBtn").data("id", id);
        $("#removeBillModalBody").html("Загрузка данных");
        $("#removeBillModalBtn").prop("disabled", true);
        $.post("/ajax.php?do=getBillListDetailTwoDetail", {id:id, supply_id:supply_id}, function(data){
            $("#removeBillModalBody").html(data);
        });
        $("#removeBillModal").modal("show");
    });

    $(document).on("click", "#removeBillModalBtn", function(){
        let data = $("#removeBillModalForm").serialize();
        let id = $(this).data("id");
        let supply_id = $("#mainId").val();
        $.post("/ajax.php?do=removeBillTwoDetail", "id="+id+"&supply_id="+supply_id+"&"+data, function(){
            updateList(false);
            getBillList();
        });
    });

    function print_modal(){
        $("#print_modal").modal("show");
    }

    function dis_btn(){
        let len = $(".checks:checked").length;
        $("#sub").prop('disabled', (len === 0));
    }

    $(document).on("change", '.checks', function(){
        dis_btn();
    });

    function act_doit(){
        let value = $("#act").val();
        switch(value){
            case "mas_date":
                $("#mas_date_modal").modal("show");
            break;
            case "mas_oid":
                $("#oid_modal").modal("show");
            break;
        }
    }

    function get_checks(){
        const checks = [];
        $(".checks:checked").each((i, elem) => {
            checks.push($(elem).val());
        });
        return checks;
    }

    function set_mas_date(){
        let date = $("#mas_date_inp").val();
        let checks = get_checks();
        $.post("/ajax.php?do=set_mas_date_supply", {date, checks}, function(){
            updateList();
        });
    } 

    function change_oid(){
        const checks = get_checks();
        const oid = $("#oid").val();
        update_field_detail(checks, 'oid', oid).then(() => {
            updateList(false);
        });
    }

    function update_field_detail(id, field, value){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/supply/twodetail_update_field", {id, field, value}, function(){
                resolve();
            });
        });
    }

    function auto_create_supply_check(){
        return new Promise((resolve, reject) => {
            const id = $("#mainId").val();
            $.post("/administration/module/supply/twodetail_auto_create_supply_check", {id}, function(data){
                if(data == ""){
                    resolve();
                } else {
                    reject(data);
                }
            });
        });
    }

    function auto_create_supply_modal(){
        auto_create_supply_check().then(() => {
            const id = $("#mainId").val();
            swal({
                title: "Вы уверены?",
                text: "Отправить в счет поставщика",
                icon: "warning",
                buttons: {
                    confirm: {
                        text: "Отправить",
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
              .then((willSend) => {
                if (willSend) {
                    auto_create_supply(id);
                }
              });
        }, data => {
            swal("Ошибка!", data, "error");
        });
    }

    function auto_create_supply(id){
        return new Promise((resolve, reject) => {
            $.post("/administration/module/supply/auto_create_supply", {id}, function(){
                resolve();
            });
        });
    }

    function error_modal_end(){
        swal("Ошибка!", "Завершите заявку", "error");
    }

    function delete_item(id){
        swal({
            title: "Вы уверены?",
            text: "Удалить позицию",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then(async (willDelete) => {
            if (willDelete) {
                const lw = new LW;
                lw.on();
                await delete_item_(id);
                await updateList();
                lw.off();
            }
          });
    }

    async function delete_item_(id){
        const form = new FormData;
        form.append("id", id);
        return await ( await fetch(`/administration/module/supply/delete_item_twodetail`, {
            method: "POST",
            body: form
        }) ).json();
    }

    /* ----------------------------------------------------------------------------- */