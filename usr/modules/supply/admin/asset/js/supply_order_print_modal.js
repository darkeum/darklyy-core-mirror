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
              <td onclick="window.open('/administration/module/supply/create_excel/${id}'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать заказ</td>
              <td class="text-right">
                  <a href="/administration/module/supply/create_excel/${id}" target="_blank">XLS</a> 
              </td>
            </tr>
            <tr>
              <td onclick="window.open('/administration/module/supply/create_excel/${id}&combine=true'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать заказ с объединением позиций</td>
              <td class="text-right">
                  <a href="/administration/module/supply/create_excel/${id}&combine=true" target="_blank">XLS</a> 
              </td>
            </tr>
            <tr>
              <td onclick="window.open('/administration/module/supply/create_excel2/${id}'); return false;" style="cursor: pointer;"><span class="icon text-alert far fa-receipt"></span>Распечатать заказ с ценами</td>
              <td class="text-right">
                <a href="/administration/module/supply/create_excel2/${id}" target="_blank">XLS</a> 
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
//administration/module/supply/create_excel/'.$id.'