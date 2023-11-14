import {Cashbox} from "/usr/modules/realization/admin/asset/js/cashbox.js";
import {CashierShifts} from "/usr/modules/realization/admin/asset/js/cashier_shifts.js";

export class CashboxCashierShifts{
    constructor(request){
        if(request?.cashiers_id === undefined || request?.cashiers_id == "") throw new Error(`Field "cashiers_id" is empty!`);
        this.cashiers_id = request.cashiers_id;
        if(request?.cashbox_id === undefined || request?.cashbox_id == "") throw new Error(`Field "cashbox_id" is empty!`);
        this.cashbox_id = request.cashbox_id;
        this.cashbox = new Cashbox(request);
        this.CashierShifts = new CashierShifts();
    }

    first_data(){
        const request = new FormData();
        request.append("cashiers_id", this.cashiers_id);
        request.append("cashbox_id", this.cashbox_id);
        return request;
    }

    async OpenShift(){
        const cashbox = await this.cashbox.OpenShift();
        if(cashbox?.Error != "") throw new Error(cashbox?.Error);
        const request = this.first_data();
        request.append("data_start", JSON.stringify(cashbox));
        const cashier_shifts = await this.CashierShifts.OpenShift(request);
        return { cashbox, cashier_shifts, };
    }

    async CloseShift(){
        const cashbox = await this.cashbox.CloseShift();
        if(cashbox?.Error != "") throw new Error(cashbox?.Error);
        const request = this.first_data();
        request.append("data_end", JSON.stringify(cashbox));
        const cashier_shifts = await this.CashierShifts.CloseShift(request);
        return { cashbox, cashier_shifts, };
    }

    async isOpen(){
        const data = await this.CashierShifts.find(this.first_data());
        return data?.id !== undefined;
    }
}