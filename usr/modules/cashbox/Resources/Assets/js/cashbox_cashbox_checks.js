import {Cashbox} from "/usr/modules/cashbox/Resources/Assets/js/cashbox.js";
import {CashboxChecks} from "/usr/modules/cashbox/Resources/Assets/js/cashbox_checks.js";

export class CashboxCashboxChecks{
    constructor(request){
        if(request?.realization_id === undefined || request?.realization_id == "") throw new Error(`Field "realization_id" is empty!`);
        this.realization_id = request.realization_id;
        if(request?.cashier_shifts_id === undefined || request?.cashier_shifts_id == "") throw new Error(`Field "cashier_shifts_id" is empty!`);
        this.cashier_shifts_id = request.cashier_shifts_id;
        this.cashbox = new Cashbox(request);
        this.CashboxChecks = new CashboxChecks();
    }

    async RegisterCheck(re){
        const cashbox = await this.cashbox.RegisterCheck(re);
        if(cashbox?.Error != "") throw new Error( cashbox?.Error);
        const request = new FormData();
        request.append("realization_id", this.realization_id);
        request.append("cashier_shifts_id", this.cashier_shifts_id);
        request.append("data", JSON.stringify(cashbox));
        request.append("TypeCheck", re.TypeCheck);
        const cashbox_checks = await this.CashboxChecks.RegisterCheck(request);
        return { cashbox, cashbox_checks, };
    }
}