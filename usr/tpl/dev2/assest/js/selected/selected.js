$(function(){
    categories_folder();
    cg_page('default');
    getFilter(false);
    positions_change(true);
    addBtnDis();
    
    $("#goods_table").sortable({
        axis: "y",
        update: function( event, ui ) {
            positions_change();
        }
    });

    const category = cg_category();
    if(category != ""){
        cg_category(category);
        findPreview("hide");
        let main = $("#point_"+category).parent().parent();
        while ($(main).attr("class") != "submain") {
            $(main).css({ "display": "block" });
            main = $(main).parent().parent();
        }
    }
});

function positions_change(first = false){
    $(".positions").each((i, elem) => {
        $(elem).val(+i + 1);
    });
    if(!first){
        cg_changed(true);
    }
}
function categories_folder(){
    const li = $(".submain li").each((i, elem) => {
        const item = $(elem);
        if(item.children().length > 1){
            item.addClass("folder");
        } else {
            item.addClass("folderEmpty");
        }
    });
}

$(document).on("click", ".point", function () {
    const elem = $(this);
    const id = elem.data("id");
    const man_old = cg_category();
    cg_category(id);
    if(man_old != id){
        category_change();
    }
});

function category_change(){
    getFilter(false);
    ll({act: 'html'});
}

function cg_category(value = null){
    const elem = $("#category");
    if(value === null){
        return elem.val();
    } else {
        const point = $("#point_"+value);
        $("#submenu_" + value).slideToggle();
        $(".point").removeClass("check");
        point.addClass("check");
        elem.val(value);
    }
}

function cg_page(act = 'get', value = null){
    const elem = $("#page");
    const page = +elem.val();
    switch(act){
        default:
        case "get":
            return page;
        break;
        case "set":
            elem.val(value);
        break;
        case "next":
            elem.val(page + 1);
        break;
        case "prev":
            if(page > 1){
                elem.val(page - 1);
            }
        break;
        case "default":
            elem.val(1);
        break;
    }
}

function ll(d = {}){
    return new Promise((resolve, reject) => {
        const act = "act" in d ? d.act : "append";
        switch(act){
            default:
                cg_page('next');
                find_load_toggle("on");
            break;
            case "html":
                cg_page('default');
                find_all_load_toggle("on");
            break;
        }
        load_list().then(data => {
            const table = $("#find_table_body");
            switch(act){
                case "append":
                    table.append(data);
                break;
                case "html":
                    table.scrollTop(0);
                    table.html(data);
                break;
            }
            if(data == ""){
                cg_page('set', -1);
            }
            tooltip_update();
            find_load_toggle("off");
            find_all_load_toggle("off");
            findPreview("hide");
        });
    });
}

function findPreview(act){
    const preview = $("#grid_container__find-preview");
    switch(act){
        case "hide":
            preview.hide();
        break;
        case "show":
            preview.show();
        break;
    }
}

function addBtnDis(){
    const goods_table = $("#goods_table");
    const btn = $("#addBtn");
    if(goods_table.children(".goods_table-item").length > 0){
        btn.prop('disabled', false);
    } else {
        btn.prop('disabled', true);
    }
}

function load_list(){
    return new Promise((resolve, reject) => {
        const form = $("#mainForm").serialize();
        ///profile/specification/load_list
        window.xhr = $.post("/ajax.php?do=selected_find", form, function(data){
            resolve(data);
        });
    });
}

function cg_load(value = null){
    const table = $("#find_table_body");
    if(value === null){
        return table.data("load") == undefined || true ? true : false;
    } else {
        table.data("load", value);
    }
}

function scroll_load(This){
    const elem = $(This);
    if (cg_page('get') > 0) {
        if ((+elem.scrollTop() + +elem.height()) >= elem[0].scrollHeight) {
            if (cg_load()) {
                cg_load(false);
                ll().then(() => {
                    cg_load(true);
                });
            }
        }
    }
}

$("#search").on("keyup", function (e) {
    const search = $(this);
    if ((search.val().length > 2 || search.val().length === 0) && (e.keyCode !== 16 && e.keyCode !== 17 && e.keyCode !== 18)){
        const delay = 500;
    
        if(typeof window.xhr !== "undefined"){
            window.xhr.abort();
        }

        clearTimeout(search.data('timer'));
    
        search.data('timer', setTimeout(function(){
            search.removeData('timer');

            ll({act: 'html'});
        }, delay));
    }
});

function addGoods(id){
    const table = $("#goods_table");
    const elem = $("#count_"+id);
    if(elem.length > 0){
        const count = +elem.val();
        elem.val(count + 1);
        addAnim(id);
        addBtnDis();
    } else {
        get_goods_tov(id).then(tov => {
            table.append(tov);
            tooltip_update();
            positions_change();
            addAnim(id);
            addBtnDis();
        });
    }
    cg_changed(true);
    
}

