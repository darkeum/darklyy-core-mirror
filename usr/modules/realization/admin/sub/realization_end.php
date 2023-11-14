<?php

function realization_end_init_modal($id = ""){
    ?>

                <div class="modal fade" id="conductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Провести?</h5>
                      </div>
                      <div class="modal-body">
                        Вы действительно хотите провести?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary universalBtn" data-dismiss="modal" id="conductBtn" onclick="realization_end('takeOffReserv', '<?php echo $id;?>');">Продолжить</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="modal fade" id="distributeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Распровести?</h5>
                      </div>
                      <div class="modal-body">
                        Вы действительно хотите распровести?
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary universalBtn" data-dismiss="modal" id="distributeBtn" onclick="realization_end('', '<?php echo $id;?>');">Продолжить</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                      </div>
                    </div>
                  </div>
				</div>

    <?php
}

function realization_end_init_js(){
    global $adminTpl;
    $adminTpl->footIncludes[] = '<script src="/usr/modules/realization/admin/asset/js/realization_end.js?'.time().'"></script>';
}