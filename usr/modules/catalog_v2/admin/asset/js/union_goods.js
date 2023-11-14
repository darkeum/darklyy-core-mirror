//console.log("~");
$(function(){

    $(document).on("keyup", "#search", function(){
        if($(this).val().length === 0){
            $("#ajaxContent").empty();
        }
        if($(this).val().length > 0){
            search($(this).val());
        }
    });

    function search(value){
        $.post("/administration/module/catalog_v2/union_goods/search", {value:value}, function(data){
            let json = JSON.parse(data);
            let str = '';
            json["data"].forEach(function(val, i){
                str += '<div class="listElem" data-id="'+json["data"][i]["id"]+'">'+json["data"][i]["title"]+' ('+json["data"][i]["quantity"]+')</div>';
            });
            $("#ajaxContent").html(str);
        });
    }

    $(document).on("click", ".listElem", function(){
        let id = $(this).data("id");
        let title = $(this).text();
        let test = $("#unionListBody").find("#ulb_"+id);
        if(test.length === 0){
            let str = '';
            str += '<tr id="ulb_'+id+'">';
            str += '<td>'+title+'</td>';
            str += '<td class="action">';
            str += '<button class="btn btn-primary btn-sm favoriteBtn" title="Избранный" data-id="'+id+'"><i class="fa fa-star favorite" aria-hidden="true"></i></button>';
            str += '<button class="btn btn-danger btn-sm actionBtn" title="Удалить из списка"><i class="fa fa-times" aria-hidden="true"></i></button>';
            str += '</td>';
            str += '</tr>';
            $("#unionListBody").append(str);
        } else {
            alert("Такой товар уже присутствует");
        }
        activeButtonUnion();
    });

    $(document).on("click", ".actionBtn", function(){
        $(this).parents("tr").remove();
        activeButtonUnion();
    });

    function activeButtonUnion(){
        if($("#unionListBody tr").length > 1 && $(".favoriteActive").length === 1){
            $("#unionBtn").prop("disabled", false);
        } else {
            $("#unionBtn").prop("disabled", true);
        }
    }

    $(document).on("click", ".favoriteBtn", function(){
        $(".favoriteActive").removeClass("favoriteActive");
        $(this).addClass("favoriteActive");
        activeButtonUnion();
    });

    $(document).on("click", "#unionBtn", function(){
        let favorite = $(".favoriteActive").data("id")
        let array = {
            "data":{
                "favorite": favorite,
                "id":[]
            }
        }
        $(".favoriteBtn").each(function(i, elem){
            let id = $(elem).data("id");
            array["data"]["id"][i] = $(elem).data("id");
        });
        if (array["data"]["id"].includes(favorite)){
            let position = array["data"]["id"].indexOf(favorite);
            array["data"]["id"].splice(position, 1);
        }
        let data = JSON.stringify(array);
        $.post("/administration/module/catalog_v2/union_goods/union", {data:data}, function(){
            notif('success', 'Успешно', 'Товары объединены');
            $("#search").val("");
            $("#unionListBody").html("");
            activeButtonUnion();
        });
    });
});