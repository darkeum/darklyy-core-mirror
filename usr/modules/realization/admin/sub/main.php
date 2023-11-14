<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2017 JMY LTD
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
	$uid = $core->auth->user_info['id'];
	if($uid  == '1'||$uid == '33')
	{
		$lnn = $lang['realization_list'];
		$adminTpl->admin_head($lang['realization']. ' | '.$lnn);
		$atributs = array();
		$atributs['opts'] = 'open';
		$atributs['module'] = 'module';
		$atributs['module_name'] = 'realization';
		$atributs['module_open'] = 'order';
		loadGrid('jqRealization', $lnn, $atributs, ROOT.'/usr/modules/realization/admin/grid/');
		$adminTpl->footIncludes[] = '<script src="/usr/modules/realization/admin/asset/js/realizationGrid.js?12316465568"></script>';		
		realization_end_init_js();
		$adminTpl->admin_foot();
	}
	else
	{
		$lnn = $lang['realization_list'];
		$adminTpl->admin_head($lang['realization']. ' | '.$lnn);
		$atributs = array();
		$atributs['opts'] = 'open';
		$atributs['module'] = 'module';
		$atributs['module_name'] = 'realization';
		$atributs['module_open'] = 'order';
		load_grid('jqRealization', $lnn, $atributs, ROOT.'/usr/modules/realization/admin/grid/');
		$adminTpl->footIncludes[] = '<script src="/usr/modules/realization/admin/asset/js/realizationGrid.js?12316465568"></script>';
		
		realization_end_init_js();

		$adminTpl->admin_foot();
	}
	echo '
			<div class="modal fade" id="modalRc" tabindex="-1" role="dialog" aria-labelledby="modalRcLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<form id="formComplCorr">
							<div class="modal-header">
								<h5 class="modal-title" id="modalComplCorrLabel">Создать документ поступления?</h5>
							</div>
							<div class="modal-body">	
								<div class="form-group">
									<label for="org">Организация</label>
									<select name="org" class="form-control" id="org">
										
									</select>
								</div>				
								<div class="form-group">
									<label for="textComplCorr">Дата документа</label>
									<textarea name="textComplCorr" class="form-control" id="textComplCorr"></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<input type="hidden" name="idComplCorr" id="idComplCorr"  value="0"/>
								<input type="hidden" name="rowID" id="rowID"  value="0"/>
								<button type="submit" class="btn btn-success">'.$lang['complete'].'</button>
								<button type="button" class="btn btn-secondary" data-dismiss="modal">'.$lang['close'].'</button>
							</div>
						</form>
					</div>
				</div>
			</div>';
} 

