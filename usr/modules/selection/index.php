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

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$dir = ROOT.'/usr/modules/selection/sub/';  
includeSub($dir);

function selectConstract($array, $value_is = false, $empty = false)
{
    $return = '';
    if ($empty) {
        $return .= '<option selected="selected"></option>';
    }
    foreach ($array as $key => $value) {
        $return .= '<option value="' . $key . '" ' . ($key == $value_is ? 'selected="selected"' : '') . '>' . $value . '</option>';
    }
    return ($return);
}



function main()
{
    global $core, $db;
    /*$page_title = 'Расчет блочного теплового пункта';
    $core->tpl->loadFile('select/select.main');
    $items = $core->tpl->return_end();
    $core->tpl->loadFile('select/select.carcase');
    include('carcase_tag.php');
    $core->tpl->end();*/
    $core->tpl->loadFile('select/select.tool');
    $core->tpl->end();
}

function my()
{
    global $core, $db;
    $bread = '<li><a href="/select">Расчет блочного теплового пункта</a></li>';
    $page_title = 'Мои расчёты';
    $query = $db->query("SELECT * FROM `" . DB_PREFIX . "_selection_request` WHERE uid = '" . $core->auth->user_info['id'] . "' ORDER BY id DESC");
    $list = '';
	 $add_detail ='';
	$info = '';
    if ($db->numRows($query) > 0) {
        while ($select = $db->getRow($query)) {
            if ($select['oid'] != '0') {
                $queryO = $db->query("SELECT name, inn FROM " . DB_PREFIX . "_organizations WHERE id = '" . $select['oid'] . "' LIMIT 1");
                $resultO = $db->getRow($queryO);
                $org = $resultO['name'] . '<br><strong>ИНН:</strong> ' . $resultO['inn'];
            } else {
                $org = 'К сожалению организация не указана!';
            }
            if ($select['object'] != '0') {
                $queryOB = $db->query("SELECT name, address FROM " . DB_PREFIX . "_object WHERE id = '" . $select['object'] . "' LIMIT 1");
                $resultOB = $db->getRow($queryOB);
                $object = '<strong>Имя объекта:</strong> ' . $resultOB['name'] . '<br><strong>Адрес:</strong> ' . $resultOB['address'];
            } else {
                $object = 'К сожалению информация об объекте не указана!';
            }
            if ($select['status'] == '0') {
                $status = '<span class="label label-warning">Ожидает заполнения</span>';
                $add_detail = '<a href="/selection/blockConstruct/' . $select['id'] . '">Продолжить</a><br>';
            } elseif ($select['status'] == '1') {
                $status = '<span class="label label-success">Расчет выполнен</span>';
            } elseif ($select['status'] == '2') {
                $status = '<span class="label label-success">Выполнен</span>';
            }
            $list .= '<tr>
						<td>#БС-' . str_pad($select['id'], 5, "0", STR_PAD_LEFT) . '</td>
                        <td width=20%>' . $object . '</td>
						<td width=20%>' . $org . '</td>
						<td width=15%>' . $select['date'] . '</td>
						<td>' . $status . '</td>
						<td>
							' . $add_detail . '
							<a href="/selection/result/' . $select['id'] . '">Результат запроса</a>
						</td>
                      </tr>';
        }
    }
    $core->tpl->loadFile('selection/selection.my');
    $core->tpl->setVar('LIST', $list);
    $items = $core->tpl->return_end();
    $core->tpl->loadFile('selection/selection.carcase');
    include('carcase_tag.php');
    $core->tpl->end();
}

function add()
{
    global $core, $db, $url;
    $oid = 0;
    if (!empty($core->auth->user_info['oid'])) {
        $oid = $core->auth->user_info['oid'];
    }
	$org_list = '';
	$object_list = '';
	$info = '';
    $query = $db->query("SELECT oid FROM " . DB_PREFIX . "_users_org WHERE uid = '" . $core->auth->user_info['id'] . "' ORDER BY oid");
    if ($db->numRows($query) > 0) {
        while ($oids = $db->getRow($query)) {
            $oid_arr[] = $oids['oid'];
        }
        $where = " WHERE id IN ('" . implode("', '", $oid_arr) . "') ";
        $queryO = $db->query("SELECT id, name FROM " . DB_PREFIX . "_organizations " . $where . " ORDER BY name");
        if ($db->numRows($queryO) > 0) {
            while ($org = $db->getRow($queryO)) {
                $org_list .= '<option value="' . $org['id'] . '">' . $org['name'] . '</option>';
            }
        }
    } else {
        $org_list = '<option value="0">Организации не найдены</option>';
    }
    $query = $db->query("SELECT object FROM " . DB_PREFIX . "_users_object WHERE uid = '" . $core->auth->user_info['id'] . "' ORDER BY object");
    if ($db->numRows($query) > 0) {
        while ($objects = $db->getRow($query)) {
            $object_arr[] = $objects['object'];
        }
        $where = " WHERE id IN ('" . implode("', '", $object_arr) . "') ";
        $queryO = $db->query("SELECT id, name FROM " . DB_PREFIX . "_object " . $where . " ORDER BY name");
        if ($db->numRows($queryO) > 0) {
            while ($object = $db->getRow($queryO)) {
                $object_list .= '<option value="' . $object['id'] . '">' . $object['name'] . '</option>';
            }
        }
    } else {
        $object_list = '<option value="0">Объекты не найдены, добавьте новый!</option>';
    }
 
    $core->tpl->loadFile('selection/selection.add');
    $core->tpl->setVar('ORG_LIST', $org_list);
    $core->tpl->setVar('OBJECT_LIST', $object_list);
    $core->tpl->setVar('CAPTCHA', captcha_image());
    $items = $core->tpl->return_end();
    $bread = '<li><a href="/select">Расчет блочного теплового пункта</a></li>';
    $page_title = 'Новый расчёт';
    $core->tpl->loadFile('selection/selection.carcase');
    include('carcase_tag.php');
    $core->tpl->end();
    
}

function addUniversal(){
    global $core, $db, $url;
    $oid = 0;
    if (!empty($core->auth->user_info['oid'])) {
        $oid = $core->auth->user_info['oid'];
    }
    $query = $db->query("SELECT oid FROM " . DB_PREFIX . "_users_org WHERE uid = '" . $core->auth->user_info['id'] . "' ORDER BY oid");
    if ($db->numRows($query) > 0) {
        while ($oids = $db->getRow($query)) {
            $oid_arr[] = $oids['oid'];
        }
        $where = " WHERE id IN ('" . implode("', '", $oid_arr) . "') ";
        $queryO = $db->query("SELECT id, name FROM " . DB_PREFIX . "_organizations " . $where . " ORDER BY name");
        if ($db->numRows($queryO) > 0) {
            while ($org = $db->getRow($queryO)) {
                $org_list .= '<option value="' . $org['id'] . '">' . $org['name'] . '</option>';
            }
        }
    } else {
        $org_list = '<option value="0">Организации не найдены</option>';
    }
    $query = $db->query("SELECT object FROM " . DB_PREFIX . "_users_object WHERE uid = '" . $core->auth->user_info['id'] . "' ORDER BY object");
    if ($db->numRows($query) > 0) {
        while ($objects = $db->getRow($query)) {
            $object_arr[] = $objects['object'];
        }
        $where = " WHERE id IN ('" . implode("', '", $object_arr) . "') ";
        $queryO = $db->query("SELECT id, name FROM " . DB_PREFIX . "_object " . $where . " ORDER BY name");
        if ($db->numRows($queryO) > 0) {
            while ($object = $db->getRow($queryO)) {
                $object_list .= '<option value="' . $object['id'] . '">' . $object['name'] . '</option>';
            }
        }
    } else {
        $object_list = '<option value="0">Объекты не найдены, добавьте новый!</option>';
    }
    
    $core->tpl->loadFile('selection/selection.addUniversal');
    switch ($url[2]) {
        case 'thermal':
            $core->tpl->setVar('FULL_TITLE', 'Новый расчёт блочного теплового пункта');
            $core->tpl->setVar('TRANSITION', 'selection/add_detail');
            $core->tpl->setVar('ORG_LIST', $org_list);
            $core->tpl->setVar('OBJECT_LIST', $object_list);
            $core->tpl->setVar('CAPTCHA', captcha_image());
            break;
        case 'cupboard':
            $core->tpl->setVar('FULL_TITLE', 'Новый расчёт шкафа автоматики');
            $core->tpl->setVar('TRANSITION', 'selection/cupboard');
            $core->tpl->setVar('ORG_LIST', $org_list);
            $core->tpl->setVar('OBJECT_LIST', $object_list);
            $core->tpl->setVar('CAPTCHA', captcha_image());
            break;
    }
    $core->tpl->end();
}

