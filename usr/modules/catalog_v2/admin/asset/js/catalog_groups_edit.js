$(function(){
    $(".select2").select2({
        placeholder: "Выберите категорию",
        allowClear: true,
    });

    $("#copy-table").on("click", function(){
        navigator.clipboard.writeText($("#wrapper-table").html())
        .then(() => {
          // Получилось!
          alert(`Скопировано`);
        })
        .catch(err => {
            alert(`Ошибка: ${err}`);
        });
    })
});

function update_m(){
    const lw = new LW;
    lw.on();
    const id = getMainId();
    const request = $("#mainForm").serialize();
    CatalogGroups.update(id, request).then(() => {
        lw.off();
    });
}

function getMainId(){
    return $("#mainId").val();
}

const discount_modal = {
    name: "#discountModal",
    nameForm: "#discountModalForm",
    show(){
        $(".discountModalBtn").hide();
        $(this.name).modal("show");
        $(this.nameForm)[0].reset();
    },
    hide(){
        $(this.name).modal("hide");
    },
    add(){
        this.show();
        $("#discountModalBtnAdd").show();
    },
    add_e(){
        const form = $(this.nameForm).serialize();
        const lw = new LW;
        lw.on();
        const id = getMainId()
        const request = "catalog_groups_id="+id+"&"+form;
        CatalogGroupsDiscount.add(request).then(() => {
            catalog_groups_discount().then(() => {
                lw.off();
            })
        });
    },
    edit(request){
        this.show();
        $("#discountModalEdit").show().off("click").on("click", () => {
            this.edit_e(request.id);
        });
        $("#discount_name").val(request.name_id);
        $("#discount_discount").val(request.discount);
    },
    edit_e(id){
        const form = $(this.nameForm).serialize();
        const lw = new LW;
        lw.on();
        CatalogGroupsDiscount.update(id, form).then(() => {
            catalog_groups_discount().then(() => {
                lw.off();
            })
        });
    },
}

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
            CatalogGroupsDiscount.delete(id).then(() => {
                catalog_groups_discount().then(() => {
                    lw.off();
                });
            });
        }
    });
}

function catalog_groups_discount(){
    return new Promise((resolve, reject) => {
        const id = getMainId();
        $.post("/administration/module/catalog_v2/groups/catalog_groups_discount/"+id, function(data){
            $("#catalog_groups_discount").html(data);
            resolve();
        });
    });
}

$(function(){
    const paginateItem = $(".paginate-item");
    paginateItem.on("click", async function(){
        const lw = new LW;
        lw.on();
        const page = $(this).data("page");
        const id = getMainId();
        paginateItem.removeClass("paginate-item-active");
        $(this).addClass("paginate-item-active");
        const table = await ( await fetch(`/administration/module/catalog_v2/groups/catalog_groups_table/${id}?page=${page}`) ).text();
        $("#main-table").html(table);
        lw.off();
    });
})