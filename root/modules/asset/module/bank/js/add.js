$(document).on("change", "#file", function(){
    let data = new FormData($("#admin-form")[0]);
    $.ajax({
        url: '/administration/bank/view_parse',
        data: data,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (data) {
            $("#viewData").html(data);
            updateSelect2Sales();
            updateSelect2SupplyOrder();
            $("#sub").prop("disabled", false);
            delInp();
        }
    });
});

$(document).on("click", ".iconPlus", function(){
    let id = $(this).data("id");
    let type = $(this).data("type");
    let check = $(this).data("check");
    const str = `<tr>
    <td></td>
    <td></td>
    <td></td>
    <td>
        <div class="col-sm-5">
            <input type="hidden" value="" name="select__`+id+`[]">
            <label for="ajaxSelect_`+type+`" class="field prepend-icon">
                <select name="select__`+id+`[]" id="select__`+id+`[]" class="ajaxSelect_`+type+` select-primary"></select>
                <label for="ajaxSelect_`+type+`" style="top: 2px;" class="field-icon"><i class="`+(type === 'getSalesToSelect' ? 'fas fa-shopping-cart' : 'far fa-file-text')+`"></i></label>
            </label>
        </div>
        <div class="col-sm-6">
            <input type="number" placeholder="Введите сумму" class="form-control" id="input__`+id+`[]" name="input__`+id+`[]">
        </div>
        <i class="fas fa-trash-alt text-danger clearSelect cp" data-id="`+id+`"></i>
    </td>
    <td><i class="fas fa-times iconDel cp linkDel_`+id+`" data-id="`+id+`"></i></td>
</tr>`;
    const ins = $(this).parents("tr").nextAll(".parent").eq(0);
    if(ins.length > 0){
        ins.before(str);
    } else {
        $(this).parents("tbody").append(str);
    }
    updateSelect2Sales();
    updateSelect2SupplyOrder();

    if(+check === 0){
        $(this).parents("tr").children(".inputGroup").children("div").removeClass("col-sm-11").addClass("col-sm-5").after(`<div class="col-sm-6"><input type="number" placeholder="Введите сумму" class="form-control" id="input__`+id+`[]" name="input__`+id+`[]"></div>`);

        $(this).data("check", 1);
    }
});

$(document).on("click", ".iconDel", function(){
    let id = $(this).data("id");
    let parent = $(this).parents("tr").prevAll(".parent")[0];
    $(this).parents('tr').remove();
    let count = $(".linkDel_"+id).length;
    if(count === 0){
        $(parent).children(".iconGroup").children().data("check", 0);
        $(parent).children(".inputGroup").children().eq(1).remove();
        $(parent).children(".inputGroup").children().eq(0).removeClass("col-sm-5").addClass("col-sm-11");
    }
});

function delInp(){
    $(".select-primary").each(function(i, elem){
        if($(elem).val() !== "" && $(elem).val() !== null){
            $(elem).parent().prev().remove("input");
        }
    });
}

$(document).on("change", ".select-primary", function(){
    if($(this).val() !== null){
        $(this).parent().prev().remove("input");
    }
});

$(document).on("click", ".clearSelect", function(){
    let key = $(this).data("id");
    $(this).parent().children().eq(0).children("label").children("select").empty();
    $(this).parent().children().eq(0).children("label").children("select").val(null).trigger("change");

    if($(this).parent().children("input").length === 0){
        $(this).parent().children().prepend(`<input type="hidden" name="select__`+key+`[]" value="">`);
    }
});
