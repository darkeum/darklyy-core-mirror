$(function(){
    $page.default();
    $victoria.init();
    $mobile_filter.init();
    $('[data-toggle="tooltip"]').tooltip();
});

const $victoria = {
    items: $("#items_body"),
    basket: $("#items_basket_body"),
    search: $("#search"),
    form: $("#mainForm"),
    filter: $("#filter"),
    by_catalog: $("#by_catalog"),
    new_sku: $("#new_sku"),
    load: true,
    load_end: false,
    xhr: null,
    categories_level: 0,
    categories_width: 100,
    categories_bg_width: 90,
    categories_width_step: 10,
    id: false,
    url_add: false,
    url_update: false,
    update_body_selector: false,
    // url_load_list: "/ajax.php?do=victoria_load_list",
    // url_filter_data: "/ajax.php?do=victoria_filter",
    // url_get_item: "/ajax.php?do=victoria_get_item",
    window_close: true,
    async transfer_to_document(){
        const btn = $("#transfer_to_document");
        const btn_value = btn.html();
        try{
            if($(".items_basket_body_item").length > 0){
                btn.prop("disabled", true);
                btn.html(`<i class="fas fa-spinner fa-spin"></i>`);
                const form = new FormData(this.form[0]);
                await this.transfer_to_document_add(form);
                await this.transfer_to_document_update();
                this.close();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Внимание',
                    text: "Товары не выбраны",
                });
            }
        }catch(e){
            btn.prop("disabled", false);
            btn.html(btn_value);
            Swal.fire({
                icon: 'error',
                title: 'Ошибка добавления',
                text: e.message,
            });
        }
    },
    close(){
        if(this.window_close) window.close();
    },
    async transfer_to_document_add(form){
        let data;
        try {
            data = await(await fetch(this.url_add, {
                method: "POST",
                body: form,
            })).text();
            return data.length == 0 ? {} : JSON.parse(data);
        } catch (error) {
            throw {message: data};
        }
    },
    transfer_to_document_update(){
        return new Promise((resolve, reject) => {
            $.post(this.url_update, update => {
                $(this.update_body_selector, opener.window.document).html(update);
                resolve();
            });
        });
    },
    init(){
        this.id = $("#id").val();
        this.url_add = $("#url_add").val();
        this.url_update = $("#url_update").val();
        this.update_body_selector = $("#update_body_selector").val();

        this.search_init();
        this.scroll_init();
        this.create_goods_init();
        this.select_init();
        this.ll("first");
    },
    select_init(){
        const select_setting = {
            dropdownAutoWidth : true,
            width: 'auto'
        };
        $("#price_type").select2(select_setting);
        $("#stock_id").select2(select_setting);
        $("#cat").select2({
            placeholder: "Выберите категорию",
        });
        $(".select2_form").select2();
    },
    async add_basket(id){
        this.add_basket_load(id, "on");
        await this.add_basket_action(id);
        this.add_basket_load(id, "off");
        await this.add_basket_func(id);
    },
    async add_basket_action(id){
        const link = this.get_link_basket(id);
        const data = this.get_data_basket(id);
        if(link){
            const count = data.count;
            link.count.val(count + 1);
        } else {
            await this.get_item(id).then(data => {
                this.basket.append(data.template);
            }, error => {
                this.add_basket_error(error);
            });
        }
        this.basket.scrollTop(this.basket[0].scrollHeight);
        this.init_position();
    },
    init_position(){
        $(".position").each(function(index){
            $(this).html(index + 1);
        });
    },
    async add_basket_func(id){
        this.change_clicks(id);
    },
    add_basket_load(id, action){
        const btn = $(`#add_basket_${id}`);
        const btn_icon = $(`#add_basket_icon_${id}`);
        const icon = btn_icon.data("icon");
        const icon_load = "fas fa-spinner fa-spin";
        switch(action){
            case "on":
                btn_icon.removeClass(icon);
                btn_icon.addClass(icon_load);
                btn.prop("disabled", true);
            break;
            case "off":
                btn_icon.removeClass(icon_load);
                btn_icon.addClass(icon);
                btn.prop("disabled", false);
            break;
        }
    },
    add_basket_error(error){
        Swal.fire({
            icon: 'warning',
            title: 'Ошибка',
            text: error,
        });
    },
    delete_basket(id){
        const item = this.get_link_basket(id);
        if(item){
            item.item.remove();
            this.init_position();
        }
    },
    get_link(id){
        const result = {};
        const item = $(`#item_${id}`);

        result.item = item;
        result.title = item.find(".c_title");
        result.sku = item.find(".c_sku");
        result.price = item.find(".cpd_price");

        return result;
    },
    get_data(id){
        const result = {};
        const link = this.get_link(id);

        result.cid = id;
        result.title = link.title.text();
        result.sku = link.sku.text();
        result.price = Price.string_to_number(link.price.text());

        return result;
    },
    get_link_basket(id){
        const result = {};
        const item = $(`#item_basket_${id}`);
        if(item.length > 0){
            result.item = item;

            result.title = item.find(".c_title");
            result.sku = item.find(".c_sku");
            result.count = item.find(".count_inp");
            result.price = item.find(".cpd_price");
            result.total = item.find(".total_inp");
            result.total_container = item.find(".total");
            result.discount = item.find(".discount_inp");
            return result;
        } else {
            return false;
        }
    },
    get_data_basket(id){
        const link = this.get_link_basket(id);
        const result = {};
        if(link){
            result.title = link.title.text();
            result.sku = link.sku.text();
            result.count = Price.string_to_number(link.count.val());
            result.price = Price.string_to_number(link.price.text());
            result.total = Price.string_to_number(link.total.val());
            result.discount = Price.string_to_number(link.discount.val());
            return result;
        } else {
            return false;
        }
    },
    get_item(id){
        return new Promise((resolve, reject) => {
            $.post(this.url_get_item, {id, main_id:this.id}, function(json){
                try{
                    const data = JSON.parse(json);
                    resolve(data);
                }catch(e){
                    reject(`${e} (${json})`);
                }
            })
        });
    },
    scroll_init(){
        const This = this;
        this.items.on('scroll', function() {
            if($(this).scrollTop() + $(this).innerHeight() >= ($(this)[0].scrollHeight - 100)) {
                This.ll("more");
            }
        })
    },
    async clear_search(){
        this.search.val("");
        await this.ll("first");
    },
    async clear_filter(){
        $(".point_radio").prop("checked", false);
        this.filter.html("");
        await this.ll("first");
    },
    async ll(type){
        //"first", "more", "filter"
        switch(type){
            default:
            case "first":
                await this.load_first();
            break;
            case "filter":
                await this.load_first();
            break;
            case "more":
                if(this.load && !this.load_end){
                    this.load = false;
                    await this.load_more();
                    this.load = true;
                } 
            break;
        }
    },
    async load_first(){
        this.load_end = false;
        $page.default();
        $cssload_loader.show();
        $cssload_filter_loader.show();
        await this.load_list().then(data => {
            this.items.html(data.template);
            this.items.scrollTop(0);
        }, error => {
            Swal.fire({
                icon: 'error',
                title: 'Ошибка',
                text: error,
            });
        });
        $cssload_loader.hide();
        $cssload_filter_loader.hide();
    },
    async load_more(){
        const page = $page.get();
        $page.set(page + 1);
        $fountainG.show();
        await this.load_list().then(data => {
            if(data.count > 0){
                this.items.append(data.template);
            } else {
                this.load_end = true;
            }
        }, error => {
            Swal.fire({
                icon: 'error',
                title: 'Ошибка',
                text: error,
            });
        });
        $fountainG.hide();
    },
    load_list(){
        return new Promise((resolve, reject) => {
            const form = this.form.serialize();
            this.xhr = $.post(this.url_load_list, form, data => {
                try{
                    const json = JSON.parse(data);
                    resolve(json);
                } catch(e){
                    reject(`${data} (${e.message})`);
                }
            });
        });
    },
    search_init(){
        const This = this;
        this.search.on("keyup", function(e){
            const search = $(this);
            const value = search.val();
            const delay = 500;
            const timer_name = "timer";
            if((value.length > 2 || value.length === 0) && (e.keyCode !== 16 && e.keyCode !== 17 && e.keyCode !== 18)){
                if(This.xhr !== null){
                    This.xhr.abort();
                    This.load = true;
                    $cssload_loader.hide();
                    $fountainG.hide();
                }
                clearTimeout(search.data(timer_name));
            
                search.data(timer_name, setTimeout(function(){
                    search.removeData(timer_name);
                    This.ll("first");
                    This.filter_load();
                }, delay));
        
            }
        });
        $(".search_ll").on("change", () => {
            this.filter_load();
            This.ll("first");
        });
    },
    change_categories(){
        if(this.by_catalog.prop("checked")){
            this.ll("first");
        }
        this.filter_load();
    },
    end_categories(){
        this.by_catalog.prop("checked", true);
    },
    open_categories(id){
        const item = $(`#children_ul_div_${id}`);
        const cc_minus = this.categories_level * this.categories_width_step;
        const categories_width = this.categories_width - cc_minus;
        item.css("width", `${categories_width}%`).show();
        const categories_bg_width = this.categories_bg_width - cc_minus;
        item.children().css("width", `${categories_bg_width}%`).show("slide", { direction: "left" }, 500);
        this.categories_level++;
        item.data("level", this.categories_level);
    },
    hide_categories(id){
        const item = $(`#children_ul_div_${id}`);
        item.children().hide("slide", { direction: "left" }, 500, () => {
            item.hide();
            item.find(".children_ul_div").hide().children().hide();
        });
        const level = item.data("level");
        this.categories_level = level - 1;
    },
    sku_generate(){
        const cat = $("#cat").val();
        const infoSku = $("#infoSku");
        if(cat == ""){
            console.log(cat);
            infoSku.html("Выберите категорию!");
            infoSku.css({ 'color': 'red' });
            infoSku.show();
        } else {
            // $("#sku").val("AE05" + cat + "XXXXXX");
            $("#sku").val(this.new_sku.val());
            infoSku.html("Артикул будет создан автоматически");
            infoSku.css({ 'color': 'green' });
            infoSku.show();
        }
    },
    async create_goods_init(){
        const This = this;
        const createGoods = $("#createGoods");
        createGoods.on("submit", async function(e){
            e.preventDefault();
            const form = createGoods.serialize();
            const btn = $("#createGoodsBtn");
            const btn_value = btn.html();
            btn.html(`<i class="fas fa-spinner fa-spin"></i>`);
            btn.prop("disabled", true);
            await This.create_goods(form).then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Успешно',
                    text: `Товар создан (${data.id})`,
                });
                $("#addGoodsModal").modal("hide");
            }, error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Ошибка',
                    text: error,
                });
            });
            $("#sku").val("");
            btn.html(btn_value);
            btn.prop("disabled", false);
        });
    },
    create_goods(request){
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=addGoods", request, function(json){
                try{
                    const data = JSON.parse(json);
                    resolve(data);
                } catch(e){
                    reject(`${e} (${json})`);
                }
            });
        });
    },
    change_sort(This){
        const item = $(This);
        const sia = "sortIconActive";
        $(".sortIcon").removeClass(sia);
        item.find(".sortIcon").addClass(sia);
        item.find("input").prop("checked", true);
        this.ll("first");
    },
    async filter_load(){
        $cssload_filter_loader.show();
        await this.filter_data().then(data => {
            this.filter.html(data.template);
        }, error => {
            Swal.fire({
                icon: 'error',
                title: 'Ошибка',
                text: error,
            });
        });
        $cssload_filter_loader.hide();
    },
    filter_data(){
        return new Promise((resolve, reject) => {
            const form = this.form.serialize();
            $.post(this.url_filter_data, form, json => {
                try{
                    const data = JSON.parse(json);
                    resolve(data);
                } catch(e){
                    reject(`${e} (${json})`);
                }
            });
        });
    },
    change_clicks(id){
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=catalog_change_clicks", {id}, function(){
                resolve();
            });
        })
    },
    toggle_filter(This){
        $(This).next().slideToggle();
    },
    get_total_value(id){
        const data = this.get_data_basket(id);
        const price = new Price;
        const pd = price.get_all(data);
        return pd.total;
    },
    change_total(id){
        const link = this.get_link_basket(id);
        const total = this.get_total_value(id);
        link.total.val(total);
    },
}