function addAnim(id){
    const goods_name = "#goods_"+id;
    $("#item_"+id).effect("transfer", { 
        to: goods_name, className: "ui-effects-transfer" 
    });
    $(goods_name).effect("shake", "slow");
}

function get_goods_tov(id){
    return new Promise((resolve, reject) => {
        $.post("/profile/specification/goods_tov", {id}, function(tov){
            resolve(tov);
        });
    });
}

function delGoods(id){
    $("#goods_"+id).remove();
    cg_changed(true);
    positions_change();
    $(".tooltip").remove();
    addBtnDis();
}

function tooltip_update(){
    $(".bs_tooltip").tooltip("update");
}

function cg_changed(value = null){
    const form = $("#mainForm");
    if(value === null){
        const changed = form.data("changed") == undefined ? false : form.data("changed");
        return changed;
    } else {
        form.data("changed", value);
    }
}
//загрузка списка фильтров
function getFilter(load = true) {
    const cat = cg_category();
    const search = $("#search").val();
    $.post("/ajax.php?do=getFilter", {cat, search, site: ""}, function (data) {
        const new_data = data == "" ? "Фильтры отсутствуют" : data;
        $("#filter").html(new_data);
        if(load){
            ll({act: 'html'});
        }
    });
}
//раскрытие списка с фильтрами
$(document).on("click", ".filterItem", function () {
    $(this).next().slideToggle();
});
//выделение если есть фильтр
$(document).on("change", ".checkFilterItem", function () {
    const parent = $(this).parent().parent().parent().parent();
    const child = $(parent).find(".checkFilterItem");
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
    ll({act: 'html'});
});

function get_link_data(id){
    const elem = $("#goods_"+id);
    const link = {};
    link.count = elem.find(".count_input").eq(0);
    link.price = elem.find(".goods_table__price").eq(0);
    link.total = elem.find(".goods_table__total").eq(0);
    return link;
}

function get_data(id){
    const data = {};
    const link = get_link_data(id);

    data.count = Price.string_to_number(link.count.val());
    data.price = Price.string_to_number(link.price.html());
    data.total = Price.string_to_number(link.total.html());

    return data;
}

function change_data(id, field, value){
    const link = get_link_data(id);
    switch(field){
        default:
            link[field].html(value);
        break;
        case "count":
            link[field].val(value);
        break;
    }
}

function total_recount(id){
    const data = get_data(id);
    const total = data.price * data.count;
    change_data(id, "total", total.toFixed(2));
}

$(document).on("change", ".count_input", function(){
    const id = $(this).data("id");
    cg_changed(true);
    total_recount(id);
});

function grid_load_toggle(act = null){
    const elem = $("#grid_container_load");
    if(act === null){
        elem.fadeToggle();
        return;
    }
    switch(act){
        default:
        case "off":
            elem.fadeOut();	
        break;
        case "on":
            elem.fadeIn();	
        break;
    }
}

function find_all_load_toggle(act = null){
    const elem = $("#grid_container__find_load_all");
    if(act === null){
        elem.fadeToggle();
        return;
    }
    switch(act){
        default:
        case "off":
            elem.fadeOut();	
        break;
        case "on":
            elem.fadeIn();	
        break;
    }
}

function find_load_toggle(act = null){
    const elem = $(".grid_container__find_load");
    if(act === null){
        elem.toggle();
        return;
    }
    switch(act){
        default:
        case "off":
            elem.hide();	
        break;
        case "on":
            elem.show();	
        break;
    }
}

function selected_sort(This, field){
    const item = $(This);
    const sort_active = 'sort_active';
    const sorting_up_name = ".sorting_up";
    const sorting_down_name = ".sorting_down";
    const sorting_up = $(sorting_up_name);
    const sorting_down = $(sorting_down_name);
    const sorting_up_this = item.find(sorting_up_name);
    const sorting_down_this = item.find(sorting_down_name);
    const orderField = $("#orderField");
    const orderType = $("#orderType");
    
    if(item.hasClass(sort_active)){
        sorting_up_this.toggle();
        sorting_down_this.toggle();
        const orderTypeVal = orderType.val();
        switch(orderTypeVal){
            default:
            case "ASC":
                orderType.val('DESC');
            break;
            case "DESC":
                orderType.val('ASC');
            break;
        }
    } else {
        sorting_up.hide();
        sorting_down.hide();
        $("."+sort_active).removeClass(sort_active);
        item.addClass(sort_active);
        sorting_up_this.show();
        orderField.val(field);
        orderType.val('ASC');
    }
    ll({act: 'html'});
}