//основная функция отправки данных и получения каталога
loadWindow(true);
function defLoad(scroll = false) {

    if (scroll === false) {
        $("#loaded_max").val(0);
        loadWindow(true);
    } else {
        loadWindowScroll(true);
    }

    //по точному совпадению
    Cookies.remove("exactCoincidence");
    let check;
    if ($("#exactCoincidence").prop("checked")) {
        check = true;
        Cookies.set('exactCoincidence', 1, { expires: 365 * 10 });
    } else {
        check = false;
        Cookies.set('exactCoincidence', 0, { expires: 365 * 10 });
    }

    //полнотекстовый поиск
    let check_full;
    if ($("#check_full").prop("checked")) {
        check_full = true;
        Cookies.set('check_full', 1, { expires: 365 * 10 });
    } else {
        check_full = false;
        Cookies.set('check_full', 0, { expires: 365 * 10 });
    }

    //экспертный режим
    let check_expert;
    if ($("#check_expert").prop("checked")) {
        check_expert = true;
        Cookies.set('check_expert', 1, { expires: 365 * 10 });
    } else {
        check_expert = false;
        Cookies.set('check_expert', 0, { expires: 365 * 10 });
    }

    let check_active = $("#check_active").prop("checked");

    //по каталогу
    Cookies.remove("catalogSearch");
    let searchCat;
    if ($("#catalogSearch").prop("checked")) {
        searchCat = $(".check").attr("id").slice(6);
        Cookies.set('catalogSearch', 1, { expires: 365 * 10 });
    } else {
        searchCat = false;
        Cookies.set('catalogSearch', 0, { expires: 365 * 10 });
    }

    //фильтр
    /*
    let filter = $(".checkFilterItem");
    let filterData = [];
    for (let i = 0; i < filter.length; i++) {
        if ($(filter[i]).prop("checked")) {
            let fid = $(filter[i]).parent().parent().parent().parent().data("id");
            let value = $(filter[i]).data("value");
            filterData.push([fid, value]);
        }
    }
    */

   let filter = $(".filterUl");
   let filterData = [];
   let filterDataMan = [];
   for (let i = 0; i < filter.length; i++) {
       let filterItem = $(filter[i]).find(".checkFilterItem");
       if(filterItem.length > 0){
            filterData[i] = [];
       }
        for(let j = 0; j < filterItem.length; j++){
            if ($(filterItem[j]).prop("checked")) {
                let fid = $(filter[i]).data("id");
                let value = $(filterItem[j]).data("value");
                if(fid === "man"){
                    filterDataMan.push(value);
                } else {
                    filterData[i].push([fid, value]);
                }
            }
        }
   }

    filterData = JSON.stringify(filterData);
    filterDataMan = JSON.stringify(filterDataMan);

    let orderField = $("#orderField").val();
    let orderType = $("#orderType").val();

    //тип цены
    let price_type = $("#price_type").val();
    //склад
    const stock_id = $("#stock_id").val();

    window.xhr = $.post("/ajax.php?do=getSearchList", { data: $("#search").val(), loaded_max: $("#loaded_max").val(), check: check, searchCat: searchCat, filterData: filterData,  filterDataMan:filterDataMan, orderField:orderField, orderType:orderType, price_type:price_type, check_full:check_full, check_expert:check_expert, check_active, stock_id}, function (data) {

        if(scroll === false){
            $("#ajaxRes").empty();
        }
        
        if (data.length === 0) {
            $("#loaded_max").val(-1);
            if (scroll === false) {
                $("#ajaxRes").html("Ничего не найдено!");
                loadWindow(false);
            }
        } else {
            $("#loaded_max").val(parseInt($('#loaded_max').val()) + 1);

            if (scroll === false) {
                $("#ajaxResDiv").scrollTop(0);
                setTimeout(function () {
                    let save = $("#resTable").clone();
                    $("#ajaxResDiv").empty();
                    $("#ajaxResDiv").html(save);
                    $("#ajaxRes").html(data);
                    $("#resTable").colResizable({ partialRefresh: true, postbackSafe: true, liveDrag: true });
                    loadWindow(false);
                }, 200);
            } else {
                $("#ajaxRes").append(data);
            }
            load = true;
        }
        loadWindowScroll(false);
    });

}

//загрузка списка фильтров
function getFilter(cat, load = true) {
    $.post("/ajax.php?do=getFilter", {cat: cat, search:$("#search").val()}, function (data) {
        $("#filter").html(data);
        if(load){
            defLoad();
        }
    });
}

