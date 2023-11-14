<?php

/**
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2020 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

if (!defined('ACCESS')) {
	header('Location: /');
	exit;
}
 
function main() 
{	
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;		
	$lnn = $lang['corrections_list'];
	$adminTpl->admin_head($lang['corrections'].' | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'none';
	$atributs['button'] = $lang['corrections_add'];	
	$atributs['button_onclick'] = "$('#modalAddCorr').modal('show');";	
	$atributs['button_url'] = "#";	
	loadGrid('jqCorr', $lnn, $atributs, ROOT.'/usr/modules/corrections/admin/grid/');
	$adminTpl->footIncludes[] = '<script src="usr/modules/corrections/admin/asset/js/script.js?'.time().'"></script>';	
	$uid = $core->auth->user_info['id'];
	echo '
	<style>
		.card-label.mod-selectable {
		border-radius: 3px;
		font-weight: 700;
		margin: 0 0 4px;
		min-height: 20px;
		padding: 6px 12px;
		position: relative;
		transition: padding 85ms,margin 85ms,box-shadow 85ms;
	}
	</style>';
	if($core->auth->allowAccessById($uid, '13'))
	{
		echo '
			<div class="modal fade" id="modalRemoveCorr" tabindex="-1" role="dialog" aria-labelledby="modalRemoveCorrLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
					<form id="formRemoveCorr">
					<div class="modal-header">
						<h5 class="modal-title" id="modalRemoveCorrLabel">'. $lang['corrections_del'].'</h5>
					</div>
					<div class="modal-body">	
						<p>'.$lang['corrections_del_text'].'</p>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="idRemoveCorr" id="idRemoveCorr"  value="0"/>
						<input type="hidden" name="rowIDRemove" id="rowIDRemove"  value="0"/>
						<button type="submit" class="btn btn-danger">'.$lang['delete'].'</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">'.$lang['close'].'</button>
					</div>
					</form>
					</div>
				</div>
			</div>';
			echo '
			<div class="modal fade" id="modalComplCorr" tabindex="-1" role="dialog" aria-labelledby="modalComplCorrLabel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
					<form id="formComplCorr">
					<div class="modal-header">
						<h5 class="modal-title" id="modalComplCorrLabel">'. $lang['corrections_compl'].'</h5>
					</div>
					<div class="modal-body">	
						<div class="form-group">
							<label for="typeComplCorr">'.$lang['corrections_compl_type'].'</label>
							<select name="typeComplCorr" class="form-control" id="typeComplCorr">
								<option value="1">'.$lang['corrections_compl_type_1'].'</option>
								<option value="2">'.$lang['corrections_compl_type_2'].'</option>
							</select>
						</div>				
						<div class="form-group">
							<label for="textComplCorr">'.$lang['comment'].'</label>
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
	echo '<div class="modal fade" id="modalAddCorr" tabindex="-1" role="dialog" aria-labelledby="modalAddCorrLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
			<form id="formCorr">
			<div class="modal-header">
				<h5 class="modal-title" id="modalAddCorrLabel">'. $lang['corrections_add'].'</h5>
			</div>
			<div class="modal-body">	
				<div class="form-group">
					<label for="typeCorr">'.$lang['corrections_type'].'</label>
					<select name="typeCorr" class="form-control" id="typeCorr">
						<option value="1">'.$lang['corrections_type_1'].'</option>
						<option value="2">'.$lang['corrections_type_2'].'</option>
						<option value="3">'.$lang['corrections_type_3'].'</option>
						<option value="4">'.$lang['corrections_type_4'].'</option>
					</select>
				</div>
				<div class="form-group">
					<label for="moduleCorr">'.$lang['corrections_module'].'</label>
					<select name="moduleCorr" class="form-control" id="moduleCorr">
						<option value="0">'.$lang['not_specified'].'</option>';
						$query_p = $db->query("SELECT * FROM ".DB_PREFIX."_plugins ORDER BY content ASC");
						if($db->numRows($query_p) > 0) 
						{
							while($p_list = $db->getRow($query_p)) 
							{
								echo '<option value="'.$p_list['title'].'">'.$p_list['content'].'</option>';
							}
						}		
						echo '
					</select>
				</div>
				<div class="form-group">
					<label for="urlCorr">'.$lang['corrections_url'].'</label>
					<input type="text" name="urlCorr" class="form-control" placeholder="'.$lang['corrections_url_pre'].'" id="urlCorr">
				</div>	
				<div class="form-group">
					<label for="textCorr">'.$lang['corrections_text'].'</label>
					<textarea name="textCorr" class="form-control" id="textCorr" placeholder="'.$lang['corrections_text_pre'].'" required></textarea>
				</div>
				<div class="form-group">
					<label for="markCorr">'.$lang['corrections_mark'].'</label>
					<select name="markCorr" class="form-control" id="markCorr">
						<option value="0">'.$lang['corrections_mark_0'].'</option>
						<option value="1">'.$lang['corrections_mark_1'].'</option>
						<option value="2">'.$lang['corrections_mark_2'].'</option>
						<option value="3">'.$lang['corrections_mark_3'].'</option>
						<option value="4">'.$lang['corrections_mark_4'].'</option>
					</select>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">'.$lang['add'].'</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">'.$lang['close'].'</button>
			</div>
			</form>
			</div>
		</div>
	</div>';
	$adminTpl->admin_foot();
} 