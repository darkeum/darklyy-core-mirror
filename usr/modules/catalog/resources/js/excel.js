$(function(){
    $("#categories").select2();
    $("#manufactures").select2();
});

async function get_files(type){
    return await( await fetch(`/administration/module/catalog/excel/files?type=${type}`) ).json();
}

function get_file(link){
    return `/administration/module/catalog/excel/get_file?file=${link}`;
}

async function get_batch_list(name){
    const json = localStorage.getItem(name);
    const list = json === null ? [] : JSON.parse(json);
    if(list.length > 0){
        const form = new FormData();
        list.forEach(item => {
            form.append("batchId[]", item.id);
        });
        const progress_list = await ( await fetch(`/administration/module/catalog/excel/batch_list`, {
            method: "POST",
            body: form
        }) ).json();
        localStorage.setItem(name, JSON.stringify(progress_list));
        return progress_list;
    } else {
        return list;
    }
}

function vue_init(data, methods, batch_name, type){
    return Vue.createApp({
        compilerOptions: {
            delimiters: ['${', '}'],
            comments: true
        },
        data(){
            return Object.assign({
                files: [],
                batch_list: [],
                progressModal: {
                    modal: null,
                    data: null,
                },
            }, data);
        },
        async mounted(){
            this.progressModal.modal = new bootstrap.Modal(document.getElementById(`progress_${type}Modal`));
            this.batch_list = await get_batch_list(batch_name);
            this.files = await get_files(type);
        },
        methods: Object.assign({
            async refresh(){
                const lw = new LW;
                lw.on();
                this.batch_list = await get_batch_list(batch_name);
                this.files = await get_files(type);
                lw.off();
            },
            get_file(index){
                const file = this.files[ index ];
                location.href = get_file(file.download);
                this.files.splice(index, 1);
            },
            moment(date){
                if(date === null) return null;
                return moment(date).format(`DD.MM.YYYY HH:mm:ss`);
            },
            delete_batch(index){
                this.batch_list.splice(index, 1);
                localStorage.setItem(batch_name, JSON.stringify(this.batch_list));
            },
            async cancel_batch(index){
                Swal.fire({
                    title: 'Вы уверены?',
                    text: "Отменить работу",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Продолжить!',
                    cancelButtonText: 'Закрыть',
                  }).then(async (result) => {
                    if (result.isConfirmed) {
                        const lw = new LW;
                        lw.on();
                        const batch = this.batch_list[index];
                        const form = new FormData;
                        form.append("batchId", batch.id);
                        this.batch_list[index] = await ( await fetch(`/administration/module/catalog/excel/cancel_batch`, {
                            method: "POST",
                            body: form,
                        }) ).json();
                        lw.off();
                    }
                })
            },
            showMore(batch){
                this.progressModal.data = batch;
                this.progressModal.modal.show();
            },
        }, methods),
    });
}

const batch_name_unload = "catalog_excel_unload";

const unload = vue_init({
    category: false,
    man: false,
    hidden: false,
    checkbox_all: true,
    fields: JSON.parse(document.getElementById("fields").value),
}, {
    changeCheckbox(){
        for(key in this.fields){
            const item = this.fields[ key ];
            if(!item?.disabled){
                item.checked = this.checkbox_all;
            }
        }
    },
    changeCategory(){
        const attribute = this.fields.attribute;
        attribute.disabled = !this.category;
        attribute.checked = false;
    },
    async unload(event){
        let flag = true;
        if(!this.category && !this.man){
            flag = confirm("Вы уверены что хотите скачать ВСЕ товары?");
        }
        if(flag){
            const lw = new LW;
            lw.on();
            try {
                const form = new FormData(event.target);
                const response = await (await fetch(`/administration/module/catalog/excel/unload`, {
                    method: "POST",
                    body: form,
                })).json();
                const list = await get_batch_list(batch_name_unload);
                list.push(response);
                localStorage.setItem(batch_name_unload, JSON.stringify(list));
                this.batch_list = list;
            } catch (error) {
                alert(error.message);
            } finally{
                lw.off();
            }
        }
    },
}, batch_name_unload, "unload");

unload.mount('#unload');

const batch_name_download = "catalog_excel_download";

const download = vue_init({
    button_disabled: true,
    count: false,
}, {
    async changeFile(){
        const lw = new LW;
        lw.on();
        this.button_disabled = false;
        const form = new FormData(this.$refs.form_download);
        const data = await ( await fetch(`/administration/module/catalog/excel/get_count`, {
            method: "POST",
            body: form,
        }) ).json();
        this.count = data.count;
        lw.off();
    },
    async download(){
        const lw = new LW;
        lw.on();
        try {
            const form = new FormData(this.$refs.form_download);
            const response = await (await fetch(`/administration/module/catalog/excel/download`, {
                method: "POST",
                body: form,
            })).json();
            const list = await get_batch_list(batch_name_download);
            list.push(response);
            localStorage.setItem(batch_name_download, JSON.stringify(list));
            this.batch_list = list;
            this.clearFile();
            alert("Загрузка началась");
        } catch (error) {
            alert(error.message);
        } finally{
            lw.off();
        }
    },
    clearFile(){
        this.button_disabled = true;
        this.count = false;
        this.$refs.file_download.value = '';
    },
}, batch_name_download, "download");

download.mount('#download');