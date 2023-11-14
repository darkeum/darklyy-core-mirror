<?php

/**
* @name			JMY CORE
* @link				https://jmy.su/
* @copyright		Copyright (C) 2012-2019 JMY LTD
* @license			LICENSE.txt (see attached file)
* @version		VERSION.txt (see attached file)
* @author			Orlov Alexsey
*/ 

if (!defined('ADMIN_ACCESS')) {
		header('Location: /');
		exit;
}

global $adminTpl, $db, $core, $admin_conf, $lang;
switch (isset($url[2]) ? $url[2] : null) {
		default:
				$adminTpl->admin_head('' . $lang['tagcode'] . '');
				echo '<div id="content" class="animated fadeIn">';
				$query = $db->query("SELECT * FROM " . DB_PREFIX . "_tagcode ORDER BY id ASC");
				if ($db->numRows($query) > 0) {
						echo '<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">' . $lang['tagcode_list'] . ':</span>				
				</div>
				<div class="panel-body pn"> 
				<form id="tablesForm"	style="margin:0; padding:0" method="POST" action="{ADMIN}/tagcode/action">
					<table class="table table-striped">
						<thead>
							<tr>
								<th><span class="pd-l-sm"></span>#</th>
								<th class="col-md text-center">' . $lang['tagcode_title'] . '</th>
								<th class="col-md-6 text-center">' . $lang['tagcode_tag'] . '</th>
								<th class="col-md-4 text-center">' . $lang['tagcode_desc'] . '</th>
								<th class="col-md-1 text-center">' . $lang['status'] . '</th>
								<th style="width: 70px">&nbsp;</th>	
								<th>
									<div class="checkbox-custom mb15">
										<input id="all" type="checkbox" name="all" onclick="setCheckboxes(\'tablesForm\', true); return true;">
										<label for="all"></label>
									</div>	
								</th>
							</tr>
						</thead>
						<tbody>';
						while ($row = $db->getRow($query)) {
						  
								if ($row['active'] == 1) {
										$status_icon = '<span class="fa fa-check-circle text-success fa-md" data-toggle="tooltip" data-original-title="' . $lang['active'] . '"></span>';
								} else {
										$status_icon = '<span class="fa fa-circle text-danger fa-md" data-toggle="tooltip" data-original-title="' . $lang['not_active'] . '"></span>';
								}
						 
								$tags = str_replace(array('<br>', '<br/>', '\p', '\n', '<', '>'), array('\n', '\n', '""', '""', '&lt;', '&gt;'), '[' . $row['tag'] . '] {%' . $row['tag'] . '%} [/' . $row['tag'] . ']');
				$command = 'document.querySelector(\'#copy_' . $row['id'] . '\').select(); document.execCommand(\'copy\'); notif(\'primary\', \'' . $lang['info'] . '\', \'' . $lang['copy_success'] . '\');';
								echo '<tr>
						<td class="text-center" style="font-size: 14px">' . $row['id'] . '</td>					
						<td class="text-center" style="font-size: 14px">' . $row['title'] . '</td>
					<td class="text-center" style="font-size: 14px"><input id="copy_' . $row['id'] . '" type="text"	class="form-control input-sm text-center" value="' . $tags . '" /></td>
						<td class="text-center" style="font-size: 14px">' . $row['desc'] . '</td>
					<td class="text-center"  style="font-size: 14px">' . $status_icon . '</td>
					<td>
				<div class="btn-group">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-bars"></i><span class="caret"></span></a>
				<ul class="dropdown-menu text-left dropdown-menu-right">
					<li><a href="' . ADMIN . '/tagcode/edit/' . $row['id'] . '" target="_blank"><i class="fa fa-external-link "></i>&emsp;' . $lang['edit'] . '</a></li>
					<li ><a href="' . $core->fullURL() . '#copy" onclick="' . $command . '"><i class="fa fa-files-o" aria-hidden="true"></i>&emsp;' . $lang['tagcode_copy'] . '</a></li>
					<li><a href="' . ADMIN . '/tagcode/retivate/' . $row['id'] . '"><i class="fa fa-power-off" aria-hidden="true"></i>&emsp;' . (($row['active'] == 0) ? $lang['do_activation'] : $lang['do_deactivation']) . '</a></li>	
					<li class="divider"></li>
					<li><a href="' . $core->fullURL() . '#" onclick="modal_o(\'#modal-form-' . $row['id'] . '\')"><i class="fa fa-trash-o "></i>&emsp;' . $lang['delete'] . '</a></li>
				</ul>
				 </div>						
						<div id="modal-form-' . $row['id'] . '" class="popup-basic bg-none mfp-with-anim mfp-hide">
										<div class="panel">
											<div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">' . $lang['confirm'] . '</span></div>
											<div class="panel-body">
											<h3 class="mt5">' . str_replace('[tagcode]', '[' . $row['tag'] . '] {%' . $row['tag'] . '%} [/' . $row['tag'] . ']', $lang['tagcode_del_desc']) . '</h3>							
											<hr class="short alt">
											<p>' . str_replace('[tagcode]', '[' . $row['tag'] . '] {%' . $row['tag'] . '%} [/' . $row['tag'] . ']', $lang['tagcode_del_text']) . '</p>
											</div>
											<div class="panel-footer text-right">
											<button type="button" onclick="location.href = \'{ADMIN}/tagcode/delete/' . $row['id'] . '\'" class="btn bg-danger btn-sm btn-raised legitRipple"><i class="fa fa-trash-o position-left fa-lg"></i>&emsp;' . $lang['delete'] . '</button>
											</div>
										</div>
									</div>
				</td>
				<td>
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $row['id'] . '" type="checkbox" name="checks[]" value="' . $row['id'] . '">
						<label for="checkbox' . $row['id'] . '"></label>
					</div>
				</td>
			</tr>';
						}
						echo '</tbody>
				<tfoot class="footer-menu">
					<tr align="right">					
						<td align="right" colspan="10">
						<nav align="right" class="text-right">						
							<select style="width: 250px; display: inline-block;" class="form-control" name="act">
								<option value="Activate">' . $lang['do_activation'] . '</option>
								<option value="deActivate">' . $lang['do_deactivation'] . '</option>
								<option value="reActivate">' . $lang['do_reactivation'] . '</option>
								<option value="Delete">' . $lang['delete'] . '</option>
							</select>
							<input name="submit" type="submit" class="btn btn-success btn-raised legitRipple" style="display: inline-block;" id="sub" value="' . $lang['doit'] . '" /><span class="pd-l-sm"></span>							
						 </nav>
						</td>					 
					</tr>
					</tfoot> 				 
					</table>
				 </form>
				</div>
			</div>';
				} else {
						$adminTpl->info($lang['tagcode_empty'], 'empty', null, $lang['tagcode_list'], $lang['tagcode_add'], ADMIN . '/tagcode/add');
				}
				echo '</div>';
				$adminTpl->admin_foot();
				break;

		case 'add':
				add();
				break;

		case 'save':
				$adminTpl->admin_head('' . $lang['tagcode_add'] . '');
				$title = isset($_POST['title']) ? filter($_POST['title']) : '';
				$desc = isset($_POST['desc']) ? filter($_POST['desc']) : '';
				$find = array("<", ">");
				$uid = $core->auth->user_info['id'];
				$replace = array("&lt;", "&gt;");
				$tag = $db->safesql(str_replace($find, $replace, $_POST['translit']));
				$active = isset($_POST['active']) ? filter($_POST['active']) : '';		   
				echo '<div id="content" class="animated fadeIn">';
				if ($title && $desc) {
						$db->query("INSERT INTO `" . DB_PREFIX . "_tagcode` ( `id` , `desc`, `uid`, `title` , `active`, `tag` , `date`) VALUES (NULL,  '" . $db->safesql(processText($desc)) . "',  '" . $db->safesql($uid) . "', '" . $db->safesql(processText($title)) . "', '" . $active . "', '" . $db->safesql(processText($tag)) . "', '" . time() . "');");
						$adminTpl->info($lang['tagcode_add_ok'], 'info', null, $lang['info'], $lang['tagcode_list'], ADMIN . '/tagcode');

						header("refresh: 5; url=" . ADMIN . "/tagcode/");
				} else {
						$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
				}
				echo '</div>';
				$adminTpl->admin_foot();
				break;

		case 'edit':
				$id = intval($url[3]);
				add($id);
				break;

		case 'save_edit':
				$adminTpl->admin_head('' . $lang['tagcode_edit'] . '');
				$id = intval($_POST['id']);
				$title = isset($_POST['title']) ? filter($_POST['title']) : '';
				$desc = isset($_POST['desc']) ? filter($_POST['desc']) : '';
				$find = array("<", ">");
				$replace = array("&lt;", "&gt;");
				$tag = $db->safesql(str_replace($find, $replace, $_POST['translit']));
				$active = isset($_POST['active']) ? filter($_POST['active']) : '';		   
				echo '<div id="content" class="animated fadeIn">';
				if ($tag && $desc) {
						$db->query("UPDATE `" . DB_PREFIX . "_tagcode` SET `title` = '" . $title . "',`desc` = '" . $desc . "',`tag` = '" . $tag . "', `active` = '" . $active . "', `date` = '" . time() . "' WHERE `id` = '" . $id . "' LIMIT 1 ;");
						$adminTpl->info($lang['tagcode_edit_ok'], 'info', null, $lang['info'], $lang['tagcode_list'], ADMIN . '/tagcode');
						header("refresh: 5; url=" . ADMIN . "/tagcode/");
				} else {
						$adminTpl->info($lang['base_error_1'], 'error', null, $lang['error'], $lang['go_back'], 'javascript:history.go(-1)');
				}
				echo '</div>';
				$adminTpl->admin_foot();
				break;


		case 'delete':
				$id = intval($url[3]);
				deltagcode($id);
				location(ADMIN . '/tagcode');
				break;

		case 'retivate':
				$id = intval($url[3]);
				if (!empty($id)) {
						$rows = $db->getRow($db->query("SELECT * FROM `" . DB_PREFIX . "_tagcode` WHERE id = '" . $id . "'"));
						$active = 0;
						if ($rows['active'] == 0) {
								$active = 1;
						}
						$db->query("UPDATE `" . DB_PREFIX . "_tagcode` SET `active` = '" . $active . "' WHERE `id` = '" . $id . "' LIMIT 1 ;");
						location(ADMIN . '/tagcode');
				} else {
						location(ADMIN . '/tagcode');
				}
				break;

		   case "action":
				$type = $_POST['act'];
				if (is_array($_POST['checks'])) {
						switch ($type) {
								case "Activate":
										foreach ($_POST['checks'] as $id) {
												$db->query("UPDATE `" . DB_PREFIX . "_tagcode` SET `active` = '1' WHERE `id` = " . intval($id) . " LIMIT 1 ;");
												location(ADMIN . '/tagcode');
										}
										break;

								case "deActivate":
										foreach ($_POST['checks'] as $id) {
												$db->query("UPDATE `" . DB_PREFIX . "_tagcode` SET `active` = '0' WHERE `id` = " . intval($id) . " LIMIT 1 ;");
												location(ADMIN . '/tagcode');
										}
										break;

								case "reActivate":
										foreach ($_POST['checks'] as $id) {
												$db->query("UPDATE `" . DB_PREFIX . "_tagcode` SET `active` = NOT `active` WHERE `id` = " . intval($id) . " LIMIT 1 ;");
												location(ADMIN . '/tagcode');
										}
										break;		  
																										  
								case "Delete":
										foreach ($_POST['checks'] as $id) {
												deltagcode(intval($id));
										}
										location(ADMIN . '/tagcode');
										break;						 
						}
				}
}

