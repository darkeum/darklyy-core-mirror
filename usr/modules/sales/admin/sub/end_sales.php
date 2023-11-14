<?php

function end_sales_init($type = 2){
    global $adminTpl;
    ?>

<div class="modal fade" id="end_sales_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Закрыть продажу</h5>
	  </div>
	  <form id="end_sales_modal_form" onsubmit="end_sales_modal<?php echo $type;?>();">
	  <div class="modal-body">
		<div class="mb10">Вы действительно хотите <span style="font-weight:bold;" id="end_sales_modal_status_text"></span> закрыть продажу?</div>
		<div class=""><textarea name="comment_end" id="end_sales_modal_textarea" class="form-control" placeholder="Введите комментарий"></textarea></div>
		<input type="hidden" value="" name="end" id="end">
		<input type="hidden" value="" name="id" id="end_id">
      </div>
	  <div class="modal-footer">
	  	<button type="submit" class="btn btn-primary">Завершить</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
	  </div>
	  </form>
    </div>
  </div>
</div>

    <?php

$adminTpl->footIncludes[] = '<script src="/usr/modules/sales/admin/assets/js/end_sales.js?'.time().'"></script>';

}