//функция для добавление в корзину
function AddBas(name) {
    let type = +$("#typeSelection").val();
    let id = $(name).attr("id").slice(4); //id товара
    let nameTov = $("#res_"+id).children(".title").eq(0).text();
    let mainId = $("#mainId").val();
    change_clicks(id);

    let flag = true;

    switch(type){
		default:
        case 1:
        case 3:
        case 4:
            //default
                let masBas = $(".bas2");
                let lenBas = masBas.length;
                for (let i = 0; i < lenBas; i++) {
                    if (id == $(masBas[i]).val()) {
                        flag = false;
                        break;
                    }
                }
            
                let price_type = $("#price_type").val();
            
                if (flag) {
                    $.post("/ajax.php?do=getIdProduct", {data: id, price_type:price_type}, function (data) {
                        $("#basket").append(data);
						getAllTotal();
                    });
                } else {
                    $("#count_" + id).val(+$("#count_" + id).val() + 1);
                }
				
        break;
        case 2:
            //minAdd
                let module = $("#module").val();
                let parentId = $("#mainId").val();
                let saveParent;
                    let num;
                    let count;
					switch(module){
                    case "sales":
                        saveParent = $("#tr_"+parentId, opener.window.document);
                        num = $(saveParent).children(".position").text();
                        count = $(saveParent).children(".count").text();
                        let discount = $(saveParent).children(".discount").text().replace("%", "");
                        opener.getCatProductSales(id, saveParent, parentId, num, count, discount);
                    break;
                    case "selection":
                        saveParent = $("#tr_"+parentId, opener.window.document);
                        num = $(saveParent).children(".position").text();
                        count = $(saveParent).children(".count").text();
                        opener.changeProduct(id, parentId, num, count);
                    break;
					case "metering_rs":
						flag = false;
						let mid = $("#mainId", opener.window.document).val();
						$.post('/ajax.php?do=setMeteringRs', {cid:id, mainId:parentId, mid}, function(){
								opener.updateHeader();
								window.close();
						});
                    break;
                    case "report": 
                        if(mainId === ""){
                            //старый функционал отчётов
                            opener.changeSetting("change", [2, "value"], [{id:id, text:nameTov}]);
                            opener.drawSetting();
                        } else {
                            //новый функционал
                            opener.setValue(mainId, id, nameTov);
                        }
                    break;
                    case "boiler_bid":
                        flag = false;
                        opener.get_tov_item(mainId, id).then(item => {
                            opener.replace_tov_item(mainId, item);
                            window.close();
                        });
                    break;
                    case "merging":
                        flag = false;
                        $.post('/administration/module/merging/tovar_add_main', {cid:id, id:parentId}, function(){
                            opener.updateHeader();
                            opener.updateBody();
                            window.close();
                        });
                    break;
                    case "supply_order":
                        flag = false;
                        opener.change_goods(id, parentId).then(() => {
                            window.close();
                        });
                    break;
                }
				if (flag){
					window.close();
				}
        break;
		
    }
}

function change_clicks(id){
    return new Promise((resolve, reject) => {
        $.post("/ajax.php?do=catalog_change_clicks", {id}, function(){
            resolve();
        });
    })
}

//фуннкция загрузки
function loadWindow(status){
    switch(status){
        case true:
            $("#ajaxResDiv").addClass("loadBlur");
            $(".cssload-loader").show();
            //$("#search").prop("disabled", true);
        break;
        case false:
            $("#ajaxResDiv").removeClass("loadBlur");
            $(".cssload-loader").hide();
            //$("#search").prop("disabled", false);
        break;
    }
}

function loadWindowScroll(status){
    switch(status){
        case true:
            $("#fountainG").show();
        break;
        case false:
            $("#fountainG").hide();
        break;
    }
}

//поиск данных при вводе в поле поиска
$("#search").on("keyup", function (e) {
    if (($(this).val().length > 2 || $(this).val().length === 0) && (e.keyCode !== 16 && e.keyCode !== 17 && e.keyCode !== 18)){
        let search = $(this);
        let delay = 500;
    
        if(typeof window.xhr !== "undefined"){
            window.xhr.abort();
        }

        clearTimeout(search.data('timer'));
    
        search.data('timer', setTimeout(function(){
            search.removeData('timer');

            defLoad();
        }, delay));

    }
});
/*
//добавление элемента в корзину при double click
$(document).on("dblclick", ".catalog", function () {
    AddBas($(this));
});
*/
//обновление списка при изм. вида цены
$(document).on("change", "#price_type", function(){
    defLoad();
})

//добавление элемента в корзину при click на кнопку "Добавить"
/*
$(document).on("click", ".addBas", function(){
    AddBas($(this));
    console.log("~");
});
*/

//удаление элемента из корзины
$(document).on("click", ".remove", function () {
    let id = $(this).attr("id").slice(4);
    $("#bas_" + id).remove();
    $("#bas2_" + id).remove();
	getAllTotal();
});

