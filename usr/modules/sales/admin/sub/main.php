<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2019 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}


function content_main() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = $lang['sales_list'];
	$adminTpl->admin_head($lang['sales']. ' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'sales';
	$atributs['module_open'] = 'order';
	// $atributs['js'] = 'setTimeout(function () {$("#jqSales").jqGrid(\'setGridParam\',{postData:{end: \'0\'}}); $(\'#jqSales\').trigger(\'reloadGrid\');}, 150);';
	loadGrid('jqSales', $lnn, $atributs, ROOT.'/usr/modules/sales/admin/grid/');
	echo '
	<div class="modal fade" id="copySalesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content">
			<div class="modal-header">
			  <h5 class="modal-title" id="exampleModalLabel">Копирование продажи</h5>
			</div>
			<div class="modal-body">
			  Вы действительно хотите скопировать продажу № PN-<span id="number_sales"></span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="copySalesBtn" data-dismiss="modal" onclick="copySalesBtn(this);">Копировать</button>
			  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		  </div>
		</div>
  	</div>

	<div id="fastTaskModalDiv"></div>

	<div class="modal fade" id="viewTaskModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
		  <div class="modal-content admin-form">
			<div class="modal-header">
			  <h5 class="modal-title" id="viewTaskModalHeader"></h5>
			</div>
			<div class="modal-body" id="viewTaskModalBody"></div>
			<div class="modal-footer">
			  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			</div>
		  </div>
		</div>
  	</div>
  ';
  print_init();
  end_sales_init(3);
	$adminTpl->footIncludes[] = '<script src="/usr/modules/sales/admin/assets/js/FastTask.js?'.time().'"></script>';
	$adminTpl->footIncludes[] = '<script src="/usr/modules/sales/admin/assets/js/main.js?'.time().'"></script>';
	$adminTpl->admin_foot();
} 

function grid_site() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = $lang['sales_list'];
	$adminTpl->admin_head($lang['sales']. ' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'sales';
	$atributs['module_open'] = 'order';
	$atributs['js'] = 'setTimeout(function () {$("#jqSalesSite").jqGrid(\'setGridParam\',{postData:{end: \'0\'}}); $(\'#jqSalesSite\').trigger(\'reloadGrid\');}, 150);';
	load_grid('jqSalesSite', $lnn, $atributs, ROOT.'/usr/modules/sales/admin/grid/');
	// $adminTpl->footIncludes[] = '<script src="/usr/modules/sales/admin/assets/js/main.js"></script>';
	$adminTpl->admin_foot();
} 

