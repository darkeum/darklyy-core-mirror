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

function relations($id = null, $task = false)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	if(isset($id))
	{
		$query = $db->query("SELECT * FROM ".DB_PREFIX."_sales WHERE id = '" . $id . "'");
		$ccid = $cat;
		$cat = $db->getRow($query);
		$name = prepareTitle($cat['name']);
		$id = $cat['id'];
		$address = $cat['address'];
		
		$comment = $cat['comment'];
		$preview = $cat['preview'];
		$id_zas = $cat['id_zas'];
		$id_pod = $cat['id_pod'];
		$id_gpr = $cat['id_gpr'];
		$id_spr = $cat['id_spr'];
		$id_mso = $cat['id_mso'];
		$id_sys = $cat['id_sys'];
		$id_itp = $cat['id_itp'];
		$id_mts = $cat['id_mts'];
		$type = explode(",", $cat['cat']);
		$status = $cat['status'];		
		$dosave = $lang['object_edit_do'];
		
		$lln = $lang['object_edit'];
	}
	$adminTpl->subNavIncludes[] = '<div class="mt-30 hidden-xs"><a href="/'.ADMIN.'/module/sales/order/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="fa fa-plus pr5"></span> Информация о продаже</a><a href="/'.ADMIN.'/module/sales/task/'.$id.'" class="btn btn-default '.($task  ? 'active' : '').' btn-sm fw600 ml10"><span class="glyphicon glyphicon-ok-sign pr5"></span> Задачи по продаже</a><a href="/'.ADMIN.'/module/sales/relations/'.$id.'" class="btn btn-default '.($task  ? '' : 'active').' btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> Все связи по продаже</a></div>';
	$adminTpl->admin_head($lang['object_map']);
	echo '<section id="content" class="table-layout animated fadeIn">
          <!-- begin: .tray-center-->
          <div class="tray tray-center">
            <!-- Begin: Content Header-->
            <div class="content-header">
              <h2>'.($task ? 'Задачи по продаже' : 'Связи по продаже').'</h2>
              <p class="lead">'. $name.' (<a href="/'.ADMIN.'/module/sales/relations_add/'.$id.'">добавить связь</a>)</p>
            </div>
            <!-- Validation Example-->
            <div style="padding-bottom: 175px;" class="admin-form theme-primary mw1000 center-block">
              <div class="panel heading-border panel-primary">              
                  <div class="panel-body bg-light">
                   
                    <!-- .section-divider-->
                    <div id="spy1" class="section row">';		
					$i = 0;
					$query = $db->query("SELECT * FROM ".DB_PREFIX."_relations WHERE (module_out = 'sales' ".($task ? 'AND module_in = \'task\'' : '')." AND pid_out = '".$id."') ORDER BY id DESC");	
					if($db->numRows($query) > 0)
					{
						while($object = $db->getRow($query))
						{
							$i++;
									
							switch($object['module_in']) 
							{
								case "task":
								$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_".$object['module_in']." WHERE id = '" . $object['pid_in'] . "'");
								$task = $db->getRow($queryR);
								echo '<a href="'.ADMIN.'/'.$object['module_in'].'/view/'.$object['pid_in'].'">Задача №'.$task['id'].' от '.shortUserName($task['fromid']).' для '.shortUserName($task['toid']).' ('.formatDate($task['date'], true).')</a><br>';
								break;
								case "object":
								$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $object['pid_in'] . "'");
								$task = $db->getRow($queryR);
								echo '<a href="'.ADMIN.'/'.$object['module_in'].'/view/'.$object['pid_in'].'">Объект '.$task['name'].' ('.$task['address'].')</a><br>';
								break;
		
							}
						}
					}
					$query = $db->query("SELECT * FROM ".DB_PREFIX."_relations WHERE (module_in = 'sales' ".($task ? 'AND module_out = \'task\'' : '')." AND pid_in = '".$id."') ORDER BY id DESC");	
					if($db->numRows($query) > 0)
					{
						while($object = $db->getRow($query))
						{	
							$i++;
							switch($object['module_out']) 
							{
								case "task":
								$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_".$object['module_out']." WHERE id = '" . $object['pid_out'] . "'");
								$task = $db->getRow($queryR);
								echo '<a href="'.ADMIN.'/'.$object['module_in'].'/view/'.$object['pid_out'].'">Задача №'.$task['id'].' от '.shortUserName($task['fromid']).' для '.shortUserName($task['toid']).' ('.formatDate($task['date'], true).')</a><br>';
								break;
								case "object":
								$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_object WHERE id = '" . $object['pid_out'] . "'");
								$task = $db->getRow($queryR);
								echo '<a href="'.ADMIN.'/'.$object['module_out'].'/view/'.$object['pid_out'].'">Объект '.$task['name'].' ('.$task['address'].')</a><br>';
								break;
								case "bank":
								$queryR = $db->query("SELECT * FROM ".DB_PREFIX."_bank_detail WHERE id = '" . $object['pid_out'] . "'");
								$task = $db->getRow($queryR);
								echo '<a href="'.ADMIN.'/'.$object['module_out'].'/view/'.$object['pid_out'].'">Оплата № '.$task['numb'].' от ('.$task['date'].')</a><br>';
								break;
		
		
							}
						}
					}
					if($i == 0)
					{
						if($task)
						{
							echo 'К сожалению задач по данной продаже не найдено';
						}
						else
						{
							echo 'К сожалению связей по данной продаже не найдено';
						}
					}					
					echo' </div>
					</div>
				</div>
            </div>
          </div>
		   </section>