//подгрузка данных из каталога при scroll
let load = true;
$("#ajaxResDiv").on("scroll", function () {
    $(this).find("thead").css('transform', 'translateY(' + $(this).scrollTop() + 'px)');
    if (+$("#loaded_max").val() >= 0) {
        if ((+$(this).scrollTop() + +$(this).height()) >= $(this)[0].scrollHeight) {
            if (load) {
                console.log("~");
                load = false;
                defLoad(true);
            }
        }
    }
});

$("#basketDiv").on("scroll", function () {
    $(this).find("thead").css('transform', 'translateY(' + $(this).scrollTop() + 'px)');
});

//очистка поиска
$("#clear").click(function () {
    $("#search").val("");
    defLoad();
});

//защита от дурака при вводе количества
$(document).on("blur", ".count", function () {
    if ($(this).val() < 0 || $(this).val() == "") $(this).val(1);
    if ($(this).val() > 100000) $(this).val(100000);
});

/*
//обновление данных каталога при включении "По точному совпадению"
$("#exactCoincidence").change(function () {
    defLoad();
});

//обновление данных каталога при включении "Полнотекстовый поиск"
$("#check_full").change(function () {
    defLoad();
});

//обновление данных каталога при включении "режима эксперта"
$("#check_expert").change(function () {
    defLoad();
});

//обновление данных каталога при включении "По каталогу"
$("#catalogSearch").change(function () {
    defLoad();
});
*/

//открытие списка категорий; обновление данных при включенном "По каталогу"
$(document).on("click", ".point", function () {
    let id = $(this).attr("id").slice(6);
    $("#submenu_" + id).slideToggle();
    // if ($(this).parent().hasClass("folderEmpty")) {
        $(".point").removeClass("check");
        $(this).addClass("check");
        Cookies.remove('checkCatalog');
        Cookies.set('checkCatalog', $(this).attr("id"), { expires: 365 * 10 });
        if ($("#catalogSearch").prop("checked")) {
            getFilter(id);
        } else {
            getFilter(id, false);
        }
    // }
});

//показывать картинку при нажатии
$(document).on("click", ".title", function () {
    let id = $(this).parent().attr("id").slice(4);
    //console.log(id);
    $("#dataLoad").html("<span style='color: grey;'>Загрузка картинки</span>");
    $("#exampleModalLabel").html("Изображение");
    $('#myModal').modal('show');
    $.post("/ajax.php?do=getImgSelect", { id: id }, function (data) {
        $("#dataLoad").html(data);
    });
});

//получение сертификатов
$(document).on("click", ".spec", function () {
    let id = $(this).parent().parent().attr("id").slice(4);
    //console.log(id);
    $("#dataLoad").html("<span style='color: grey;'>Загрузка сертификатов</span>");
    $("#exampleModalLabel").html("Сертификаты");
    $('#myModal').modal('show');
    $.post("/ajax.php?do=getCert", { id: id }, function (data) {
        $("#dataLoad").html(data);
    });
});

//открытие окна добавление товара
$("#openCreateGoods").on("click", function () {
    $('#addGoods').modal('show');
});

//сохранение номенклатуры
$("#createGoods").on("submit", function () {
    let title = $("#title").val();
    let sku = $("#sku").val();
    let unit = $("#unit").val();
    let cat = $("#cat").val();
    let types = $("#types").val();
    let man = $("#man").val();
    //let translit = getTranslit(title, 'translit');
    $("#infoSku").hide();
    $.post("/ajax.php?do=checkSku", { sku: sku, cat: cat }, function (data) {
        if (data == "true") {
            $('#addGoods').modal('hide');
            $("#infoSku").hide();
            $("#title").val("");
            $("#sku").val("");
            $("#types").select2("val", "3");
            $("#man").select2("val", "-1");
            $.post("/ajax.php?do=addGoods", { title: title, sku: sku, unit: unit, category: cat, types: types, man: man}, function (data) {
                console.log(data);
            });
        } else {
            $("#infoSku").html("Такой артикул существует!");
            $("#infoSku").css({ 'color': 'red' });
            $("#infoSku").show();
        }
    });
    return false;
});

//получение атрибута
$(document).on("click", ".attr", function () {
    let id = $(this).parent().parent().attr("id").slice(4);
    //console.log(id);
    $("#dataLoad").html("<span style='color: grey;'>Загрузка аттрибутов</span>");
    $("#exampleModalLabel").html("Атрибуты");
    $('#myModal').modal('show');
    $.post("/ajax.php?do=getAttr", { id: id }, function (data) {
        $("#dataLoad").html(data);
    });
});

//создание артикула
$("#skuGenerate").click(function () {
    let cat = $("#cat").val();
    $("#infoSku").hide();
    if (cat == 0) {
        $("#infoSku").html("Выберите категорию!");
        $("#infoSku").css({ 'color': 'red' });
        $("#infoSku").show();
    } else {
        $("#sku").val("AE05" + cat + "XXXXXX");
        $("#infoSku").html("Артикул будет создан автоматически");
        $("#infoSku").css({ 'color': 'grey' });
        $("#infoSku").show();
    }
});

