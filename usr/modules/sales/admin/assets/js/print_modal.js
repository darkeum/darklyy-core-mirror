function modal_print(id){

	checkZeroSum(id).then(() => {
        print_add(id);
		modal_o('#modal-print');
	}, (name) => {
		swal("Ошибка!", "Сумма товара "+name+" равна 0!", "error");
	});
}

function print_add(id){
    print_del();
    let str = `
    <div id="modal-print" class="popup-basic popup-lg bg-none mfp-with-anim mfp-hide">
    <div class="panel">
        <div class="panel-heading"><span class="panel-icon"><i class="fas fa-print"></i></span><span class="panel-title">Выберите дальнейшее действие</span></div>
            <div class="panel-body">
                <h3 class="mt5">Какими образом вы хотите разместить заказ?</h3>
                <hr class="short alt">
                <p>                          
                   <div class="">
                      <table class="table list-table">
                        <tbody>
                          <tr>
                            <td onclick="window.open('/administration/module/sales/print/sales/${id}&format=pdf&autoprint=true','_blank'); return false;"  style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать заказ</td>
                            <td class="text-right">
                                <a href="/administration/module/sales/print/sales/${id}&format=pdf&autoprint=false" target="_blank">PDF</a> 
                                <a href="/administration/module/sales/print/sales/${id}&format=docx&autoprint=false" target="_blank">DOC</a> 
                                <a href="/administration/module/sales/print/sales/${id}&format=xlsx&autoprint=false" target="_blank">XLS</a> 
                                <a href="/administration/module/sales/print/sales/${id}&format=all&autoprint=false" target="_blank"><i class="far fa-archive"></i></a>
                            </td>
                          </tr>
                          <tr>
                            <td onclick="window.open('/administration/module/sales/print/bill/${id}&format=pdf&autoprint=true','_blank'); return false;" style="cursor: pointer;"><span class="icon text-info far fa-file-alt"></span>Распечатать счет</td>
                            <td class="text-right">
                                <a href="/administration/module/sales/print/bill/${id}&format=pdf&autoprint=false" target="_blank">PDF</a> 
                                <a href="/administration/module/sales/print/bill/${id}&format=docx&autoprint=false" target="_blank">DOC</a> 
                                <a href="/administration/module/sales/print/bill/${id}&format=xlsx&autoprint=false" target="_blank">XLS</a> 
                                <a href="/administration/module/sales/print/bill/${id}&format=all&autoprint=false" target="_blank"><i class="far fa-archive"></i></a>
                            </td>
                          </tr>			
                           <tr>
                            <td onclick="window.open('/administration/module/sales/print/bill_sign/${id}&format=pdf&autoprint=true','_blank'); return false;" style="cursor: pointer;"><span class="icon text-success far fa-file-invoice"></span>Распечатать счет с факсимиле</td>
                            <td class="text-right">
                                <a href="/administration/module/sales/print/bill_sign/${id}&format=pdf&autoprint=false" target="_blank">PDF</a> 
                                <a href="/administration/module/sales/print/bill_sign/${id}&format=docx&autoprint=false" target="_blank">DOC</a> 
                                <a href="/administration/module/sales/print/bill_sign/${id}&format=xlsx&autoprint=false" target="_blank">XLS</a> 
                                <a href="/administration/module/sales/print/bill_sign/${id}&format=all&autoprint=false" target="_blank"><i class="far fa-archive"></i></a>
                            </td>
                          </tr>		
                           <tr>
                            <td onclick="window.open('/administration/module/sales/print/kp/${id}&format=pdf&autoprint=true','_blank'); return false;" style="cursor: pointer;"><span class="icon text-system far fa-file-invoice-dollar"></span>Распечатать коммерческое предложение</td>
                            <td class="text-right">
                                <a href="/administration/module/sales/print/kp/${id}&format=pdf&autoprint=false" target="_blank">PDF</a> 
                                <a href="/administration/module/sales/print/kp/${id}&format=docx&autoprint=false" target="_blank">DOC</a> 
                                <a href="/administration/module/sales/print/kp/${id}&format=xlsx&autoprint=false" target="_blank">XLS</a> 
                                <a href="/administration/module/sales/print/kp/${id}&format=all&autoprint=false" target="_blank"><i class="far fa-archive"></i></a>
                            </td>
                          </tr>	
                           <tr>
                            <td onclick="window.open('/administration/module/sales/print/kp_sign/${id}&format=pdf&autoprint=true','_blank'); return false;" style="cursor: pointer;"><span class="icon text-system far fa-file-invoice-dollar"></span>Распечатать коммерческое предложение с факсимиле</td>
                            <td class="text-right">
                                <a href="/administration/module/sales/print/kp_sign/${id}&format=pdf&autoprint=false" target="_blank">PDF</a> 
                                <a href="/administration/module/sales/print/kp_sign/${id}&format=docx&autoprint=false" target="_blank">DOC</a> 
                            </td>
                          </tr>	
                          <tr>
                            <td onclick="window.open('/administration/module/sales/print/costing/${id}&format=xlsx','_blank'); return false;" style="cursor: pointer;"><span class="icon text-warning far fa-calculator"></span>Распечатать калькуляцию</td>
                            <td class="text-right">
                                <a href="/administration/module/sales/print/costing/${id}&format=xlsx" target="_blank">XLS</a> 
                            </td>
                          </tr>	
                          <tr>
                            <td onclick="window.open('/administration/module/sales/print/ks2/${id}&format=xlsx','_blank'); return false;" style="cursor: pointer;"><span class="icon text-primary far fa-abacus"></span>Распечатать КС-2</td>
                            <td class="text-right">
                                <a href="/administration/module/sales/print/ks2/${id}&format=xlsx" target="_blank">XLS</a> 
                            </td>
                          </tr>	
                          <tr>
                            <td onclick="window.open('/administration/module/sales/print/kp_locos/${id}&format=pdf&autoprint=true','_blank'); return false;" style="cursor: pointer;"><span class="icon text-system far fa-file-invoice-dollar"></span>Распечатать коммерческое предложение Локос</td>
                            <td class="text-right">
                                <a href="/administration/module/sales/print/kp_locos/${id}&format=pdf&autoprint=false" target="_blank">PDF</a> 
                                <a href="/administration/module/sales/print/kp_locos/${id}&format=docx&autoprint=false" target="_blank">DOC</a> 
                            </td>
                          </tr>	
                        </tbody>
                      </table>
                    </div>                        
                </p>
            </div>
            <div class="panel-footer text-right">
                <button type="button" onclick="$('#modal-print').magnificPopup('close');" class="btn btn-danger">Закрыть</button>						
            </div>
        </div>
    </div>
    `;

    $('body').append(str);
}

function print_del(){
    $("#modal-print").remove();
}

function checkZeroSum(id){
	return new Promise(function(resolve, reject){
    const d = typeof id === "object" ? id : {id};
		$.post("/ajax.php?do=checkZeroSum", d, function(data){
			if(data === ""){
				resolve();
			} else {
				reject(data);
			}
		});
	});
}