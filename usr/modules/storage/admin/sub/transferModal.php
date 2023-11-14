<?php

//require_once('usr/modules/storage/admin/sub/transferModal.php');

function transferModal($request){
    global $adminTpl;

    $id = $request["id"];

    ?>

        <div class="modal fade admin-form" id="transferModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Перемещение</h5>
              </div>
        	  <div class="modal-body">
                <form onsubmit="return false;" id="transferModalForm">
        	        <select name="stock_in" id="stock_in" class="form-control">
                      <?php echo get_stock_select_option(["selected" => 2]); ?>
        	        </select>
                </form>
        	  </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="transferModalBtn" onclick="transferModal.create(<?php echo $id;?>);" ondblclick="return false;"         data-dismiss="modal">Переместить</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
              </div>
            </div>
          </div>
        </div>

    <?php

    $adminTpl->footIncludes[] = '<script src="/usr/modules/storage/admin/asset/js/transferModal.js?'.time().'"></script>';
}