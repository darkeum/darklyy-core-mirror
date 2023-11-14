<?php

//require_once('usr/modules/reestr/admin/sub/spec_modal.php');

function reestr_spec_modal($d){
    //['btn_func' => '']
    ?>
        <div class="modal fade admin-form" id="reestr_spec_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	      <div class="modal-dialog" role="document">
	        <div class="modal-content">
	          <div class="modal-header">
	            <h5 class="modal-title" id="exampleModalLabel">Спецификация</h5>
	          </div>
	          <div class="modal-body" id="reestr_spec_modal_body">
	    			<form id="reestr_spec_modal_body_form">
	    				<p>Укажите процент предоплаты</p>
	    				<input type="text" name="pr" class="form-control">
	    				<p>Укажите отсрочку</p>
	    				<input type="text" name="deferment" class="form-control" id="reestr_deferment">
	    				<p>Укажите способ доставки</p>
	    				<select name="delivery" class="form-control" onchange="toggle_delivery_address(this);">
                            <option value="1">Самовывоз</option>
                            <option value="2">Доставка</option>
                        </select>
                        <div id="reestr_delivery_address_con" style="display:none;">
                            <p>Укажите способ доставки(оставьте пустым, чтобы подставился адрес по умолчанию)</p>
                            <input type="text" value="" name="address" id="reestr_delivery_address" class="form-control">
                        </div>
	    			</form>
	          </div>
	          <div class="modal-footer">
	    	  	<button type="button" class="btn btn-info" onclick="<?php echo $d["btn_func"];?>" id="reestr_spec_modal_create_btn" data-dismiss="modal">Создать</button>
	            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	          </div>
	        </div>
	      </div>
	    </div>
    <?php
}

function reestr_spec_modal_js(){
    return '<script src="/usr/modules/reestr/admin/asset/js/spec_modal.js"></script>';
}