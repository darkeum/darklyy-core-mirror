$(function(){
    $("#oid").on("change", function(){
        const oid = $(this).val();
        organizations_edit(oid);
    });

    $(".select2").select2();

    $("#discount_template_modal_form").on("submit", async function(e){
        e.preventDefault();
        const form = $(this);
        const lw = new LW;
        const request = form.serialize();
        form[0].reset();
        $("#discount_template_modal").modal("hide");
        lw.on();
        await add_discount_template_name(request);
        await list_template();
        lw.off();
    });
});

function organizations_edit(oid){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/catalog_v2/groups_organizations/discount_list/"+oid, function(data){
            $("#user_edit").html(data);
            resolve();
        });
    });
}

function template_edit(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/catalog_v2/groups_organizations/discount_template_list/"+id, function(data){
            $("#user_edit").html(data);
            resolve();
        });
    });
}

function organizations_list(){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/catalog_v2/groups_organizations/organizations_list", function(data){
            $("#users_list").html(data);
            resolve();
        });
    });
}

function list_template(){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/catalog_v2/groups_organizations/list_template", function(data){
            $("#discount_template").html(data);
            resolve();
        });
    });
}

function catalog_groups_select(id){
    return new Promise((resolve, reject) => {
        const lw = new LW;
        lw.on();
        catalog_groups_option(id).then(() => {
            lw.off();
            resolve();
        });
    });
}

function catalog_groups_option(id){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/catalog_v2/groups_organizations/catalog_groups_option/"+id, function(data){
            const name = ".catalog_groups_discount_select";
            $(name).html(data).val('').trigger('change');
            $(name+' option:eq(0)').prop('selected',true).trigger('change');
            resolve();
        });
    });
}

function getOidSelect(){
    return $("#oid_select").val();
}

function getTemplateSelect(){
    return $("#template_select").val();
}

class GroupsModal{
    constructor(name){
        this.modal = $(`#${name}_modal`);
        this.form = $(`#${name}_modal_form`);
        this.add_btn = $(`#${name}_add_btn`);
        this.edit_btn = $(`#${name}_edit_btn`);
    }
    add_modal_e(request){
        return false;
    }
    edit_modal_e(request){
        return false;
    }
    show(){
        this.modal.modal("show");
        $(".btn_groups_users_modal").hide();
        // $("#catalog_groups").off("change", this, 'catalog_groups_select').on("change", catalog_groups_select);
    }
    hide(){
        this.modal.modal("hide");
    }
    add(request){
        this.show();
        this.add_btn.show().off("click").on("click", () => {
            this.add_modal_e();
        });
        catalog_groups_select(request.group_id);
    }
    edit(request){
        this.show();
        this.edit_btn.show().off("click").on("click", () => {
            this.edit_modal_e(request.id);
        });
        catalog_groups_select(request.group_id).then(() => {
            $(".catalog_groups_discount_select").val(request.discount_id).trigger('change');
        });
    }
    async delete(id){
        swal({
            title: "Вы уверены?",
            text: "После удаления вы не сможете восстановить эту позицию!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then(async willDelete => {
            if (willDelete) {
                const lw = new LW;
                lw.on();
                await this.l_delete(id);
                lw.off();
            }
        });
    }
    async l_delete(id){
        return;
    }
}

const groups_organizations_modal = new GroupsModal("add");
groups_organizations_modal.add_modal_e = add_modal_e;
groups_organizations_modal.edit_modal_e = edit_modal_e;
groups_organizations_modal.l_delete = async function(id){
    await CatalogGroupsOrganizations.delete(id);
    const oid = getOidSelect();
    await organizations_edit(oid);
    await organizations_list();
};

const groups_template_modal = new GroupsModal("add");
groups_template_modal.add_modal_e = async function() {
    const form = this.form.serialize();
    const id = getTemplateSelect();
    const request = `catalog_groups_template_name_id=${id}&${form}`;
    const lw = new LW;
    lw.on();
    await CatalogGroupsTemplate.add(request);
    await template_edit(id);
    lw.off();
};
groups_template_modal.edit_modal_e = async function (id) {
    const form = this.form.serialize();
    const lw = new LW;
    lw.on();
    await CatalogGroupsTemplate.update(id, form);
    const t_id = getTemplateSelect();
    await template_edit(t_id);
    lw.off();
};
groups_template_modal.l_delete = async function(id){
    await CatalogGroupsTemplate.delete(id);
    const t_id = getTemplateSelect();
    await template_edit(t_id);
};


async function edit_modal_e(id){
    const form = this.form.serialize();
    const lw = new LW;
    lw.on();
    await CatalogGroupsOrganizations.update(id, form);
    const oid = getOidSelect();
    await organizations_edit(oid);
    lw.off();
}

async function add_modal_e(){
    const form = this.form.serialize();
    const oid = getOidSelect();
    const request = `oid=${oid}&${form}`;
    const lw = new LW;
    lw.on();
    await CatalogGroupsOrganizations.add(request);
    await organizations_edit(oid);
    await organizations_list();
    lw.off();
}

function template_set_modal(oid){
    const modal = $("#template_set_modal");
    modal.modal("show");
    $("#template_set_modal_oid").val(oid);
    $("#template_set_modal_form").off().on("submit", async function(e){
        e.preventDefault();
        const form = $(this).serialize();
        const lw = new LW;
        lw.on();
        await template_set(form);
        await Promise.all([organizations_edit(oid), organizations_list()]);
        lw.off();
        modal.modal("hide");
    });
}

function template_set(request){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/catalog_v2/groups_organizations/template_set", request, function(){
            resolve();
        });
    });
}

function add_discount_template_name(request){
    return new Promise((resolve, reject) => {
        $.post("/administration/module/catalog_v2/groups_organizations/template_name_add", request, function(){
            resolve();
        });
    });
}
