//console.log("~");

$(function(){
    $("#select2").select2();
    $("#typeCF").select2();

    let id = $("#mainId").val();
    bankListLoad();
    if(id !== ""){
        $.post("/administration/module/organizations/getDataUser", {id:id}, function(data){
            $("#CFTBody").empty();
            let array = JSON.parse(data);
            array.forEach(function(val, i){
                let str = drawContactFaces(val, false);
                $("#CFTBody").append(str);
            });
        });
    }

    $(document).on("input", "input", set_change_field);
    $(document).on("click", ".delCF", set_change_field);

    window.onbeforeunload = function (evt) {
        if(window.change_field){
            return "Данные будут утеряны. Вы уверенные что хотите покинуть страницу?";
        }
    }

});

function set_change_field(){
    window.change_field = true;
}

async function bankListLoad(){
    const id = $("#mainId").val();
    const bankList = $("#bankList");
    bankList.html("Загрузка данных...");
    const json = await $.post("/administration/module/organizations/getBankList", {id});
    bankList.empty();
    const data = JSON.parse(json);
    data["data"].forEach(val => {
        $("#bankList").append( drawBankList(val, false) );
    });
}

//банковские данные
function drawBankList(array, edit){
    const name = (array["name"] || array["name_short"]) + ` (${array["rschet"]})`;
    const str = `<td>${name}</td>
    <td><i style="color: blue; cursor: pointer; font-size: 25px;" class="fa fa-pencil" aria-hidden="true" title="Редактировать" onclick="modalEditBD(this);"></i></td>
    <td><i style="color: red; cursor: pointer; font-size: 25px;" class="fa fa-trash" aria-hidden="true" title="Удалить" onclick="modalRemoveBD(this);"></i></td>
    <td class="saveData"><input type="hidden" value='${JSON.stringify(array)}' name="saveBank[]" data-id="${array["id"]}" class="saveBank"></td>`;
    return edit ? str : `<tr>${str}</tr>`;
}

function modalAddBD(){
    $("#bank").val("");
    $("#bsname").val("");
    $("#bname").val("");
    $("#baddress").val("");
    $("#bik").val("");
    $("#kschet").val("");
    $("#rschet").val("");
    $("#modalAddBD").modal("show");
}

function modalAddBDBtn(This){
    $("#bankListTable").show();
    let bsname = $("#bsname").val();
    let bname = $("#bname").val();
    if(bname == ""){
        bname = bsname;
    }
    let baddress = $("#baddress").val();
    let bik = $("#bik").val();
    let kschet = $("#kschet").val();
    let rschet = $("#rschet").val();
    if(bsname != "" && baddress != "" && bik != "" && kschet != "" && rschet != ""){
        let edit;
        let id;
        if($(This).data("context") == "" || $(This).data("context") == undefined){
            id = "";
            edit = false;
        } else {
            edit = true;
            id = $(This).data("id");
        }

        console.log(edit);

        let array = {
            "id": id,
            "bik": bik,
            "kschet": kschet,
            "rschet": rschet,
            "address": baddress,
            "name": bname,
            "name_short": bsname,
        };
        
        let str = drawBankList(array, edit);

        if(edit){
            let context = $(This).data("context");
            context = $(context).parent().parent();
            $(context).html(str);
        } else {
            $("#bankList").append(str);
        }
        
        $(This).data("id", "");
        $(This).data("context", "");
        $("#modalAddBD").modal("hide")
    } else {
        alert("Заполнены не все поля!");
    }
}

function modalRemoveBD(This){
    let context = $(This).parent().parent();
    $("#modalRemoveBDBtn").data("context", context);
    $("#modalRemoveBD").modal("show");
}

$(document).on("click", "#modalRemoveBDBtn", function(){
    let context = $(this).data("context");
    $(context).remove();
});

function modalEditBD(This){
    let saveBank = $(This).parent().parent().children(".saveData").children(".saveBank").val();
    saveBank = JSON.parse(saveBank);
    let id = $(This).parent().parent().children(".saveData").children(".saveBank").data("id");
    $("#modalAddBDBtn1").data("context", This);
    $("#modalAddBDBtn1").data("id", id);

    $("#bsname").val(saveBank["name_short"]);
    $("#bname").val(saveBank["name"]);
    $("#baddress").val(saveBank["address"]);
    $("#bik").val(saveBank["bik"]);
    $("#kschet").val(saveBank["kschet"]);
    $("#rschet").val(saveBank["rschet"]);

    $("#modalAddBD").modal("show");
}

//контактные лица
function addContactFaces(){
    $("#addContactFacesModal").modal("show");
}

$("#addContactFacesModal").on("hide.bs.modal", function(){
    $("#surnameCF").val("");
    $("#nameCF").val("");
    $("#ochestvoCF").val("");
    $("#emailCF").val("");
    $("#phoneCF").val("");
    $("#phone2CF").val("");
    $("#postCF").val("");
    $("#commentCF").val("");
    $("#select2").select2("val", "");
    $("#typeCF").val("-1").trigger("change");
    $("#addContactFacesModalBtn").data("id", "");
    $("#addContactFacesModalBtn").data("context", "");
});

