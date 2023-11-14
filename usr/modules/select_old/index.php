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
    $query = $db->query("SELECT * FROM " . DB_PREFIX . "_select WHERE uid = '" . $core->auth->user_info['id'] . "' ORDER BY id ASC");
    $list = '';
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
                $add_detail = '<a href="/select/add_detail/' . $select['id'] . '">Продолжить</a><br>';
            } elseif ($select['status'] == '1') {
                $status = '<span class="label label-info">Выполняется</span>';
            } elseif ($select['status'] == '2') {
                $status = '<span class="label label-success">Выполнен</span>';
            }
            $list .= '<tr>
						<td>#БС-' . str_pad($select['id'], 5, "0", STR_PAD_LEFT) . '</td>
                        <td width=20%>' . $object . '</td>
						<td width=20%>' . $org . '</td>
						<td width=15%>' . formatdate($select['date']) . '</td>
						<td>' . $status . '</td>
						<td>
							' . $add_detail . '
							<a href="/select/result/' . $select['id'] . '">Результат запроса</a><br>
							<a href="/select/view/' . $select['id'] . '">Детали запроса</a>
						</td>
                      </tr>';
        }
    }
    $core->tpl->loadFile('select/select.my');
    $core->tpl->setVar('LIST', $list);
    $items = $core->tpl->return_end();
    $core->tpl->loadFile('select/select.carcase');
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
 
    $core->tpl->loadFile('select/select.add');
    $core->tpl->setVar('ORG_LIST', $org_list);
    $core->tpl->setVar('OBJECT_LIST', $object_list);
    $core->tpl->setVar('CAPTCHA', captcha_image());
    $items = $core->tpl->return_end();
    $bread = '<li><a href="/select">Расчет блочного теплового пункта</a></li>';
    $page_title = 'Новый расчёт';
    $core->tpl->loadFile('select/select.carcase');
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
    
    $core->tpl->loadFile('select/select.addUniversal');
    switch ($url[2]) {
        case 'thermal':
            $core->tpl->setVar('FULL_TITLE', 'Новый расчёт блочного теплового пункта');
            $core->tpl->setVar('TRANSITION', 'select/add_detail');
            $core->tpl->setVar('ORG_LIST', $org_list);
            $core->tpl->setVar('OBJECT_LIST', $object_list);
            $core->tpl->setVar('CAPTCHA', captcha_image());
            break;
        case 'cupboard':
            $core->tpl->setVar('FULL_TITLE', 'Новый расчёт шкафа автоматики');
            $core->tpl->setVar('TRANSITION', 'select/cupboard');
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

function res()
{
    require_once ROOT . '/vendor/autoload.php';
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . ROOT . '/8f715c9461e6.json');
    $client = new Google_Client;
    $client->useApplicationDefaultCredentials();
    $client->setApplicationName("Something to do with my representatives");
    $client->setScopes(['https://www.googleapis.com/auth/drive', 'https://spreadsheets.google.com/feeds']);
    if ($client->isAccessTokenExpired()) {
        $client->refreshTokenWithAssertion();
    }
    $accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
    ServiceRequestFactory::setInstance(
        new DefaultServiceRequest($accessToken)
    );
    $spreadsheet = (new Google\Spreadsheet\SpreadsheetService)
        ->getSpreadsheetFeed()
        ->getByTitle('copy_btp');
    $worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
    $worksheet = $worksheets[6];

    foreach ($_POST as $key => $value) {
        $$key = (!empty($value) ? str_replace('.', ',', $value) : 0);
    }
    global $core, $db;
    $cellFeed = $worksheet->getCellFeed();
    $batchRequest = new Google\Spreadsheet\Batch\BatchRequest();

    $batchRequest->addEntry($cellFeed->createCell(14, 1, $block_1));
    $batchRequest->addEntry($cellFeed->createCell(15, 1, $block_2));
    $batchRequest->addEntry($cellFeed->createCell(16, 1, $block_3));

    $batchRequest->addEntry($cellFeed->createCell(14, 3, $n_1));
    $batchRequest->addEntry($cellFeed->createCell(15, 3, $n_2));
    $batchRequest->addEntry($cellFeed->createCell(16, 3, $n_3));
    $batchRequest->addEntry($cellFeed->createCell(17, 3, $n_gvs));

    $batchRequest->addEntry($cellFeed->createCell(13, 14, $t_1));
    $batchRequest->addEntry($cellFeed->createCell(14, 14, $t_11));
    $batchRequest->addEntry($cellFeed->createCell(15, 14, $t_12));
    $batchRequest->addEntry($cellFeed->createCell(16, 14, $t_13));
    $batchRequest->addEntry($cellFeed->createCell(17, 14, $t_3));

    $batchRequest->addEntry($cellFeed->createCell(13, 18, $t_2));
    $batchRequest->addEntry($cellFeed->createCell(14, 18, $t_21));
    $batchRequest->addEntry($cellFeed->createCell(15, 18, $t_22));
    $batchRequest->addEntry($cellFeed->createCell(16, 18, $t_23));
    $batchRequest->addEntry($cellFeed->createCell(17, 18, $b_1));

    $batchRequest->addEntry($cellFeed->createCell(13, 23, $p_1));
    $batchRequest->addEntry($cellFeed->createCell(13, 29, $p_2));

    $batchRequest->addEntry($cellFeed->createCell(14, 26, $pot_1));
    $batchRequest->addEntry($cellFeed->createCell(15, 26, $pot_2));
    $batchRequest->addEntry($cellFeed->createCell(16, 26, $pot_3));
    $batchRequest->addEntry($cellFeed->createCell(17, 26, $dav_xvs));

    $batchRequest->addEntry($cellFeed->createCell(20, 3, $block_bv_uzel));
    $batchRequest->addEntry($cellFeed->createCell(20, 9, $block_bv_podpit));
    $batchRequest->addEntry($cellFeed->createCell(20, 15, $block_bv_reg));
    $batchRequest->addEntry($cellFeed->createCell(20, 18, $block_bv_reg_d));
    $batchRequest->addEntry($cellFeed->createCell(20, 23, $block_bv_ogr));
    $batchRequest->addEntry($cellFeed->createCell(20, 26, $block_bv_ogr_d));

    $batchRequest->addEntry($cellFeed->createCell(24, 3, $block_bov_1_schema));
    $batchRequest->addEntry($cellFeed->createCell(24, 9, $block_bov_1_pump));
    $batchRequest->addEntry($cellFeed->createCell(24, 15, $block_bov_1_klapan));
    $batchRequest->addEntry($cellFeed->createCell(24, 21, $block_bov_1_control));
    $batchRequest->addEntry($cellFeed->createCell(24, 27, $block_bov_1_auto));

    $batchRequest->addEntry($cellFeed->createCell(28, 3, $block_bov_2_schema));
    $batchRequest->addEntry($cellFeed->createCell(28, 9, $block_bov_2_pump));
    $batchRequest->addEntry($cellFeed->createCell(28, 15, $block_bov_2_klapan));
    $batchRequest->addEntry($cellFeed->createCell(28, 21, $block_bov_2_control));
    $batchRequest->addEntry($cellFeed->createCell(28, 27, $block_bov_2_auto));

    $batchRequest->addEntry($cellFeed->createCell(32, 3, $block_bov_3_schema));
    $batchRequest->addEntry($cellFeed->createCell(32, 9, $block_bov_3_pump));
    $batchRequest->addEntry($cellFeed->createCell(32, 15, $block_bov_3_klapan));
    $batchRequest->addEntry($cellFeed->createCell(32, 21, $block_bov_3_control));
    $batchRequest->addEntry($cellFeed->createCell(32, 27, $block_bov_3_auto));

    $batchRequest->addEntry($cellFeed->createCell(36, 3, $block_bgv_schema));
    $batchRequest->addEntry($cellFeed->createCell(36, 9, $block_bgv_pump));
    $batchRequest->addEntry($cellFeed->createCell(36, 15, $block_bgv_klapan));
    $batchRequest->addEntry($cellFeed->createCell(36, 21, $block_bgv_control));
    $batchRequest->addEntry($cellFeed->createCell(36, 27, $block_bgv_auto));

    $batchResponse = $cellFeed->insertBatch($batchRequest);


    if ($core->auth->isAdmin) {

        $cellFeed = $worksheet->getCellFeed();
        echo 'Блок ввода БВ <br>';
        for ($i = 2; $i < 21; $i++) {
            $topLeftCornerCell = $cellFeed->getCell(21, $i);
            echo $topLeftCornerCell->getContent();
        }

        echo '<br>Блок отоп. вент. БОВ-1<br>';
        for ($i = 2; $i < 25; $i++) {
            $topLeftCornerCell = $cellFeed->getCell(25, $i);
            echo $topLeftCornerCell->getContent();
        }

        echo '<br>Блок отоп. вент. БОВ-2<br>';
        for ($i = 2; $i < 25; $i++) {
            $topLeftCornerCell = $cellFeed->getCell(29, $i);
            echo $topLeftCornerCell->getContent();
        }

        echo '<br>Блок отоп. вент. БОВ-3<br>';
        for ($i = 2; $i < 25; $i++) {
            $topLeftCornerCell = $cellFeed->getCell(33, $i);
            echo $topLeftCornerCell->getContent();
        }

        echo '<br>Блок ГВС БГВ<br>';
        for ($i = 2; $i < 31; $i++) {
            $topLeftCornerCell = $cellFeed->getCell(37, $i);
            echo $topLeftCornerCell->getContent();
        }
    }
    $bread = '<li><a href="/select">Расчет блочного теплового пункта</a></li>';
    $page_title = 'Результаты расчёта';
    $info = $core->tpl->info('Ваш запрос добавлен в систему! В ближайшее время наши специалисты расмотрят Вашу заявку и выполнят расчёт. Информацию о результате вы получите на Вашу почту. Вы также можете посмотреть все свои заявки в разделе <a href="/select/my">мои расчеты</a>.', 'info-2', false, false, false, false, false, true);
    $core->tpl->loadFile('select/select.carcase');
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
                $insert = $db->query("INSERT INTO `" . DB_PREFIX . "_select` ( `id` , `uid` , `oid` , `object` , `comment` , `date` ,  `status`, `type`) VALUES (NULL, '" . $uid . "', " . $oid . ", '" . $object . "', '" . $comment . "', " . time() . ", '0', '".$type."');");
                if ($insert) {
                    $sid = $db->insert_id();
                    header('Location: /select/'.$nameFunc.'/' . intval($sid) . '');
                }

            }
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

        case "add":
            add();
            break;
            
        case "addUniversal":
            addUniversal();
            break;

        case "add_detail":
            idCreater(1, 'add_detail');
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

    }
} else {
    set_title(array('Добро пожаловать в профиль'));
    $core->tpl->loadFile('profile/login');
    $core->tpl->end();
}