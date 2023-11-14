$(".select2-single").select2({
    placeholder: "Выберите атрибут",
});	
$(".select2-single1").select2({
    
});	

$(document).on("change", "#attributeValue, #attributeValueEdit", function(){
    const input = $(this);
    const value = input.val();
    const new_value = value.replace(/\s+/g, ' ').trim();
    input.val(new_value);
});

function getAttributeList(This){
    $("#attribute").select2("val", "");
    $.post("/administration/attribute/getAttributeList", {id: $(This).val()}, function(data){
        $("#attribute").html(data);
        $("#attribute").val("");
    });
}
function getAttribute(id){
    $.post("/administration/attribute/getAttribute", {id: id}, function(data){
        $("#attributeData").html(data);
        $("#attributeData ").sortable({
            items: ".attributeElem",
            update: function(){
                let id = $("#attribute").val();
                let value = [];
                $(".attributeElem").each(function(i, elem){
                    let hh = $(elem).children().eq(0).text();
                    if(hh !== ""){
                        value.push(hh);
                    }
                });
                value = value.join(",");
                $.post("/administration/attribute/moveAttribute", {value:value, id:id}, function(){

                });
            }
        });
    });
}
$(document).on("change", "#attribute", function(){
    let id = $(this).val();
    if(id !== null){
        getAttribute(id);
    }
});
function addAttribute(){
    $("#addAttributeModal").modal("show");
}
function addAttributeBtn(){
    let id = $("#attribute").val();
    let value = $("#attributeValue").val().replace(/,/g, ".");
    if(id !== null && value != ""){
        $.post("/administration/attribute/addAttribute", {id: id, value:value}, function(){
            getAttribute(id);
        });
    }
}
function deleteAttribute(This){
    let value = $(This).data("value");
    $("#delAttributeBtn").data("value", value);
    $("#delAttributeModal").modal("show"); 
}
function delAttributeBtn(This){
    let id = $("#attribute").val();
    let value = $(This).data("value");
    if(id !== null && value != ""){
        $.post("/administration/attribute/delAttribute", {id: id, value:value}, function(){
            getAttribute(id);
        });
    }
}
function editAttribute(This){
    let value = $(This).data("value");
    $("#attributeValueEdit").val(value);
    $("#editAttributeBtn").data("valueOld", value);
    $("#editAttributeModal").modal("show"); 
}
$(document).on("keyup", "#attributeValue, #attributeValueEdit", function(e){
    $(this).val($(this).val().replace(/,/g, ""));
});
function editAttributeBtn(This){
    let id = $("#attribute").val();
    let valueOld = $(This).data("valueOld");
    let valueNew = $("#attributeValueEdit").val();
    if(id !== null && valueOld != "" && valueNew != ""){
        $.post("/administration/attribute/editAttribute", {id: id, valueOld:valueOld, valueNew:valueNew}, function(){
            getAttribute(id);
        });
    }
}
function addAttributeMainModal(){
    $("#attributeMainModal").modal("show");
}

//поиск 
$(document).on("keyup", "#title", function() {
    let search = $(this).val();
    if(search.length >= 2){
        $.post("/administration/attribute/getAttributeListVal", {search:search}, function(data){
            $(".search_result").html(data).fadeIn(); //Выводим полученные данные в списке
        });
    }
    if(search.length == 0){
        $(".search_result").fadeOut();
    }
})
    
$(".search_result").hover(function(){
    $("#title").blur(); //Убираем фокус с input
})
    
//При выборе результата поиска, прячем список и заносим выбранный результат в input
$(".search_result").on("click", "li", function(){
    $("#title").val($(this).text());
    let id = $(this).data("id");
    $(".search_result").fadeOut();
    $("#addToCurrent").prop("disabled", false);
    $("#addToCurrent").data("id", id);
    $.post("/administration/attribute/getAttributeListVal", {id:id, mod: 2}, function(data){
        $("#listAttribute").html(data);
    });
});

