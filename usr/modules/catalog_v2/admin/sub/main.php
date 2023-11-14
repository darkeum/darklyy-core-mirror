<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ADMIN_SWITCH')) {
    location(' /');
    exit;
}

function catalog_main() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang, $url;
	$lnn = 'Менеджер каталога';
	$adminTpl->admin_head('Менеджер каталога | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'catalog_v2';
  $atributs['module_open'] = 'edit';	
  if(($url[3] ?? "") != "all"){
    $atributs['button'] = 'Показать всю номенклатуру';
    $atributs['button_url'] = '/administration/module/catalog_v2/all';	
  }
  

    //load_grid('jqCatalog', $lnn, $atributs);
	load_grid('jqCatalog', $lnn, $atributs, ROOT.'/usr/modules/catalog_v2/admin/grid/');
    echo '
    <div class="modal fade" id="copyCatModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Скопировать номенклатуру?</h5>
        </div>
        <div class="modal-body">
          Вы действительно хотите скопировать номенклатуру?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="copyCatModalBtn" onclick="copyCat(this)" data-dismiss="modal">Скопировать</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>

    <div class="modal fade" id="hiddenModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Выключить номенклатуру?</h5>
        </div>
        <div class="modal-body">
          Вы действительно хотите выключить номенклатуру?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary hiddenBtn" onclick="hiddenBtn($(this).data(\'type\'), $(this).data(\'id\'));" data-dismiss="modal">Выключить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>
    <div class="modal fade" id="hiddenModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Включить номенклатуру?</h5>
        </div>
        <div class="modal-body">
          Вы действительно хотите включить номенклатуру?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary hiddenBtn" onclick="hiddenBtn($(this).data(\'type\'), $(this).data(\'id\'));" data-dismiss="modal">Включить</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>
    ';
    $adminTpl->footIncludes[] = '
    <script>
        function copyCatModal(This){
            let id = $(This).data("id");
            $("#copyCatModalBtn").data("id", id);
            $("#copyCatModal1").modal("show");
        }
        
        function copyCat(This){
            let id = $(This).data("id");
            $.post("/administration/module/catalog_v2/copyCat", {id:id}, function(data){
                $("#jqCatalog").trigger("reloadGrid");
                notif("success", "Успешно", "Номенклатура успешно скопирована");
            });
        }

        function hiddenCat(type, id){
          $(".hiddenBtn").data("type", type);
          $(".hiddenBtn").data("id", id);
          if(+type === 0){
            $("#hiddenModal2").modal("show");
          } else {
            $("#hiddenModal").modal("show");
          }

        }

        function hiddenBtn(type, id){
          $.post("/administration/module/catalog_v2/hiddenCat", {type, id}, function(){
              $("#jqCatalog").trigger("reloadGrid");
              let typeText = +type === 0 ? "включена" : "выключена";
              notif("success", "Успешно", "Номенклатура успешно "+typeText);
          });
        }
    </script>';	
	$adminTpl->admin_foot();
}
 