function add($id = null)
{
		global $adminTpl, $admin_conf, $config, $core, $lang, $db;
		$rows = $db->getRow($db->query("SELECT * FROM `" . DB_PREFIX . "_tagcode` WHERE id = '" . $id . "'"));
		if (isset($id)) {
				$tags = '<div class="form-group">
							<label class="col-sm-3 control-label">' . $lang['tagcode_tag'] . '</label>
							<div class="col-sm-4">
								<label for="vars" class="field prepend-icon">
								<pre><code>[' . $rows['tag'] . '] {%' . $rows['tag'] . '%} [/' . $rows['tag'] . ']</code></pre>	
								</label>
								</div>
						</div>';
				$admhead = '' . $lang['tagcode_edit'] . '';
				$button = '<button name="submit" type="submit" id="sub" class="btn btn-primary btn-sm btn-raised legitRipple"><i class="fa fa-refresh fa-lg" aria-hidden="true"></i>&emsp;' . $lang['update'] . '</button>';
				$action = ADMIN . '/tagcode/save_edit';
		} else {
				$admhead = '' . $lang['tagcode_add'] . '';
				$tags = '';
				$button = '<button name="submit" type="submit" id="sub" class="btn bg-success btn-sm btn-raised legitRipple"><i class="fa fa-floppy-o fa-lg" aria-hidden="true"></i>&emsp;' . $lang['add'] . '</button>';
				$action = ADMIN . '/tagcode/save';
		}
		$adminTpl->admin_head('' . $admhead . '');
		echo '<div id="content" class="animated fadeIn">
				<div class="panel panel-dark panel-border top">
					<div class="panel-heading"><span class="panel-title">' . $admhead . '</span>					
				</div>
				<div class="panel-body admin-form">
					<form class="form-horizontal parsley-form" role="form" action="' . $action . '" method="post" data-parsley-validate>
						<div class="form-group">
							<label class="col-sm-3 control-label">' . $lang['tagcode_title'] . '</label>
							<div class="col-lg-4">
								<label for="title" class="field prepend-icon">
									<input id="title" type="text" name="title" placeholder="' . $lang['tagcode_title_pre'] . '" class="event-name form-control" ' . (isset($id) ? '' : 'onchange="getTranslit(gid(\'title\').value, \'translit\'),getTagcode(gid(\'title\').value, \'related_tag\');"')  . '  value="' . $rows['title'] . '">				
									<input id="translit" type="hidden" name="translit"  class="event-name form-control"  ' . (isset($id) ? '' : 'onchange="getTagcode(gid(\'title\').value, \'related_tag\');"')  . '   value="' . $rows['tag']  . '">
									<label for="title" class="field-icon"><i class="fa1 fa-pencil"></i></label>
								</label><span class="help-block mt5"><i class="fa fa-bell"></i> ' . $lang['tagcode_title_alert'] . '</span>
									<div id="related_tag"></div>
							</div>
						</div>
							' . $tags . '
						<div class="form-group">
							<label for="desc"	class="col-lg-3 control-label">' . $lang['tagcode_desc_field'] . '</label>
							<div class="col-lg-4">
								<label for="desc" class="field prepend-icon">
									<textarea name="desc" id="desc" placeholder="' . $lang['tagcode_desc_pre'] . '" class="form-control" style="width: 440px; min-width: 240px; max-width: 440px; height: 124px; margin: 0 auto" data-parsley-required="true" data-parsley-trigger="change">' . $rows['desc'] . '</textarea>
									<label for="desc" class="field-icon"><i class="fa1 fa-pencil"></i></label>
								</label>						
							</div>
						</div>					
				<div class="form-group">
					<div class="col-xs-6">
					<label class="field option">			
					<input type="checkbox" name="active" ' . ($rows['active'] ? 'checked' : '') . ' value="1" checked><span class="checkbox mr10"></span>' . $lang['tagcode_on'] . '</label><br>
					<hr class="alt short mv15">
					</div> 
				</div>
		</div>
	<div class="panel-footer">
			' . $button . '
			</div>';
		if (isset($id)) {
				echo '<input type="hidden" name="edit" value="1">';
				echo '<input type="hidden" name="id" value="' . $id . '">';
		}
		echo '</form>
				</div></div>';
		$adminTpl->admin_foot();
}

function deltagcode($id)
{
		global $adminTpl, $db, $core, $lang;
		$query = $db->query("SELECT * FROM `" . USER_DB . "`.`" . USER_PREFIX . "_tagcode` WHERE id='" . $id . "'");
		if ($db->numRows($query) == 1) {
				$tagcode = $db->getRow($query);
		}
		$db->query("DELETE FROM `" . DB_PREFIX . "_tagcode` WHERE `id` = '" . $id . "'");
		header("refresh: 5; url=" . ADMIN . "/tagcode/");
}