';
	$adminTpl->admin_foot();
}

function relations_add($id = null)
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	
	$adminTpl->subNavIncludes[] = '<div class="mt-30 hidden-xs"><a href="/'.ADMIN.'/module/sales/order/'.$id.'" class="btn btn-default btn-sm fw600 ml10"><span class="fa fa-plus pr5"></span> Информация о продаже</a><a href="/'.ADMIN.'/module/sales/task/'.$id.'" class="btn btn-default '.($task  ? 'active' : '').' btn-sm fw600 ml10"><span class="glyphicon glyphicon-ok-sign pr5"></span> Задачи по продаже</a><a href="/'.ADMIN.'/module/sales/relations/'.$id.'" class="btn btn-default '.($task  ? '' : 'active').' btn-sm fw600 ml10"><span class="glyphicon glyphicon-link pr5"></span> Все связи по продаже</a></div>';
	$adminTpl->admin_head($lang['object_map']);
	echo '<section id="content" class="table-layout animated fadeIn">
          <!-- begin: .tray-center-->
          <div class="tray tray-center">
            <!-- Begin: Content Header-->
            <div class="content-header">
              <h2>Добавить связь</h2>
              <p class="lead">Привязать продажу к другому элементу</p>
            </div>
            <!-- Validation Example-->
            <div style="padding-bottom: 175px;" class="admin-form theme-primary mw1000 center-block">
              <div class="panel heading-border panel-primary">              
                  <div class="panel-body bg-light">
                  		
				
                    <!-- .section-divider-->
                    <div id="spy1" class="section row">
					 	<form id="admin-form" action="{ADMIN}/module/sales/relations_save/'.$id.'" method="post" name="cats" role="form" class="form-horizontal parsley-form">		';					
					echo relationsForm();
					echo' 
					<div class="form-group">
						<label class="col-sm-3 control-label"></label>
						<div class="col-sm-4">
							<input type="submit" class="btn btn-primary btn-parsley" id="sub" value="Создать связь">
						</div>
					</div>
					</form></div>
					</div>
				</div>
            </div>
          </div>
		   </section>
';
	$adminTpl->admin_foot();
}

function relations_save($id = null)
{
	
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	if (!empty($id))
	{
		$adminTpl->admin_head($lang['cats'].' | '. $lang['cats_save']);
		relationsSave('sales', $id);
		echo '<div id="content" class="animated fadeIn">';
		$adminTpl->info('Объекты успешно связаны', 'info', null, $lang['info'], 'Вернуться', ADMIN.'/module/sales/relations/'.$id);				
		echo '</div>';
		$adminTpl->admin_foot();
	}
}