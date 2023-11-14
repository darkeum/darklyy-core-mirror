import {CashboxCashierShifts} from "/usr/modules/cashbox/Resources/Assets/js/cashbox_cashier_shifts.js";

window.addEventListener('load', function () {

    const log = true;

    //const lw = new LW;

    const mainConsole = document.getElementById("main-console");

    function btn_init(btn, callback){
        btn.addEventListener("click", async function(){
            try{
                //lw.on();
                const data = await callback();
                if(log) console.log(data);
            } catch(error){
                Swal.fire("Ошибка!", error.message, "error")
            } finally{
                //lw.off();
            }
        });
    }

    const CashierName = document.getElementById("CashierName");

    const buttons = {
        openShift: document.getElementById("open-shift"),
        closeShift: document.getElementById("close-shift"),
        xReport: document.getElementById("x-report"),
        printOfd: document.getElementById("print-ofd"),
        dataFn: document.getElementById("data-fn"),
        dataKkt: document.getElementById("data-kkt"),
    }

    document.getElementById("button-init").addEventListener("click", load);
    const block1 = document.getElementById("block1");

    if(cashier_shifts?.id){
        load();
    } else {
        block1.style.display = "block";
    }

    async function load(){
        block1.style.display = "none";
        //lw.on();

        try {
            const opt = CashierName.options[CashierName.selectedIndex];
            const ccs = new CashboxCashierShifts({
                CashierName: CashierName.value,
                CashierVATIN: opt.dataset.inn,
                NumDevice: document.getElementById("NumDevice").value,
                cashiers_id: opt.dataset.id,
                cashbox_id: document.getElementById("cashbox_id").value,
            });
            if(log) console.log(KkmServer);
            if(log) console.log(ccs);

            const isOpen = await ccs.isOpen();
            if(log) console.log(isOpen);
            if(isOpen){
                buttons.openShift.style.display = "none";
            } else {
                buttons.closeShift.style.display = "none";
            }

            document.getElementById("cashier-name").innerHTML = opt.text;

            document.getElementById("block2").style.display = "block";

            btn_init(buttons.openShift, async () => {
                const data = await ccs.OpenShift();
                buttons.openShift.style.display = "none";
                buttons.closeShift.style.display = "block";
                return data;
            });
    
            btn_init(buttons.closeShift, async () => {
                const data = await ccs.CloseShift();
                buttons.openShift.style.display = "block";
                buttons.closeShift.style.display = "none";
                return data;
            });
            
            btn_init(buttons.xReport, async () => await ccs.cashbox.XReport());
            
            btn_init(buttons.printOfd, async () => await ccs.cashbox.OfdReport());
            
            btn_init(buttons.dataFn, async () => await ccs.cashbox.GetDataForCheck(0, 1));
            
            btn_init(buttons.dataKkt, async () => {
                const data = await cashbox.GetDataKKT();
                mainConsole.innerHTML = JSON.stringify(data, undefined, 2);
                return data;
            });
            
        } catch (error) {
            Swal.fire("Ошибка инициализации!", error.message, "error");
        } finally{
            //lw.off();
        }
    }
});
