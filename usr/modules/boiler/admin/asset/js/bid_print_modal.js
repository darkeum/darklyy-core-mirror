function print_modal(id){
  $("#printModal").remove();
    let str = `
    <div class="modal fade" id="printModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Выберите дальнейшее действие</h5>
        </div>
        <div class="modal-body">
          
        <table class="table list-table" style="padding: 0px;">
          <tbody>
            <tr>
              <td onclick="window.open('/catalog_v2/specification/boiler_bid/${id}'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать спецификацию</td>
              <td class="text-right">
                  <a href="/catalog_v2/specification/boiler_bid/${id}" target="_blank">DOCX</a>  
              </td>
            </tr>
            <tr>
              <td onclick="window.open('/administration/module/boiler/bid/print/${id}'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать ???</td>
              <td class="text-right">
                  <a href="/administration/module/boiler/bid/print/${id}" target="_blank">DOCX</a>  
              </td>
            </tr>
            <tr>
              <td onclick="window.open('/administration/module/boiler/bid/print_boiler_detail_sales/costing/${id}&format=xlsx'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать калькуляцию</td>
              <td class="text-right">
                  <a href="/administration/module/boiler/bid/print_boiler_detail_sales/costing/${id}&format=xlsx" target="_blank">XLSX</a>  
              </td>
            </tr>
            <tr>
              <td onclick="window.open('/administration/module/boiler/bid/print_boiler_detail_sales/costing_purchase/${id}&format=xlsx'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать калькуляцию (прайсовая цена)</td>
              <td class="text-right">
                  <a href="/administration/module/boiler/bid/print_boiler_detail_sales/costing_purchase/${id}&format=xlsx" target="_blank">XLSX</a>  
              </td>
            </tr>
            <tr>
              <td onclick="window.open('/administration/module/boiler/bid/print_boiler_detail_sales/kp_locos/${id}&format=pdf&autoprint=true'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать коммерческое предложение Локос</td>
              <td class="text-right">
                  <a href="/administration/module/boiler/bid/print_boiler_detail_sales/kp_locos/${id}&format=pdf" target="_blank">PDF</a>  
                  <a href="/administration/module/boiler/bid/print_boiler_detail_sales/kp_locos/${id}&format=doc" target="_blank">DOCX</a>  
              </td>
            </tr>
          </tbody>
        </table>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>
    `;
    $("body").append(str);
    $("#printModal").modal("show");
}