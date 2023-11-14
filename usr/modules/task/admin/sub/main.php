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


function main($type = 'me') 
{
    global $adminTpl, $config, $core, $admin_conf, $db, $lang, $taskAttr;

    $lnn = '';

    switch($type){
        default:
            header('Location: /'.ADMIN.'/');
        break;
        case "me":
            $lnn = $lang['task_me'];
            $taskAttr['query'] = "SELECT {fields} FROM ".DB_PREFIX."_task as t LEFT JOIN JMY_object as obj on(obj.id=t.object) WHERE t.archive = 0 AND t.toid = '".$core->auth->user_info['id']."' AND  {where} ";
            $taskAttr['uid'] = "r.toid=".$core->auth->user_info['id'];
        break;
        case "my":
            $lnn = $lang['task_my'];
            $taskAttr['query'] = "SELECT {fields} FROM ".DB_PREFIX."_task as t LEFT JOIN JMY_object as obj on(obj.id=t.object) WHERE t.archive = 0 AND t.fromid = '".$core->auth->user_info['id']."' AND {where} ";
            $taskAttr['uid'] = "r.fromid=".$core->auth->user_info['id'];
        break;
        case "coperformer":
            $lnn = "Соисполнитель";
            $taskAttr['query'] = "SELECT {fields} FROM ".DB_PREFIX."_task as t LEFT JOIN JMY_object as obj on(obj.id=t.object) WHERE t.archive = 0 AND t.coperformerid LIKE '%\"".$core->auth->user_info['id']."\"%'  AND  {where} ";
            $taskAttr['uid'] = "r.coperformerid LIKE '%\"".$core->auth->user_info['id']."\"%'";
        break;
        case "observer":
            $lnn = "Наблюдатель";
            $taskAttr['query'] = "SELECT {fields} FROM ".DB_PREFIX."_task as t LEFT JOIN JMY_object as obj on(obj.id=t.object) WHERE t.archive = 0 AND t.observerid LIKE '%\"".$core->auth->user_info['id']."\"%'  AND  {where} ";
            $taskAttr['uid'] = "r.observerid LIKE '%\"".$core->auth->user_info['id']."\"%'";
        break;
        case "archive":
            $lnn = $lang['task_archive'];
            $taskAttr['query'] = "SELECT {fields} FROM ".DB_PREFIX."_task as t LEFT JOIN JMY_object as obj on(obj.id=t.object) WHERE t.archive = 1 AND (toid = '".$core->auth->user_info['id']."' OR t.fromid = '".$core->auth->user_info['id']."') AND {where} ";
            $taskAttr['uid'] = "r.toid=".$core->auth->user_info['id']." OR r.fromid =".$core->auth->user_info['id']."";
        break;
        case "admin":
            $lnn = "0_o";
            $taskAttr['query'] = "SELECT {fields} FROM ".DB_PREFIX."_task as t LEFT JOIN JMY_object as obj on(obj.id=t.object) WHERE {where} ";
            $taskAttr['uid'] = "1";
        break;
    }
    $adminTpl->admin_head($lang['modules'] .' | Задачи');
    $atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'task';
    $atributs['module_open'] = 'view';

    require_once(ROOT."/usr/modules/task/admin/rules/rules.php");
    $hideAdmin = hideAdminFunc();
    if(!$hideAdmin){
        $atributs['button'] = 'Все задачи';
        $atributs['button_url'] = '/administration/module/task/admin';	
    }
    load_grid('jqTask', $lnn, $atributs, ROOT.'/usr/modules/task/admin/grid/');

    echo '<div class="modal fade" id="archiveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">В архив</h5>
        </div>
        <div class="modal-body">
          Вы действительно хотите отправить задачу в архив?
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="addArchiveBtn" data-dismiss="modal">В архив</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>';
    
    $adminTpl->footIncludes[] = '<script src="/usr/modules/task/admin/asset/js/main.js"></script>';
    // $adminTpl->footIncludes[] = '<script src="/usr/other/sub/load_window/asset/js/load_window.js"></script>';


	$adminTpl->admin_foot();
} 



