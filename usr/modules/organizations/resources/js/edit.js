window.onload = function(){
    const mainId = document.getElementById("mainId").value;
    const lw = new LW;
    const token = "ddeafb9ff43e79578243d8f031f6e9d8b047ca0e";

    function create_(url){
        return {
            async create_(request){
                return await ( await fetch(url, {
                    method: "POST",
                    body: request,
                }) ).json();
            },
            async create(request){
                try {
                    await this.create_(request);
                    Swal.fire("Успешно", "Данные сохранены", "success");
                } catch (error) {
                    Swal.fire("Ошибка", error.message, "error");
                }
            },
        }
    }

    function update_(url, temp = "@id"){
        return {
            async update_(id, request){
                request.append("_method", "PUT");
                const u = url.replace(temp, id);
                return await ( await fetch(u, {
                    body: request,
                    method: "POST",
                }) ).json();
            },
            async update(id, request){
                try {
                    await this.update_(id, request);
                    Swal.fire("Успешно", "Данные сохранены", "success");
                } catch (error) {
                    Swal.fire("Ошибка", error.message, "error");
                }
            },
        }
    }

    function delete_(url, temp = "@id"){
        return {
            async delete_(id){
                const request = new FormData();
                request.append("_method", "DELETE");
                const u = url.replace(temp, id);
                return await ( await fetch(u, {
                    method: "POST",
                    body: request,
                }) ).json();
            },
            async delete(id){
                try {
                    await this.delete_(id);
                    Swal.fire("Успешно", "Удалено", "success");
                } catch (error) {
                    Swal.fire("Ошибка", error.message, "error");
                }
            },
        }
    }

    const uo = update_(document.getElementById("router_update")?.value);

    function modal_init(button_name, link, update_func, callback){
        link.button.innerHTML = button_name;
        if(link.form_listener) link.form.removeEventListener("submit", link.form_listener);
        link.form_listener = async function(e){
            e.preventDefault();
            lw.on();
            try {
                link.modal.hide();
                await callback(new FormData(this));
                await update_func();
                link.form.reset();
            } catch (error) {
                console.log(error);
                Swal.fire("Ошибка", error.message, "error");
                link.modal.show();
            }
            lw.off();
        }
        link.form.addEventListener("submit", link.form_listener);
        link.modal.show();
    }

    /* tab-main */

    //link tab main
    const ltm = {
        tab_main_form       : document.getElementById("tab-main-form"),
        fill_btn            : document.getElementById("fill-btn"),
        inn                 : document.getElementById("inn"),
        name                : document.getElementById("name"),
        name_full           : document.getElementById("name_full"),
        yaddress            : document.getElementById("yaddress"),
        maddress            : document.getElementById("maddress"),
        kpp                 : document.getElementById("kpp"),
        ogrn                : document.getElementById("ogrn"),
        post                : document.getElementById("post"),
        director            : document.getElementById("director"),
        dey                 : document.getElementById("dey"),
        type                : document.getElementById("type"),
    }

    ltm.tab_main_form.addEventListener("submit", async function(e){
        if(mainId){
            e.preventDefault();
            lw.on();
            uo.update(mainId, new FormData(this));
            lw.off();
        }
    });

    ltm.fill_btn.addEventListener("click", async function(){
        const btnAction = fillBtnAction();
        try{
            const inn = ltm.inn.value.trim();
            if(inn == "") throw {message:"Поле ИНН пустое!"}
            btnAction(true);
            const organization = await findOrgByInn(inn);
            if(organization?.id){
                Swal.fire({
                    title: "Внимание",
                    text: "Организация с таким ИНН уже существует! Перейти?",
                    icon: "warning",
                    showConfirmButton: true,
                    showCancelButton: true,
                  })
                  .then((move) => {
                    if (move.isConfirmed) location.href = document.getElementById("router_show").value.replace("@id", organization.id);
                  });                  
            }
            const data = await getDataByInn(inn);
            if(data.suggestions.length > 0){
                fillFieldsWithData(data.suggestions[0].data);
            } else {
                throw {message:"Такая организация не найдена"}
            }
        } catch(e){
            Swal.fire("Ошибка!", `${e.message}`, "error");
        } finally{
            btnAction(false);
        }
    });

    async function findOrgByInn(inn){
        const url = document.getElementById("router_findByInn").value.replace("@id", inn);
        const request = new FormData();
        if(mainId != "") request.append("id", mainId); 
        return await( await fetch(url, {
            method: "POST",
            body: request,
        }) ).json();
    }

    async function getDataByInn(inn){
        return await( await fetch(`https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party`, {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Token ${token}`,
            },
            body: JSON.stringify({query:inn}),
        }) ).json();
    }

    function fillFieldsWithData(item){
        console.log(item);
        if(item.name.short_with_opf == null){
            ltm.name.value = item.name.full_with_opf;
        } else {
            ltm.name.value = item.name.short_with_opf;
        }
        ltm.name_full.value = item.name.full_with_opf ?? "";
        ltm.yaddress.value = item.address.data.source ?? "";
        ltm.maddress.value = item.address.data.source ?? "";
        ltm.ogrn.value = item.ogrn ?? "";
        if(item.type == "INDIVIDUAL"){
            ltm.director.value = item.name?.full ?? "";
            ltm.post.value = item.opf?.full ?? "";
            ltm.dey.value = "Свидетельство о регистрации ИП";
        } else {
            ltm.kpp.value = item.kpp ?? "";
            ltm.post.value = item.management?.post ?? "";
            ltm.director.value = item.management?.name ?? "";
        }
        // ltm.dey.value = item.;
        ltm.type.value = item.type;
        ltm.type.dispatchEvent(new Event("change"));
    }

    function fillBtnAction(){
        const btn_value_before = ltm.fill_btn.innerHTML;
        const iLoad = `<i class="fas fa-spinner fa-spin"></i>`;
        return function(load){
            ltm.fill_btn.innerHTML = load ? iLoad : btn_value_before;
            ltm.fill_btn.disabled = load;
        }
    }

    ltm.type.addEventListener("change", function(){
        if(this.value == "INDIVIDUAL"){
            ltm.kpp.parentElement.parentElement.style.display = "none";
            ltm.kpp.required = false;
        } else {
            ltm.kpp.parentElement.parentElement.style.display = "flex";
            ltm.kpp.required = true;
        }
    });

    if(mainId){
        /* tab-contracts */

        document.getElementById("reset_contract_default").addEventListener("click", function(){
            Swal.fire({
                title: "Вы уверены?",
                text: "Сбросить договор по умолчанию",
                icon: "warning",
                showConfirmButton: true,
                showCancelButton: true,
              })
              .then(async (willDelete) => {
                if (willDelete.isConfirmed) {
                    lw.on();
                    const request = new FormData();
                    request.append("reestr_id", "");
                    await uo.update(mainId, request);
                    await loadContractsList();
                    lw.off();
                }
              });
        });
    
        async function loadContractsList(){
            document.getElementById("bodyContractsList").innerHTML = await( await fetch(document.getElementById("router_edit-contracts-list").value) ).text();
            button_contract_init();
        }
    
        function button_contract_init(){
            document.querySelectorAll(".button-contract-default").forEach(btn => {
                btn.addEventListener("click", async function(){
                    lw.on();
                    const request = new FormData();
                    request.append("reestr_id", this.dataset.id);
                    await uo.update(mainId, request);
                    await loadContractsList();
                    lw.off();
                })
            });
        }
        button_contract_init();
    
        /* tab-bank-accounts */
    
        const lba = {
            modal: new bootstrap.Modal(document.getElementById('bankAccountsCreateModal')),
            form: document.getElementById("bankAccountsCreateModalForm"),
            form_listener: null,
            search: document.getElementById("ba_search"),
            name_short: document.getElementById("ba_name_short"),
            name: document.getElementById("ba_name"),
            address: document.getElementById("ba_address"),
            bik: document.getElementById("ba_bik"),
            kschet: document.getElementById("ba_kschet"),
            rschet: document.getElementById("ba_rschet"),
            button: document.getElementById("bankAccountsCreateModalBtn"),
        }
    
        async function load_organizations_bank(){
            document.getElementById("bodyBankAccountsList").innerHTML = await( await fetch(document.getElementById("router_organizations_bank_list").value) ).text();
            button_bank_accounts_init();
        }
    
        function getBankAccountsSource(){
            return JSON.parse( document.getElementById("bank-accounts-source").innerHTML );
        }
    
        const cob = create_(document.getElementById("router_organizations_bank_create").value);
        const uob = update_(document.getElementById("router_organizations_bank_update").value);
        const dob = delete_(document.getElementById("router_organizations_bank_delete").value);
    
        function button_bank_accounts_init(){
            document.querySelectorAll(".bank-accounts-button-edit").forEach(btn => {
                btn.addEventListener("click", function(){
                    const id = this.dataset.id;
                    getBankAccountsSource().forEach(bank => {
                        if(bank.id == id){
                            lba.name_short.value = bank.name_short;
                            lba.name.value = bank.name;
                            lba.address.value = bank.address;
                            lba.bik.value = bank.bik;
                            lba.kschet.value = bank.kschet;
                            lba.rschet.value = bank.rschet;
                        }
                    });
                    modal_init("Обновить", lba, load_organizations_bank, async function(request){
                        await uob.update(id, request);
                    });
                });
            });
        
            document.querySelectorAll(".bank-accounts-button-delete").forEach(btn => {
                btn.addEventListener("click", function(){
                    const id = this.dataset.id;
                    Swal.fire({
                        title: "Вы уверены?",
                        text: "Удалить",
                        icon: "warning",
                        showConfirmButton: true,
                        showCancelButton: true
                      })
                      .then(async (willDelete) => {
                        if (willDelete.isConfirmed) {
                            lw.on();
                            await dob.delete_(id);
                            await load_organizations_bank();
                            lw.off();
                        }
                      });
                })
            });

            document.querySelectorAll(".bank-accounts-button-set_default").forEach(btn => {
                btn.addEventListener("click", function(){
                    const id = this.dataset.id;
                    Swal.fire({
                        title: "Вы уверены?",
                        text: "Установить банк по умолчанию",
                        icon: "info",
                        showConfirmButton: true,
                        showCancelButton: true,
                      })
                      .then(async (willDefault) => {
                        if (willDefault.isConfirmed) {
                            lw.on();
                            const form = new FormData();
                            form.append("organizations_bank_default", id);
                            await uo.update_(mainId, form);
                            await load_organizations_bank();
                            lw.off();
                        }
                      });
                })
            });
            document.querySelectorAll(".bank-accounts-button-default").forEach(btn => {
                btn.addEventListener("click", function(){
                    const value = this.dataset.value ?? null;
                    Swal.fire({
                        title: "Вы уверены?",
                        text: value ? "Установить банк по умолчанию" : "Очистить банк по умолчанию",
                        icon: "info",
                        showConfirmButton: true,
                        showCancelButton: true,
                      })
                      .then(async (willDefault) => {
                        if (willDefault.isConfirmed) {
                            lw.on();
                            const form = new FormData();
                            form.append("organizations_bank_default", value);
                            await uo.update_(mainId, form);
                            await load_organizations_bank();
                            lw.off();
                        }
                      });
                })
            });
        }
        button_bank_accounts_init();
    
        document.getElementById("bankAccountsCreateBtn").addEventListener("click", function(){
            modal_init("Создать", lba, load_organizations_bank, cob.create_);
        });
    
        $(lba.search).suggestions({
            token,
            type: "BANK",
            count: 5,
            onSelect: showSuggestion
        });
    
        function showSuggestion(suggestion) {
            const data = suggestion.data;
            lba.name_short.value = data?.name.payment || "";
            lba.name.value = data?.name.full || data?.name.payment;
            lba.bik.value = data.bic;
            lba.kschet.value = data.correspondent_account;
            lba.address.value = data.address.value;
        }
    
        /* tab-contact-faces */
    
        const lcf = {
            modal_: document.getElementById('contactFacesModal'),
            modal: new bootstrap.Modal(document.getElementById('contactFacesModal')),
            form: document.getElementById("contactFacesModalForm"),
            form_listener: null,
            // search: document.getElementById("ba_search"),
            surname: document.getElementById("cf_surname"),
            name: document.getElementById("cf_name"),
            ochestvo: document.getElementById("cf_ochestvo"),
            email: document.getElementById("cf_email"),
            phone: document.getElementById("cf_phone"),
            phone2: document.getElementById("cf_phone2"),
            type: document.getElementById("cf_type"),
            post: document.getElementById("cf_post"),
            comment: document.getElementById("cf_comment"),
            button: document.getElementById("contactFacesModalFormButton"),
            uid: document.getElementById("cf_uid"),
        }
    
        const ccf = create_(document.getElementById("router_contact_faces_create").value);
        const ucf = update_(document.getElementById("router_contact_faces_update").value);
        const dcf = delete_(document.getElementById("router_contact_faces_delete").value);
    
        document.getElementById("contactFacesButtonCreate").addEventListener("click", function(){
            modal_init("Создать", lcf, load_contact_faces, ccf.create_);
        });
    
        async function load_contact_faces(){
            document.getElementById("bodyContactFacesList").innerHTML = await( await fetch(document.getElementById("router_contact_faces_list").value) ).text();
            button_contact_faces_init();
        }
    
        function getContactFacesSource(){
            return JSON.parse( document.getElementById("contact-faces-source").innerHTML );
        }
    
        function getUsersSource(){
            return JSON.parse( document.getElementById("users-source").innerHTML );
        }
    
        function button_contact_faces_init(){
            document.querySelectorAll(".contact-faces-button-edit").forEach(btn => {
                btn.addEventListener("click", function(){
                    const id = this.dataset.id;
                    getContactFacesSource().forEach(userOrg => {
                        if(userOrg.id == id){
                            const user = userOrg.user;
                            user_init(user);
                            lcf.type.value = userOrg.type;
                            lcf.post.value = userOrg.post;
                            lcf.comment.value = userOrg.comment;
                        }
                    });
                    modal_init("Обновить", lcf, load_contact_faces, async function(request){
                        await ucf.update(id, request);
                    });
                });
            });
            document.querySelectorAll(".contact-faces-button-delete").forEach(btn => {
                btn.addEventListener("click", function(){
                    const id = this.dataset.id;
                    Swal.fire({
                        title: "Вы уверены?",
                        text: "Удалить",
                        icon: "warning",
                        showConfirmButton: true,
                        showCancelButton: true,
                      })
                      .then(async (willDelete) => {
                        if (willDelete.isConfirmed) {
                          lw.on();
                          await dcf.delete_(id);
                          await load_contact_faces();
                          lw.off();
                        }
                      });
                });
            });
        }
        button_contact_faces_init();
    
        function user_init(user){
            lcf.surname.value = user.surname;
            lcf.name.value = user.name;
            lcf.ochestvo.value = user.ochestvo;
            lcf.email.value = user.email;
            lcf.phone.value = user.phone;
            lcf.phone2.value = user.phone2;
        }
    
        $(lcf.uid).select2();
        $(lcf.uid).on("change", function(){
            if(this.value != ""){
                getUsersSource().forEach(user => {
                    if(user.id == this.value){
                        user_init(user);
                    }
                });
            } 
        })
    }

}