function add_detail($id = null)
{
    $long = '';
    $width = '';
    $hieght = '';
    $vistup = '';
    $house_height = '';
    $n_1 = '';
    $n_2 = '';
    $n_3 = '';
    $n_gvs = '';
    $t_11 = '';
    $t_12 = '';
    $t_13 = '';
    $t_2 = '';
    $t_21 = '';
    $t_22 = '';
    $t_23 = '';
    $t_3 = '';
    $b_1 = '';
    $p_1 = '';
    $p_2 = '';

    $array_block = array("Отопление" => "Отопление", "Вентиляция" => "Вентиляция", "Технология" => "Технология");
    $array_yes = array("Есть" => "Есть", "Нет" => "Нет");
    $array_rel = array("Зависимая" => "Зависимая", "Независимая" => "Независимая");
    $array_pump = array("TOP-SD" => "TOP-SD", "Yonos MAXO-D" => "Yonos MAXO-D", "Stratos-D" => "Stratos-D");
    $array_klap = array("IMI Hydronic" => "IMI Hydronic", "Danfoss" => "Danfoss", "Siemens" => "Siemens");
    $array_ctrl = array("Segnetics" => "Segnetics", "Danfoss" => "Danfoss", "Siemens" => "Siemens");
    $array_auto = array("Отдельный" => "Отдельный", "Учтен в БОВ-1" => "Учтен в БОВ-1");

    $t_1 = selectConstract(array("150" => "150", "130" => "130", "115" => "115", "105" => "105", "95" => "95"), '', true);
    $block_1 = selectConstract($array_block, '', true);
    $block_2 = selectConstract($array_block, '', true);
    $block_3 = selectConstract($array_block, '', true);
    $block_bv_uzel = selectConstract($array_yes, '', true);
    $block_bv_podpit = selectConstract(array("Есть" => "Есть", "Нет" => "Нет", "Автоматическая" => "Автоматическая"), '', true);
    $block_bv_reg = selectConstract($array_yes, '', true);
    $block_bv_ogr = selectConstract($array_yes, '', true);
    $block_bv_reg_d = selectConstract(array("Danfoss" => "Danfoss", "Вогез" => "Вогез"), '', true);
    $block_bv_ogr_d = selectConstract(array("IMI Hydronic" => "IMI Hydronic", "Danfoss" => "Danfoss", "ADL" => "ADL"), '', true);

    $bread = '<li><a href="/select">Расчет блочного теплового пункта</a></li>';
    $page_title = 'Расчет блочного теплового пункта';
    global $core, $db;
    $core->tpl->loadFile('select/select.form');
    $core->tpl->setVar('ID', $id);
    $core->tpl->setVar('LONG', $long);
    $core->tpl->setVar('WIDTH', $width);
    $core->tpl->setVar('HEIGHT', $hieght);
    $core->tpl->setVar('VISTUP', $vistup);
    $core->tpl->setVar('HOUSE_HEIGHT', $house_height);
    $core->tpl->setVar('N_1', $n_1);
    $core->tpl->setVar('N_2', $n_2);
    $core->tpl->setVar('N_3', $n_3);
    $core->tpl->setVar('T_1', $t_1);

    $core->tpl->setVar('T_11', $t_11);
    $core->tpl->setVar('T_12', $t_12);
    $core->tpl->setVar('T_13', $t_13);
    $core->tpl->setVar('T_2', $t_2);
    $core->tpl->setVar('T_21', $t_21);
    $core->tpl->setVar('T_22', $t_22);
    $core->tpl->setVar('T_23', $t_23);
    $core->tpl->setVar('T_3', $t_3);
    $core->tpl->setVar('B_1', $b_1);
    $core->tpl->setVar('P_1', $p_1);
    $core->tpl->setVar('P_2', $p_2);
    $core->tpl->setVar('N_GVS', $n_gvs);
    $core->tpl->setVar('BLOCK_1', $block_1);
    $core->tpl->setVar('BLOCK_2', $block_2);
    $core->tpl->setVar('BLOCK_3', $block_3);
    $core->tpl->setVar('BLOCK_BV_UZEL', $block_bv_uzel);
    $core->tpl->setVar('BLOCK_BV_PODPIT', $block_bv_podpit);
    $core->tpl->setVar('BLOCK_BV_REG', $block_bv_reg);
    $core->tpl->setVar('BLOCK_BV_OGR', $block_bv_ogr);
    $core->tpl->setVar('BLOCK_BV_REG_D', $block_bv_reg_d);
    $core->tpl->setVar('BLOCK_BV_OGR_D', $block_bv_ogr_d);

    $core->tpl->setVar('BLOCK_BOV_1_SCHEMA', selectConstract($array_rel, '', true));
    $core->tpl->setVar('BLOCK_BOV_1_PUMP', selectConstract($array_pump, '', true));
    $core->tpl->setVar('BLOCK_BOV_1_KLAPAN', selectConstract($array_klap, '', true));
    $core->tpl->setVar('BLOCK_BOV_1_CONTROL', selectConstract($array_ctrl, '', true));
    $core->tpl->setVar('BLOCK_BOV_1_AUTO', selectConstract(array("Отдельный" => "Отдельный"), '', true));

    $core->tpl->setVar('BLOCK_BOV_2_SCHEMA', selectConstract($array_rel, '', true));
    $core->tpl->setVar('BLOCK_BOV_2_PUMP', selectConstract($array_pump, '', true));
    $core->tpl->setVar('BLOCK_BOV_2_KLAPAN', selectConstract($array_klap, '', true));
    $core->tpl->setVar('BLOCK_BOV_2_CONTROL', selectConstract($array_ctrl, '', true));
    $core->tpl->setVar('BLOCK_BOV_2_AUTO', selectConstract($array_auto, '', true));

    $core->tpl->setVar('BLOCK_BOV_3_SCHEMA', selectConstract($array_rel, '', true));
    $core->tpl->setVar('BLOCK_BOV_3_PUMP', selectConstract($array_pump, '', true));
    $core->tpl->setVar('BLOCK_BOV_3_KLAPAN', selectConstract($array_klap, '', true));
    $core->tpl->setVar('BLOCK_BOV_3_CONTROL', selectConstract($array_ctrl, '', true));
    $core->tpl->setVar('BLOCK_BOV_3_AUTO', selectConstract($array_auto, '', true));

    $core->tpl->setVar('BLOCK_BGV_SCHEMA', selectConstract(array("Автоматически" => "Автоматически", "Принудит. Смеш" => "Принудит. Смеш", "Принуд. Паралл." => "Принуд. Паралл."), '', true));
    $core->tpl->setVar('BLOCK_BGV_PUMP', selectConstract(array("Yonos MAXO-Z" => "Yonos MAXO-Z", "Stratos-Z" => "Stratos-Z"), '', true));
    $core->tpl->setVar('BLOCK_BGV_KLAPAN', selectConstract(array("IMI Hydronic" => "IMI Hydronic", "Danfoss" => "Danfoss", "Siemens" => "Siemens", "Прямого действия" => "Прямого действия"), '', true));
    $core->tpl->setVar('BLOCK_BGV_CONTROL', selectConstract(array("Segnetics" => "Segnetics", "Danfoss" => "Danfoss", "Siemens" => "Siemens"), '', true));
    $core->tpl->setVar('BLOCK_BOV_3_AUTO', selectConstract($array_auto, '', true));


    $items .= $core->tpl->return_end();
    $core->tpl->loadFile('select/select.carcase');
    include('carcase_tag.php');
    $core->tpl->end();


}


