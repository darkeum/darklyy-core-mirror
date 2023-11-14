async function load_list(){
    const data = await get_list();
    document.getElementById("table_body").innerHTML = data;
}

async function get_list(){
    return await (await fetch(`/administration/module/catalog_v2/backup/view_body/`+getMainId())).text();
}

function getMainId(){
    return document.getElementById("mainId").value;
}

function restore_modal(id, T){
    const modal = $("#restoreModal");
    modal.modal("show");
    const parent = $(T).parent();
    const data = JSON.parse(parent.find(".data").eq(0).val());
    const restoreModalTBody = $("#restoreModalTBody");
    restoreModalTBody.html("");

    $("#restoreModalBtn").off().on("click", async function(){
        modal.modal("hide");
        await (await fetch(`/administration/module/catalog_v2/backup/restore/${id}`)).text();
        await load_list();
    });

    data.forEach((value, index, array) => {
        switch(value.field){
            default:
                restoreModalTBody.append(`<tr>
                    <td>${value.rus}: ${value.catalog}</td>
                    <td>${value.rus}: ${value.original}</td>
                </tr>`);
            break;
            case "id":
            case "created_at":
            case "updated_at":
            break;
        }
    })
}