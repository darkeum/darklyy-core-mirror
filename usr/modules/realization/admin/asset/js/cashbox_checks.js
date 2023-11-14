export class CashboxChecks {
    async RegisterCheck(request) {
        return await (await fetch(`/administration/module/cashbox/checks`, {
            method: "POST",
            body: request,
        })).json();
    }
}