function view($id = null)
{
    global $core, $db;
    $query = $db->query("SELECT * FROM " . DB_PREFIX . "_select WHERE id = '" . intval($id) . "' LIMIT 1");
    $result = $db->getRow($query);

    $json = json_decode($result['detail']);
    $long = $json->long;
    $width = $json->width;
    $hieght = '';
    $vistup = '';
    $house_height = $json->house_height;
    $n_1 = $json->n_1;
    $n_2 = $json->n_2;
    $n_3 = $json->n_3;
    $n_gvs = $json->n_gvs;
    $t_11 = $json->t_11;
    $t_12 = $json->t_12;
    $t_13 = $json->t_13;
    $t_2 = $json->t_2;
    $t_21 = $json->t_21;
    $t_22 = $json->t_22;
    $t_23 = $json->t_23;
    $t_3 = $json->t_3;
    $b_1 = $json->b_1;
    $p_1 = $json->p_1;
    $p_2 = $json->p_2;

    $array_block = array("Отопление" => "Отопление", "Вентиляция" => "Вентиляция", "Технология" => "Технология");
    $array_yes = array("Есть" => "Есть", "Нет" => "Нет");
    $array_rel = array("Зависимая" => "Зависимая", "Независимая" => "Независимая");
    $array_pump = array("TOP-SD" => "TOP-SD", "Yonos MAXO-D" => "Yonos MAXO-D", "Stratos-D" => "Stratos-D");
    $array_klap = array("IMI Hydronic" => "IMI Hydronic", "Danfoss" => "Danfoss", "Siemens" => "Siemens");
    $array_ctrl = array("Segnetics" => "Segnetics", "Danfoss" => "Danfoss", "Siemens" => "Siemens");
    $array_auto = array("Отдельный" => "Отдельный", "Учтен в БОВ-1" => "Учтен в БОВ-1");

    $t_1 = selectConstract(array("150" => "150", "130" => "130", "115" => "115", "105" => "105", "95" => "95"), $json->t_1, true);
    $block_1 = selectConstract($array_block, $json->block_1, true);
    $block_2 = selectConstract($array_block, $json->block_2, true);
    $block_3 = selectConstract($array_block, $json->block_3, true);
    $block_bv_uzel = selectConstract($array_yes, '', true);
    $block_bv_podpit = selectConstract(array("Есть" => "Есть", "Нет" => "Нет", "Автоматическая" => "Автоматическая"), '', true);
    $block_bv_reg = selectConstract($array_yes, '', true);
    $block_bv_ogr = selectConstract($array_yes, '', true);
    $block_bv_reg_d = selectConstract(array("Danfoss" => "Danfoss", "Вогез" => "Вогез"), '', true);
    $block_bv_ogr_d = selectConstract(array("IMI Hydronic" => "IMI Hydronic", "Danfoss" => "Danfoss", "ADL" => "ADL"), '', true);

    $bread = '<li><a href="/select">Расчет блочного теплового пункта</a></li>';
    $page_title = 'Расчет блочного теплового пункта';

    $core->tpl->loadFile('select/select.view');
    $core->tpl->setVar('ID', $id);
    $core->tpl->setVar('LONG', $long);
    $core->tpl->setVar('WIDTH', $width);
    $core->tpl->setVar('HEIGHT', $hieght);
    $core->tpl->setVar('VISTUP', $vistup);
    $core->tpl->setVar('HOUSE_HEIGHT', $house_height);
    $core->tpl->setVar('N_1', $n_1);
    $core->tpl->setVar('N_2', $n_2);
    $core->tpl->setVar('N_3', $n_3);
    $core->tpl->setVar('T_1', $t_1);

    $core->tpl->setVar('T_11', $t_11);
    $core->tpl->setVar('T_12', $t_12);
    $core->tpl->setVar('T_13', $t_13);
    $core->tpl->setVar('T_2', $t_2);
    $core->tpl->setVar('T_21', $t_21);
    $core->tpl->setVar('T_22', $t_22);
    $core->tpl->setVar('T_23', $t_23);
    $core->tpl->setVar('T_3', $t_3);
    $core->tpl->setVar('B_1', $b_1);
    $core->tpl->setVar('P_1', $p_1);
    $core->tpl->setVar('P_2', $p_2);
    $core->tpl->setVar('N_GVS', $n_gvs);
    $core->tpl->setVar('BLOCK_1', $block_1);
    $core->tpl->setVar('BLOCK_2', $block_2);
    $core->tpl->setVar('BLOCK_3', $block_3);
    $core->tpl->setVar('BLOCK_BV_UZEL', $block_bv_uzel);
    $core->tpl->setVar('BLOCK_BV_PODPIT', $block_bv_podpit);
    $core->tpl->setVar('BLOCK_BV_REG', $block_bv_reg);
    $core->tpl->setVar('BLOCK_BV_OGR', $block_bv_ogr);
    $core->tpl->setVar('BLOCK_BV_REG_D', $block_bv_reg_d);
    $core->tpl->setVar('BLOCK_BV_OGR_D', $block_bv_ogr_d);

    $core->tpl->setVar('BLOCK_BOV_1_SCHEMA', selectConstract($array_rel, $json->block_bov_1_schema, true));
    $core->tpl->setVar('BLOCK_BOV_1_PUMP', selectConstract($array_pump, $json->block_bov_1_pump, true));
    $core->tpl->setVar('BLOCK_BOV_1_KLAPAN', selectConstract($array_klap, $json->block_bov_1_klapan, true));
    $core->tpl->setVar('BLOCK_BOV_1_CONTROL', selectConstract($array_ctrl, $json->block_bov_1_control, true));
    $core->tpl->setVar('BLOCK_BOV_1_AUTO', selectConstract(array("Отдельный" => "Отдельный"), $json->block_bov_1_auto, true));

    $core->tpl->setVar('BLOCK_BOV_2_SCHEMA', selectConstract($array_rel, '', true));
    $core->tpl->setVar('BLOCK_BOV_2_PUMP', selectConstract($array_pump, '', true));
    $core->tpl->setVar('BLOCK_BOV_2_KLAPAN', selectConstract($array_klap, '', true));
    $core->tpl->setVar('BLOCK_BOV_2_CONTROL', selectConstract($array_ctrl, '', true));
    $core->tpl->setVar('BLOCK_BOV_2_AUTO', selectConstract($array_auto, '', true));

    $core->tpl->setVar('BLOCK_BOV_3_SCHEMA', selectConstract($array_rel, '', true));
    $core->tpl->setVar('BLOCK_BOV_3_PUMP', selectConstract($array_pump, '', true));
    $core->tpl->setVar('BLOCK_BOV_3_KLAPAN', selectConstract($array_klap, '', true));
    $core->tpl->setVar('BLOCK_BOV_3_CONTROL', selectConstract($array_ctrl, '', true));
    $core->tpl->setVar('BLOCK_BOV_3_AUTO', selectConstract($array_auto, '', true));

    $core->tpl->setVar('BLOCK_BGV_SCHEMA', selectConstract(array("Автоматически" => "Автоматически", "Принудит. Смеш" => "Принудит. Смеш", "Принуд. Паралл." => "Принуд. Паралл."), '', true));
    $core->tpl->setVar('BLOCK_BGV_PUMP', selectConstract(array("Yonos MAXO-Z" => "Yonos MAXO-Z", "Stratos-Z" => "Stratos-Z"), '', true));
    $core->tpl->setVar('BLOCK_BGV_KLAPAN', selectConstract(array("IMI Hydronic" => "IMI Hydronic", "Danfoss" => "Danfoss", "Siemens" => "Siemens", "Прямого действия" => "Прямого действия"), '', true));
    $core->tpl->setVar('BLOCK_BGV_CONTROL', selectConstract(array("Segnetics" => "Segnetics", "Danfoss" => "Danfoss", "Siemens" => "Siemens"), '', true));
    $core->tpl->setVar('BLOCK_BOV_3_AUTO', selectConstract($array_auto, '', true));


    $items .= $core->tpl->return_end();
    $core->tpl->loadFile('select/select.carcase');
    include('carcase_tag.php');
    $core->tpl->end();


}

