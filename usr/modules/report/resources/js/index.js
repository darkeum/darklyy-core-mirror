const buttons_func = {};
const link_view = document.getElementById("link_view").value;
const link_excel = document.getElementById("link_excel").value;
const download_excel = document.getElementById("download_excel").value;
const Report_name = document.getElementById("Report_name").value;

const app = Vue.createApp({
    compilerOptions: {
        delimiters: ['${', '}'],
        comments: true
    },
    data() {
        const selections = localStorage.getItem(getReportName());
        return {
            selections: selections ? JSON.parse(selections) : [],
            modals_data: JSON.parse(document.getElementById("modals_data").value),
            list_value: [],
            list_value_index: null,
        }
    },
    methods: {
        saveLocalStorage() {
            localStorage.setItem(getReportName(), JSON.stringify(this.selections));
        },
        addSelection() {
            this.selections.push({
                active: false,
                field: null,
                condition: 1,
                value_view: null,
                value: null,
                class: null,
            });
            this.saveLocalStorage();
        },
        deleteSelection(index) {
            this.selections.splice(index, 1);
            this.saveLocalStorage();
        },
        changeActive(item) {
            const save = item.active;
            item.active = !save;
            try {
                if (item.field === null) {
                    throw new Error("Не выбран фильтр");
                }
                if (item.value === null) {
                    throw new Error("Не выбрано значение");
                }
                this.saveLocalStorage();
            } catch (error) {
                Swal.fire("Ошибка", error.message, "error");
                this.$nextTick(() => {
                    item.active = save;
                });
            }
        },
        changeField(item) {
            if (item.condition == 13) {
                item.value = [];
                item.value_view = [];
            } else {
                item.value = null;
                item.value_view = null;
            }
            item.condition = 1;
            item.class = this.modals_data[item.field].class;
            this.saveLocalStorage();
        },
        changeCondition(item) {
            if (item.condition == 13) {
                item.value = item.value ? [item.value] : [];
                item.value_view = item.value_view ? [item.value_view] : [];
            } else {
                if (Array.isArray(item.value)) {
                    item.value = item.value[0] ? ? null;
                    item.value_view = item.value_view[0] ? ? null;
                }
            }
            this.saveLocalStorage();
        },
        changeValue(item, index) {
            if (item.field === null) {
                Swal.fire("Ошибка", "Выберите фильтр", "error");
            } else {
                if (item.condition == 13) {
                    this.list_value_index = index;
                    this.syncList(index);
                    const modal = new bootstrap.Modal(document.getElementById("listModal"));
                    modal.show();
                    const button_name = "listModalButton";
                    const button = document.getElementById(button_name);
                    if (button_name in buttons_func) {
                        button.removeEventListener('click', buttons_func[button_name]);
                    }
                    const func = () => {
                        this.modalChange(item, index);
                    }
                    buttons_func[button_name] = func;
                    button.addEventListener("click", func);
                } else {
                    this.modalChange(item, index);
                }
            }
        },
        modalChange(item, index) {
            const data = this.modals_data[item.field];
            if (data.function && window[data.function] && typeof window[data.function] === "function") {
                const fn = window[data.function];
                fn(item, index);
            } else {
                const modal = new bootstrap.Modal(document.getElementById(data.name));
                modal.show();
                const element = document.getElementById(data.element);
                if (item.condition != 13) {
                    element.value = item.value;
                }
                const button = document.getElementById(data.button);
                const func = () => {
                    let value, value_view;
                    switch (data.type) {
                        case "input":
                            value = element.value;
                            value_view = element.value;
                            break;
                        case "select":
                            value = element.value;
                            value_view = element.options[element.selectedIndex].text;
                            break;
                    }
                    this.appendValue(item, index, value, value_view);
                    modal.hide();
                };
                if (data.button in buttons_func) {
                    button.removeEventListener('click', buttons_func[data.button]);
                }
                buttons_func[data.button] = func;
                button.addEventListener("click", func);
            }
        },
        appendValue(item, index, value, value_view) {
            if (item.condition == 13) {
                item.value.push(value);
                item.value_view.push(value_view);
                this.syncList(index);
            } else {
                item.value = value;
                item.value_view = value_view;
            }
            this.saveLocalStorage();
        },
        deleteItemList(index) {
            const item = this.selections[this.list_value_index];
            item.value.splice(index, 1);
            item.value_view.splice(index, 1);
            this.syncList(index);
            this.saveLocalStorage();
        },
        async send(url) {
            const lw = new LW();
            lw.on();
            try {
                middleware(this.selections);
                const form = new FormData(document.getElementById("additional_terms"));
                form.append("selections", JSON.stringify(this.selections));
                const response = await (await fetch(url, {
                    method: "POST",
                    body: form,
                })).text();
                const data = JSON.parse(response);
                if (data ? .error) {
                    throw new Error(data.error);
                }
                return data;
            } catch (error) {
                Swal.fire("Ошибка", `${error}`, "error");
                return false;
            } finally {
                lw.off();
            }
        },
        async generate() {
            const data = await this.send(link_view);
            if (data) {
                document.getElementById("Report-body").innerHTML = data.template;
            }
        },
        async excel() {
            const data = await this.send(link_excel);
            if (data) {
                location.href = `${download_excel}?link=${data.link}`;
            }
        },
        getConditions(item, index) {
            if (item ? .field) {
                if (this.modals_data[item.field]) {
                    const data = this.modals_data[item.field];
                    return JSON.parse(document.getElementById(data.condition).value);
                } else {
                    this.selections.splice(index, 1);
                    this.saveLocalStorage();
                }
            }
            return [];
        },
        syncList(index) {
            this.list_value = this.selections[index].value_view;
        },
    },
});

const vm = app.mount('#content');

function middleware(selections) {
    if (selections.length == 0) {
        throw new Error("Фильтры не выбраны");
    }
    let error = true;
    selections.forEach(item => {
        if (item.active) {
            error = false;
        }
    });
    if (error) {
        throw new Error("Фильтры не включены");
    }
}

function setValue(index, cid, title) {
    vm.appendValue(vm.selections[index], index, cid, title);
}

function getReportName() {
    return `${Report_name}_v1`;
}