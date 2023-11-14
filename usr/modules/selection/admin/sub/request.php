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


function content_request() 
{
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$lnn = 'Блок';
	$adminTpl->admin_head('Блок | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'selection';
	$atributs['module_open'] = 'type_request';
	load_grid('jqSelectionRequest', $lnn, $atributs, ROOT.'/usr/modules/selection/admin/grid/');
	$adminTpl->admin_foot();
} 

function type_request($id){
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$query = $db->query("SELECT id, type FROM JMY_selection_request WHERE `id` = '".intval($id)."'");
    if($db->numRows($query) > 0)
	{
        $r = $db->getRow($query);
		if($r['type'] == '1')
		{
			location(ADMIN.'/module/selection/view_request/'.$id);
		}
		elseif($r['type'] == '2')
		{
			location(ADMIN.'/module/selection/view_request_free/'.$id);
		}
		else
		{
			location(ADMIN);
		}		
    } 
	else 
	{
		location(ADMIN);
    }
	
}

function prepareDataArray($data){
	$allArray = array();
	$allArray['sfWidth'] = array('name' => 'Ширина помещения', 'unit' => 'м.');
	$allArray['sfLenght'] = array('name' => 'Длина помещения', 'unit' => 'м.');
	$allArray['sfHeight'] = array('name' => 'Высота помещения', 'unit' => 'м.');
	$allArray['sfHeightBuilding'] = 'Высота здания';
	$allArray['nKolon'] = 'Наличие выступов/колон';
	$allArray['message'] = 'Комментарий';
	$allArray['q'] = 'Нагрузка';
	$allArray['t1'] = 'T1';
	$allArray['t2'] = 'T2';
	$allArray['p1'] = 'P1';
	$allArray['p2'] = 'P2';
	$allArray['uzel_ucheta'] = 'Узел учета';
	$allArray['podpitka'] = 'Подпитка';
	$allArray['regulator_davleniya'] = 'Регулятор давления';
	$allArray['SFregDavVal'] = 'Производитель регулятора давления';
	$allArray['ogranichenie_rashoda'] = 'Ограничитель расхода';
	$allArray['SForgRasVal'] = 'Производитель ограничителя расхода';
	$allArray['SFnasosStan'] = 'Насосная станция';
	$allArray['SFnasosPov'] = 'Повысительный насос ';
	$return = '';
	//print_r($data);
	if(is_array($data))
	{
		foreach($data as $key => $val)
		{
			//echo $key;
			if(isset($allArray[$key]))				
			{		
				if(is_array($allArray[$key]))
				{
					$return .= '<b>'.$allArray[$key]['name'].': </b>'.$val.' '.(isset($allArray[$key]['unit']) ? $allArray[$key]['unit'] : '').' '.(isset($data[$key.'Type']) ? $data[$key.'Type'] : '').'<br>';
				}
				else
				{
					$return .= '<b>'.$allArray[$key].': </b>'.$val.' '.(isset($data[$key.'Type']) ? $data[$key.'Type'] : '').'<br>';
				}
			}
			if(is_array($data[$key]))
			{
				$return .= prepareDataArray($data[$key]);
			}
		}
	}
	return $return;
	
}

function view_request_free($id){
	global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	$query = $db->query("SELECT * FROM JMY_selection_request WHERE `id` = '".intval($id)."'");
	if($db->numRows($query) > 0)
	{
		$lnn = 'Информация о блоке';
        $r = $db->getRow($query);
		$code = json_decode($r['detail_free'], true);
		$adminTpl->admin_head('Блок | '.$lnn);
		echo '<div id="content" class="animated fadeIn">';
		echo '<div class="row">
		<div class="col-sm-8">';
		//общие данные
		echo '<div class="col-sm-12">
				<div class="panel panel-alert panel-border top">
					<div class="panel-heading"><span class="panel-title">Общая информация</span>
						<div class="widget-menu pull-right"></div>
					</div>
					<div class="panel-body">
						<p class="p2">'.prepareDataArray($code['data']['data']).'</p>
					</div>
				</div>
			</div>';
		//блок ввода
		echo '<div class="col-sm-12">
				<div class="panel panel-success panel-border top">
					<div class="panel-heading"><span class="panel-title">Блок ввода</span>
						<div class="widget-menu pull-right"></div>
					</div>
					<div class="panel-body">
						<p class="p2">'.prepareDataArray($code['blockVvod']['blockVvoda']).'</p>
					</div>
				</div>
			</div>';
		foreach($code['bov'] as $key => $val)
		{
			echo '<div class="col-sm-6">
				<div class="panel panel-warning panel-border top">
					<div class="panel-heading"><span class="panel-title">Блок отопления</span>
						<div class="widget-menu pull-right"></div>
					</div>
					<div class="panel-body">
						<p class="p2">'.prepareDataArray($code['bov'][$key]).'</p>';
						
						
			echo '</div>
				</div>
			</div>';
		}
		foreach($code['gvs'] as $key => $val)
		{
			echo '<div class="col-sm-6">
				<div class="panel panel-info panel-border top">
					<div class="panel-heading"><span class="panel-title">Блок ГВС</span>
						<div class="widget-menu pull-right"></div>
					</div>
					<div class="panel-body">
						<p class="p2">'.prepareDataArray($code['bov'][$key]).'</p>
					</div>
				</div>
			</div>';
		}
		


echo '</div>
<div class="col-sm-4" id="loadForm">';
if($r["report_status"] === "0"){
	echo '<form id="fomrMain" onsubmit="return false;">
	<input type="hidden" value="'.$id.'" name="request_free_id" id="request_free_id">';
	$attribute = [];
	$attribute["name"] = "1";
	$attribute["script"] = true;
	$adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
	$adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
	echo uploaderMaster("css", $attribute);
	echo uploaderMaster("upload", $attribute);
	echo '<div class="mb20"></div>';
	echo uploaderMaster("view", $attribute);

	echo '<textarea id="report" name="report" class="form-control mb20 mt20" placeholder="Введите комментарий..."></textarea>
	<input type="button" value="Выполнить" class="btn btn-success" id="send">
	</form>';
} else {
	fileCommView($id);
}

echo '</div>
</div></div>';

$adminTpl->footIncludes[] = '<script src="/usr/modules/selection/admin/assets/js/request_free.js"></script>';

		echo '<pre>';
		print_r($code);
			$adminTpl->admin_foot();
	}
}	

function fileCommView($id){
	global $db;
	$query = $db->query("SELECT * FROM JMY_selection_request WHERE `id` = '".intval($id)."'");
	$r = $db->getRow($query);
	$query = $db->query("SELECT id, name FROM ".DB_PREFIX."_attach WHERE `module` = 'request_free' AND pid = '".$id."'");
	echo '
	<div class="panel">
		<div class="panel-heading">Файл</div>
		<div class="panel-body">';
			while($f = $db->getRow($query)){
				echo '<div><a href="/download/'.$f["id"].'">'.$f["name"].'</a></div>';
			}
		echo '</div></div>
	<div class="panel">
		<div class="panel-heading">Комментарий</div>
		<div class="panel-body">'.$r["report"].'</div>
	</div>
	';
}

function getBlockData($type, $blockData) {
	$result = array();
	switch($type) {			
		case "BV":
			$result['Нагрузка'] = $blockData;
		break;		
	}
}

function view_request($id){
	$sd = new \Selection\Controller\Request;
	$sd->setRequestId($id);
	$sd->getSelectionRequest();
	$sd->getCalculationData();
	$sd->getInputData();
	$sd->getResultAdminTemplates();
	
}

function createSales($id, $type){
	\Selection\Support\SelectionRequestSupport::create($id, $type);
}