function res($id)
{
    global $core, $db, $url;	
	$allowFuncs = array();
	require_once(ROOT.'usr/modules/selection/ajax.php');	
	$query_request = $db->query("SELECT * FROM `".DB_PREFIX."_selection_request` WHERE id = '" . $db->safesql(intval($id)) . "' LIMIT 1");		
	//echo "SELECT * FROM `".DB_PREFIX."_selection_request` WHERE id = '" . $db->safesql(intval($id)) . "' LIMIT 1";
	if($db->numRows($query_request) > 0)
	{
		$request = $db->getRow($query_request);	
		$bread = '<li><a href="/select">Расчет блочного теплового пункта</a></li>';
		$page_title = 'Результаты расчёта';
		if (!empty($request['detail_code']))
		{
			$returnJson = getDetail(json_decode($request['detail_code'], true), $request['detail_scheme']);
        $update = $db->query("UPDATE `" . DB_PREFIX . "_selection_request` SET `detail_save` = '" . $returnJson . "' WHERE `id` = '" . $id . "' LIMIT 1 ;");
        //echo '<input type="hidden" value=\''.$returnJson.'\' id="jsonRes">';
        echo '<script type="text" id="jsonRes">
        '.$returnJson.'
        </script>';
		$js = '
			<script>
			
			let res = \'\';

            //let json = JSON.parse($("#jsonRes").val());
            let json = JSON.parse($("#jsonRes").html());

            console.log(json);

            res += \'<div class="groupBlock"><div><h3>Блочный тепловой пункт (БТП: \'+json["group"]["code"]+\')</h3></div>\';

            if(json["group"]["id"] == -1){
                res += \'<div class="info-msg1"><i class="fa fa-info-circle"></i> По данному БТП информация отсутствует. Наши сотрудники подготовят данные в течении 1 часа. Результат выполнения вы можете посмотреть в разделе мои расчеты.</div>\';
            } else {
                res += \'<div><a class="btnClassic" href="/selection/print/pz/'.$id.'">Получить ПЗ</a><a class="btnClassic" href="/selection/specification_all/'.$id.'">Получить общую спецификацию</a><a class="btnClassic" href="/download/\'+json["group"]["aid_scheme"]+\'">Получить общую схему</a></div>\';
            }
            res += \'</div>\';
            res += \'<div class="groupBlock">\';
            for(let i=0;i<json["blocks"].length;i++){
                res += \'<div class="hrBlock"><div><h3>\'+json["blocks"][i]["type"]+\' - \'+json["blocks"][i]["code"]+\'</h3></div>\';
                if(json["blocks"][i]["id"] == -1){
                    res += \'<div class="info-msg1"><i class="fa fa-info-circle"></i> По данному блоку информация отсутствует. Наши сотрудники подготовят данные в течении 1 часа. Результат выполнения вы можете посмотреть в разделе мои расчеты.</div>\';
                } else {
                    res += \'<div>\';
                    if(json["blocks"][i]["aid_scheme"] != -1){
                        res += \'<a href="/download/\'+json["blocks"][i]["aid_scheme"]+\'" class="btnClassic">Получить схему</a>\';
                    }
                    if(json["blocks"][i]["aid_size"] != -1){
                        res += \'<a href="/download/\'+json["blocks"][i]["aid_size"]+\'" class="btnClassic">Получить размеры</a>\';
                    }
                    res += \'<a href="/selection/specification_all/\'+json["blocks"][i]["id"]+\'" class="btnClassic">Получить спецификацию</a>\';
                    res += \'<a href="javascript:void(0);" onclick=\"cart.addToCart(this, \\\'\'+json["blocks"][i]["cart"]["id"]+\'\\\', priceList[\'+json["blocks"][i]["cart"]["id"]+\'])\" id="wicartbutton_\'+json["blocks"][i]["cart"]["id"]+\'" class="btnClassic" style="background-color: green;">В корзину</a>\';
                    priceList[json["blocks"][i]["cart"]["id"]] = json["blocks"][i]["cart"];
                    res +=\'</div>\';
                }
                res +=\'</div>\';
            }
            res +=\'</div>\';
            res +=\'</div>\';

            console.log(priceList);

            $(\'.raschetStoimostyResult\').html(res);
			</script>';
			$info ='';
			$core->tpl->js_foot[] = $js;	  
			$core->tpl->loadFile('selection/selection.result');
	
			$items = $core->tpl->return_end();
		}
		else
		{
			$items = 'Расчет появится позже...';
			$coreinfo = 'Вы не завершили построение схемы перейдите в <a href="/selection/blockConstruct/'.$id.'">конструктор</a> чтобы закончить!';
			$itype = 'error-2';
			$info = $core->tpl->info($coreinfo, $itype, false, false, false, false, false, true);			
		}
	}
    $core->tpl->loadFile('selection/selection.carcase');
    include('carcase_tag.php');
    $core->tpl->end();
}

function cupboard($id = null)
{
    global $core, $db, $url;
    if (isset($_POST['send'])) {
        /*
        X1  УТП - управление тепловым пунктом
            НС - насосные станции
            ДАСУ - другие АСУ ТП
        X2  id контроллера
            Системы управление
            Система отопления
        X3  0 - нет
            1 - есть
            Система вентиляции
        X4  0 - нет
            1 - есть
            Система ГВС
        X5  0 - нет
            1 - есть
            Характеристика регулирующего клапана
        X6  1 - 220v
            2 - 24v
        X7  1 - 3-х точечный
            2 - аналоговый
            Характеристики насосного оборудования
        X8  Количество
        X9  1 - 220v
            2 - 380v
            Плавный пуск
        X10 0 - нет
            1 - есть
            Частотное управление
        X11  0 - нет
            1 - по давлению
            2 - по перепаду давления
            
        Х12 Ток, А
        Х13 Мощность, кВ
            Наличие дополнительных контактов
        X14 0 - нет
            1 - SSM
            2 - PTC
            3 - SSB
            Характеристика датчиков, t°
        X15 1 - pt100
            2 - pt500
            3 - pt1000
            4 - Ni1000
            Наличие подпитки
        X16 0 - нет
            1 - клапан соленоидный
            2 - насос
        
        */
        $code = [];
        switch ($_POST['purpose']) {
            case 1:
                $code[1] = 'УТП';
                break;
            case 2:
                $code[1] = 'НС';
                break;
            case 3:
                $code[1] = 'ДАСУ';
                break;
        }

        $code[2] = $_POST['controller'];

        if (isset($_POST['controlSystem_1']))
            $code[3] = 1;
        else
            $code[3] = 0;

        if (isset($_POST['controlSystem_2']))
            $code[4] = 1;
        else
            $code[4] = 0;

        if (isset($_POST['controlSystem_3']))
            $code[5] = 1;
        else
            $code[5] = 0;

        $code[6] = $_POST['controlValve_1'];

        $code[7] = $_POST['controlValve_2'];

        $code[8] = $_POST['count'];

        $code[9] = $_POST['pumpingEquipment_1'];

        if (isset($_POST['floatStart']))
            $code[10] = 1;
        else
            $code[10] = 0;

        if (isset($_POST['frequencyControl'])) {
            $code[11] = $_POST['frequencyControlChoice'];
        } else
            $code[11] = 0;

        $code[12] = $_POST['currentA'];

        $code[13] = $_POST['powerKB'];

        if (isset($_POST['additionalComp'])) {
            $code[14] = '';
            isset($_POST['SSM']) ? $code[14] .= '1' : '';
            isset($_POST['PTC']) ? $code[14] .= '2' : '';
            isset($_POST['SSB']) ? $code[14] .= '3' : '';
        } else
            $code[14] = 0;

        $code[15] = $_POST['sensors'];

        if (isset($_POST['recharge'])) {
            $code[16] = '';
            isset($_POST['solenoidValve']) ? $code[16] .= '1' : '';
            isset($_POST['pump']) ? $code[16] .= '2' : '';
        } else
            $code[16] = 0;
        echo '<div class="container">
                <div class="row">';
        echo '<h2>Ваш код шкафа: ' . join("-", $code) . '</h2>';
        echo '</div>
                </div>';
        $json = json_encode($code, JSON_UNESCAPED_UNICODE);
        $db->query("UPDATE `" . DB_PREFIX . "_select` SET detail = '" . $json . "', status='1', code='".join("-", $code)."' WHERE id='" . $db->safesql(intval($url[2])) . "' LIMIT 1 ;");
    } else {
        $query = $db->query("SELECT c.*, m.name as mname FROM " . DB_PREFIX . "_catalog AS c LEFt JOIN " . DB_PREFIX . "_catalog_manufacture AS m ON(c.manufacture=m.id_1c)");
        $sel = '';
        while ($catalog = $db->getRow($query)) {
            if (isset($catalog['attribute'])) {
                $attribute = json_decode($catalog['attribute'], true);
            } else continue;
            if (isset($catalog['spec'])) {
                $spec = json_decode($catalog['spec'], true);
            } else continue;
            if (!isset($attribute['102'])) continue;
            if (!isset($spec['2'])) continue;
            if ($attribute['102'] == 'Контроллер') {
                $sel .= '<option value="' . $catalog["id"] . '">' . $catalog['mname'] . ' ' . $spec['2'] . '</option>';
            }
        }
        $core->tpl->setVar('CONTROLLER', $sel);
        $core->tpl->loadFile('select/select.cupboard');
        $core->tpl->end();
    }
}

function idCreater($type, $nameFunc){
    global $url, $db, $core;
    /*
    if (isset($url[2])) {
                $query = $db->query("SELECT id FROM " . DB_PREFIX . "_select WHERE id = '" . $db->safesql(intval($url[2])) . "' AND status = '0' AND uid = '" . $core->auth->user_info['id'] . "' LIMIT 1");
                if ($db->numRows($query) > 0) {
                    add_detail(intval($url[2]));
                } else {
                    header('Location: /select/my');
                }
            } else {
                $uid = 0;
                $oid = isset($_POST['org']) ? intval($_POST['org']) : '0';
                $object = isset($_POST['object']) ? intval($_POST['object']) : '0';
                $comment = isset($_POST['comment']) ? filter($_POST['comment']) : '';
                if ($core->auth->isUser) {
                    $uid = $core->auth->user_info['id'];
                }
                $insert = $db->query("INSERT INTO `" . DB_PREFIX . "_select` ( `id` , `uid` , `oid` , `object` , `comment` , `date` ,  `status`) VALUES (NULL, '" . $uid . "', " . $oid . ", '" . $object . "', '" . $comment . "', " . time() . ", '0');");
                if ($insert) {
                    $sid = $db->insert_id();
                    header('Location: /select/add_detail/' . intval($sid) . '');
                }

            }
    */
    if (isset($url[2])) {
                $query = $db->query("SELECT id FROM " . DB_PREFIX . "_select WHERE id = '" . $db->safesql(intval($url[2])) . "' AND status = '0' AND uid = '" . $core->auth->user_info['id'] . "' LIMIT 1");
                if ($db->numRows($query) > 0) {
                    $nameFunc(intval($url[2])); //подстановка функции
                } else {
                    header('Location: /select/my');
                }
            } else {
                $uid = 0;
                $oid = isset($_POST['org']) ? intval($_POST['org']) : '0';
                $object = isset($_POST['object']) ? intval($_POST['object']) : '0';
                $comment = isset($_POST['comment']) ? filter($_POST['comment']) : '';
                if ($core->auth->isUser) {
                    $uid = $core->auth->user_info['id'];
                }
                $insert = $db->query("INSERT INTO `" . DB_PREFIX . "_selection_request` ( `id` , `uid` , `oid` , `object` , `comment` , `date` ,  `status`) VALUES (NULL, '" . $uid . "', " . $oid . ", '" . $object . "', '" . $comment . "', '" . date('Y-m-d H:i:s') . "', '0');");
                if ($insert) {
                    $sid = $db->insert_id();
                    header('Location: /selection/'.$nameFunc.'/' . intval($sid) . '');
                }

            }
}

function specification($id = null, $type = null)
{	
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;
	$id = intval($id);
	$query_request = $db->query("SELECT * FROM `".DB_PREFIX."_selection_request` WHERE id = '" . $db->safesql($id) . "' LIMIT 1");		
	if($db->numRows($query_request) > 0)
	{
		$spec_data_all = array();
		$blocks_count = array();
		$blocks_count['0'] = 0;
		$blocks_count['1'] = 0;
		$blocks_count['2'] = 0;	
		$blocks_count['3'] = 0;	
		$request = $db->getRow($query_request);	
		$block = json_decode($request['detail_code'], true);
		$spec_data = array(
				'num' => array(),
				'sku' => array(),
				'unit' => array(),
				'name' => array(),
				'sum' => array(),
				'model' => array(),
				'manufacter' => array(),
				'weight' => array()
			);
		echo '<pre>';
		$bi = 0;
		foreach ($block as $key => $value) 
		{
			echo $value['1']['type'];
			$bi++;
			switch ($value['1']['type']) {
				case '0':										
					$ss = specification_list($value['1']['code'], $value['1']['type'], $spec_data, $bi);						
					if (!empty($ss))
					{
						$spec_data_all['0'] = $ss;
					}	
				break;
				case '1':	
					$blocks_count[$value['1']['type']]++;
					if($blocks_count[$value['1']['type']] != 1) 
					{
						$ss = specification_list($value['1']['code'], $value['1']['type'], $spec_data, $bi);						
						if (!empty($ss))
						{
							$spec_data_all['3'] = $ss;
						}
					}
					else
					{
						$ss = specification_list($value['1']['code'], $value['1']['type'], $spec_data, $bi);						
						if (!empty($ss))
						{
							$spec_data_all['2'] = $ss;
						}		
					}				
				break;
				case '2':
					$ss = specification_list($value['1']['code'], $value['1']['type'], $spec_data, $bi);						
					if (!empty($ss))
					{
						$spec_data_all['4'] = $ss;
					}	
				break;
				case '3':	
					$blocks_count[$value['1']['type']]++;
					if($blocks_count[$value['1']['type']] != 1) 
					{					
						$ss = specification_list($value['1']['code'], $value['1']['type'], $spec_data, $bi);						
						if (!empty($ss))
						{
							$spec_data_all['6'] = $ss;
						}	
					}	
					else
					{
						$ss = specification_list($value['1']['code'], $value['1']['type'], $spec_data, $bi);						
						if (!empty($ss))
						{
							$spec_data_all['5'] = $ss;
						}
					}				
				break;		
			}			
		}
		foreach ($spec_data_all as $key => $value)			
		{
			foreach ($value as $kk => $vv)			
			{
				$$kk = array();
			}
		}
		foreach ($spec_data_all as $key => $value)			
		{
			foreach ($value as $kk => $vv)			
			{
				$$kk = array_merge($$kk, $vv);				
			}
		}
		$spec_data_compl = array(
				'num' => $num,
				'sku' => $sku,
				'unit' => $unit,
				'name' => $name,
				'sum' => $sum,
				'model' => $model,
				'manufacter' => $manufacter,
				'weight' => $weight
			);
		print_r($spec_data_compl);
		require_once(ROOT.'usr/plugins/PHPWord.php');

			$PHPWord = new PHPWord();

			$document = $PHPWord->loadTemplate(ROOT.'/files/tpl/specification.docx');

			$Month_rus = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
			
					
			
			
			$document->cloneRow('spec', $spec_data_compl);		
			$uid = $core->auth->user_info['id'];
			$document->save('files/user/'.$uid .'_spec.docx');
			$items = '<a href="files/user/'.$uid .'_spec.docx" target="_blank" > Скачать </a>';
			



			header('Location: https://atri-energo.ru/files/user/'.$uid .'_spec.docx');
	
		
	}
}

function specification_list($code, $type, $spec_data, $bi)
{
	global $db, $config, $core, $tags, $url, $headTag, $cache, $lang;
	$code_array = explode(':',$code);
	$query = $db->query("SELECT* FROM ".DB_PREFIX."_selection WHERE type='" . $db->safesql($type) . "' AND code LIKE '%".$code_array['1']."%'");		
	if($db->numRows($query) > 0)
	{
		$info = $db->getRow($query);
		$block_info['id'] = $info['id'];	
		$query_detail = $db->query("SELECT sd.*, c.*, c.id as cid,  cu.unit, cu.sname as unit_sname, cm.id_1c, cm.name as manufacture_name, cm.id as manufacture_id FROM JMY_selection_detail as sd LEFT JOIN JMY_catalog as c ON(sd.cid = c.id) LEFT JOIN JMY_catalog_manufacture as cm ON(cm.id_1c = c.manufacture) LEFT JOIN JMY_catalog_unit as cu ON(cu.unit = c.unit) WHERE sd.selection_id = '".$block_info['id']."' ORDER BY sd.position");	
		if($db->numRows($query_detail) > 0)
		{
			$i = 0;
			$block_info['specification'] = array();
			while($catalog = $db->getRow($query_detail)) 
			{
				/*
				$block_info['specification'][$detail['id']]['id'] = $detail['id'];						
				$block_info['specification'][$detail['id']]['cid'] = $detail['cid'];						
				$block_info['specification'][$detail['id']]['title'] = $detail['title'];						
				$block_info['specification'][$detail['id']]['sku'] = $detail['sku'];						
				$block_info['specification'][$detail['id']]['count'] = $detail['count'];						
				$block_info['specification'][$detail['id']]['manufacture'] = $detail['cmname'];						
				$block_info['specification'][$detail['id']]['unit'] = $detail['cuname'];	
				*/
				$i++;
				$sku = $catalog['sku'];
				$unit = $catalog['unit_sname'];
				$name = $catalog['title'];
				$model = ' ';
				$manufacter = (!empty($catalog['manufacture_name']) ? $catalog['manufacture_name'] : ' ');
				$spec = json_decode($catalog['spec'], true);
				$weight	 = json_decode($catalog['weight'], true);
				if (!empty($spec['1']))
				{
					$name = $spec['1'];
				}
				if (!empty($spec['2']))
				{
					$model = $spec['2'];
				}
				if (!empty($spec['3']))
				{
					$manufacter = $spec['3'];
				}
				$weight_good = '';
				if (!empty($weight['1']))
				{
					$weight_good = $weight['1'];
				}						
				array_push($spec_data['num'], $bi.'.'.$i);
				array_push($spec_data['sku'], $sku);
				array_push($spec_data['unit'], $unit);
				array_push($spec_data['name'], $name);
				array_push($spec_data['sum'], $catalog['count']);
				array_push($spec_data['model'], $model);
				array_push($spec_data['manufacter'], $manufacter);
				array_push($spec_data['weight'], $weight_good);
			}
				return $spec_data;
		}	
		else
		{
			return '';
		}
	}
	
}


function translitFile($str){

    //$str = str_replace ('/\s+/','_',$str);
    $tr = array(
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
        "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
        "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
        "."=>""," "=>"_","?"=>"","/"=>"_","\\"=>"_","#"=>"_","&"=>"_",
        "*"=>"",":"=>"","'"=>"","`"=>"","*"=>"","\""=>"","<"=>"",
        ">"=>"","|"=>"",","=>"","("=>"",")"=>""
    );

    return strtr($str,$tr);

}

if ($core->auth->isUser) {

    switch (isset($url[1]) ? $url[1] : null) {
        default:
            main();
            break;

        case "cupboard":
			idCreater(2, 'cupboard');
            break;

        case "my":
            my();
            break; 
             case "scheme":
            scheme();
            break;
			
		case "blockConstruct":
			define('ROOT_CONTRUCTOR', ROOT.'/usr/modules/selection/blockConstruct');
            require_once(ROOT_CONTRUCTOR.'/index.php');
			exit;
        break;


        case "add":
            add();
            break;
			
		
            
        case "addUniversal":
            addUniversal();
            break;

        case "add_detail":
            idCreater(1, 'add_detail');
            break;
			
		
        case "addBlockConstructor":
            idCreater(1, 'blockConstruct');
            break;

        case "save_detail":
            if (isset($url[2])) {
                $query = $db->query("SELECT id FROM " . DB_PREFIX . "_select WHERE id = '" . $db->safesql(intval($url[2])) . "' AND status = '0' AND uid = '" . $core->auth->user_info['id'] . "' LIMIT 1");
                if ($db->numRows($query) > 0) {
                    $json = json_encode($_POST, JSON_UNESCAPED_UNICODE);
                    $db->query("UPDATE `" . DB_PREFIX . "_select` SET detail = '" . $json . "', status='1' WHERE id='" . $db->safesql(intval($url[2])) . "' LIMIT 1 ;");
                    header('Location: /select/result/' . $url[2] . '');
                }
            } else {
                header('Location: /select/my');
            }
            break;

        case "result":
            if (isset($url[2])) {
                res(intval($url[2]));
            } else {
                header('Location: /select/my');
            }
            break;

        case "get_info":
            get_info();
            break;

        case "date":
            main('date');
            break;

        case "view":
            view($url[2]);
        break;

        case "ajax":

                /* --- Отправка формы --- */
                header('Content-type: text/html');
                header('Access-Control-Allow-Origin: *');

                //$catalog = isset($_POST['modul']);

                $catalog = 'files/block_construct';

                if(1){

                    $valid_ext = array('rar','zip','7z','doc','docx','xls','xlsx','dwg','pdf','png','bmp','jpg','jpeg','gif','dws','dxf',); // допустимые расширения
                    $filename2 = isset($_FILES['userfile']['name']) ? $_FILES['userfile']['name'] : '';
                    $arrayFilename2 = explode(".",  $filename2);
                    $ext = array_pop($arrayFilename2);

                    $nameext = explode(".", $_FILES['userfile']['name']);

                    if(in_array($ext, $valid_ext)){

                        $translit_filename = translit($nameext[0]);

                        if(strlen($translit_filename) > 30){

                            $translit_filename = substr($translit_filename,0,30);

                        }

                        $filename = 's_'.date("d-m-y-H-i-s").'_'.$translit_filename.'.'.$ext; // переименовываем файлик

                        move_uploaded_file($_FILES['userfile']['tmp_name'], $catalog.'/'.$filename);

                        // Запись имени файла в БД и вывод ID
                        echo $filename;

                    }else{

                        echo '<span style="color: red">Недопустимый формат файла!</span>';

                    }

                }


            exit;
            break;
		
		case "specification_all":
           specification($url[2]);
        break;

        case "getBlocksCalculationTpl":
            $bc = new \Calculation\BlockCalculation();
            $bc->getRequsetDataFromSQL($url[2], true);
            $t = $bc->getResultTemplate();
            echo $t;
            echo ' <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
            <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
            <style>
                .aspect-tab-content table,
                .aspect-tab-content th,
                .aspect-tab-content td {
                    border: 1px solid rgba(0, 0, 0, 0.1);
                    font-size: 16px;
                }
                
                .aspect-tab-content table {
                    border-collapse: separate;
                    border-spacing: 0px;
                    border-width: 1px 0 0 1px;
                    margin-bottom: 24px;
                    width: 100%;
                }
                
                .aspect-tab-content caption,
                .aspect-tab-content th,
                .aspect-tab-content td {
                    font-weight: normal;
                    text-align: left;
                }
                
                .aspect-tab-content th {
                    border-width: 0 1px 1px 0;
                    font-weight: bold;
                    line-height: 24px;
                    display: table-cell;
                    vertical-align: middle;
                    text-align: left;
                    padding: 10px
                }
                
                .aspect-tab-content td {
                    border-width: 0 1px 1px 0;
                    padding: 10px;
                }
                
                .aspect-tab-content tr:nth-child(odd) {
                    background: #EEE;
                }
                
                #aspect-content {
                    margin: 50px 0 0;
                    font-family: "Poppins", sans-serif;
                }
                
                #aspect-content * {
                    box-sizing: border-box;
                }
                
                .aspect-tab {
                    position: relative;
                    width: 100%;
                    max-width: 1800px;
                    margin: 0 auto 10px;
                    border-radius: 4px;
                    background-color: #fff;
                    box-shadow: 0 0 0 1px #ececec;
                    opacity: 1;
                    transition: box-shadow 0.2s, opacity 0.4s;
                }
                
                .aspect-tab:hover {
                    box-shadow: 0 4px 10px 0 rgba(0, 0, 0, 0.11);
                }
                
                .aspect-input {
                    display: none;
                }
                
                .aspect-input:checked~.aspect-content+.aspect-tab-content {
                    max-height: 3000px;
                }
                
                .aspect-input:checked~.aspect-content:after {
                    transform: rotate(0);
                }
                
                .aspect-label {
                    position: absolute;
                    top: 0;
                    left: 0;
                    height: 100%;
                    max-height: 80px;
                    width: 100%;
                    margin: 0;
                    padding: 0;
                    font-size: 0;
                    z-index: 1;
                    cursor: pointer;
                }
                
                .aspect-label:hover~.aspect-content:after {
                    background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZD0iTTI0IDI0SDBWMGgyNHoiIG9wYWNpdHk9Ii44NyIvPgogICAgICAgIDxwYXRoIGZpbGw9IiM1NTZBRUEiIGZpbGwtcnVsZT0ibm9uemVybyIgZD0iTTE1Ljg4IDE1LjI5TDEyIDExLjQxbC0zLjg4IDMuODhhLjk5Ni45OTYgMCAxIDEtMS40MS0xLjQxbDQuNTktNC41OWEuOTk2Ljk5NiAwIDAgMSAxLjQxIDBsNC41OSA0LjU5Yy4zOS4zOS4zOSAxLjAyIDAgMS40MS0uMzkuMzgtMS4wMy4zOS0xLjQyIDB6Ii8+CiAgICA8L2c+Cjwvc3ZnPgo=");
                }
                
                .aspect-content {
                    position: relative;
                    display: block;
                    height: 80px;
                    margin: 0;
                    padding: 0 87px 0 30px;
                    font-size: 0;
                    white-space: nowrap;
                    cursor: pointer;
                    user-select: none;
                    -webkit-user-select: none;
                    -khtml-user-select: none;
                    -moz-user-select: none;
                    -ms-user-select: none;
                    -o-user-select: none;
                }
                
                .aspect-content:before,
                .aspect-content:after {
                    content: "";
                    display: inline-block;
                    vertical-align: middle;
                }
                
                .aspect-content:before {
                    height: 100%;
                }
                
                .aspect-content:after {
                    position: absolute;
                    width: 24px;
                    height: 100%;
                    right: 30px;
                    background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZD0iTTI0IDI0SDBWMGgyNHoiIG9wYWNpdHk9Ii44NyIvPgogICAgICAgIDxwYXRoIGZpbGw9IiNBOUFDQUYiIGZpbGwtcnVsZT0ibm9uemVybyIgZD0iTTE1Ljg4IDE1LjI5TDEyIDExLjQxbC0zLjg4IDMuODhhLjk5Ni45OTYgMCAxIDEtMS40MS0xLjQxbDQuNTktNC41OWEuOTk2Ljk5NiAwIDAgMSAxLjQxIDBsNC41OSA0LjU5Yy4zOS4zOS4zOSAxLjAyIDAgMS40MS0uMzkuMzgtMS4wMy4zOS0xLjQyIDB6Ii8+CiAgICA8L2c+Cjwvc3ZnPgo=");
                    background-repeat: no-repeat;
                    background-position: center;
                    transform: rotate(180deg);
                }
                
                .aspect-name {
                    display: inline-block;
                    width: 75%;
                    margin-left: 16px;
                    font-weight: 500;
                    color: #242a32;
                    white-space: normal;
                    text-align: left;
                    vertical-align: middle;
                }
                
                .aspect-stat {
                    width: 40%;
                    text-align: right;
                }
                
                .all-opinions,
                .aspect-name {
                    font-size: 14px;
                    line-height: 22px;
                }
                
                .all-opinions {
                    color: #5d5d5d;
                    text-align: left;
                }
                
                .aspect-content+.aspect-tab-content {
                    max-height: 0;
                    overflow: hidden;
                    transition: max-height 0.3s;
                }
                
                .aspect-content>div,
                .aspect-stat>div {
                    display: inline-block;
                }
                
                .aspect-content>div {
                    vertical-align: middle;
                }
                
                .positive-count,
                .negative-count,
                .neutral-count {
                    display: inline-block;
                    margin: 0 0 0 20px;
                    padding-left: 26px;
                    background-repeat: no-repeat;
                    font-size: 13px;
                    line-height: 20px;
                    color: #363636;
                }
                
                .positive-count {
                    background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDIwIDIwIj4KICAgIDxwYXRoIGZpbGw9IiM3RUQzMjEiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTEwIDE4LjU3MWMtNC43MjYgMC04LjU3MS0zLjg0NS04LjU3MS04LjU3MSAwLTQuNzI2IDMuODQ1LTguNTcxIDguNTcxLTguNTcxIDQuNzI2IDAgOC41NzEgMy44NDUgOC41NzEgOC41NzEgMCA0LjcyNi0zLjg0NSA4LjU3MS04LjU3MSA4LjU3MXpNMjAgMTBjMCA1LjUxNC00LjQ4NiAxMC0xMCAxMFMwIDE1LjUxNCAwIDEwIDQuNDg2IDAgMTAgMHMxMCA0LjQ4NiAxMCAxMHpNNSAxMS40MjdhNSA1IDAgMCAwIDEwIDAgLjcxNC43MTQgMCAxIDAtMS40MjkgMCAzLjU3MSAzLjU3MSAwIDAgMS03LjE0MiAwIC43MTQuNzE0IDAgMSAwLTEuNDI5IDB6bTEuMDcxLTVhMS4wNzEgMS4wNzEgMCAxIDAgMCAyLjE0MyAxLjA3MSAxLjA3MSAwIDAgMCAwLTIuMTQzem03Ljg1OCAwYTEuMDcxIDEuMDcxIDAgMSAwIDAgMi4xNDMgMS4wNzEgMS4wNzEgMCAwIDAgMC0yLjE0M3oiLz4KPC9zdmc+Cg==");
                }
                
                .negative-count {
                    background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDIwIDIwIj4KICAgIDxwYXRoIGZpbGw9IiNGRjZFMDAiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTEwIDE4LjU3MWMtNC43MjYgMC04LjU3MS0zLjg0NS04LjU3MS04LjU3MSAwLTQuNzI2IDMuODQ1LTguNTcxIDguNTcxLTguNTcxIDQuNzI2IDAgOC41NzEgMy44NDUgOC41NzEgOC41NzEgMCA0LjcyNi0zLjg0NSA4LjU3MS04LjU3MSA4LjU3MXpNMjAgMTBjMCA1LjUxNC00LjQ4NiAxMC0xMCAxMFMwIDE1LjUxNCAwIDEwIDQuNDg2IDAgMTAgMHMxMCA0LjQ4NiAxMCAxMHpNNSAxNC45OThhLjcxNC43MTQgMCAwIDAgMS40MjkgMCAzLjU3MSAzLjU3MSAwIDAgMSA3LjE0MiAwIC43MTQuNzE0IDAgMSAwIDEuNDI5IDAgNSA1IDAgMSAwLTEwIDB6bTEuMDcxLTguNTdhMS4wNzEgMS4wNzEgMCAxIDAgMCAyLjE0MiAxLjA3MSAxLjA3MSAwIDAgMCAwLTIuMTQzem03Ljg1OCAwYTEuMDcxIDEuMDcxIDAgMSAwIDAgMi4xNDIgMS4wNzEgMS4wNzEgMCAwIDAgMC0yLjE0M3oiLz4KPC9zdmc+Cg==");
                }
                
                .neutral-count {
                    background-image: url("data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDIwIDIwIj4KICAgIDxwYXRoIGZpbGw9IiNCQUMyRDYiIGZpbGwtcnVsZT0iZXZlbm9kZCIgZD0iTTEwIDE4LjU3MWMtNC43MjYgMC04LjU3MS0zLjg0NS04LjU3MS04LjU3MSAwLTQuNzI2IDMuODQ1LTguNTcxIDguNTcxLTguNTcxIDQuNzI2IDAgOC41NzEgMy44NDUgOC41NzEgOC41NzEgMCA0LjcyNi0zLjg0NSA4LjU3MS04LjU3MSA4LjU3MXpNMjAgMTBjMCA1LjUxNC00LjQ4NiAxMC0xMCAxMFMwIDE1LjUxNCAwIDEwIDQuNDg2IDAgMTAgMHMxMCA0LjQ4NiAxMCAxMHpNNS43MTQgMTEuNDI3YS43MTQuNzE0IDAgMSAwIDAgMS40MjloOC41NzJhLjcxNC43MTQgMCAxIDAgMC0xLjQyOUg1LjcxNHptLjM1Ny01YTEuMDcxIDEuMDcxIDAgMSAwIDAgMi4xNDMgMS4wNzEgMS4wNzEgMCAwIDAgMC0yLjE0M3ptNy44NTggMGExLjA3MSAxLjA3MSAwIDEgMCAwIDIuMTQzIDEuMDcxIDEuMDcxIDAgMCAwIDAtMi4xNDN6Ii8+Cjwvc3ZnPgo=");
                }
                
                .aspect-info {
                    width: 60%;
                    white-space: nowrap;
                    font-size: 0;
                }
                
                .aspect-info:before {
                    content: "";
                    display: inline-block;
                    height: 44px;
                    vertical-align: middle;
                }
                
                .chart-pie {
                    position: relative;
                    display: inline-block;
                    height: 44px;
                    width: 44px;
                    border-radius: 50%;
                    background-color: #e4e4e4;
                    vertical-align: middle;
                }
                
                .chart-pie:after {
                    content: "";
                    display: block;
                    position: absolute;
                    height: 40px;
                    width: 40px;
                    top: 2px;
                    left: 2px;
                    border-radius: 50%;
                    background-color: #fff;
                }
                
                .chart-pie-count {
                    position: absolute;
                    display: block;
                    height: 100%;
                    width: 100%;
                    font-size: 14px;
                    font-weight: 500;
                    line-height: 44px;
                    color: #242a32;
                    text-align: center;
                    z-index: 1;
                }
                
                .chart-pie>div {
                    clip: rect(0, 44px, 44px, 22px);
                }
                
                .chart-pie>div,
                .chart-pie.over50 .first-fill {
                    position: absolute;
                    height: 44px;
                    width: 44px;
                    border-radius: 50%;
                }
                
                .chart-pie.over50>div {
                    clip: rect(auto, auto, auto, auto);
                }
                
                .chart-pie.over50 .first-fill {
                    clip: rect(0, 44px, 44px, 22px);
                }
                
                .chart-pie:not(.over50) .first-fill {
                    display: none;
                }
                
                .second-fill {
                    position: absolute;
                    clip: rect(0, 22px, 44px, 0);
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    border-width: 3px;
                    border-style: solid;
                    box-sizing: border-box;
                }
                
                .chart-pie.positive .first-fill {
                    background-color: #82d428;
                }
                
                .chart-pie.positive .second-fill {
                    border-color: #82d428;
                }
                
                .chart-pie.negative .first-fill {
                    background-color: #ff6e00;
                }
                
                .chart-pie.negative .second-fill {
                    border-color: #ff6e00;
                }
                
                .aspect-tab-content {
                    background-color: #f9f9f9;
                    font-size: 0;
                    text-align: justify;
                }
                
                .sentiment-wrapper {
                    padding: 24px 30px 30px;
                }
                
                .sentiment-wrapper>div {
                    display: inline-block;
                    width: 100%;
                    padding: 0 5px;
                    box-sizing: border-box;
                    vertical-align: top;
                    cursor: default;
                }
                
                .sentiment-wrapper>div>div {
                    width: 100%;
                    padding: 16px 24px 20px;
                    box-sizing: border-box;
                    border-radius: 4px;
                    background-color: #fff;
                    border: 1px solid #ececec;
                    text-align: left;
                }
                
                .opinion-header {
                    position: relative;
                    width: 100%;
                    margin: 0 0 24px;
                    font-size: 13px;
                    font-weight: 500;
                    line-height: 20px;
                    color: #242a32;
                    text-transform: capitalize;
                }
                
                .opinion-header>span:nth-child(2) {
                    position: absolute;
                    right: 0;
                }
                
                .opinion-header+div>span {
                    font-size: 13px;
                    font-weight: 400;
                    line-height: 22px;
                    color: #363636;
                }
                
                @media screen and (max-width: 800px) {
                    .aspect-label {
                        max-height: 102px;
                    }
                    .aspect-content {
                        height: auto;
                        padding: 10px 87px 10px 30px;
                    }
                    .aspect-content:before {
                        display: none;
                    }
                    .aspect-content:after {
                        top: 0;
                    }
                    .aspect-content>div {
                        display: block;
                        width: 100%;
                    }
                    .aspect-stat {
                        margin-top: 10px;
                        text-align: left;
                    }
                }
                
                @media screen and (max-width: 750px) {
                    .sentiment-wrapper>div {
                        display: block;
                        width: 100%;
                        max-width: 100%;
                    }
                    .sentiment-wrapper>div:not(:first-child) {
                        margin-top: 10px;
                    }
                }
                
                @media screen and (max-width: 500px) {
                    .aspect-label {
                        max-height: 140px;
                    }
                    .aspect-stat>div {
                        display: block;
                        width: 100%;
                    }
                    .all-opinions {
                        margin-bottom: 10px;
                    }
                    .all-opinions+div>span:first-child {
                        margin: 0;
                    }
                }
            </style>';
            exit;
         break;
      
        case "print":
			$id = intval($url[2]);
			printSelection($id);		
		break;

    }
} else {
    


    switch (isset($url[1]) ? $url[1] : null) {
        default:
        set_title(array('Добро пожаловать в профиль'));
        $core->tpl->loadFile('profile/login');
        $core->tpl->end();
       break;

		
       case "scheme":
        scheme();
        break;
        case "print":
			$id = intval($url[2]);
			printSelection($id);		
		break;
  }
}