$(function(){
    $(".select2").select2();
});

function del_m(id){
    swal({
        title: "Вы уверены?",
        text: "После удаления вы не сможете восстановить эту позицию!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
            const lw = new LW;
            lw.on();
            CatalogGroups.delete(id).then(() => {
                reload_grid();
                lw.off();
            });
        }
    });
}

function reload_grid(){
    $('#jqCatalogGroups').trigger('reloadGrid');
}

function add_m(){
    $('#add_catalog_groups').modal("show");
}

function add_m_e(){
    const form = $("#add_catalog_groups_form").serialize();
    const lw = new LW;
    lw.on();
    CatalogGroups.add(form).then(() => {
        lw.off();
        reload_grid();
    })
}

function switch_modal(){
    $("#switchModal").modal("show");
}

const excel_modal = {
    show(){
        $('#save_excel_modal').modal("show");
        $(".save_excel_modal_btn").hide();
    },
    group(){
        this.show();
        $("#save_excel_modal_btn_add").show();
    },
    group_discount(){
        this.show();
        $("#save_excel_modal_btn_add_discount").show();
    },
    save(url){
        const form = $("#save_excel_modal_form")[0];
        const formData = new FormData(form);
        form.reset();
        const lw = new LW;
        lw.on();
        $.ajax({
            url,
            type: "POST",
            data: formData,
            async: false,
            success: function (msg) {
                reload_grid();
                lw.off();
            },
            error: function(msg) {
                alert('Ошибка! '+msg);
                lw.off();
            },
            cache: false,
            contentType: false,
            processData: false
        });
    },
    group_save(){
        this.save('/administration/module/catalog_v2/groups/save_excel');
    },
    group_discount_save(){
        this.save('/administration/module/catalog_v2/groups/save_discount_excel');
    },
}