function clearAttributeMainModal(){
    $("#title").val("");
    $("#category_new").val("");
    $("#category_new").select2("val", "");
    $("#listAttribute").html("");
}

$(document).on("change", "#category_new", function(){
    $("#addNew").prop("disabled", false);
});

$(document).on("click", "#addNew", function(){
    let title = $("#title").val();
    let cat = $("#category_new").val();
    let type = "";
    $(".radioType").each(function(i, elem){
        if($(this).attr("checked")){
            type = $(this).val();
        }
    });
    if(cat.length > 0 && title != "" && type != ""){
        $("#attributeMainModal").modal("hide");
        clearAttributeMainModal();
        $.post("/administration/attribute/addNewAttribute", {title:title, cat:cat, type:type}, function(){

        });
    } else {
        alert("Заполните необходимые поля!");
    }
});

$(document).on("click", "#addToCurrent", function(){
    let cat = $("#category_new").val();
    let id = $(this).data("id");
    if(cat.length > 0){
        $("#attributeMainModal").modal("hide"); //,406,
        clearAttributeMainModal();
        $.post("/administration/attribute/appendAttribute", {id:id, cat:cat}, function(){

        });
    } else {
        alert("Заполните необходимые поля!");
    }
});

$(document).on("click", "#delMainAttribute", function(){
    $("#delMainAttributeModal").modal("show");
});

$(document).on("click", "#delMainAttributeBtn", function(){
    let cat = $("#category").val();
    let id = $("#attribute").val();
    $.post("/administration/attribute/delMainAttribute", {id:id, cat:cat}, function(){
        getAttributeList($("#category"));
        $("#attributeData").html("");
    });
});

$(document).on("click", "#editMainAttribute", function(){
    let value = $("#attribute option:selected").text();
    value = value.replace(/[{]%.+%[}]/i, "").trim();
    $("#editMainAttributeModalInp").val(value);
    $("#editMainAttributeModal").modal("show");
});

$(document).on("click", "#editMainAttributeModalBtn", function(){
    let value = $("#editMainAttributeModalInp").val();
    let id = $("#attribute").val();
    $.post("/administration/attribute/editMainAttribute", {id:id, value:value}, function(){
        getAttributeList($("#category"));
        $("#attributeData").html("");
    });
});

function sortModal(){
    $("#sortModalBody").html("Загрузка данных...");
    $("#sortModal").modal("show");
    let id = $("#category").val();
    $.post("/administration/attribute/getAttributeListData", {id:id}, function(data){
        $("#sortModalBody").html(data);
        $("#sortModalBody").sortable({
            items: ".sortElem",
            update: function(){
                let order = [];
                let count = 0;
                $(".sortElem").each(function(i, elem){
                    let id = $(elem).data("id");
                    if(id !== null && id !== undefined){
                        order[count] = id;
                        count++;
                    }
                });
                order = JSON.stringify(order);
                $.post("/administration/attribute/moveAttribute2", {id:id, order:order}, function(){

                });
            }
        });
    });
}

$(document).on("click", ".changeHidden", function(){
    let checkbox = $(this).next();
    let checked = checkbox.prop("checked");
    if(checked){
        checked = false;
    } else {
        checked = true;
    }
    checkbox.prop("checked", checked);
    let id = checkbox.data("id");
    let category = $("#category").val();
    if(checked){
        $(this).removeClass("fa-eye").removeClass("chvg").addClass("fa-eye-slash").addClass("chvr");
    } else {
        $(this).removeClass("fa-eye-slash").removeClass("chvr").addClass("fa-eye").addClass("chvg");
    }
    $.post("/administration/attribute/changeHidden", {checked:checked, id:id, category:category}, function(){

    });
});

// $(document).keyup("#attributeValue", function(event){
//     if(event.keyCode == 13){
//        $("#addAttributeBtn").click();
//     }
// });