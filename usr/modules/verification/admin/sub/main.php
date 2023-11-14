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
    global $adminTpl,  $lang;   
	$lnn = 'Список заявок';
	$adminTpl->admin_head($lang['modules'] .' | Поверка приборов');
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'verification';
	$atributs['module_open'] = 'order';
	$atributs['remove']['enable'] = true;
	$atributs['remove']['title'] = 'Удаление наряд-заказа';
	$atributs['remove']['text'] = 'Вы действительно хотите удалить выбранный наряд-заказ';
	$atributs['remove']['ajax'] = 'removeVer';
	

	$atributs['restore']['enable'] = true;
	$atributs['restore']['title'] = 'Восстановление наряд-заказа';
	$atributs['restore']['text'] = 'Вы действительно хотите восстановить выбранный наряд-заказ';
	$atributs['restore']['ajax'] = 'restoreVer';

	loadGrid('jqVerification', $lnn, $atributs, ROOT.'/usr/modules/verification/admin/grid/');
	$adminTpl->admin_foot();
} 

function ajax_order_list()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	$page = init_page();
	$cut = ($page-1)*$admin_conf['num'];		
	$query = $db->query("SELECT d.*, o.name as oname  FROM ".DB_PREFIX."_ver as d LEFT JOIN ".DB_PREFIX."_organizations AS o on(d.oid=o.id) ORDER by id desc");
	if($db->numRows($query) > 0)
	{	
		while($content = $db->getRow($query)) 
		{
			$c_flag = true;
			$query_det = $db->query("SELECT id FROM ".DB_PREFIX."_ver_detail WHERE vid='".$content['id']."' AND status='1'");
			if($db->numRows($query_det) > 0)
			{
				$c_flag = false;
			}
			
			$n_flag = true;
			$query_numb = $db->query("SELECT id FROM ".DB_PREFIX."_ver_detail WHERE vid='".$content['id']."'");
			if($db->numRows($query_numb) > 0)
			{
				$n_flag = false;
			}
			
			if ($c_flag)
			{
				$status_icon = '<span class="fa fa-check-circle text-success fa-md"></span>';
			}		
			else
			{
				$status_icon = '<span class="fa fa-clock-o text-warning fa-md"></span>';
			}
			
			if ($n_flag)
			{
				$status_icon = '<span class="fa fa-clock-o text-info fa-md"></span>';
			}
			echo '
			<tr>
				<td><span class="pd-l-sm"></span>ПР-' . $content['id'] . '</td>
				<td>' . $content['oname'] . '</td>
				<td>' . formatDate($content['date'], true) . '</td>				
				<td>' . ($content['date_end'] != '0' ? formatDate($content['date_end'], true) : 'Заявка в работе') . '</td>
				<td>' . (empty($content['total']) ? 'Неизвестно' : $content['total'].' руб.') . '</td>
				<td>' . shortUserName($content['uid']) . '</td>	
				<td class="text-center">' . $status_icon . '</td>
				<td class="w150">				
					<div class="btn-group">
						<button type="button" onclick="location.href = \''.ADMIN.'/module/verification/order/' . $content['id'] . '\'" class="btn btn-xs btn-primary">'.$lang['view'].'</button>
						<button type="button" data-toggle="dropdown" class="btn btn-xs btn-primary dropdown-toggle"><span class="caret"></span><span class="sr-only">' . $lang['action'] . '</span></button>
						<ul role="menu" class="dropdown-menu">	
							<li><a target="_blank" href="#">Распечатать акт</a></li>  
															
							<li class="divider"></li>
							<li><a href="'.$core->fullURL().'#" onclick="modal_o(\'#modal-form-'.$content['id'].'\')">' . $lang['delete'] .'</a></li>
						</ul>
					</div>
					<div id="modal-form-'.$content['id'].'" class="popup-basic bg-none mfp-with-anim mfp-hide">
						<div class="panel">
						  <div class="panel-heading"><span class="panel-icon"><i class="fa fa-check-square-o"></i></span><span class="panel-title">'.$lang['confirm'].'</span></div>
						  <div class="panel-body">
							<h3 class="mt5"></h3>					
							<hr class="short alt">
							<p></p>
						  </div>
						  <div class="panel-footer text-right">
							<button type="button" onclick="location.href = \'{MOD_LINK}/delete/'.$content['id'].'\'" class="btn btn-danger">' . $lang['delete'] .'</button>
						  </div>
						</div>
					  </div>
				</td>
				<td  class="text-right">
					<div class="checkbox-custom mb15">
						<input id="checkbox' . $content['id'] . '" type="checkbox" name="checks[]" value="' . $content['id'] . '">
						<label for="checkbox' . $content['id'] . '"></label>
					</div>
				</td>
			</tr>';		
		}	
	} 	
	else 
	{
			echo '<tr><td colspan="8">Не найдено ни одной заявки!</td></tr>';	
	}	
}

function ajax_ddevice_add()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['name'])&&$_POST['name']!=""))
	{
		$name = $_POST['name'];
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
		if($db->query("INSERT INTO `" . DB_PREFIX . "_ver_device` ( `id` , `name`, `status`) VALUES (NULL, '" . $db->safesql($name) . "', '" . $status . "');")) 
		{
			if((isset($_POST['price'])&&$_POST['price']!="0"))
			{
				$vdid = $db->insert_id();
				$price = $_POST['price'];
				$db->query("INSERT INTO `" . DB_PREFIX . "_ver_device_price` ( `id` , `vdid`, `uid`, `price`, `nds`,`status`) VALUES (NULL, '" . $db->safesql($vdid) . "', '0', '" . $db->safesql(intval($price)) . "', '0', '1');");
			}		
		}
	}
}

function ajax_ddevice_edit()
{
	global $adminTpl, $core, $db, $admin_conf, $lang;
	if((isset($_POST['name'])&&$_POST['name']!="") && isset($_POST['id']))
	{
		$id = intval($_POST['id']);
		$name = $_POST['name'];
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
		$db->query("UPDATE `" . DB_PREFIX . "_ver_device` SET `status` = '".$status."', `name` = '".$name."' WHERE `id` = " . $id . " LIMIT 1 ;");
		echo $_POST['name'].' успешно изменён!';
	}
}

function ajax_order_del($id)
{
    global $adminTpl, $core, $db, $admin_conf, $lang;
    //$id = $url[5];
    $db->query("UPDATE `" . DB_PREFIX . "_ver` SET `status` = 0 WHERE `id` = " . $id . " LIMIT 1 ;");
    $db->query("UPDATE `" . DB_PREFIX . "_ver_detail` SET `status` = 0 WHERE `vid` = " . $id . "");
    header('Location: https://atri-energo.ru/administration/module/verification/');
    exit;
}