const $page = {
    item: $("#page"),
    default(){
        this.set(1);
    },
    get(){
        return +this.item.val();
    },
    set(value){
        this.item.val(value);
    },
};

class FountainG{
    constructor(id){
        this.item = $(`#${id}`);
    }
    show(){
        this.item.show();
    }
    hide(){
        this.item.hide();
    }
}

const $fountainG = new FountainG("fountainG");

class CSSLoad{
    constructor(request){
        this.blur_count = 5;
        this.item = request.item;
        this.item_body = request.item_body;
    }
    show(){
        this.item.show();
        this.blur(this.blur_count);
    }
    hide(){
        this.item.hide();
        this.blur(0);
    }
    blur(value){
        this.item_body.css({
           'filter'         : `blur(${value}px)`,
           '-webkit-filter' : `blur(${value}px)`,
           '-moz-filter'    : `blur(${value}px)`,
           '-o-filter'      : `blur(${value}px)`,
           '-ms-filter'     : `blur(${value}px)`
        });
    }
}

const $cssload_loader = new CSSLoad({
    item: $("#cssload_loader"),
    item_body: $("#grid__items"),
});
const $cssload_filter_loader = new CSSLoad({
    item: $("#cssload_filter_loader"),
    item_body: $("#filter"),
});

const $mobile_filter = {
    item: $("#mobile_filter"),
    categories: $(".grid__categories"), 
    filter: $(".grid__filter"), 
    mfc: "mobile_filter_categories", 
    mff: "mobile_filter_filter", 
    init(){
        $(".open_filter").on("click", () => {
            this.show();
        });
        $(".close_filter").on("click", () => {
            this.hide();
        });
        this.item.on("click", () => {
            this.hide();
        });
    },
    show(){
        this.item.show("slide", { direction: "right" }, 700, () => {
            this.categories.addClass(this.mfc);
            this.filter.addClass(this.mff);
        });
        $("body").css("overflow", "hidden");
    },
    hide(){
        this.item.hide("slide", { direction: "right" }, 700);
        this.categories.removeClass(this.mfc);
        this.filter.removeClass(this.mff);
        $("body").css("overflow", "");
    },
}

class ModalGenerate{
    constructor(request){
        //{name: "", url: ""}
        const name = request.name;
        const url = request.url;
        this.modal = $(`#${name}Modal`);
        this.body = $(`#${name}Body`);
        this.url = url;
    }
    async show(id){
        this.modal.modal("show");
        await this.load(id).then(data => {
            this.body.html(data.template);
        }, error => {
            // Swal.fire({
            //     icon: 'error',
            //     title: 'Ошибка',
            //     text: error,
            // });
        });
    }
    hide(){
        this.modal.modal("hide");
        this.body.html("Загрузка сертификатов...");
    }
    load(id){
        return new Promise((resolve, reject) => {
            $.post(this.url, {id}, function(json){
                try{
                    const data = JSON.parse(json);
                    resolve(data);
                } catch(e){
                    reject(`Ошибка ${e} (${json})`);
                }
            });
        });
    }
}

const $cert_modal = new ModalGenerate({name: "cert", url: "/ajax.php?do=victoria_cert"});
const $attribute_modal = new ModalGenerate({name: "attribute", url: "/ajax.php?do=victoria_attribute"});