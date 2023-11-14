$(function(){
    $(document).on("change", "#org", function(){
        const org = +$(this).val();
        const clone_active = $("#clone_active");
        switch(org){
            default:
                clone_active.prop("disabled", false).prop("checked", true);
            break;
            case 1:
                clone_active.prop("disabled", true).prop("checked", false);
            break;
        }
    });
})


function toggle_cat(This){
    let value = $(This).val();
    switch(+value){
        default:
            $("#sales_cat-div").show();
        break;
        case 2:
            $("#sales_cat-div").hide();
        break;
    }
}

