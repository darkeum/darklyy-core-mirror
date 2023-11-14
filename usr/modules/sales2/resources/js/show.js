window.onbeforeunload = function () {
    if(vm && vm.is_changed){
        return "Уйти";
    }
}
const sales_lw = new LW;
const app = Vue.createApp({
    compilerOptions: {
        delimiters: ['${', '}'],
        comments: true
    },
    computed: {
        is_changed(){
            return this.check_CRDU();
        },
        is_checked(){
            return this.getCheckedItems().length > 0;
        },
    },
    data(){
        return {
            sales: JSON.parse(first_data_sales),
            sales_detail: JSON.parse(first_data_sales_detail),
            headButtons: {
                flag: true,
                style: {},
            },
            bodySwitch: [true,true,0,true,true],
            modals: getModalData(),
            selectMassAction: false,
            comments_count: 0,
            bindings_list: {},
            bindings_list_id: {},
            editable: {},
            purchase_price_hide: (Cookies.get("purchase_price_hide") ?? true) === "false" ? false : true,
            contract: {},
            specification: {},
            confirm_supply_order: [],
        };
    },
    mounted(){
        const sortable = new Sortable(document.getElementById("sales_table_tbody"), {
            handle: ".position",
            direction: 'vertical',
            scroll: document.getElementById("sales_table_tbody"), // Enable the plugin. Can be HTMLElement.
	        forceAutoscrollFallback: true, // force autoscroll plugin to enable even when native browser autoscroll is available
	        scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
	        scrollSpeed: 10, // px, speed of the scrolling
	        bubbleScroll: true, // apply autoscroll to all parent elements, allowing for easier movement
            onEnd: evt => {
                this.sales_detail.splice(evt.newIndex, 0, this.sales_detail.splice(evt.oldIndex, 1)[0]);
                this.init_binding_list();
                this.sort_items();
            },
        });
        this.init();
        modalsInit(this);
    },
    methods: {
        nf(value){
            return Price.priceView(value, {
                postfix: `₽`,
                postfix_space: ` `,
                mark: `,`,
            })
        },
        nfp(value){
            const num = Number.parseFloat(value);
            return `${num.toFixed(2).replace(".", ",")}%`;
        },
        init(){
            this.re_purchase();
            this.sales_detail.forEach((item, index) => {
                this.init_mark(item);
                if (item.isSupplied) {
                    item.class = 'view_supply_order';
                } else {
                    item.class = {};
                }
            })
            this.init_binding_list();
        },
        init_binding_list(){
            this.bindings_list = {};
            this.bindings_list_id = {};
            this.sales_detail.forEach((item, index) => {
                this.bindings_list[ item.uniqid ] = index;
                if(item?.id){
                    this.bindings_list_id[ item.id ] = index;
                }
            })
        },
        async price_type_select(item){
            if(this.sales.price_type_list[ item.price_type ].type != "OTHER"){
                const data = await $priceTypeSelect.get_price({
                    type: item.price_type,
                    id: item?.id ?? null,
                    cid: item.cid,
                    sales_detail: JSON.stringify(item),
                });
                const price = new Price;
                const price_data = price.get_all({
                    price: data.price,
                    count: item.count,
                    discount: item.discount,
                    nds_proc: item.nds_proc,
                    nds_includes: this.sales.nds_includes,
                });
                this.set_new_price_data(item, price_data);
                this.re_price();
            }
            item.update_ = true;
        },
        re_total(){
            let total = 0;
            this.sales_detail.forEach(item => {
                if(!item.delete_){
                    total += +item.total;
                }
            });
            this.sales.total = total;
        },
        re_nds(){
            let nds = 0;
            this.sales_detail.forEach(item => {
                if(!item.delete_){
                    nds += +item.nds;
                }
            });
            this.sales.nds = nds;
        },
        re_price(){
            this.re_total();
            this.re_nds();
        },
        re_purchase(){
            const purchase = [0,0,0];
            this.sales_detail.forEach(item => {
                if(!item.delete_){
                    // purchase[0] += +item.purchase;
                    purchase[0] = purchase[1] += +(item.purchase.price * item.count);
                    purchase[2] += item.total - +(item.purchase.price * item.count);
                }
            });
            this.sales.purchase = purchase;
        },
        edit_field(index, prop){
            const item = this.sales_detail[ index ];
            item[ prop ] = true;
            this.$nextTick(() => {
                const input = this.$refs[prop][index];
                input.focus();
                input.select();
            });
        },
        edit_count(index){
            this.edit_field(index, `count_edit`);
        },
        edit_price(index){
            const item = this.sales_detail[ index ];
            if(item.price_type == 4){
                this.edit_field(index, `price_edit`);
            } else {
                Swal.fire(`Для изменения цены, измените тип цены`);
            }
        },
        edit_discount(index){
            this.edit_field(index, `discount_edit`);
        },
        edit_total(index){
            this.edit_field(index, `total_edit`);
        },
        save_count(item){
            item.count_edit = false;
            if(item.count <= 0 || item.count == "") item.count = 1;
            item.update_ = true;
            this.re_price_data_item(item);
            this.re_purchase();
        },
        save_price(item){
            item.price_edit = false;
            if(item.s_price == "") item.s_price = 0;
            item.update_ = true;
            this.re_price_data_item(item);
        },
        save_discount(item){
            item.discount_edit = false;
            if(item.discount == "") item.discount = 0;
            item.update_ = true;
            this.re_price_data_item(item);
        },
        save_total(item){
            item.total_edit = false;
            if(item.total == "") item.total = 0;
            item.discount = ( 100 * (item.s_price * item.count - item.total) ) / (item.s_price * item.count);
            item.update_ = true;
            this.re_price_data_item(item);
        },
        re_price_data_item(item){
            const price = new Price;
            const price_data = price.get_all({
                price: item.s_price,
                count: item.count,
                discount: item.discount,
                nds_proc: item.nds_proc,
                nds_includes: this.sales.nds_includes,
            });
            this.set_new_price_data(item, price_data);
            this.re_price();
            this.re_purchase();
        },
        set_new_price_data(item, price_data){
            item.s_price = price_data.price;
            item.discount_sum = price_data.discount_sum;
            item.total = price_data.total;
            item.nds = price_data.nds;
        },
        checkbox_all(){
            this.sales_detail.forEach(item => {
                item.checkbox = this.sales.checkbox;
            })
        },
        appendGoods(){
            appendGoods();
        },
        async replaceGoods(index){
            const item = this.sales_detail[ index ];
            if(await this.check_can_delete_items([item])){
                replaceGoods(index);
            }
        },
        change_sales(data){
            this.sales = Object.assign(this.sales, data.sales);
        },
        async change_sales_detail(data){
            this.sales_detail = [];
            await this.$nextTick(() => {
                this.sales_detail = data.sales_detail;
                this.init();
            });
        },
        async refresh_head(){
            sales_lw.on();
            const data = await this.get_show("head");
            this.change_sales(data);
            sales_lw.off();
        },
        async refresh(){
            sales_lw.on();
            const data = await this.get_show("body");
            this.change_sales(data);
            await this.change_sales_detail(data);
            load_start_detail();
            sales_lw.off();
        },
        async get_show(display){
            return await ( await fetch(`${router.show}?display=${display}`) ).json();
        },
        check_CRD(){
            for(let i = 0; i < this.sales_detail.length; i++){
                const item = this.sales_detail[i];
                if(item.create_ || item.replace_ || item.delete_) return true;
            }
            return false;
        },
        check_CRDU(){
            for(let i = 0; i < this.sales_detail.length; i++){
                const item = this.sales_detail[i];
                if(item.create_ || item.replace_ || item.delete_ || item.update_) return true;
            }
            return false;
        },
        async save(){
            let response;
            try {
                sales_lw.on();
                const form = new FormData;
                form.append("sales_id", this.sales.id);
                form.append("sales_detail", JSON.stringify(this.sales_detail));
                response = await (fetch(router.save, {
                    method: "POST",
                    body: form,
                }));
                if(response.ok){
                    const result = await response.json();
                    const update_ndr = [];
                    result.sales_detail.forEach(item_ => {
                        if(item_.del == 1){
                            this.delete_item_(this.bindings_list[ item_.uniqid ]);
                        } else {
                            const item = this.sales_detail[ this.bindings_list[ item_.uniqid ] ];
                            item.id = item_.id;
                            if(item_?.update_ndr){
                                update_ndr.push(item);
                            }
                        }
                    });
                    if(update_ndr.length){
                        this.load_ndr(update_ndr);
                    }
                    this.init();
                } else {
                    throw new Error(await response.text());
                }
            } catch (error) {
                throw_modal(error);
            } finally{
                sales_lw.off();
            }
        },
        init_mark(item){
            item.create_ = false;
            item.update_ = false;
            item.replace_ = false;
            item.delete_ = false;
        },
        async delete_item_modal(index){
            const item = this.sales_detail[ index ];
            if(await this.check_can_delete_items([item])){
                Swal.fire({
                    title: "Вы уверены?",
                    text: "Удалить позицию!",
                    icon: "info",
                    showConfirmButton: true,
                    showCancelButton: true,
                  })
                  .then((willDelete) => {
                    if (willDelete.isConfirmed) {
                        this.delete_item(item.uniqid);
                    }
                  });
            }
        },
        is_now_delete(item){
            return item.create_;
        },
        async delete_item(uniqid){
            const index = this.bindings_list[ uniqid ];
            const item = this.sales_detail[ index ];
            if(this.is_now_delete(item)){
                this.delete_item_(index);
            } else {
                item.delete_ = true;
            }
            this.re_price();
            this.re_purchase();
            this.sort_items();
        },
        delete_item_(index){
            this.sales_detail.splice(index, 1);
            this.init_binding_list();
        },
        async reserve_item(index){
            const item = this.sales_detail[ index ];
            if(item.create_ || item.replace_){
                Swal.fire("Внимание!", "Прежде чем поставить в резерв, сохраните позицию!", "warning");
            } else {
                try {
                    sales_lw.on();
                    const response = await( await fetch(router.ajax.reserve.action.replace("@id", item.id)) ).json();
                    sales_lw.off();
                    switch(response.action){
                        default:
                            notif("primary", "Информация", "Действия не указаны!");
                        break;
                        case "add":
                            this.reserve_modal("Добавить в резерв!", router.ajax.reserve.create, [item]);
                        break;
                        case "delete":
                            this.reserve_modal("Удалить резерв!", router.ajax.reserve.delete, [item]);
                        break;
                    }
                } catch (error) {
                    sales_lw.off();
                    throw_modal(error);
                }
            }
        },
        reserveModalAll(){
            const is_all = this.modals.reserveModal.all;
            document.querySelectorAll(".reserveModalOrgCheckbox").forEach(item => {
                item.checked = is_all;
            });
        },
        check_reserve_item(item){
            if(item.create_ || item.replace_){
                Swal.fire("Внимание!", "Прежде чем поставить в резерв, сохраните позицию!", "warning");
                return false;
            }
            return true;
        },
        reserve_modal_item(index, action){
            const item = this.sales_detail[ index ];
            if(this.check_reserve_item(item)){
                const modal = this.modals.reserveModal;
                modal.action = action;
                modal.id = [item.id];
                modal.modal.show();
            }
        },
        async reserve_action(e){
            try {
                const modal = this.modals.reserveModal;
                modal.modal.hide();
                sales_lw.on();
                const action = e.target.dataset.action;
                const form = new FormData(e.target);
                modal.id.forEach((value, index, array) => {
                    form.append("data[id][]", value);
                });
                form.append("sales_id", this.sales.id);
                const url = router.ajax.reserve[ action ];
                const new_ndr = await ( await fetch(url, {
                    method: "POST",
                    body: form,
                }) ).json();
                new_ndr.forEach(item => {
                    this.sales_detail[ this.bindings_list_id[ item.id ] ].ndr = item.ndr;
                });
            } catch (error) {
                throw_modal(error);
            } finally{
                sales_lw.off();
            }
        },
        reserve_modal(text, link, items){
            Swal.fire({
                title: "Вы уверены?",
                text,
                icon: "info",
                showConfirmButton: true,
                showCancelButton: true,
              })
              .then(async (willReserve) => {
                if (willReserve.isConfirmed) {
                    const form = new FormData();
                    form.append("sales_id", this.sales.id);
                    form.append("data[org][]", this.sales.org);
                    form.append(`data[stock][${this.sales.org}]`, this.sales.stock_id);
                    items.forEach(item => {
                        item.ndr.load = true;
                        form.append("data[id][]", item.id);
                    })
                    const new_ndr = await ( await fetch(link, {
                        method: "POST",
                        body: form,
                    }) ).json();
                    new_ndr.forEach(item => {
                        this.sales_detail[ this.bindings_list_id[ item.id ] ].ndr = item.ndr;
                    })
                }
              });
        },
        toggleHeadButtons(){
            this.headButtons.flag = !this.headButtons.flag;
            this.headButtons.style = this.headButtons.flag ? {} : {height: "calc(100% - 20px)"};
        },
        async showStatus(item){
            const modal = this.modals.statusModal;
            modal.data = "";
            modal.modal.show();
            modal.load = true;
            modal.data = await ( await fetch(router.ajax.status_log.replace("@id", item.id)) ).text();
            modal.load = false;
        },
        changePurchasePrice(index, max){
            this.bodySwitch[index] = (this.bodySwitch[index] + 1 >= max) ? 0 : (this.bodySwitch[index] + 1);
        },
        getCheckedItems(){
            const items = [];
            this.sales_detail.forEach(item => {
                if(item.checkbox){
                    items.push(item);
                }
            })
            return items;
        },
        getCheckedIndex(){
            const items = [];
            this.sales_detail.forEach((item, index) => {
                if(item.checkbox){
                    items.push(index);
                }
            })
            return items;
        },
        placeOrderModal(){
            if(this.is_checked){
                if(this.save_notice_modal()){
                    this.modals.placeOrderModal.flag = false;
                    this.modals.placeOrderModal.list.forEach((item, index) => {
                        this.modals.placeOrderModal.list[index] = false;
                    })
                    this.modals.placeOrderModal.modal.show();
                }
            } else {
                no_goods_check();
            }
        },
        async reset_stock_id_view(){
            Cookies.remove(stock_id_view_name);
            await this.refresh();
            this.editable.stock_view.setValue(this.sales.stock_id_view);
        },
        async massAction(){
            switch(this.selectMassAction){
                default:
                    notif('primary', 'Информация', 'Действие не указано!');
                break;
                case "discount":
                    Swal.fire({
                        title: 'Введите скидку:',
                        input: 'text',
                        showCancelButton: true,
                    })
                    .then((result) => {
                        if(result.isConfirmed){
                            this.getCheckedItems().forEach(item => {
                                item.discount = result.value;
                                this.save_discount(item);
                            })
                        }
                    });
                break;
                case "type_price":
                    const inputOptions = {};
                    for(key in this.sales.price_type_list){
                        inputOptions[ key ] = this.sales.price_type_list[ key ].title;
                    }
                    Swal.fire({
                        title: 'Выберите тип цены',
                        input: 'select',
                        inputOptions,
                        showCancelButton: true,
                      }).then(async (result) => {
                        if (result.isConfirmed) {
                            const jobs = [];
                            this.getCheckedItems().forEach(item => {
                                item.price_type = result.value;
                                jobs.push(this.price_type_select(item));
                            })
                            sales_lw.on();
                            await Promise.all(jobs);
                            sales_lw.off();
                        }
                      });
                break;
                case "update_checked_price":
                    Swal.fire({
                        title: 'Обновить цены?',
                        showCancelButton: true,
                    }).then( async (result) => {
                      if (result.isConfirmed) {
                          const jobs = [];
                          this.getCheckedItems().forEach(item => {
                            jobs.push(this.price_type_select(item));
                          })
                          sales_lw.on();
                          await Promise.all(jobs);
                          sales_lw.off();
                      }
                    });
                break;
                case "reserv_additional":
                case "reserv_del_additional":
                    const items = this.getCheckedItems();
                    const id = [];
                    for(key in items){
                        id.push(items[ key ].id);
                        if(!this.check_reserve_item(items[ key ])){
                            return;
                        }
                    }
                    const modal = this.modals.reserveModal;
                    modal.action = null;
                    if(this.selectMassAction == "reserv_additional"){
                        modal.action = "create";
                    }
                    if(this.selectMassAction == "reserv_del_additional"){
                        modal.action = "delete";
                    }
                    if(modal.action == null){
                        Swal.fire({
                            title: "Внимание",
                            text: 'Неизвестный тип резерва',
                            icon: "warning",
                        })
                        return;
                    }
                    modal.id = id;
                    modal.modal.show();
                break;
                case "reserv":
                    this.reserve_modal("Добавить в резерв!", router.ajax.reserve.create, this.getCheckedItems());
                break;
                case "reserv_del":
                    this.reserve_modal("Удалить резерв!", router.ajax.reserve.delete, this.getCheckedItems());
                break;
                case "del":
                    if(await this.check_can_delete_items(this.getCheckedItems())){
                        Swal.fire({
                            title: "Вы уверены?",
                            text: 'Удалить выбранные позиции?',
                            icon: "info",
                            showConfirmButton: true,
                            showCancelButton: true,
                        }).then((result) => {
                          if (result.isConfirmed) {
                              this.getCheckedItems().forEach(item => {
                                this.delete_item(item.uniqid);
                              })
                          }
                        });
                    }
                break;
                case "discount_oid":
                    Swal.fire({
                        title: 'Проставить скидки по контрагенту?',
                        showCancelButton: true,
                    }).then(async (result) => {
                      if (result.isConfirmed) {
                        sales_lw.on();
                        try {
                            const form = new FormData;
                            form.append("sales_id", this.sales.id);
                            this.getCheckedItems().forEach(item => {
                              form.append("sales_detail[]", JSON.stringify(item));
                            });
                            const result = await( await fetch(router.ajax.discount_oid, {
                                method: "POST",
                                body: form,
                            }) ).json();
                            result.forEach(element => {
                                const item = this.sales_detail[ this.bindings_list[ element.item.uniqid ] ];
                                item.discount = element.discount;
                                this.save_discount(item);
                            })
                        } catch (error) {
                            throw_modal(error);
                        } finally{
                            sales_lw.off();
                        }
                      }
                    });
                break;
                case "assembly":

                    if($('#title').text() !== 'Отсутствует' || $('#sales_type').text() !== 'Заявка на производство'){
                        this.assemblyModal();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ошибка',
                            text: 'Для отправки на сборку необходимо заполнить заголовок продажи',
                        });
                    }

                break;
            }
        },
        async sales_check(){
            const errors = await ( await fetch(router.ajax.sales_check) ).json();
            if(errors.length == 0){
                return true;
            } else {
                sales_lw.off();
                errors.forEach(item => {
                    Swal.fire(item.message);
                });
                return false;
            }
        },
        async send_supply(url, form){
            try {
                sales_lw.on();
                if(await this.sales_check()){
                    const data = await ( await fetch(url, {
                        method: "POST",
                        body: form,
                    }) ).json();
                    await this.refresh();
                    Swal.fire(
                        'Успешно',
                        'Заявка успешно отправлена',
                        'success'
                    )
                    return data;
                }
            } catch (error) {
                throw_modal(error);
            } finally{
                sales_lw.off();
            }
        },
        async send_sos(notice = false){
            const form = new FormData;
            form.append("sales_id", this.sales.id);
            form.append("notice", notice);
            this.getCheckedItems().forEach(item => {
              form.append("sales_detail_id[]", item.id);
            });
            await this.send_supply(router.ajax.actualize_create, form);
        },
        async send_purchase(status = 1){
            const form = new FormData(this.$refs.placeOrderForm);
            form.append("sales_id", this.sales.id);
            form.append("status", status);
            this.getCheckedItems().forEach(item => {
              form.append("sales_detail_id[]", item.id);
            });
            await this.send_supply(router.ajax.send_purchase, form);
        },
        async check_can_delete(id){
            const form = new FormData;
            const id_array = Array.isArray(id) ? id : [id];
            id_array.forEach(id => {
                form.append("id[]", id);
            })
            return await( await fetch(router.ajax.check_can_delete, {
                method: "POST",
                body: form,
            }) ).json();
        },
        async check_can_delete_items(items){
            const id = [];
            items.forEach(item => {
                if(!this.is_now_delete(item)){
                    id.push(item.id);
                }
            })
            if(id.length > 0){
                sales_lw.on();
                const ccd = await this.check_can_delete(id);
                sales_lw.off();
                if(ccd.status){
                    return true;
                } else {
                    error_modal_show_move(ccd.data);
                    return false;
                }
            } else {
                return true;
            }
        },
        async realizationModal(){
            if(this.is_checked){
                if(this.save_notice_modal()){
                    try {
                        sales_lw.on();
                        const form = new FormData;
                        form.append("sales_id", this.sales.id);
                        const id = [];
                        this.getCheckedItems().forEach(item => {
                            id.push(item.id);
                            form.append("sales_detail_id[]", item.id);
                        });
                        if(await this.sales_check() && await this.checkZeroSum(id)){
                            sales_lw.off();
                            const go = await Swal.fire({
                                title: "Создать реализацию?",
                                text: "Вы хотите создать реализацию?",
                                icon: "info",
                                showCancelButton: true,
                                confirmButtonText: 'Создать',
                                cancelButtonText: 'Закрыть',
                            });
                            if(go.isConfirmed){
                                sales_lw.on();
                                const data = await ( await fetch(router.ajax.realization_create, {
                                    method: "POST",
                                    body: form,
                                }) ).json();
                                sales_lw.off();
                                window.open(`/administration/module/realization/order/${data.realization_id}`);
                            }
                        }
                    } catch (error) {
                        throw_modal(error);
                    } finally{
                        sales_lw.off();
                    }
                }
            } else {
                no_goods_check();
            }
        },
        async assemblyModal(){
            if(this.is_checked){
                try {
                    sales_lw.on();
                    const assemblyModal = this.modals.assemblyModal;
                    assemblyModal.data = [];
                    const form = new FormData;
                    this.getCheckedItems().forEach(item => {
                        form.append("sales_detail_id[]", item.id);
                    });
                    assemblyModal.data = await ( await fetch(router.ajax.assembly_count, {
                        method: "POST",
                        body: form,
                    }) ).json();
                    assemblyModal.modal.show();
                } catch (error) {
                    throw_modal(error);
                } finally{
                    sales_lw.off();
                }
            } else {
                no_goods_check();
            }
        },
        async assemblyCreate(e){
            try {
                sales_lw.on();
                const form = new FormData(e.target);
                form.append("sales_id", this.sales.id);
                this.getCheckedItems().forEach(item => {
                    form.append("sales_detail_id[]", item.id);
                });
                await ( await fetch(router.ajax.assembly_create, {
                    method: "POST",
                    body: form,
                }) ).json();
                await this.refresh();
                Swal.fire({
                    title: "Успешно!",
                    icon: "success",
                    text: "Сборка отправлена!",
                });
            } catch (error) {
                throw_modal(error);
            } finally{
                sales_lw.off();
            }
        },
        async transferModal(){
            if(this.is_checked){
                if(this.save_notice_modal()){
                    try {
                        const form = new FormData;
                        form.append("sales_id", this.sales.id);
                        this.getCheckedItems().forEach(item => {
                            form.append("sales_detail_id[]", item.id);
                        });
                        const go = await Swal.fire({
                            title: "Создать перемещение?",
                            text: "Вы хотите создать перемещение?",
                            icon: "info",
                            showCancelButton: true,
                            confirmButtonText: 'Создать',
                            cancelButtonText: 'Закрыть',
                        });
                        if(go.isConfirmed){
                            sales_lw.on();
                            const data = await ( await fetch(router.ajax.transfer.create, {
                                method: "POST",
                                body: form,
                            }) ).json();
                            sales_lw.off();

                            Swal.fire({
                                title: "Успешно",
                                text: "Перемещение создано",
                                icon: "success",
                                showCancelButton: true,
                                confirmButtonText: 'Перейти',
                                cancelButtonText: 'Закрыть',
                            }).then(async (go) => {
                                if(go.isConfirmed){
                                    window.open(`/administration/module/storage/transfer/detail/${data.stock_transfer.id}`);
                                }
                            });
                        }
                    } catch (error) {
                        throw_modal(error);
                    } finally{
                        sales_lw.off();
                    }
                }
            } else {
                no_goods_check();
            }
        },
        async checkZeroSum(id){
            const form = new FormData;
            id.forEach((value, index, array) => {
                form.append("id[]", value);
            })
            form.append("sales_id", this.sales.id);
            const data = await ( await fetch(router.ajax.check_zero_sum, {
                method: "POST",
                body: form,
            }) ).json();
            if(data.status){
                return true;
            } else {
                sales_lw.off();
                let error = "";
                data.items.forEach(item => {
                    error += `<div>Сумма товара <b>${item.s_name}</b> равна 0</div>`;
                })
                Swal.fire({
                    title: "Внимание!",
                    icon: "warning",
                    html: error,
                });
                return false;
            }
        },
        async showCommentModal(){
            const commentModal = this.modals.commentModal;
            commentModal.modal.show();
            commentModal.load = true;
            commentModal.messages = [];
            commentModal.messages = await ( await fetch(router.ajax.comments.get) ).json();
            commentModal.load = false;
            this.comments_count = commentModal.messages.length;
        },
        async addCommentModal(e){
            const commentModal = this.modals.commentModal;
            const form = new FormData(e.target);
            form.append("module", "sales");
            form.append("module_id", this.sales.id);
            commentModal.message = null;
            commentModal.load_button = true;
            e.target.reset();
            try {
                commentModal.messages.unshift(await( await fetch(router.ajax.comments.create, {
                    method: "POST",
                    body: form,
                }) ).json());
                this.comments_count = commentModal.messages.length;
            } catch (error) {
                throw_modal(error);
            } finally{
                commentModal.load_button = false;
            }
        },
        async sendMessageModal(){
            const messageModal = this.modals.messageModal;
            messageModal.modal.show();
            messageModal.step = 1;
            messageModal.load = true;
            messageModal.data = [];
            messageModal.data = await ( await fetch(router.ajax.messages.get) ).json();
            messageModal.load = false;
        },
        setMessengers(item){
            const messageModal = this.modals.messageModal;
            messageModal.data_select = item;
            messageModal.step = 2;
        },
        async sendMessage(){
            notif(`primary`, `Информация`, `Отправка сообщения...`);
            try {
                const messageModal = this.modals.messageModal;
                const form = new FormData;
                form.append("sales_id", this.sales.id);
                form.append("uid", messageModal.data_select.users_org.uid);
                messageModal.data_select.messengers.forEach(item => {
                    if(item?.value){
                        form.append("role[]", item.field);
                    }
                })
                await ( await fetch(router.ajax.messages.send, {
                    method: "POST",
                    body: form,
                }) ).json();
                notif(`success`, `Информация`, `Сообщения отправлены!`);
            } catch (error) {
                throw_modal(error);
            }
        },
        async endSalesModal(){
            if(this.save_notice_modal()){
                sales_lw.on();
                const form = new FormData;
                form.append("sales_id", this.sales.id);
                const middleware = await ( await fetch(router.ajax.end_sales.middleware)).json();
                if(middleware.status){
                    const check = await ( await fetch(router.ajax.check_all_realization_and_transfer) ).json();
                    const options = {
                        title: "Закрыть продажу",
                        icon: "info",
                        showCancelButton: true,
                        confirmButtonText: 'Продолжить',
                        cancelButtonText: 'Отмена',
                        input: 'text',
                    };
                    const form2 = new FormData;
                    form2.append("sales_id", this.sales.id);
                    if(check.status){
                        options.text = "Вы действительно хотите успешно закрыть продажу?";
                        form2.append("end", 1);
                    } else {
                        options.text = "Вы действительно хотите не успешно закрыть продажу?";
                        options.inputValidator = value => {
                            if (!value) {
                              return 'Вам нужно что-то написать!'
                            }
                        };
                        form2.append("end", 2);
                    }
                    sales_lw.off();
                    Swal.fire(options).then(async result => {
                        if(result.isConfirmed){
                            sales_lw.on();
                            form2.append("comment_end", result.value);
                            const re = await ( await fetch(router.ajax.end_sales.end, {
                                method: "POST",
                                body: form2,
                            }) ).json();
                            this.sales = Object.assign(this.sales, re.changed);
                            sales_lw.off();
                            notif(`success`, `Успешно`, `Продажа закрыта`);
                        }
                    })
                } else {
                    sales_lw.off();
                    Swal.fire({
                        title: "Внимание",
                        text: `Прежде чем закрыть продажу: ${middleware.error[0]}`,
                        icon: "warning"
                    });
                }
            }
        },
        endSalesZeroModal(){
            if(this.save_notice_modal()){
                Swal.fire({
                    title: "Вы уверены?",
                    text: `Возродить продажу`,
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonText: 'Продолжить',
                    cancelButtonText: 'Отмена',
                }).then(async result => {
                    if(result.isConfirmed){
                        sales_lw.on();
                        const form = new FormData;
                        form.append("sales_id", this.sales.id);
                        form.append("end", 0);
                        const re = await ( await fetch(router.ajax.end_sales.end, {
                            method: "POST",
                            body: form,
                        }) ).json();
                        this.sales = Object.assign(this.sales, re.changed);
                        sales_lw.off();
                        notif(`success`, `Успешно`, `Продажа возрождена`);
                    }
                });
            }
        },
        sortModal(){
            $extendedSort.open();
        },
        sort_items(){
            let position = 0;
            this.sales_detail.forEach(item => {
                if(!item.delete_){
                    position++;
                    if(item.position != position){
                        item.update_ = true;
                    }
                    item.position = position;
                }
            })
        },
        async usersModal(){
            const usersModal = this.modals.usersModal;
            usersModal.modal.show();
            usersModal.data = [];
            usersModal.data = await ( await fetch(router.ajax.users_list.get) ).json();
        },
        async addUser(e){
            const form = new FormData(e.target);
            form.append("sales_id", this.sales.id);
            form.append("type", "CLIENT");
            sales_lw.on();
            try {
                this.modals.usersModal.data.push( await ( await fetch(router.ajax.users_list.create, {
                    method: "POST",
                    body: form,
                }) ).json() );
            } catch (error) {
                throw_modal(error);
            } finally{
                sales_lw.off();
            }
        },
        async deleteUser(index){
            Swal.fire({
                title: "Вы уверены?",
                text: `Удалить пользователя`,
                icon: "info",
                showCancelButton: true,
                confirmButtonText: 'Продолжить',
                cancelButtonText: 'Отмена',
            }).then(async result => {
                if(result.isConfirmed){
                    const usersModal = this.modals.usersModal;
                    sales_lw.on();
                    try {
                        await ( await fetch(router.ajax.users_list.delete.replace("@id", usersModal.data[ index ].id)) ).json();
                        usersModal.data.splice(index, 1);
                    } catch (error) {
                        throw_modal(error);
                    } finally{
                        sales_lw.off();
                    }
                }
            });
        },
        async change_sales_form(form){
            sales_lw.on();
            try {
                await ( await fetch(router.ajax.change_sales_form, {
                    method: "POST",
                    body: form,
                }) ).json();
                await this.refresh_head();
            } catch (error) {
                throw_modal(error);
            } finally{
                sales_lw.off();
            }
        },
        async save_modal_form(e){
            await this.change_sales_form(new FormData(e.target));
        },
        async view_ndr_modal(item){
            sales_lw.on();
            await view_ndr_modal(item.cid);
            sales_lw.off();
        },
        purchase_price_hide_change(){
            Cookies.set("purchase_price_hide", this.purchase_price_hide);
        },
        async contractModal(){
            const contractModal = this.modals.contractModal;
            contractModal.modal.show();
            contractModal.elements.body.innerHTML = "Загрузка...";
            contractModal.elements.save.disabled = true;
            const form = new FormData;
            form.append("sales_id", this.sales.id);
            form.append("oid", this.sales.oid);
            contractModal.elements.body.innerHTML = await ( await fetch(router.ajax.contract.get, {
                method: "POST",
                body: form,
            }) ).text();
        },
        async contractModalSave(){
            const contractModal = this.modals.contractModal;
            const form = new FormData(contractModal.elements.form)
            form.append("sales_id", this.sales.id);
            sales_lw.on();
            try {
                await ( await fetch(router.ajax.contract.save, {
                    method: "POST",
                    body: form,
                }) ).json();
                await this.refresh_head();
            } catch (error) {
                throw_modal(error);
            } finally{
                sales_lw.off();
            }
        },
        async contractModalCheck(){
            sales_lw.on();
            const form = new FormData();
            form.append("sales_id", this.sales.id);
            const check = await ( await fetch(router.ajax.contract.check, {
                method: "POST",
                body: form,
            }) ).json();
            if(check.error.length == 0){
                this.contractModalCreate();
            } else{
                Swal.fire({
                    title: 'Внимание',
                    text: check.error.join("; "),
                    icon: "warning",
                })
            }
            sales_lw.off();
        },
        async contractModalCreate(){
            Swal.fire({
              title: 'Вы уверены?',
              text: 'Создать договор',
              showCancelButton: true,
              confirmButtonText: 'Создать',
              cancelButtonText: `Отмена`,
            }).then(async (result) => {
              if (result.isConfirmed) {
                  try {
                    sales_lw.on();
                    const form = new FormData();
                    form.append("sales_id", this.sales.id);
                    await ( await fetch(router.ajax.contract.create, {
                        method: "POST",
                        body: form,
                    }) ).json();
                    await this.refresh_head();
                    Swal.fire({
                        icon: 'success',
                        title: 'Успешно',
                        text: 'Ваш договор создан',
                      })
                  } catch (error) {
                    throw_modal(error);
                  } finally{
                    sales_lw.off();
                  }
              }
            })
        },
        async specificationCreate(e){
            try {
                sales_lw.on();
                const form = new FormData(e.target)
                form.append("sales_id", this.sales.id);
                form.append("reestr_id", this.sales.contract.id);
                await ( await fetch(router.ajax.specification.create, {
                    method: "POST",
                    body: form,
                }) ).json();
                await this.refresh_head();
                Swal.fire({
                    icon: 'success',
                    title: 'Успешно',
                    text: 'Ваша спецификация создана',
                })
            } catch (error) {
              throw_modal(error);
            } finally{
              sales_lw.off();
            }
        },
        view_supply_order_position(supply_order){
            supply_order.supply_order_detail.forEach(item => {
                if(vm.bindings_list_id[ item.sale_detail_id ] != undefined){
                    vm.sales_detail[ vm.bindings_list_id[ item.sale_detail_id ] ].class.view_supply_order = true;
                }
            });
        },
        supply_order_confirm_modal(item, index){
            Swal.fire({
                title: 'Подтвердить заявку на закупку',
                text: 'Вы действительно хотите подтвердить заявку?',
                showCancelButton: true,
              }).then(async (result) => {
                  if (result.isConfirmed) {
                      try {
                        sales_lw.on();
                        await this.supply_order_send_control(item.id, 2);
                        Swal.fire({
                            icon: 'success',
                            title: 'Успешно',
                            text: 'Данные успешно отправлены',
                        });
                        this.confirm_supply_order.splice(index, 1);
                      } catch (error) {
                        throw_modal(error);
                      } finally{
                        sales_lw.off();
                      }
                  }
              })
        },
        status_client_block(){
            Swal.fire({
                title: 'Согласовать заявку?',
                text: 'Вы действительно хотите подтвердить согласование заявки?',
                showCancelButton: true,
              }).then(async (result) => {
                  if (result.isConfirmed) {
                      try {
                        sales_lw.on();
                        await this.status_client_block_send(3);
                        Swal.fire({
                            icon: 'success',
                            title: 'Успешно',
                            text: 'Данные успешно отправлены',
                        });
                        this.$refs.status_client_block_key.remove();
                      } catch (error) {
                        throw_modal(error);
                      } finally{
                        sales_lw.off();
                      }
                  }
              })
        },
        supply_order_cancel_modal(item, index){
            Swal.fire({
              title: 'Отправить на доработку заявку на закупку',
              input: 'text',
              text: 'Вы действительно хотите отправить на доработку заявку на закупку?',
              inputPlaceholder: 'Причина:',
              showCancelButton: true,
              inputValidator: (value) => {
                if (!value) {
                  return 'Вам нужно что-то написать!'
                }
              }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        sales_lw.on();
                        await this.supply_order_send_control(item.id, 3, result.value);
                        Swal.fire({
                          icon: 'success',
                          title: 'Успешно',
                          text: 'Данные успешно отправлены',
                        });
                        this.confirm_supply_order.splice(index, 1);
                    } catch (error) {
                      throw_modal(error);
                    } finally{
                      sales_lw.off();
                    }
                }
            })
        },
        async supply_order_send_control(supply_order_id, status, comment = null){
            const form = new FormData;
            form.append("sales_id", this.sales.id);
            form.append("supply_order_id", supply_order_id);
            form.append("status", status);
            form.append("comment", comment);
            return await ( await fetch(router.ajax.confirm_supply_order.send, {
                method: "POST",
                body: form,
            }) ).json();
        },
        async status_client_block_send(status){
            const form = new FormData;
            form.append("sales_id", this.sales.id);
            form.append("status_client", status);
            return await ( await fetch(router.ajax.status_client_block.send, {
                method: "POST",
                body: form,
            }) ).json();
        },
        async pay_modal(){
            const payModal = this.modals.payModal;
            payModal.template = "Загрузка данных...";
            payModal.modal.show();
            payModal.template = await ( await fetch(router.ajax.pay.get) ).text();
        },
        async local_price_modal(index){
            try {
                const item = this.sales_detail[ index ];
                const go = await Swal.fire({
                    title: 'Локальная цена закупки',
                    input: 'text',
                    showCancelButton: true,
                    showDenyButton: true,
                    denyButtonText: "Удалить",
                    cancelButtonText: "Отмена",
                })
                if(go.isConfirmed){
                    item.s_price_local = Price.string_to_number(go.value);
                    item.update_ = true;
                }
                if(go.isDenied){
                    item.s_price_local = null;
                    item.update_ = true;
                }
                if(go.isConfirmed || go.isDenied){
                    this.load_purchase([item]);
                }
            } catch (error) {
                throw_modal(error);
            }
        },
        async load_purchase(items){
            if(items && items.length > 0){
                try {
                    const form = new FormData;
                    items.forEach(item => {
                        item.purchase.load = true;
                        form.append("sales_detail[]", JSON.stringify(item));
                    });
                    const data = await ( await fetch(router.ajax.purchase_data.get, {
                        method: "POST",
                        body: form,
                    }) ).json();
                    data.forEach(purchase => {
                        const item = this.sales_detail[ this.bindings_list[ purchase.uniqid ] ];
                        item.purchase = purchase;
                    })
                    this.re_purchase();
                } catch (error) {
                    throw_modal(error);
                }
            }
        },
        async load_ndr(items){
            if(items && items.length > 0){
                try {
                    const form = new FormData;
                    form.append("sales", JSON.stringify(this.sales));
                    items.forEach(item => {
                        item.ndr.load = true;
                        form.append("sales_detail[]", JSON.stringify(item));
                    });
                    const data = await ( await fetch(router.ajax.ndr.get, {
                        method: "POST",
                        body: form,
                    }) ).json();
                    data.forEach(ndr => {
                        const item = this.sales_detail[ this.bindings_list[ ndr.uniqid ] ];
                        item.ndr = ndr.data;
                    })
                } catch (error) {
                    throw_modal(error);
                }
            }
        },
        async load_max_discount(items){
            if(items.length > 0){
                try {
                    const form = new FormData;
                    items.forEach(item => {
                        form.append("sales_detail[]", JSON.stringify(item));
                    });
                    const load_max_discount = await ( await fetch(router.ajax.load_max_discount, {
                        method: "POST",
                        body: form,
                    })).json();
                    load_max_discount.forEach(item => {
                        this.sales_detail[ this.bindings_list[ item.uniqid ] ].max_allowable_discount = item.max_allowable_discount;
                    });
                } catch (error) {
                    throw_modal(error);
                }
            }
        },
        async view_modal(index, title, url){
            try {
                const item = this.sales_detail[ index ];
                const viewModal = this.modals.viewModal;
                viewModal.modal.show();
                viewModal.template = "Загрузка данных...";
                viewModal.title = title;
                viewModal.template = await ( await fetch(url.replace("@id", item.id)) ).text();
            } catch (error) {
                throw_modal(error);
            }
        },
        async commentEditItemModal(item){
            const result = await Swal.fire({
                title: 'Комментарий',
                input: 'textarea',
                inputLabel: 'Введите комментарий',
                inputValue: item.comment,
                showCancelButton: true,
            })
            if(result.isConfirmed){
                item.comment = result.value;
                item.update_ = true;
            }
        },
        async commentShowItemModal(item){
            Swal.fire({
                title: 'Комментарий',
                text: item.comment,
            })
        },
        redirectOrganization(){
            window.open(router.organizations.show.replace("@id", this.sales.oid));
        },
        async modal_files_item(index){
            const modal = this.modals.filesModal;
            const item = this.sales_detail[ index ];
            const form = new FormData;
            sales_lw.on();
            item.catalog.attach_id.forEach(file => {
                form.append("attach_id[]", file);
            })
            modal.files = await ( await fetch(router.ajax.files.get, {
                method: "POST",
                body: form,
            }) ).json();
            sales_lw.off();
            modal.modal.show();
        },
        move_clone(){
            window.open(router.show_.replace("@id", this.sales.clone));
        },
        append_chat(){
           	const lw = new LW;
	        const pid = this.sales.id;
            const request = {
                module: "sales",
                pid
            }
            Swal.fire({
                title: "Добавиться в чат?",
                text: "Вы действительно хотите добавиться в чат?",
                icon: "info",
                buttons: true,
                showCancelButton: true,
            })
            .then((willAppend) => {
                if (willAppend) {
                    lw.on();
                    request.title = `Продажа`+this.sales.numb;
                    CHAT.find_append_or_create(request).then(() => {
                        lw.off();
                        Swal.fire("Успешно!", "Вы добавлены в чат!", "success");
                    });
                                    }
	  });
        },
        checkPurchaseFile(item){
            return item.purchase.files && item.purchase.files.length > 0;
        },
        openPurchaseFile(item){
            window.open(`/download/${item.purchase.files[0].id}`);
        },
        save_notice_modal(){
            if(this.is_changed){
                Swal.fire({
                    icon: 'info',
                    title: 'Внимание',
                    text: 'Данные не сохранены',
                })
                return false;
            }
            return true;
        },
        async printModal(){
                if(this.save_notice_modal()){
                    try {
                        sales_lw.on();
                        const id = [];
                        if(await this.sales_check() && await this.checkZeroSum(id)){
                            this.modals.printModal.modal.show();
                        }
                    } catch (error) {
                        throw_modal(error);
                    } finally{
                        sales_lw.off();
                    }
                }
        },
    }
});

const vm = app.mount('#content');

function no_goods_check(){
    notif(`danger`, `Предупреждение`, `Выберите товары`);
}

function error_modal_show_move(data){
    Swal.fire({
        title: "Внимание!",
        text: data.error,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: 'Перейти',
        cancelButtonText: 'Закрыть',
    }).then((go) => {
        if(go.isConfirmed){
            window.open(data.link);
        }
    });
}

function throw_modal(error){
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: error.message,
    })
}

function load_start(){
    load_confirm_supply_order()
    load_start_detail();
}

function load_start_detail(){
    vm.load_max_discount(vm.sales_detail);
    vm.load_purchase(vm.sales_detail)
    vm.load_ndr(vm.sales_detail)
}

async function load_confirm_supply_order(){
    if (Object.keys(vm.sales.confirm_supply_order).length > 0){
        const form = new FormData;
        for(key in vm.sales.confirm_supply_order){
            form.append("supply_order_id[]", vm.sales.confirm_supply_order[key]);
        }
        vm.confirm_supply_order = await ( await fetch(router.ajax.confirm_supply_order.get, {
            method: "POST",
            body: form,
        }) ).json();
    }
}

window.onload = async function(){
    load_start();
};