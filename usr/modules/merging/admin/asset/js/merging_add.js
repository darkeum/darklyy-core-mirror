$(function(){
    view_stock();
});

function view_stock(){
    const org = $("#org").val();
    $(".stock_opt").hide();
    const so = $(".stock_opt_"+org);
    const stock = $("#stock_id");
    stock.val("");
    const btn = $("#sub");
    btn.prop("disabled", true);
    if(so.length > 0){
        so.show();
        const value = $(so[0]).val();
        stock.val(value);
        btn.prop("disabled", false);
    }
}