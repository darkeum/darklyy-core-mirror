class ExtendedSort{
    modal = $("#extendedSortModal");
    ul_sort = $("#extendedSortModalSort");
    ul_search = $("#extendedSortModalSearchItems");
    form = $("#extendedSortModalForm");
    input_link = $("#extendedSortModalLink");
    input_link_update = $("#extendedSortModalLinkUpdate");
    input_item_update = $("#extendedSortModalItemUpdate");
    link = false;
    link_update = false;
    item_update = false;
    sort_items = [
        {name: "ID", field: "t_id", type:"field"},
        {name: "CID", field: "id", type:"field"},
        {name: "Наименование", field: "title", type:"field"},
        {name: "Категория", field: "category", type:"value", more: true, more_function: this.category_gen.bind(this), parent_id: 0},
        {name: "Производитель", field: "man", type:"field"},
    ];
    constructor(){
        
    }
    modal_show(){
        this.modal.modal("show");
    }
    modal_hide(){
        this.modal.modal("hide");
    }
    init(){
        this.sort_init();
        this.sort_items_init();
        this.sort_send_init();
        this.data_init();
        this.btn_init();
        this.template_init();
    }
    sort_init(){
        this.ul_sort.sortable({
            handle: ".extendedSortItem__order",
            axis: "y",
            update: this.sort_position,
        });
    }
    sort_items_init(){
        this.ul_search.html("");
        this.sort_items.forEach(item => {
            this.ul_search.append(this.sort_item(item));
        });
    }
    sort_send_init(){
        const This = this;
        This.form.on("submit", async function(e){
            e.preventDefault();
            const request = new FormData(this);
            const lw = new LW;
            lw.on();
            This.modal_hide();
            await This.sort_update_items(request);
            await This.sort_update();
            lw.off();
        });
    }
    async sort_update_items(request){
        return await (await fetch(this.link, {
            method: "POST",
            body: request,
        })).text();
    }
    sort_update(){
        return new Promise((resolve, reject) => {
            $.post(this.link_update, data => {
                $(this.item_update).html(data);
                resolve();
            });
        });
    }
    data_init(){
        this.link = this.input_link.val();
        this.link_update = this.input_link_update.val();
        this.item_update = this.input_item_update.val();
    }
    btn_init(){
        $("#extendedSortModalBtn").on("click", this.open.bind(this));
    }
    sort_item(item){
        const li = $(`<li></li>`);
        const div2 = $(`<div style="flex: 2;">${item.name}</div>`);
        const move_sort = "move_sort" in item ? item.move_sort : true;
        const i = move_sort ? $(`<i class="fas fa-caret-right cp move_sort"></i>`)
        .on("click", () => {
            item.li = li;
            this.move_sort(item);
        }) : "";
        if(item.more){
            const ul = $(`<ul class="items_list_more" style="display:none;"></ul>`);
            div2.on("click", () => {
                this.toggle_more(ul, item);
            });
            li.append(ul);
            div2.addClass(`font-weight-bold cp`);
        }
        const div = $(`<div class="d-flex justify-content-between align-items-center"></div>`).append(div2).append(i);
        return li.append(div);
    }
    open(){
        this.template_items();
        $(".items_list_more").hide();
        this.ul_search.css("overflow-y", "auto").scrollTop(0);
        this.ul_search.find("ul").css("overflow-y", "auto").scrollTop(0);
        this.modal_show();
    }
    toggle_more(ul, item){
        ul.show();
        const ul_parent = ul.parents("ul").eq(0);
        this.ul_parent_top = ul_parent.scrollTop();
        ul_parent.css("overflow-y", "hidden").scrollTop(0);
        if(!ul.html().length){
            ul.html(`<li>Загрузка данных</li>`);
            item.more_function(item).then(data => {
                ul.html(this.toggle_more_back());
                data.forEach(item => {
                    ul.append(item);
                });
            });
        }
    }
    back(This){
        const ul = $(This).parents("ul");
        ul.eq(0).hide();
        ul.eq(1).css("overflow-y", "auto").scrollTop(this.ul_parent_top);
    }
    toggle_more_back(){
        const This = this;
        return $(`<li class="cp"><i class="fas fa-angle-left"></i></li>`)
        .on("click", function(){
            This.back(this);
        });
    }
    async category_gen(ii){
        const data = await this.get_category();
        const items = [];
        const parent_id = ii.parent_id;
        const links = data.links[ parent_id ];
        if(links != undefined && links.length){
            links.forEach(item => {
                const prepend_item = {};
                prepend_item.name = item.name;
                prepend_item.field = "category";
                prepend_item.value = item.id;
                prepend_item.type = "value";
                if(item.id in data.links || item.id in data.attribute){
                    prepend_item.more = true;
                    prepend_item.parent_id = item.id;
                    prepend_item.more_function = this.category_gen.bind(this);
                }
                items.push(this.sort_item(prepend_item));
            });
        }
        if(parent_id in data.attribute){
            items.push(this.sort_item({name:"Атрибуты", field:"attribute", type:"field", more: true, more_function: this.attribute_gen.bind(this), move_sort: false, parent_id}));
        }
        return items;
    }
    get_category(){
        return new Promise((resolve, reject) => {
            if(this.category_cache){
                resolve(this.category_cache);
            } else {
                $.post(`/ajax.php?do=extendedSortGetCategory`, json => {
                    const data = JSON.parse(json);
                    this.category_cache = data;
                    resolve(data);
                });
            }
        });
    }
    async attribute_gen(ii){
        const items = [];
        const data = await this.get_category();
        data.attribute[ ii.parent_id ].forEach(item => {
            const prepend_item = {};
            prepend_item.name = item.title;
            prepend_item.field = "attribute";
            prepend_item.value = item.id;
            prepend_item.type = "value";
            items.push(this.sort_item(prepend_item));
        });
        return items;
    }
    get_sort_id(field, value = false){
        const value_postfix = value ? `_${value}` : ``;
        return `sort_${field}${value_postfix}`;
    }
    move_sort(request){
        const field = request.field;
        const value = "value" in request ? request.value : false;
        const id_field = this.get_sort_id(field, value);
        if($(`#${id_field}`).length){
            request.li.effect("shake", 500);
        } else {
            this.append_sort(request);
        }
    }
    append_sort(request){
        let new_request = {};
        switch(request.type){
            default:
            case "field":
                new_request = this.append_sort_field(request);
            break;
            case "value":
                new_request = this.append_sort_value(request);
            break;
        }
        const field = request.field;
        const t = new_request.t;
        const order = "order" in request ? request.order : "ASC";
        new_request.name = request.name;
        new_request.input_name = this.append_sort_input_gen(`sort[${t}][name]`, request.name);
        new_request.input_type = this.append_sort_input_gen(`sort[${t}][type]`, request.type);
        new_request.input_field = this.append_sort_input_gen(`sort[${t}][field]`, field);
        new_request.sort_asc = this.append_sort_sort_gen(`sort[${t}][order]`, `ASC`, order == `ASC`);
        new_request.sort_desc = this.append_sort_sort_gen(`sort[${t}][order]`, `DESC`, order == `DESC`);
        this.append_sort_li(new_request);
    }
    append_sort_input_gen(name, value){
        return `<input type="hidden" name="${name}" value="${value}">`;
    }
    append_sort_sort_gen(name, value, checked){
        const c = checked ? "checked" : "";
        return `<input type="radio" class="mr10" name="${name}" value="${value}" ${c}>`;
    }
    generate_id(){
        const rand = Math.floor(1 + Math.random() * 50000);
        const date = +Date.now();
        const id = date + rand;
        return id;
    }
    append_sort_value(request){
        const new_request = {};
        const field = request.field;
        const value = request.value;
        const t = this.generate_id();
        new_request.t = t;
        new_request.id = this.get_sort_id(field, value);
        new_request.input_value = this.append_sort_input_gen(`sort[${t}][value]`, value);
        return new_request;
    }
    append_sort_field(request){
        const new_request = {};
        const field = request.field;
        const t = this.generate_id();
        new_request.t = t;
        new_request.id = this.get_sort_id(field);
        new_request.input_value = this.append_sort_input_gen(`sort[${t}][value]`, field);
        return new_request;
    }
    append_sort_li(request){
        const id = request.id;
        const input_name = request.input_name;
        const input_field = request.input_field;
        const input_value = request.input_value;
        const input_type = request.input_type;
        const name = request.name;
        const sort_asc = request.sort_asc;
        const sort_desc = request.sort_desc;

        const i = $(`<i class="fas fa-times cp"></i>`)
        .on("click", () => {
            this.delete_sort(`#${id}`);
        });
        const div2 = $(`<div class="extendedSortItem__main">
            <div style="flex: 1;">
                ${input_name}
                ${input_field}
                ${input_value}
                ${input_type}
                ${name}
            </div>
            <div class="mr10">
                <label class="mr10" title="от меньших значений к большим (ASC)">${sort_asc}<i class="fas fa-sort-numeric-down fa-lg"></i></label>
                <label title="от больших значений к меньшим (DESC)">${sort_desc}<i class="fas fa-sort-numeric-up fa-lg"></i></i></label>
            </div>
        </div>`).append(i);
        const div = $(`<div class="d-flex align-items-center">
            <div class="extendedSortItem__order"><i class="fas fa-grip-lines"></i></div>
        </div>`).append(div2);
        const li = $(`<li id="${id}"></li>`).append(div);
        this.ul_sort.append(li);
    }
    delete_sort(selector){
        $(selector).remove();
    }
    template_init(){
        $("#extendedSortModalTemplateBtnSave").on("click", this.template_save_modal.bind(this));
        // this.template_items();
    }
    async template_items(){
        const extendedSortModalTemplate = $("#extendedSortModalTemplate");
        extendedSortModalTemplate.html(`<li>Загрузка данных</li>`);
        const items = await this.template_get();
        extendedSortModalTemplate.html(``);
        if(items.length > 0){
            items.forEach(item => {
                const common_flag = item.common == 1;
                const common = common_flag ? "common" : "";
                const li = $(`<li class="cp d-flex justify-content-between ${common}">
                    <div>${item.name}</div>
                </li>`).on("click", () => {
                    this.template_restore(JSON.parse(item.template));
                });
                if(!common_flag){
                    const del_div = $(`<div><i class="fas fa-times cp"></i></div>`).on("click", (e) => {
                        e.stopPropagation();
                        swal({
                            title: "Вы уверены?",
                            text: "Удалить шаблон",
                            icon: "warning",
                            buttons: true,
                            dangerMode: true,
                        })
                          .then( async (willDelete) => {
                            if (willDelete) {
                              await this.template_delete(item.id);
                              this.template_items();
                            }
                        });
                    });
                    li.append(del_div);
                }
                extendedSortModalTemplate.append(li);
            });
        } else {
            extendedSortModalTemplate.append(`<li>Шаблоны отсутствуют</li>`);
        }
    }
    template_save_modal(){
        const template = this.form.serialize();
        if(template){
            swal("Введите имя шаблона:", {
                content: "input",
            })
            .then(async name => {
                if(name){
                    await this.template_save(`name=${name}&${template}`);
                    await this.template_items();
                } else {
                    swal("Введите имя шаблона!");
                }
            });
        } else {
            swal("Выберите позиции!");
        }
    }
    template_save(request){
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=extendedSortTemplateSave", request, function(){
                resolve();
            })
        });
    }
    template_get(){
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=extendedSortTemplateGet", json => {
                resolve(JSON.parse(json));
            });
        });
    }
    template_restore(template){
        this.ul_sort.html(``);
        template.forEach(item => {
            this.append_sort(item);
        });
    }
    template_delete(id){
        return new Promise((resolve, reject) => {
            $.post("/ajax.php?do=extendedSortTemplateDelete", {id}, () => {
                resolve();
            });
        });
    }
}