function drawContactFaces(array, edit){
    const status = array["data"]["commentCF"].length > 0 ? "activeComment" : "";
    const json = JSON.stringify(array);
    const str = `<td>${array["data"]["surnameCF"]} ${array["data"]["nameCF"]} ${array["data"]["ochestvoCF"]}</td>
    <td>${array["data"]["emailCF"]}</td>
    <td>${array["data"]["phoneCF"]}</td>
    <td>${array["data"]["phone2CF"]}</td>
    <td>${array["data"]["postCF"]}</td>
    <td><i style="color: blue; cursor: pointer; font-size: 25px;" class="fa fa-pencil changeCF" aria-hidden="true" title="Редактировать"></i></td>
    <td><i style="color: red; cursor: pointer; font-size: 25px;" class="fa fa-trash delCF" aria-hidden="true" title="Удалить"></i></td>
    <td><i style="font-size: 25px;" class="fa fa-commenting-o ${status}" aria-hidden="true" title="Комментарий" data-comment="${array["data"]["commentCF"]}"></i></td>
    <td class="saveData"><input type="hidden" value='${json}' name="usersCF[]"></td>`;

    return edit ? str : `<tr>${str}</tr>`;
}

$(document).on("click", "#addContactFacesModalBtn", function(){
    let id = $(this).data("id");
    let array = {data: {
        surnameCF: $("#surnameCF").val(),
        nameCF: $("#nameCF").val(),
        ochestvoCF: $("#ochestvoCF").val(),
        emailCF: $("#emailCF").val(),
        phoneCF: $("#phoneCF").val(),
        phone2CF: $("#phone2CF").val(),
        typeCF: $("#typeCF").val(),
        postCF: $("#postCF").val(),
        commentCF: $("#commentCF").val(),
        id: id,
    }};

    if(array["data"]["surnameCF"] !== "" && array["data"]["nameCF"] !== "" && array["data"]["ochestvoCF"] !== "" && array["data"]["emailCF"] !== "" && array["data"]["phoneCF"] !== "" && array["data"]["postCF"] !== ""){
        let edit = true;
        let context = $(this).data("context");
        if(context == "" || context == undefined){
            edit = false;
        }

        let str = drawContactFaces(array, edit);

        if(!edit){
            $("#CFTBody").append(str);
        } else {
            $(context).html(str);
        }
        $("#addContactFacesModal").modal("hide");
    } else {
        alert("Заполнены не все поля");
    }
});

$(document).on("click", ".changeCF", function(){
    let json = $(this).parent().parent().children(".saveData").children().val();
    let array = JSON.parse(json);
    let context = $(this).parent().parent();
    $("#addContactFacesModalBtn").data("id", array["data"]["id"]);
    $("#addContactFacesModalBtn").data("context", context);
    $("#surnameCF").val(array["data"]["surnameCF"]);
    $("#nameCF").val(array["data"]["nameCF"]);
    $("#ochestvoCF").val(array["data"]["ochestvoCF"]);
    $("#emailCF").val(array["data"]["emailCF"]);
    $("#phoneCF").val(array["data"]["phoneCF"]);
    $("#phone2CF").val(array["data"]["phone2CF"]);
    $("#typeCF").val(array["data"]["typeCF"]).trigger("change");
    $("#postCF").val(array["data"]["postCF"]);
    $("#commentCF").val(array["data"]["commentCF"]);

    $("#addContactFacesModal").modal("show");
});

$(document).on("click", ".delCF", function(){
    $(this).parent().parent().remove();
});

$(document).on("change", "#select2", function(){
    const uid = $(this).val();
    if(uid){
        $.post("/administration/module/organizations/getDataUser", {uid}, function(data){
            let json = JSON.parse(data);
            json = json[0];
            $("#surnameCF").val(json["data"]["surnameCF"]);
            $("#nameCF").val(json["data"]["nameCF"]);
            $("#ochestvoCF").val(json["data"]["ochestvoCF"]);
            $("#emailCF").val(json["data"]["emailCF"]);
            $("#phoneCF").val(json["data"]["phoneCF"]);
            $("#phone2CF").val(json["data"]["phone2CF"]);
            $("#typeCF").val(json["data"]["typeCF"]).trigger("change");
            $("#postCF").val(json["data"]["postCF"]);
            $("#commentCF").val(json["data"]["commentCF"]);
            $("#addContactFacesModalBtn").data("id", json["data"]["id"])
        });
    }
});

$(document).on("click", ".activeComment", function(){
    let comment = $(this).data("comment");
    $("#viewCommentModalBody").html(comment);
    $("#viewCommentModal").modal("show");
});

$(document).on("change", "#typeCF", function(){
    if($(this).val() != "-1"){
        let typeCF = $("#typeCF option:selected").text();
        $("#postCF").val(typeCF);
    }
});

$(document).on("submit", "#admin-form", function(e){
    if($(this).data("check") == undefined){
        if($("#mainId").val() == ""){
            e.preventDefault();
            const form = $(this).serialize();
            check_org(form).then(data => {
                if(data.check){
                    $(this).data("check", true);
                    window.change_field = false;
                    $(this).submit();
                } else {
                    Swal.fire("Внимание!", "Такая организация уже заведена!", "warning");
                }
            });
        } else {
            window.change_field = false;
        }
    }
})

function check_org(request){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/organizations/check_org", request, function(data){
            resolve(JSON.parse(data));
        })
    });
}