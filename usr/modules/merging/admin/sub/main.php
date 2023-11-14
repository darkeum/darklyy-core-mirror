<?php

function main() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Комплектация товаров';
	$adminTpl->admin_head($lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'merging';
	$atributs['module_open'] = 'detail';
    loadGrid('jqMerging', $lnn, $atributs, ROOT.'/usr/modules/merging/admin/grid/');    
	$adminTpl->footIncludes[] = '<script src="/usr/modules/merging/admin/asset/js/grid.js"></script>';	
	echo '<div class="modal fade" id="modalRemove" tabindex="-1" role="dialog" aria-labelledby="modalRemoveLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
					<form id="formRemove">
					<div class="modal-header">
						<h5 class="modal-title" id="modalRemoveCorrLabel">Удалить комплектацию товаров</h5>
					</div>
					<div class="modal-body">	
						<p>Вы действительно хотите удалить данную комплектацию?</p>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="idRemove" id="idRemove"  value="0"/>
						<input type="hidden" name="rowIDRemove" id="rowIDRemove"  value="0"/>
						<button type="submit" class="btn btn-danger">'.$lang['delete'].'</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">'.$lang['close'].'</button>
					</div>
					</form>
					</div>
				</div>
			</div>
			<div class="modal fade" id="modalRestore" tabindex="-1" role="dialog" aria-labelledby="modalRestoreLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
					<form id="formRestore">
					<div class="modal-header">
						<h5 class="modal-title" id="modalRestoreLabel">Восстановить комплектацию?</h5>
					</div>
					<div class="modal-body">	
						<p>Вы действительно хотите восстановить данную комплектацию?</p>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="idRestore" id="idRestore"  value="0"/>
						<input type="hidden" name="rowIDRestore" id="rowIDRestore"  value="0"/>
						<button type="submit" class="btn btn-danger">Восстановить</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">'.$lang['close'].'</button>
					</div>
					</form>
					</div>
				</div>
			</div>';
	$adminTpl->admin_foot();
} 