//раскрытие списка с фильтрами
$(document).on("click", ".filterItem", function () {
    $(this).next().slideToggle();
});

//выделение если есть фильтр
$(document).on("change", ".checkFilterItem", function () {
    let parent = $(this).parent().parent().parent().parent();
    let child = $(parent).find(".checkFilterItem");
    let flag = false;
    for (let i = 0; i < child.length; i++) {
        if ($(child[i]).prop("checked")) {
            flag = true;
            break;
        }
    }
    if (flag) {
        $(parent).prev().addClass("checkFilter");
    } else {
        $(parent).prev().removeClass("checkFilter");
    }
    defLoad();
});

//сбросить фильтр
$(document).on("click", "#filterClear", function () {
    let id = $(".check").attr("id").slice(6);
    getFilter(id);
});

$(function () {
    if($("#typeSelection").val() == "2"){
        let query = $(".inputChange:first", opener.window.document).val();
        $("#search").val(query);
    }

    $('.js-example-basic-single').select2({ width: '100%' });

    //загрузка категорий в создании номенклатруы
    $.post("/ajax.php?do=getCategory", function (data) {
        $('#cat').html(data);
    });

    //загрузка единиц измерений в создании номенклатруы
    $.post("/ajax.php?do=getUnit", function (data) {
        $('#unit').html(data);
    });

    $.post("/ajax.php?do=getPriceType", function(data){
        $("#price_type").html(data);
    });

    if ((Cookies.get("exactCoincidence") !== undefined) && (Cookies.get("exactCoincidence") == 1)) {
        $("#exactCoincidence").prop("checked", true);
    }

    if ((Cookies.get("check_full") !== undefined) && (Cookies.get("check_full") == 1)) {
        $("#check_full").prop("checked", true);
    }

    if ((Cookies.get("check_expert") !== undefined) && (Cookies.get("check_expert") == 1)) {
        $("#check_expert").prop("checked", true);
    }

    if ((Cookies.get("catalogSearch") !== undefined) && (Cookies.get("catalogSearch") == 1)) {
        $("#catalogSearch").prop("checked", true);
    }

    //получаем данные категории, фильтры и основная подгрузка
    $.post("/ajax.php?do=getCat", function (data) {
        $("#ajaxCat").html(data);

        let li = $("#ajaxCat li");
        for (let i = 0; i < li.length; i++) {
            if ($(li[i]).children().length > 1) {
                $(li[i]).addClass("folder");
            } else {
                $(li[i]).addClass("folderEmpty");
            }
        }
        let cookie = Cookies.get("checkCatalog");
        if (cookie != undefined) {
            $("#" + cookie).addClass("check");
            let main = $("#" + cookie).parent().parent();
            while ($(main).attr("class") != "submain") {
                $(main).css({ "display": "block" });
                main = $(main).parent().parent();
            }
            let id = cookie.slice(6);
            getFilter(id);
        } else {
            let radMy = $(".folderEmpty");
            $(radMy[0]).children(".point").addClass("check");
            let id = $(radMy[0]).children(".point").attr("id");
            let main = $("#" + id).parent().parent();
            Cookies.set('checkCatalog', id, { expires: 365 * 10 });

            while ($(main).attr("class") != "submain") {
                $(main).css({ "display": "block" });
                main = $(main).parent().parent();
            }
            getFilter(id);
        }
    });

    $(document).on("change", ".changeSort", function(){
        let orderField = $(this).parent().parent().parent().data("name");
        let orderType = $(this).data("sort");
        $("#orderField").val(orderField);
        $("#orderType").val(orderType);
        defLoad();
    });

    //смена цвета сортировки
    $(document).on("click", ".sortIcon", function(){
        $(".sortIcon").removeClass("sortIconActive");
        $(this).addClass("sortIconActive");
    });
});

//
function getTotal(id){
	let count = $('#count_'+id).val();
	let discount = $('#discount_'+id).val();
	let price = $('#price_'+id).data("price");
	let total = count*price*(100-discount)/100;
	total = total.toFixed(2);
	$('#total_'+id).val(total);
	getAllTotal();
}

function getDiscount(id){	
	let count = $('#count_'+id).val();
	let total = $('#total_'+id).val();
	let price = $('#price_'+id).data("price");
	let discount = ((count*price)/total)*100;	
	discount = discount.toFixed(2);
	$('#discount_'+id).val(discount);
	getAllTotal();
}

function getAllTotal(){
	let totalAll = 0;
	$(".total").each(function(i, elem){
	totalAll += + $(elem).val();
	});
	totalAll = totalAll.toFixed(2);
	$('#total_all').html(totalAll+" руб.");
}
