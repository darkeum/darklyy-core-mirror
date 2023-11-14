export class CashierShifts{
    async OpenShift(request){
        return await ( await fetch(`/administration/module/cashbox/cashier`, {
            method: "POST",
            body: request,
        }) ).json();
    }

    async CloseShift(request){
        return await ( await fetch(`/administration/module/cashbox/cashier/close`, {
            method: "POST",
            body: request,
        }) ).json();
    }

    async find(request){
        return await ( await fetch(`/administration/module/cashbox/cashier/find`, {
            method: "POST",
            body: request,
        }) ).json();
    }
}