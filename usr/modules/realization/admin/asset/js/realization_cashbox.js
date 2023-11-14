import {CashboxCashboxChecks} from "/usr/modules/realization/admin/asset/js/cashbox_cashbox_checks.js";

$("#modal-pay-form").on("submit", async function(e){
    e.preventDefault();
    $("#modal-pay").modal("hide");
    const lw = new LW;
    try{
        lw.on();
        const opt = $("#modal-pay-select :selected");
        const cashbox_cashbox_checks = new CashboxCashboxChecks({
            CashierName: opt.data("cashier_name"),
            CashierVATIN: opt.data("cashier_vatin"),
            NumDevice: opt.data("num_device"),
            realization_id: $("#mainId").val(),
            cashier_shifts_id: $("#modal-pay-select").val(),
        });
        const grd = await get_realization_data();
        const RegisterCheck = await cashbox_cashbox_checks.RegisterCheck({
            TypeCheck: 0,
            items: await get_realization_list(),
            tableLinkItems: getTableLinkItems(),
            mainData: getMainData(grd),
            cash: +$("#modal-pay-cash").data("value"),
            electronicPayment: +$("#modal-pay-card").val(),
        });
        console.log(RegisterCheck);
        if(RegisterCheck.cashbox?.Error != "") throw new Error(RegisterCheck.cashbox?.Error);
        await realization_end();
    } catch(error){
        swal("Ошибка!", error.message, "error");
    } finally{
        lw.off();
    }
})

$("#modal-pay-cancel-form").on("submit", async function(e){
    e.preventDefault();
    $("#modal-pay-cancel").modal("hide");
    const lw = new LW;
    try{
        lw.on();
        const cashbox_cashbox_checks = new CashboxCashboxChecks({
            CashierName: $("#modal-pay-cancel-CashierName").val(),
            CashierVATIN: $("#modal-pay-cancel-CashierVATIN").val(),
            NumDevice: $("#modal-pay-cancel-NumDevice").val(),
            realization_id: $("#mainId").val(),
            cashier_shifts_id: $("#modal-pay-cancel-cashier_shifts_id").val(),
        });
        const grd = await get_realization_data();
        const RegisterCheck = await cashbox_cashbox_checks.RegisterCheck({
            TypeCheck: 1,
            items: await get_realization_list(),
            tableLinkItems: getTableLinkItems(),
            mainData: getMainData(grd),
            cash: +$("#modal-pay-cancel-cash").data("value"),
            electronicPayment: +$("#modal-pay-cancel-card").data("value"),
        });
        console.log(RegisterCheck);
        if(RegisterCheck.cashbox?.Error != "") throw new Error(RegisterCheck.cashbox?.Error);
        await realization_end();
    } catch(error){
        swal("Ошибка!", error.message, "error");
    } finally{
        lw.off();
    }
})

function getMainData(grd){
    return {
        org_name: grd.org_.organization.name,
        total: grd.total,
    };
}

function getTableLinkItems(){
    return {
        name: "s_name",
        count: "count",
        price: "s_price",
        total: "total",
        unit_name: "unit_name",
        ofd: "ct_ofd",
    };
}