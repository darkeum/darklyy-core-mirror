<?php

/**
 * @name        JMY CORE
 * @link        https://jmy.su/
 * @copyright   Copyright (C) 2012-2017 JMY LTD
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      Komarov Ivan
 * @info_ru     Место под ваши собственные функции
 * @info_en     Place for your own functions
 */

use \Boot\Support\Facades\Config;

function includeSub($dir = null)
{
    if (empty($dir)) {
        $dir = __DIR__;
        $dir .= '/sub';
    }
    $catalog = opendir($dir);
    while ($filename = readdir($catalog)) {
        $filename = $dir . "/" . $filename;
        if (file_exists($filename) && is_file($filename)) {
            include_once($filename);
        }
    }
    closedir($catalog);
}

includeSub();

foreach (glob(ROOT . 'usr/modules/*/admin/function.php') as $listed) {
    if (file_exists($listed) && is_file($listed)) {
        include_once($listed);
    }
}


//функция вывода префикса
function module_perfix($module, $id = false, $org = false)
{
    $module_prefix = array();
    $module_prefix['sales'] = 'PN';
    $module_prefix['realization'] = 'RN';
    $module_prefix['supply'] = 'SN';
    $module_prefix['supply_order'] = 'OSN';
    $module_prefix['supply_logistics'] = 'LSN';
    $module_prefix['fcontrol'] = 'CTR';
    $module_prefix['storage_transfer'] = 'TR';
    $module_prefix['stock_transfer'] = 'TR';
    $module_prefix['object'] = 'OB';
    $module_prefix['receipts'] = 'PT';
    $module_prefix['fcontrol_storage'] = 'CTS';
    $module_prefix['price'] = 'CP';
    $module_prefix['calls'] = 'CS';
    $module_prefix['selection'] = 'SC';
    $module_prefix['sync'] = 'SB';
    $module_prefix['guestbook'] = 'GB';
    $module_prefix['categories'] = 'CT';
    $module_prefix['storage'] = 'ST';
    $module_prefix['assembly'] = 'AS';
    $module_prefix['boiler_bid'] = 'BB';
    $module_prefix['merging'] = 'ME';
    $module_prefix['correct'] = 'CO';
    $module_prefix['act_reconciliation'] = 'AR';
    $return = $module_prefix[$module];
    if ($id) {
        if ($org) {
            $cache = new \Boot\Services\Cache;
            $op = $cache->getItem("org_prefix");
            if ($op->isHit()) {
                $org_prefix = $op->get();
            } else {
                $org_prefix = [];
                foreach (\Organizations\Model\Org::all() as $item) {
                    $org_prefix[$item->id] = $item->prefix;
                }
                $op->set($org_prefix);
                $op->expiresAfter(43200);
                $cache->save($op);
            }

            // $org_prefix['1'] = 'AE';
            // $org_prefix['2'] = 'TT';
            // $org_prefix['5'] = 'AA';
            // $org_prefix['7'] = 'IN';
            // $org_prefix['8'] = 'VI';
            // $org_prefix['9'] = 'KL';
            // $org_prefix['10'] = 'PP';
            // $org_prefix['11'] = 'MM';
            $return = $org_prefix[$org] . $module_prefix[$module] . '-' . $id;
        } else {
            $return = $module_prefix[$module] . '-' . $id;
        }
    }
    return $return;
}


//Получение ндс организации
function get_nds($org = 1)
{
    $return = 0;
    if ($org == 1) {
        $return = 20;
    }
    return $return;
}


function createOrgByInn($inn)
{
    try {
        $org = \Organizations\Support\OrganizationsSupport::createOrgByInn($inn);
        return $org->id;
    } catch (\Exception $e) {
        return 0;
    }
    // global $core, $db;
    // $queryO = $db->query("SELECT * FROM ".DB_PREFIX."_organizations WHERE inn = '" . $db->safesql($inn) . "' LIMIT 1");
    // if($db->numRows($queryO) <= 0)
    // {
    //     $token = 'ddeafb9ff43e79578243d8f031f6e9d8b047ca0e';
    //     $url = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party?token='.$token.'&query='.$inn;
    //     $json = file_get_contents($url);
    //     $data = json_decode($json, true);
    //     if (isset($data['suggestions'][0]['data']))
    //     {
    //         foreach ($data['suggestions'][0]['data'] as $key1 => $value1)
    //         {
    //             $$key1 = $value1;
    //         }
    //         if (!empty($name['short_with_opf']))
    //         {
    //             $class_path = ROOT.'boot/sub_classes/dadata.class.php';
    //             if (file_exists($class_path))
    //             {
    //                 require_once($class_path);
    //             }

    //             if($type == "INDIVIDUAL"){
    //                 $myname = $data['suggestions'][0]['data']['name']['full'];
    //                 } else {
    //                 $myname = $management['name'];
    //             }
    //             $dadata = new dadata();
    //             $result = $dadata->clean('name', $myname);
    //             $db->query("INSERT INTO `" . DB_PREFIX . "_organizations` ( `id` , `name`, `name_full`,  `yaddress`, `maddress`, `inn`, `kpp`, `ogrn`, `post`, `director`, `director_detail`, `status`, `type`)  VALUES (NULL, '".$name['short_with_opf']."', '".$name['full_with_opf']."', '".$address['data']['source']."', '".$address['data']['source']."', '".$inn."', '".$kpp."', '".$ogrn."', '".$management['post']."', '".$db->safesql($myname)."', '" . $db->safesql(json_encode($result, JSON_UNESCAPED_UNICODE)) . "', '3', '".$db->safesql($type)."');");
    //             $oid = $db->insert_id();
    //         }
    //         else
    //         {
    //             $oid = '0';
    //         }
    // 		}
    //     else
    //     {
    //         $oid = '0';
    //     }

    // }
    // else
    // {
    //     $resultO = $db->getRow($queryO);
    //     $oid = $resultO['id'];
    // }
    // return $oid;
}

function createBankByBRSO($bik, $rschet, $oid)
{
    try {
        $bank = \Organizations\Support\OrganizationsSupport::createBankByBRSO($bik, $rschet, $oid);
        return $bank->id;
    } catch (\Exception $e) {
        return 0;
    }
    // global $core, $db;
    // $token = 'ddeafb9ff43e79578243d8f031f6e9d8b047ca0e';
    // if((isset($bik)&&$bik!="0"&&$oid!="0"))
    // {
    //     $queryO = $db->query("SELECT id FROM ".DB_PREFIX."_organizations_bank WHERE oid = '" . $db->safesql($oid) . "' AND  rschet = '" . $db->safesql($rschet) . "' AND  bik = '" . $db->safesql($bik) . "' LIMIT 1");
    //     if($db->numRows($queryO) <= 0)
    //     {
    //         $url_bank = 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/bank?token='.$token.'&query='.$bik;
    //         $json_bank = file_get_contents($url_bank);
    //         $data_bank = json_decode($json_bank, true);
    //         if (isset($data_bank['suggestions'][0]['data']))
    //         {
    //             foreach ($data_bank['suggestions'][0]['data'] as $key1 => $value1)
    //             {
    //                 $$key1 = $value1;
    //             }
    //             $rs = $rschet;
    //             $db->query("INSERT INTO `" . DB_PREFIX . "_organizations_bank` ( `id` , `oid`, `name_short`, `name`, `address`, `bik`, `kschet`, `rschet`,`status`) VALUES (NULL, '" . $db->safesql($oid) . "', '" . $db->safesql($name['payment']) . "', '" . $db->safesql($name['full']) . "', '" . $db->safesql($address['value']) . "', '" . $db->safesql($bic) . "', '" . $db->safesql($correspondent_account) . "', '" . $db->safesql($rs) . "','1');"	);
    //             $bid = $db->insert_id();
    //         }
    //         else
    //         {
    //             $bid = '0';
    //         }
    //     }
    //     else
    //     {
    //         $resultO = $db->getRow($queryO);
    //         $bid = $resultO['id'];
    //     }
    // }
    // else
    // {
    //     $bid = '0';
    // }
    // return $bid;
}

function sclon($inn)
{
    global $core, $db;
    $queryO = $db->query("SELECT * FROM " . DB_PREFIX . "_organizations WHERE inn = '" . $inn . "' LIMIT 1");
    if ($db->numRows($queryO) <= 0) {
        $token = 'ddeafb9ff43e79578243d8f031f6e9d8b047ca0e';
        $url = 'https://dadata.ru/api/v2/clean/name?token=' . $token . '&query=' . $inn;
        $json = file_get_contents($url);
        $data = json_decode($json, true);
        foreach ($data['suggestions'][0]['data'] as $key1 => $value1) {
            $$key1 = $value1;
        }
        if (!empty($name['short_with_opf'])) {
            $db->query("INSERT INTO `" . DB_PREFIX . "_organizations` ( `id` , `name`, `name_full`,  `yaddress`, `maddress`, `inn`, `kpp`, `ogrn`, `post`, `director`, `status`)  VALUES (NULL, '" . $name['short_with_opf'] . "', '" . $name['full_with_opf'] . "', '" . $address['data']['source'] . "', '" . $address['data']['source'] . "', '" . $inn . "', '" . $kpp . "', '" . $ogrn . "', '" . $management['post'] . "', '" . $management['name'] . "','3');");
        }
        $oid = $db->insert_id();
    } else {
        $resultO = $db->getRow($queryO);
        $oid = $resultO['id'];
    }
    return $oid;
}


function shortUserName($uid, $name = false, $surname = false, $och = false)
{
    global $core, $db;
    if ($name == false) {
        if ($uid && $uid != 0) {
            $user = \Boot\Support\Facades\Cache::remember('user.' . $uid, 86400, function () use ($uid) {
                return \Boot\Models\Users::find($uid);
            });

            if ($user) return $user->short_user_name;
        }
        return 'Не указан';
    } else {
        $result = $name . ' ';
        $result .= mb_substr($surname, 0, 1) . '. ';
        $result .= $och != null ? mb_substr($och, 0, 1) . '.' : '';
        return $result;
    }
}

function fullUserName($uid)
{
    if ($uid && $uid != 0) {
        $user = \Boot\Models\Users::find($uid);
        if ($user) return $user->full_user_name;
    }
    return 'Не указан';
}

function getOrgInfo($id, $format = 'array')
{
    $cache_name = "org_info_{$id}";
    if (\Boot\Support\Cache::has($cache_name)) {
        $result = \Boot\Support\Cache::get($cache_name);
    } else {
        $result = [];
        $org = \Organizations\Model\Org::findOrFail($id);
        $organizations = $org->organization()->first();

        //поиск банка, если не указан по умолчанию -> берем первый
        $organizations_bank = $organizations->banks();
        if (!empty($organizations->organizations_bank_default)) {
            $organizations_bank = $organizations_bank->where("id", $organizations->organizations_bank_default);
        }
        $organizations_bank = $organizations_bank->first();

        $result['org'] = $org->toArray();
        $result['organizations'] = $organizations->toArray();
        $result['organizations_bank'] = $organizations_bank->toArray();
        \Boot\Support\Cache::set($cache_name, $result);
    }
    switch ($format) {
        default:
        case "array":
            return $result;
            break;
        case "json":
            return json_encode($result, JSON_UNESCAPED_UNICODE);
            break;
        case "object":
            return json_decode(json_encode($result, JSON_UNESCAPED_UNICODE));
            break;
    }
}

function relationsForm()
{
    global $core, $db, $adminTpl;
    $mod_array = array();
    $mod_array['object'] = 'Объекты';
    $mod_array['calls'] = 'Звонки';
    $mod_array['sales'] = 'Продажи';
    $mod_array['supply'] = 'Заявки на закупку';

    $result = '<div class="form-group">
    <label for="r_mod"  class="col-lg-3 control-label">Тип элемента</label>
    <div class="col-lg-3">
    <label class="field select">
    <select class="form-control" name="r_mod" id="r_mod">';
    foreach ($mod_array as $key => $value) {
        $result .= '<option value="' . $key . '"  id="r_' . $key . '">' . $value . '</option>';
    }
    $result .= '</select><i class="arrow double"></i></label></div></div>';

    $result .= '<div class="r_container">';
    foreach ($mod_array as $key => $value) {
        switch ($key) {
            default:
                // Возможно то же div2
                $result .= '<div class="r_' . $key . '" style="display:none;">Произошла ошибка обратитесь к администрации</div>';
                break;

            case "object":
                $result .= '<div2 class="r_' . $key . ' form-group" style="display:none;">
                    <label class="col-lg-3 control-label">&nbsp;</label>';
                // $query = $db->query("SELECT id, name FROM ".DB_PREFIX."_object ORDER BY name ASC");
                // if($db->numRows($query) > 0)
                // {
                // 	$result .= '<div class="form-group">
                //     <label for="r_pid"  class="col-lg-3 control-label">Выберите объект</label>
                //     <div class="col-lg-3">
                //     <label for="r_pid" class="field prepend-icon">
                //     <select name="r_'.$key.'_pid"  class="select2-single form-control">';
                //     while($object = $db->getRow($query))
                //     {
                //         $result .= '<option value="'.$object['id'].'" >'.$object['name'].'</option>';
                //     }
                //     $result .='	</select>
                //     <label for="r_pid" class="field-icon"><i class="fa fa-briefcase"></i></label>
                //     </label>
                //     </div>
                //     </div>	';
                // }
                $select_setting = [
                    "mod" => "jsAdminTpl",
                    "name" => "r_{$key}_pid",
                    "ajaxUrl" => "getObjectToSelect",
                    "return" => true,
                ];
                oSelectCustom($select_setting);
                $select_setting["mod"] = "select";
                $result .= "<div class=\"col-lg-3\">";
                $result .= oSelectCustom($select_setting);
                $result .= "</div>";
                $result .= '</div2>';
                break;

            case "sales":
                $result .= '<div2 class="r_' . $key . '" style="display:none;">';
                $query = $db->query("SELECT id, date FROM " . DB_PREFIX . "_sales ORDER BY date ASC");
                if ($db->numRows($query) > 0) {
                    $result .= '<div class="form-group">
                <label for="r_pid"  class="col-lg-3 control-label">Выберите продажу</label>
                <div class="col-lg-3">
                <label for="r_pid" class="field prepend-icon">
                <select name="r_' . $key . '_pid"  class="select2-single form-control">';
                    while ($object = $db->getRow($query)) {
                        $result .= '<option value="' . $object['id'] . '" >PN-' . $object['id'] . ' от ' . $object['date'] . '</option>';
                    }
                    $result .= '</select><label for="r_pid" class="field-icon"><i class="fa fa-briefcase"></i></label></label></div></div>';
                }
                $result .= '</div2>';
                break;

            case "calls":
                $queryP = $db->query("SELECT phoneid FROM " . DB_PREFIX . "_users_phoneid WHERE uid = '" . $core->auth->user_info['id'] . "' LIMIT 1");
                $resultP = $db->getRow($queryP);
                $phone = empty($resultP['phoneid']) ? 0 : $resultP['phoneid'];
                $result .= '<div2 class="r_' . $key . '" style="display:none;">';
                $query = $db->query("SELECT * FROM " . DB_PREFIX . "_calls WHERE src = '" . $phone . "' OR dst = '" . $phone . "' ORDER BY calldate ASC");
                if ($db->numRows($query) > 0) {
                    $result .= '<div class="form-group">
                <label for="r_pid"  class="col-lg-3 control-label">Выберите звонок</label>
                <div class="col-lg-3">
                <label for="r_pid" class="field prepend-icon">
                <select name="r_' . $key . '_pid" class="select2-single form-control">';
                    while ($object = $db->getRow($query)) {
                        $result .= '<option value="' . $object['id'] . '" >' . $object['src'] . ' -> ' . $object['dst'] . '</option>';
                    }
                    $result .= '	</select>
                <label for="r_pid" class="field-icon"><i class="fa fa-phone-square"></i></label>
                </label>
                </div>
                </div>	';
                }
                $result .= '</div2>';
                break;
        }
    }
    $result .= '</div>';
    $adminTpl->js_code[] = "$('.r_object').show();
    $('#r_mod').change(function(){
    $('.r_container').find('div2').hide();
    var selected = $('#r_mod option:selected').attr('id');
    $('.' + selected).show();
    });";

    return $result;
}

function relationsSave($module_in, $pid_in, $module_out = false, $pid_out = false)
{
    $relation = new \Relations;
    if ($module_out == false) {
        $relation->add([
            "module_in" => $module_in,
            "pid_in" => $pid_in,
            "module_out" => $_POST['r_mod'],
            "pid_out" => $_POST['r_' . $_POST['r_mod'] . '_pid'],
        ]);
    } else {
        $relation->add([
            "module_in" => $module_in,
            "pid_in" => $pid_in,
            "module_out" => $module_out,
            "pid_out" => $pid_out,
        ]);
    }
    return true;
}

function relationsDelete($module_in, $pid_in, $module_out, $pid_out)
{
    $relation = new \Relations;
    $relation->delete([
        "module_in" => $module_in,
        "pid_in" => $pid_in,
        "module_out" => $module_out,
        "pid_out" => $pid_out,
    ]);
    return true;
}

function relationsFind($data = ["module_in" => "", "pid_in" => "", "module_out" => "", "pid_out" => "",])
{
    $relation = new \Relations;
    return $relation->find($data);
}

function orderShow($matches)
{
    global $url;
    $orderArray = explode(',', $matches[1]);
    $tag = $matches[2];
    $content = $matches[3];
    if (isset($url[1]) && in_array($url[1], $orderArray)) {

        if ($tag == 1) return stripslashes($content);
    } else {
        if ($tag == 0) return stripslashes($content);
    }
}


use \Boot\Support\Str;

function num2str($num)
{
    return Str::numbToString($num);
}


function presetRanges()
{
    $return = array(
        array('text' => 'За 2016 год', 'dateStart' => '2016-01-01', 'dateEnd' => '2016-12-31'),
        array('text' => 'За 2017 год', 'dateStart' => '2017-01-01', 'dateEnd' => '2017-12-31'),
        array('text' => 'За 2018 год', 'dateStart' => '2018-01-01', 'dateEnd' => '2018-12-31'),
        array('text' => 'За 2019 год', 'dateStart' => '2019-01-01', 'dateEnd' => '2019-12-31'),
    );
    return $return;
}

function load_grid($grid_name = 'jqSimple', $lnn = 'Простая таблица', $atribut = array(), $grid_path = ROOT . '/root/modules/grid/' . DIRECTORY_SEPARATOR)
{
    global $adminTpl, $config, $core, $admin_conf, $db;
    require ROOT . 'etc/db.config.php';
    $_CONFIG = array(
        'root_path' => ROOT . '/boot/sub_classes/phpgrid/',
        'encoding' => 'utf-8',
        'pdo_dsn' => 'mysql:dbname=' . $dbname . ';host=' . $dbhost . ';port=' . $dbport . ';',
        'pdo_user' => $dbuser,
        'pdo_pass' => $dbpass,
    );
    require_once($_CONFIG['root_path'] . 'jqGridLoader.php');
    $jq_loader = new jqGridLoader();
    $jq_loader->set('grid_path', $grid_path);
    $jq_loader->set('grid_path', $grid_path);
    $jq_loader->table = "JMY_catalog";
    $jq_loader->set('pdo_dsn', $_CONFIG['pdo_dsn']);
    $jq_loader->set('pdo_user', $_CONFIG['pdo_user']);
    $jq_loader->set('pdo_pass', $_CONFIG['pdo_pass']);
    $jq_loader->set('debug_output', true);
    $jq_loader->addInitQuery("SET NAMES 'utf8'");
    $jq_loader->autorun();
    $grid = isset($_REQUEST['render']) ? $_REQUEST['render'] : $grid_name;
    $grid = preg_replace('#[^a-zA-Z0-9_-]#', '', $grid);
    $rendered_grid = $jq_loader->render($grid);
    preg_match('#<script>(.+?)</script>#is', $rendered_grid . '</script>', $m);
    $rg = str_replace("<script>", "", $m[0]);
    $rg = str_replace("</script>", "", $rg);
    // $rg = str_replace("?jqgrid", "&jqgrid", $rg);
    $opts = '';
    $adminTpl->footIncludes[] = "<script src='" . PLUGINS . "js/js.cookie.js'></script>";
    $js_code = isset($atribut['js']) ? $atribut['js'] : '';
    $pag = 'onPaging: function(pgButton){
    let myPage = $("#' . $grid_name . '").getGridParam();
    Cookies.remove("page", {path: "/administration/' . $atribut['module'] . '/' . $atribut['module_name'] . '"});
    Cookies.remove("rowNum", {path: "/administration/' . $atribut['module'] . '/' . $atribut['module_name'] . '"});
    if(myPage.page != null){
    Cookies.set("page", myPage.page, {path: "/administration/' . $atribut['module'] . '/' . $atribut['module_name'] . '"});
    }
    if(myPage.rowNum != null){
    Cookies.set("rowNum", myPage.rowNum, {path: "/administration/' . $atribut['module'] . '/' . $atribut['module_name'] . '"});
    }
    },';
    if ($atribut['opts'] == 'open') {
        $opts = 'var opts = {
        ' . $pag . '
        \'ondblClickRow\':function (id) {
			var winRef;
			//winRef = window.open("", "winPopup-' . $atribut['module_name'] . '-' . $atribut['module_open'] . '-"+id);
			if(typeof(winRef) == \'undefined\' || winRef.closed)
			 {

				var url = \'' . Config::get('global.url') . '/administration/' . $atribut['module'] . '/' . $atribut['module_name'] . '/' . $atribut['module_open'] . '/\'+id;
				winRef = window.open(\'\', "winPopup-' . $atribut['module_name'] . '-' . $atribut['module_open'] . '-"+id);
				if(winRef == null || winRef.document.location.href != url)
				{
				  winRef = window.open(url, "winPopup-' . $atribut['module_name'] . '-' . $atribut['module_open'] . '-"+id);
				}
			  }
			  else
			  {
				//give it focus (in case it got burried)
				winRef.focus();
			  }

			console.log(winRef);


		//var a = window.open(\'' . Config::get('global.url') . '/administration/' . $atribut['module'] . '/' . $atribut['module_name'] . '/' . $atribut['module_open'] . '/\'+id, id);
		//a.focus();

        }
		};';
    }
    if ($atribut['opts'] == 'modal') {
        $opts = 'var opts = {
        ' . $pag . '
        \'ondblClickRow\':function (id) {
        $jqCols.jqGrid(\'extLoading\', true);
        var uurl;
        uurl = $(this).jqGrid(\'getGridParam\', \'url\');
        uurl.replace("?","&");

        $.get(window.location.href+uurl,
        {
        \'oper\':\'dialog\',
        \'id\':id
        }, function (ret) {
        var $cont = $(\'<DIV>\');
        $cont.dialog({
        \'modal\':true,
        \'width\':520,
        \'height\':280,
        \'title\':\'Информация по договору:\',
        \'resizable\':false,
        \'close\':function (event, ui) {
        $(\'.ui-dialog-content\').dialog(\'destroy\');
        $cont.remove();
        }
        });
        $cont.html(\'<div id="at">Загрузка информации...</div>\');
        ajaxGet(\'/administration/reestr/ajax/attach/\'+id, \'at\');
        $jqCols.jqGrid(\'extLoading\', false);
        });
        }
		};';
    } elseif ($atribut['opts'] == 'fastedit') {

        $opts = 'var opts = {
        ' . $pag . '
        \'ondblClickRow\': function(id) {

        var row_id = $("' . $grid_name . '").getGridParam(\'selrow\');
        jQuery(\'' . $grid_name . '\').editRow(row_id, true);

        }


		};';
    }

    $module = empty($atribut['module']) ? "" : $atribut['module'] . "/";
    $module_name = empty($atribut['module_name']) ? "" : $atribut['module_name'];
    $path = "/administration/" . $module . $module_name;
    $safe = '

    let mas = Cookies.get();
    let ori = [];
    ori["postData"] = [];

    for(let key in mas){
    if(key.slice(0,3) == "gs_"){
    if(mas[key] != ""){
    $("#"+key).val(mas[key]);
    ori["postData"][key.slice(3)] = mas[key];
    $("#' . $grid_name . '").jqGrid(\'setGridParam\', ori);
    }
    }
    }

    let getPage = 1;
    if(Cookies.get("page")){
    getPage = Cookies.get("page");
    }

    setTimeout(function () {$(\'#' . $grid_name . '\').trigger(\'reloadGrid\',[{page: getPage}]);}, 800);

    $("[id^=gs_]").on("input", function(){
    let name = $(this).attr("id");
    let val = $(this).val();
    Cookies.remove(name, {path: "' . $path . '"});
    if(val != ""){
    Cookies.set(name, val, {path: "' . $path . '"});
    }
    });

    $("[id^=gs_date]").attr("autocomplete", "off");


    ';


    $adminTpl->js_code[] = '

    let myRow = 30;
    if(Cookies.get("rowNum")){
    myRow = Cookies.get("rowNum");
    }
    $.extend($.jgrid.defaults,
    {
    hidegrid:false,
    hoverrows:true,
    viewrecords:true,
    scrollOffset:21,
    autowidth: true,
    rowNum: myRow,
    rowList:[30,50,100],
    height:490,
    cellEdit : true,
    editurl: \'clientArray\',

    multipleSearch: true

    });



    $.jgrid.nav.refreshtext = \'Обновить\';
    $.jgrid.formatter.date.newformat = \'ISO8601Short\';
    $.jgrid.edit.closeAfterEdit = true;
    $.jgrid.edit.closeAfterAdd = true;
    ' . $opts . '



    function dateRangePicker_onChange() {
    var $input = $(\'#gs_date_view\');
    var old_val = $input.val();

    setTimeout(function () {
    if ($input.val() == old_val) {
    $grid[0].triggerToolbar();

    }
    }, 50);

    }

    ' . $rg . $js_code . $safe . '











	$grid.navButtonAdd(pager, { caption:"Column chooser", buttonicon:"ui-icon-newwin", onClickButton:function () {
        $grid.jqGrid(\'columnChooser\', {\'done\':function (perm) {
            this.jqGrid("remapColumns", perm, true);
        }});
    }});
	';
    echo '	<link rel="stylesheet" type="text/css" href="' . Config::get('global.url') . '/usr/tpl/corp/assets/css/ui.jqgrid-bootstrap.css">
	<link rel="stylesheet" type="text/css" href="' . Config::get('global.url') . '/usr/tpl/corp/assets/css/jqgrid-ext.css"><div id="content" class="animated fadeIn">';
    require ROOT . '/root/modules/grid/templates/_layout.php';
    echo '<div id="' . $grid . '_p"></div>
    <div class="panel panel-dark panel-border top">
    <div class="panel-heading"><span class="panel-title">' . $lnn . ':</span>
    ';
    if (isset($atribut['button_url'])) {
        echo '<div class="widget-menu pull-right">

        <div class="btn-group">
        <button onclick="' . (isset($atribut['button_onclick']) ? $atribut['button_onclick'] : 'location.href = \'' . $atribut['button_url'] . '\'') . ' " type="button" class="btn btn-xs btn-success btn-block">' . $atribut['button'] . '</button>
        </div>
        </div>';
    }

    echo '
    </div>
    <div class="panel-body pn table-responsive">
    <form id="table_form_' . $grid . '">
    <table id="' . $grid . '"  class="table ' . (isset($atribut['table_class']) ? $atribut['table_class'] : 'table-striped table-hover') . '" style="min-width: 800px;"></table>
    </form>
    </div>
    </div> </div>';


    $adminTpl->footIncludes[''] = '



    <!--jQuery UI-->
    <style>
    .ui-widget-content { border: 0px;}
    .ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default, .ui-button, html .ui-button.ui-state-disabled:hover, html .ui-button.ui-state-disabled:active { border: 0px;}
    </style>

    <script src="/usr/tpl/corp/assets/js/jquery-migrate-121.min.js"></script>
    <script src="/usr/plugins/phpgrid/client/ui.multiselect.js"></script>
    <link href="/usr/plugins/phpgrid/client/ui.multiselect.css" rel="stylesheet" type="text/css"/>
    <script src="/usr/tpl/corp/assets/js/daterangepicker.jQuery.compressed.js"></script>
    <script src="/usr/plugins/phpgrid/client/grid.locale-en.js"></script>
    <script src="/usr/plugins/phpgrid/client/datepicker-ru.js"></script>
    <script>$.datepicker.setDefaults( $.datepicker.regional[ "ru" ] );</script>
	<link href="/usr/tpl/corp/assets/css/ui.daterangepicker.css" rel="stylesheet" type="text/css"/>
    <!--jqGrid-->
    <link href="{ADM_THEME}/assets/css/ui.jqgrid-bootstrap-ui.css" rel="stylesheet" type="text/css"/>

	<script src="/usr/plugins/phpgrid/client/jquery.jqGrid.min.js"></script>



    <!--jqGrid Extension-->
    <link href="/usr/plugins/phpgrid/client/jqgrid-ext.css" rel="stylesheet" type="text/css"/>
    <script src="/usr/plugins/phpgrid/client/jqgrid-ext.js"></script>

    <!-- Other plugins -->
    <script src="https://yandex.st/jquery/form/2.67/jquery.form.min.js"></script>


    <!-- Code highlighter -->
    <script src="https://yandex.st/highlightjs/6.0/highlight.min.js"></script>
    <link href="https://yandex.st/highlightjs/6.0/styles/vs.css" rel="stylesheet" type="text/css"/>		';
}


function loadGrid($grid_name = 'jqSimple', $lnn = 'Простая таблица', $atribut = array(), $grid_path = ROOT . '/root/modules/grid/' . DIRECTORY_SEPARATOR)
{
    global $adminTpl, $config, $core, $admin_conf, $db;
    require ROOT . 'etc/db.config.php';
    $return = array();
    $_CONFIG = array(
        'root_path' => ROOT . '/boot/sub_classes/phpgrid/',
        'encoding' => 'utf-8',
        'pdo_dsn' => 'mysql:dbname=' . $dbname . ';host=' . $dbhost . ';port=' . $dbport . ';',
        'pdo_user' => $dbuser,
        'pdo_pass' => $dbpass,
        'client' => PLUGINS . '/phpgrid/client/vendor',
    );
    require_once($_CONFIG['root_path'] . 'jqGridLoader.php');
    $jq_loader = new jqGridLoader();
    $jq_loader->set('grid_path', $grid_path);
    $jq_loader->set('pdo_dsn', $_CONFIG['pdo_dsn']);
    $jq_loader->set('pdo_user', $_CONFIG['pdo_user']);
    $jq_loader->set('pdo_pass', $_CONFIG['pdo_pass']);
    $jq_loader->set('debug_output', true);
    $jq_loader->set('input_grid', 'jqgrid');
    $jq_loader->set('input_patch', $grid_path);
    $jq_loader->set('input_url', '/grid');
    $jq_loader->set('input_module', (isset($atribut['module_name']) ? $atribut['module_name'] : ''));
    $jq_loader->set('clear', true);
    $jq_loader->addInitQuery("SET NAMES 'utf8'");
    $jq_loader->autorun();
    $grid = isset($_REQUEST['render']) ? $_REQUEST['render'] : $grid_name;
    $grid = preg_replace('#[^a-zA-Z0-9_-]#', '', $grid);
    $rendered_grid = $jq_loader->render($grid);
    $return['grid'] = $rendered_grid;
    $return['javascript'] = array();
    $return['css'] = '';
    #Библиотека jQuery необходима для работы jqGrid
    #$return['javascript']['jQuery'] = '<script src="'.$_CONFIG['client'].'/js/jquery.min.js"></script>';
    #Библиотека jQuery UI
    #$return['javascript']['jQueryUI'] = '<script src="'.$_CONFIG['client'].'/js/jquery-ui.min.js"></script>';
    #Плагин Multiselect для jQuery UI необходим для выбора колонок
    // $return['javascript']['multiselect'] = '<script src="'.$_CONFIG['client'].'/js/ui.multiselect.js"></script>';
    $return['javascript']['multiselect'] = '<script src="' . $_CONFIG['client'] . '/jquery-ui-multiselect-widget/src/jquery.multiselect.js"></script>';
    #Плагин DateRangePicker для выбора даты
    $return['javascript']['daterangepicker'] = '<script src="' . $_CONFIG['client'] . '/js/daterangepicker.js"></script>';
    #Библиотека jqGrid
    $return['javascript']['jqGrid'] = '<script src="' . $_CONFIG['client'] . '/js/trirand/jquery.jqGrid.min.js"></script>';

    #Язык для  библиотеки jqGrid
    $return['javascript']['jqGridLocale'] = '<script src="' . $_CONFIG['client'] . '/js/trirand/i18n/grid.locale-ru.js"></script>';
    #Расширение библиотеки jqGrid для работы с phpGrid
    $return['javascript']['jqGridExt'] = '<script src="' . $_CONFIG['client'] . '/js/jquery.jqGrid.ext.js"></script>';
    $return['javascript']['context'] = '<script src="' . $_CONFIG['client'] . '/js/context-menu.js"></script>';
    #Стиль для плагина multiselect
    // $return['css'] .= '<link rel="stylesheet" type="text/css" media="screen" href="'.$_CONFIG['client'].'/css/ui.multiselect.css" />';
    $return['css'] .= '<link rel="stylesheet" type="text/css" media="screen" href="' . $_CONFIG['client'] . '/jquery-ui-multiselect-widget/css/jquery.multiselect.css" />';
    #Стиль для jqGrid
    $return['css'] .= '<link rel="stylesheet" type="text/css" media="screen" href="' . $_CONFIG['client'] . '/css/trirand/ui.jqgrid-bootstrap.css" />';
    $return['css'] .= '<link href="/usr/tpl/corp/assets/css/ui.daterangepicker.css" rel="stylesheet" type="text/css"/>';
    $return['css'] .= '<link href="/usr/plugins/phpgrid/client/jqgrid-ext.css" rel="stylesheet" type="text/css"/>';
    #Подгружаем кастомный js код из атрибутов
    $js_code = isset($atribut['js']) ? $atribut['js'] : '';
    #Формируем дополнительные настройки
    $opts = "var opts = {";
    $opts .= "'sortable':true,";
    #действие при двойном клике
    switch ($atribut['opts']) {
            #открытие нового окна с элементом
        case "open":
            $opts .= '
				\'ondblClickRow\':function (id) {
					var winRef;
					//winRef = window.open("", "winPopup-' . $atribut['module_name'] . '-' . $atribut['module_open'] . '-"+id);
					if(typeof(winRef) == \'undefined\' || winRef.closed)
					 {
						var url = \'' . Config::get('global.url') . '/administration/' . $atribut['module'] . '/' . $atribut['module_name'] . '/' . $atribut['module_open'] . '/\'+id;
						winRef = window.open(\'\', "winPopup-' . $atribut['module_name'] . '-' . $atribut['module_open'] . '-"+id);
						if(winRef == null || winRef.document.location.href != url)
						{
						  winRef = window.open(url, "winPopup-' . $atribut['module_name'] . '-' . $atribut['module_open'] . '-"+id);
						}
					  }
					  else
					  {
						//give it focus (in case it got burried)
						winRef.focus();
					  }
					console.log(winRef);
				//var a = window.open(\'' . Config::get('global.url') . '/administration/' . $atribut['module'] . '/' . $atribut['module_name'] . '/' . $atribut['module_open'] . '/\'+id, id);
				//a.focus();
				},';
            break;
            #быстрое редактирование элемента
        case "fastedit":
            $opts .= '
			\'ondblClickRow\': function(id) {
			var row_id = $("' . $grid_name . '").getGridParam(\'selrow\');
			jQuery(\'' . $grid_name . '\').editRow(row_id, true);
			},';
            break;
    }
    $opts .= '
	//onSortCol: loadGrid,
	//resizeStop: saveGrid,
	 gridComplete: loadGrid,
	};';
    $module = empty($atribut['module']) ? "" : $atribut['module'] . "/";
    $module_name = empty($atribut['module_name']) ? "" : $atribut['module_name'];
    $path = "/administration/" . $module . $module_name;
    $caption = '<div style="display: inline-block; top: 13px; position: absolute;">' . $lnn . ':</div><div style="right: 0px; display: inline-block;" class="widget-menu pull-right"></div>';
    if (isset($atribut['button_url'])) {
        $caption = '<div style="display: inline-block; top: 13px; position: absolute;">' . $lnn . ':</div><div style="right: 6px; top: 6px; display: inline-block;  position: absolute;" class="widget-menu pull-right"> <button onclick="' . (isset($atribut['button_onclick']) ? $atribut['button_onclick'] : 'location.href = \'' . $atribut['button_url'] . '\'') . '" type="button" class="btn btn-xs btn-success btn-block">' . $atribut['button'] . '</button></div>';
    }
    $return['js'] = '
    <script type="text/javascript">
		var loadFirst = 0;
		$(window).on("unload", function () {
			saveGrid();
        });
        $("#' . $grid_name . '").on("destroy", function () {
           saveGrid();
        })


        function initGrid() {
            $(this).contextMenu(\'contextMenu\', {
				menuStyle :{
					width : "150px"
				},
                bindings: {
                    \'edit\': function (t) {
                        alert("Edit Row Command Selected");
                    },
                    \'add\': function (t) {
                        alert("Add Row Command Selected");
                    },
                    \'del\': function (t) {



					 removeModal(1);


                    }
                },
                onContextMenu: function (event, menu) {
                    var rowId = $(event.target).parent("tr").attr("id")

                   $("#' . $grid_name . '").setSelection(rowId);

                    return true;
                }
            });

        }
   var lastSelection;
		  function editRow(id) {
                if (id && id !== lastSelection) {
                    var grid = $("#' . $grid_name . '");
                    grid.jqGrid(\'restoreRow\',lastSelection);
                    grid.jqGrid(\'editRow\',id, {keys: true} );
                    lastSelection = id;
                }
            }
		function loadGrid()
		{
			if(loadFirst == 0)
			{
				loadFirst++;
				if( $.jgrid.isGridInStorage("' . $grid_name . '", {storageType:\'localStorage\'}) )
				{
					const options = {
						useStorage : true,
						storageType : "localStorage",
						clearAfterLoad: false,
						beforeSetGrid : null,
						afterSetGrid : null,
						decompression: false,
						decompressionModule :  "LZString",
						decompressionMethod : "decompressFromUTF16",
						restoreData : false
					};
					setTimeout(function () {
					//$.jgrid.loadState("' . $grid_name . '", "", options);
					//$("#idGrid").clearGridData();
					//$("#' . $grid_name . '").trigger("reloadGrid");
					//initGrid();
					 $("#' . $grid_name . '").jqGrid("bindKeys");




					}, 300);
				}
				else
				{
				  console.log(\'grid is not in session storage\');
				}

			}

		};


		function saveGrid()
		{
			if(loadFirst != 0)
			{
				var	options = {
				  useStorage : true,
				  storageType : "localStorage",
				  beforeSetItem : null,
				  compression: false,
				  compressionModule :  \'LZString\',
				  compressionMethod : \'compressToUTF16\',
				  debug : false,
				  saveData : false
				}
				$.jgrid.saveState("' . $grid_name . '", options);
			}
			else
			{
				if( $.jgrid.isGridInStorage("' . $grid_name . '", {storageType:\'localStorage\'}) )
				{
					var options = {
						useStorage : true,
						storageType : "localStorage",
						clearAfterLoad: false,
						beforeSetGrid : null,
						afterSetGrid : null,
						decompression: false,
						decompressionModule :  \'LZString\',
						decompressionMethod : \'decompressFromUTF16\',
						restoreData : true
					}
					$.jgrid.loadState("' . $grid_name . '", false, options);
					//$("#idGrid").clearGridData();
					$("#' . $grid_name . '").trigger("reloadGrid");
				}
				else
				{
				  alert(\'grid is not in session storage\');
				}

			}
			loadFirst++;
			console.log(loadFirst);
		};
		let myRow = 30;
		if(Cookies.get("rowNum")){
		myRow = Cookies.get("rowNum");
		}
		$.extend($.jgrid.defaults,
		{
			caption: `' . $caption . '`,
			hidegrid:false,
			hoverrows:true,
			colMenu : true,
			viewrecords:true,
			scrollOffset:21,
			autowidth: true,
			rowNum: myRow,
			rowList:[30,50,100],
			height:490,
			responsive: true,
			treeGrid_bigData: true,
			styleUI: "Bootstrap",
			hidegrid: false,
			cellEdit : false,
			editurl: \'clientArray\',
			scrollPopUp:true,
			scrollLeftOffset: "83%",
			viewrecords: true,
			scroll: 1, // set the scroll property to 1 to enable paging with scrollbar - virtual loading of records
			emptyrecords: "Scroll to bottom to retrieve new page", // the message will be displayed at the bottom
			storeNavOptions : true,
			pager: "#jqGridPager",
			multipleSearch: true
		});

		' . $opts . '
		' . $js_code . '
	</script>';
    echo '<div id="content" class="animated fadeIn">
			<div id="' . $grid . '_p"></div>
			<div class="row">
				<form id="table_form_' . $grid . '">
					<table id="' . $grid . '"  class="table ' . (isset($atribut['table_class']) ? $atribut['table_class'] : 'table-striped table-hover') . '" style="min-width: 800px;"></table>
				</form>
			</div>
		</div>';
    if (isset($atribut['remove']['enable']) && $atribut['remove']['enable'] == true) {

        echo '<div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="removeTitle" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="removeForm">
				<div class="modal-header">
					<h5 class="modal-title" id="removeTitle">' . $atribut['remove']['title'] . '</h5>
				</div>
				<div class="modal-body">
					<p>' . $atribut['remove']['text'] . '</p>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="removeId" id="removeId"  value="0"/>
					<input type="hidden" name="removeRowId" id="removeRowId"  value="0"/>
					<button type="submit" class="btn btn-danger">Удалить</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
function removeModal(id)
		{
			setTimeout(function ()
			{
				let rowID =  $grid.jqGrid(\'getGridParam\', \'selrow\');
				$(\'#removeRowId\').val(rowID);
			}, 300);
			$(\'#removeId\').val(id);
			$(\'#removeModal\').modal(\'show\');
		}
		$("#removeForm").submit(function (e) {
			e.preventDefault();
			var data = $("#removeForm").serializeArray();
			$.ajax({
				type: "POST",
				url: "/ajax.php?do=' . $atribut['remove']['ajax'] . '",
				data: data,
				success: function(response) {
					result = jQuery.parseJSON(response);
					if(result["type"] == \'success\')
					{
						$grid.jqGrid(\'delRowData\',result["data"]["rowID"]);
					}
					$(\'#removeModal\').modal(\'hide\');
					$(\'#removeForm\')[0].reset();
					Swal.fire(
						result["title"],
						result["text"],
						result["type"]
					);
				},
				error: function(response) {
					alert("no");
				}
			});
		});
</script>

		';
    }

    if (isset($atribut['restore']['enable']) && $atribut['restore']['enable'] == true) {

        echo '<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog" aria-labelledby="removeTitle" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="resoreForm">
				<div class="modal-header">
					<h5 class="modal-title" id="removeTitle">' . $atribut['restore']['title'] . '</h5>
				</div>
				<div class="modal-body">
					<p>' . $atribut['restore']['text'] . '</p>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="restoreId" id="restoreId"  value="0"/>
					<input type="hidden" name="restoreRowId" id="restoreRowId"  value="0"/>
					<button type="submit" class="btn btn-warning">Восстановить</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
function restoreModal(id)
		{
			setTimeout(function ()
			{
				let rowID =  $grid.jqGrid(\'getGridParam\', \'selrow\');
				$(\'#restoreRowId\').val(rowID);
			}, 300);
			$(\'#restoreId\').val(id);
			$(\'#restoreModal\').modal(\'show\');
		}
		$("#resoreForm").submit(function (e) {
			e.preventDefault();
			var data = $("#resoreForm").serializeArray();
			$.ajax({
				type: "POST",
				url: "/ajax.php?do=' . $atribut['restore']['ajax'] . '",
				data: data,
				success: function(response) {
					result = jQuery.parseJSON(response);
					if(result["type"] == \'success\')
					{
						$grid.jqGrid(\'delRowData\',result["data"]["rowID"]);
					}
					$(\'#restoreModal\').modal(\'hide\');
					$(\'#resoreForm\')[0].reset();
					Swal.fire(
						result["title"],
						result["text"],
						result["type"]
					);
				},
				error: function(response) {
					alert("no");
				}
			});
		});
</script>

		';
    }


    echo $return['css'];
    echo implode(" ", $return['javascript']);
    echo '<script type="text/javascript">
		 function dateRangePicker_onChange() {
			var $input = $(\'#gs_date_view\');
			var old_val = $input.val();

				setTimeout(function () {
				if ($input.val() == old_val) {
				$grid[0].triggerToolbar();

				}
				}, 50);

			}
			</script>';
    echo $return['js'];
    echo $return['grid'];
    echo '<script type="text/javascript">



				$("[id^=gs_date]").attr("autocomplete", "off");

				</script>
    <div class="contextMenu" id="contextMenu" style="display:none; width:400px;">
        <ul style="width: 400px; font-size: 65%;">
            <li id="add">
                <span class="ui-icon ui-icon-plus" style="float:left"></span>
                <span style="font-size:130%; font-family:Verdana">Add Row</span>
            </li>
            <li id="edit">
                <span class="ui-icon ui-icon-pencil" style="float:left"></span>
                <span style="font-size:130%; font-family:Verdana">Edit Row</span>
            </li>
            <li id="del">
                <span class="ui-icon ui-icon-trash" style="float:left"></span>
                <span style="font-size:130%; font-family:Verdana">Delete Row</span>
            </li>
        </ul>
    </div>';
}

function datetostamp($date = null)
{
    if (empty($date)) {
        $date = date('Y.m.d H:m', time());
    }
    $parseDate = explode(' ', $date);
    $subDate = explode('.', $parseDate[0]);
    if (isset($parseDate[1])) {
        $subTime = explode(':', $parseDate[1]);
    } else {
        $subTime[0] = 12;
        $subTime[1] = 0;
    }
    return gmmktime($subTime[0], $subTime[1], 0, $subDate[1], $subDate[0], $subDate[2]);
}

function parseCertById($id)
{
    $org = \Organizations\Model\Organizations::find($id);
    if (!empty($org)) {
        if (empty($org->dey) && $org->type == 'INDIVIDUAL') {
            $str = $org->inn;
            $theft = file_get_contents("https://dadata.ru/find/party/" . $str . "/");
            //dd($theft);
            $certificate = substr(stristr($theft, 'Свидетельство о регистрации'), 0, strpos(stristr($theft, 'Свидетельство о регистрации'), "Пенсионный фонд"));
            //$certificate = substr(stristr($theft, '№'), 0, strpos(stristr($theft, '№'), "Статус")-197);
            $certificate = str_replace("Свидетельство о регистрации", "", $certificate);
            //$certificate = str_replace("Свидетельство о регистрации", "", $certificate);
            $certificate = strstr($certificate, '№');
            $certificate = preg_replace("/\s{2,}/", " ", $certificate);

            $IFNS = substr(stristr($theft, 'Налоговая инспекция'), 0, strpos(stristr($theft, 'Налоговая инспекция'), "Свидетельство о регистрации"));
            $IFNS = strstr($IFNS, 'код');
            $IFNS = str_replace("код ", "", $IFNS);
            $IFNS = str_replace(")", "", $IFNS);
            //$IFNS = substr(stristr($theft, 'ОГРН'), 93, 4);
            //$IFNS = preg_replace("/\s{2,}/"," ",$IFNS);
            //dd($IFNS);
            //exit;
            $org->update(["dey" => "св-ва о регистрации " . strip_tags($certificate) . ", выдан ИФНС " . strip_tags($IFNS)]);
        }
    }
}

function HighChartsInit($content)
{
    global $core, $config, $adminTpl;
    if (isset($content)) {
        $HighPiesFlag = false;
        $HighAreasFlag = false;
        $circularsFlag = false;
        $HighPiesCount = 0;
        $HighPies = ' var demoHighPies = function() {';
        foreach ($content as $ChartsName => $parametr) {
            switch ($parametr['type']) {
                default:
                    //main();
                    break;

                case "pies":
                    if (is_array($parametr)) {
                        $HighPiesCount++;
                        $HighPiesFlag = true;
                        $id = $parametr['id'];
                        $name = $parametr['name'];
                        if (is_array($parametr['data'])) {
                            $data_p = '';
                            foreach ($parametr['data'] as $data => $atrb) {
                                $data_p .= '{
                            name: \'' . $atrb[0] . '\',
                            y: ' . $atrb[1] . ',
                            sliced: ' . $atrb[2] . ',
                            selected: ' . $atrb[3] . '
                            },';
                            }
                        }
                        $HighPies .= "var pie" . $HighPiesCount . " = $('#" . $id . "');
					if (pie" . $HighPiesCount . ".length) {
					$('#" . $id . "').highcharts({
                    credits: false,
                    chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                    },
                    title: {
                    text: '" . $parametr['title'] . "'
                    },
                    tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    plotOptions: {
                    pie: {
                    center: ['20%', '50%'],
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                    enabled: false
                    },
                    showInLegend: true
                    }
                    },
                    colors: highColors,
                    legend: {
                    x: 90,
                    floating: true,
                    verticalAlign: \"middle\",
                    layout: \"vertical\",
                    itemMarginTop: 10
                    },
                    series: [{
                    type: 'pie',
                    name: '" . $name . "',
                    data: [
                    " . $data_p . "
                    ]
                    }]
					});
                    }";
                    }
                    break;

                case "circulars":
                    $circularsFlag = true;
                    $circulars = "var demoCircleGraphs = function() {
                var infoCircle = $('.info-circle');
                if (infoCircle.length) {
                var colors = {
                \"primary\": [bgPrimary, bgPrimaryLr,
                bgPrimaryDr
                ],
                \"info\": [bgInfo, bgInfoLr, bgInfoDr],
                \"warning\": [bgWarning, bgWarningLr,
                bgWarningDr
                ],
                \"success\": [bgSuccess, bgSuccessLr,
                bgSuccessDr
                ],
                \"alert\": [bgAlert, bgAlertLr, bgAlertDr]
                };
                // Store all circles
                var circles = [];
                infoCircle.each(function(i, e) {
                // Define default color
                var color = ['#fff', bgPrimary];
                // Modify color if user has defined one
                var targetColor = $(e).data(
                'circle-color');
                if (targetColor) {
                var color = ['#fff', colors[
                targetColor][0]]
                }
                var circle = Circles.create({
                id: $(e).attr('id'),
                value: $(e).attr('value'),
                radius: $(e).width() / 2,
                width: 14,
                colors: color,
                text: function(value) {
                var title = $(e).attr('title');
                if (title) {
                return '<h2 class=\"circle-text-value\">' + value + '</h2><p>' + title + '</p>'
                }
                else {
                return '<h2 class=\"circle-text-value mb5\">' + value + '</h2>'
                }
                }
                });
                circles.push(circle);
                });
                var rescale = function() {
                infoCircle.each(function(i, e) {
                var getWidth = $(e).width() / 2;
                circles[i].updateRadius(
                getWidth);
                });
                setTimeout(function() {
                // Add responsive font sizing functionality
                $('.info-circle').find('.circle-text-value').fitText(0.4);
                },50);
                }
                var lazyLayout = _.debounce(rescale, 300);
                $(window).resize(lazyLayout);

                }
                }
                demoCircleGraphs();";
            }
        }
        $HighPies .= ' } ';
    }
    if ($HighPiesFlag || $HighAreasFlag) {
        $HighCharts = 'var demoHighCharts = function() {';
        if ($HighPiesFlag) {
            $HighCharts .= $HighPies;
            $HighCharts .= '
			demoHighPies();
			';
        }
        if ($circularsFlag && isset($circulars)) {
            $HighCharts .= $circulars;
        }
        $HighCharts .= '}';
    }


    $script = '<script type="text/javascript">
    var demoHighCharts = function () {
    var highColors = [bgWarning, bgPrimary, bgInfo, bgAlert,
    bgDanger, bgSuccess, bgSystem, bgDark
    ];
    var sparkColors = {
    "primary": [bgPrimary, bgPrimaryLr, bgPrimaryDr],
    "info": [bgInfo, bgInfoLr, bgInfoDr],
    "warning": [bgWarning, bgWarningLr, bgWarningDr],
    "success": [bgSuccess, bgSuccessLr, bgSuccessDr],
    "alert": [bgAlert, bgAlertLr, bgAlertDr]
    };
    ' . $HighCharts . '
    return {
    init: function () {


    demoHighCharts();

    }
    }


    }();
    </script>';
    $adminTpl->footIncludes['validation'] = $script;
    $adminTpl->js_code[] = ' demoHighCharts.init();';
}

function recursiveOutput($id, $pid, $module, $level = 0, $parent = 0)
{
    global $db, $recursiveArray, $unCircule;

    if (!isset($unCircule[$module . '_' . $pid])) {
        $unCircule[$module . '_' . $pid] = 'true';
        $recursiveArray[] = ['id' => $id, 'pid' => $pid, 'module' => $module, 'level' => $level, 'parent' => $parent];
        $where = '';
        if ($level <> 0) $where = " AND id <> " . $id;
        $query_in = $db->query("SELECT * FROM " . DB_PREFIX . "_relations WHERE module_in = '" . $module . "' AND pid_in = " . $pid . $where . " ");
        if ($db->numRows($query_in) > 0) $level += 1;
        if ($level <= 5) {
            while ($select = $db->getRow($query_in)) {
                recursiveOutput($select['id'], $select['pid_out'], $select['module_out'], $level, $parent = $pid);
            }
        }
        $query_out = $db->query("SELECT * FROM " . DB_PREFIX . "_relations WHERE module_out = '" . $module . "' AND pid_out = " . $pid . $where . "");
        if ($db->numRows($query_out) > 0) $level += 1;
        if ($level <= 5) {
            while ($select = $db->getRow($query_out)) {
                recursiveOutput($select['id'], $select['pid_in'], $select['module_in'], $level, $parent = $pid);
            }
        }
    }
}

function recursiveOutputGet()
{
    global $db, $recursiveArray, $adminTpl;

    $i = 0;
    if (count($recursiveArray) > 0) {
        // echo '<a href="'.ADMIN.'/module/sales/order/'.$recursiveArray[0]['pid'].'">Заказ клиента № PN-'.$recursiveArray[0]['pid'].'</a><br>';
        //var_dump($recursiveArray);
        $levelSave = -1;

        echo '<style>
      .ulElem{
          //display: none;
      }
      ul{
        list-style-type: none;
      }
      .ulElemBtn{
          cursor: pointer;
          margin-right: 10px;
      }
      .spaceX{
          color: white;
          margin-right: 10px;
        }
      </style>';

        $adminTpl->js_code[] = '
        $(".ulElem").prev().prepend("<i class=\"fa fa-minus-circle ulElemBtn\" data-display=\"1\" aria-hidden=\"true\"></i>");
        $(".ulElem").prev().find(".spaceX").remove();

        $(document).on("click", ".ulElemBtn", function(){
            let elem = $(this).parent().next();
            elem.slideToggle();
            let display = +$(this).data("display");
            if(display === 0){
                $(this).data("display", 1);
                $(this).removeClass("fa-plus-circle");
                $(this).addClass("fa-minus-circle");
            } else {
                $(this).data("display", 0);
                $(this).addClass("fa-plus-circle");
                $(this).removeClass("fa-minus-circle");
            }
        });
      ';

        foreach ($recursiveArray as $value) {
            $i++;
            if ($levelSave !== $value['level']) {
                if ($levelSave > $value['level']) {
                    for ($j = $value['level']; $j < $levelSave; $j++) {
                        echo '</ul>';
                    }
                } else {
                    echo '<ul class="' . ($i !== 1 ? 'ulElem' : '') . '">';
                }
                $levelSave = $value['level'];
            }

            switch ($value['module']) {
                case 'task':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_task WHERE id = " . $value['pid'] . "");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/' . $value['module'] . '/view/' . $value['pid'] . '">Задача №' . $task['id'] . ' от ' . shortUserName($task['fromid']) . ' для ' . shortUserName($task['toid']) . ' (' . formatDate($task['date'], true) . ')</a></li>';
                    break;

                case 'object':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_object WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/' . $value['module'] . '/view/' . $value['pid'] . '">Объект ' . $task['name'] . ' (' . $task['address'] . ')</a></li>';
                    break;

                case 'storage':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_storage WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/' . $value['module'] . '/view/' . $value['pid'] . '">Продажа ' . module_perfix('sales', $task['sales_id']) . ' отправлена на склад для подсчета количества</a></li>';
                    break;

                case 'transfer':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_stock_transfer WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/module/storage/transfer/detail/' . $value['pid'] . '">Перемещение №' . module_perfix('storage_transfer', $task['id']) . '</a></li>';
                    break;

                case 'sales':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_sales WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/module/' . $value['module'] . '/order/' . $value['pid'] . '">Заказ клиента №' . $task['numb'] . ' (' . (empty($task["title"]) ? 'Отсутствует' : $task["title"]) . ')</a></li>';
                    break;

                case 'realization':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_realization WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/module/' . $value['module'] . '/order/' . $value['pid'] . '">Реализация №' . $task['numb'] . '</a></li>';
                    break;

                case 'receipts':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_receipts WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/module/supply/receipts_detail/' . $value['pid'] . '">Поступление №' . $task['numb'] . '</a></li>';
                    break;

                case 'supply':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_supply WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/module/supply/detail/' . $value['pid'] . '">Создана заявка на закупку №' . module_perfix('supply', $value['pid']) . '</a></li>';
                    break;

                case 'supply_order':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_supply_order WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    $queryRR = $db->query("SELECT * FROM " . DB_PREFIX . "_sales WHERE id = '" . $task['sales_id'] . "' LIMIT 1");

                    $taskR = $db->getRow($queryRR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/module/supply/order_detail/' . $value['pid'] . '">К заявке на закупку №' . module_perfix('supply', $taskR['supply']) . ' прикреплен счет от поставщика №' . module_perfix('supply_order', $value['pid']) . '</a></li>';
                    break;

                case 'control':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_supply_order WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    $queryRR = $db->query("SELECT * FROM " . DB_PREFIX . "_sales WHERE id = '" . $task['sales_id'] . "' LIMIT 1");

                    $taskR = $db->getRow($queryRR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/module/supply/order_detail/' . $value['pid'] . '">Счет отправен на согласование #' . module_perfix('supply', $taskR['supply']) . ' прикреплен счет от поставщика №' . module_perfix('supply_order', $value['pid']) . '</a></li>';
                    break;

                case 'supply_date':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_supply WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/module/supply/twodetail/' . $value['pid'] . '">Продажа ' . module_perfix('sales', $task['sales_id']) . ' отправлена на постановку даты поставки</a></li>';
                    break;

                case 'notif':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_notif WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/' . $value['module'] . '/view/' . $value['pid'] . '">' . $task['text'] . '</a></li>';
                    break;

                case 'calls':
                    $queryR = $db->query("SELECT * FROM " . DB_PREFIX . "_calls WHERE id = '" . $value['pid'] . "'");
                    $task = $db->getRow($queryR);
                    echo '<li><i class="fa fa-wheelchair-alt spaceX" aria-hidden="true"></i><a target="_blank" href="' . ADMIN . '/' . $value['module'] . '/view/' . $value['pid'] . '">Звонок №' . $task['id'] . ' от ' . $task['src'] . ' для ' . $task['dst'] . ' (' . formatDate($task['calldate'], true) . ')</a></li>';
                    break;
            }
        }

    }

    if ($i == 0) {
        if ($task) {
            echo 'К сожалению задач по данной продаже не найдено';
        } else {
            echo 'К сожалению связей по данной продаже не найдено';
        }
    }

}

function uploaderMaster($type, $attribute = [])
{
    /*
        $type - что выбрать
        $attribute["script"] - true - добавить <script>
        $attribute["name"] - добавочное имя
        $attribute["file"] - файл
        $attribute["module"] - имя модуля
        $attribute["mod"] - имя мода (task, task_comment...)
        $attribute["id"] - id объекта
        $attribute["comm_id"] - id комментария
    */
    global $adminTpl, $db;

    if (!isset($attribute["name"])) {
        $attribute["name"] = "";
    }
    if (!isset($attribute["script"])) {
        $attribute["script"] = true;
    }

    if ($type == "plugin" || $type == "script") {
        if ($attribute["script"]) {
            $scriptBegin = "<script>";
            $scriptEnd = "</script>";
        } else {
            $scriptBegin = "";
            $scriptEnd = "";
        }
    }

    $li_empty = '<li class="text-muted text-center empty">Файлы не загружены.</li>';

    switch ($type) {
        case "plugin":
            //$adminTpl->footIncludes[] =
            return "<script src='" . PLUGINS . "uploader-master/js/jquery.dm-uploader.js'></script>";
        case "script":
            //$adminTpl->footIncludes[] =
            return $scriptBegin . "$(function(){
        /*
        * For the sake keeping the code clean and the examples simple this file
        * contains only the plugin configuration & callbacks.
        *
        * UI functions ui_* can be located in: demo-ui.js
        */
        $('#drag-and-drop-zone" . $attribute["name"] . "').dmUploader({ //
        url: '/upload.php',
        maxFileSize: 1000000000, // 100 Megs
        //allowedTypes: 'text/*|image/*|application/*',

        extFilter: ['jpg','jpeg','png','gif','txt','doc','docx','pdf','xls','xlsx','rar','zip','dwg','cat','dws','dwt','dxf','rtf','bmp','cdr','7z','odt','tif','ppt', 'pptx', 'pps', 'ppsx', 'pot', 'potx', 'rfa'],

        onDragEnter: function(){
        // Happens when dragging something over the DnD area
        this.addClass('active');
        },
        onDragLeave: function(){
        // Happens when dragging something OUT of the DnD area
        this.removeClass('active');
        },
        onInit: function(){
        // Plugin is ready to use
        //ui_add_log('Penguin initialized :)', 'info');
        },
        onComplete: function(){
        // All files in the queue are processed (success or error)
        //ui_add_log('All pending tranfers finished');
        },
        onNewFile: function(id, file){
        // When a new file is added using the file selector or the DnD area
        //ui_add_log('New file added #' + id);
        ui_multi_add_file" . $attribute["name"] . "(id, file);

        if (typeof FileReader !== 'undefined'){
        var reader = new FileReader();
        var img = $('#uploaderFile" . $attribute["name"] . "' + id).find('img');

        reader.onload = function (e) {
        console.log(e.target.result);
        let str = e.target.result
        let end = str.indexOf('/');
        let res = str.slice(5, end);
        console.log(res);
        if(res == 'image')
        img.attr('src', e.target.result);
        }

        reader.readAsDataURL(file);

        }
        },
        onFileTypeError: function(file){
        alert('Такой тип файлов не поддерживается');
        },
        onBeforeUpload: function(id){
        // about tho start uploading a file
        //ui_add_log('Starting the upload of #' + id);
        ui_multi_update_file_status" . $attribute["name"] . "(id, 'uploading', 'Загружается...');
        ui_multi_update_file_progress" . $attribute["name"] . "(id, 0, '', true);
        },
        onUploadCanceled: function(id) {
        // Happens when a file is directly canceled by the user.
        ui_multi_update_file_status" . $attribute["name"] . "(id, 'warning', 'Отменено пользователем');
        ui_multi_update_file_progress" . $attribute["name"] . "(id, 0, 'warning', false);
        },
        onUploadProgress: function(id, percent){
        // Updating file progress
        ui_multi_update_file_progress" . $attribute["name"] . "(id, percent);
        },
        onUploadSuccess: function(id, data){
        // A file was successfully uploaded

        //ui_add_log('Server Response for file #' + id + ': ' + JSON.stringify(data));
        //ui_add_log('Upload of file #' + id + ' COMPLETED', 'success');
        let json = JSON.parse(data);
        ui_add_log" . $attribute["name"] . "(json.path);
        ui_multi_update_file_status" . $attribute["name"] . "(id, 'success', json.message);
        ui_multi_update_file_progress" . $attribute["name"] . "(id, 100, 'success', false);
        },
        onUploadError: function(id, xhr, status, message){
        ui_multi_update_file_status" . $attribute["name"] . "(id, 'danger', JSON.parse(xhr.responseText).message);
        ui_multi_update_file_progress" . $attribute["name"] . "(id, 0, 'danger', false);
        },
        onFallbackMode: function(){
        // When the browser doesn't support this plugin :(
        //ui_add_log('Plugin cant be used here, running Fallback callback', 'danger');
        },
        onFileSizeError: function(file){
        //ui_add_log('File \'' + file.name + '\' cannot be added: size excess limit', 'danger');
        alert('Файл слишком большой(ограничение в 100 Мб)');
        }
        });
        });

        /*
        * Some helper functions to work with our UI and keep our code cleaner
        */

        // Adds an entry to our debug area
        function ui_add_log" . $attribute["name"] . "(message, color)
        {

		var d = new Date();
		var dateString = (('0' + d.getHours())).slice(-2) + ':' +
        (('0' + d.getMinutes())).slice(-2) + ':' +
        (('0' + d.getSeconds())).slice(-2);

		color = (typeof color === 'undefined' ? 'muted' : color);
		var template = $('#debug-template" . $attribute["name"] . "').text();
		template = template.replace('%%date%%', dateString);
		template = template.replace('%%message%%', message);
		template = template.replace('%%color%%', color);
		$('#debug" . $attribute["name"] . "').find('li.empty').fadeOut(); // remove the 'no messages yet'
		$('#debug" . $attribute["name"] . "').prepend(template);
        }

        // Creates a new file and add it to our list
        function ui_multi_add_file" . $attribute["name"] . "(id, file)
        {
        var template = $('#files-template" . $attribute["name"] . "').text();
        template = template.replace('%%filename%%', file.name);
        // template = template.replace('%%filenamevalue%%', file.name);

        template = $(template);
        template.prop('id', 'uploaderFile" . $attribute["name"] . "' + id);
        template.data('file-id', id);

        $('#files" . $attribute["name"] . "').find('li.empty').fadeOut(); // remove the 'no files yet'
        $('#files" . $attribute["name"] . "').prepend(template);
        }

        // Changes the status messages on our list
        function ui_multi_update_file_status" . $attribute["name"] . "(id, status, message)
        {
        $('#uploaderFile" . $attribute["name"] . "' + id).find('span').html(message).prop('class', 'status text-' + status);
        }

        // Updates a file progress, depending on the parameters it may animate it or change the color.
        function ui_multi_update_file_progress" . $attribute["name"] . "(id, percent, color, active)
        {
        color = (typeof color === 'undefined' ? false : color);
        active = (typeof active === 'undefined' ? true : active);

        var bar = $('#uploaderFile" . $attribute["name"] . "' + id).find('div.progress-bar');

        bar.width(percent + '%').attr('aria-valuenow', percent);
        bar.toggleClass('progress-bar-striped progress-bar-animated', active);

        if (percent === 0){
        bar.html('');
        } else {
        bar.html(percent + '%');
        }

        if (color !== false){
        bar.removeClass('bg-success bg-info bg-warning bg-danger');
        bar.addClass('bg-' + color);
        }
        }
        function removeFileUploadMaster_" . $attribute["name"] . "(This){
            let count = $(This).parent().parent().prevAll('li').length;
            $(This).parent().parent().remove();
            $('#debug" . $attribute["name"] . "').children().eq(count).remove();
        }
        function clearAll_" . $attribute["name"] . "(){
            const files = $('#files" . $attribute["name"] . "');
            files.children().remove();
            files.append('" . $li_empty . "');
            $('#debug" . $attribute["name"] . "').children().remove();
        }

        " . $scriptEnd;
            break;

        case "css":
            return '<link href="' . PLUGINS . 'uploader-master/css/jquery.dm-uploader.css" rel="stylesheet">
        <link href="' . PLUGINS . 'uploader-master/css/styles.css" rel="stylesheet">

        <style>
        .dm-uploader {
        border: 0.25rem dashed #A5A5C7;
        }
        .p-5 {
        padding: 3rem!important;
        }
        .text-muted {
        color: #868e96!important;
        }
        .mb-5, .my-5 {
        margin-bottom: 3rem!important;
        }
        .mt-5, .my-5 {
        margin-top: 3rem!important;
        }
        .h3, h3 {
        font-size: 1.75rem;
        }
        .mb-5, .my-5 {
        margin-bottom: 3rem!important;
        }
        .btn-block {
        display: block;
        width: 100%;
        }

        .h-100 {
        height: 100%!important;
        }
        .card {
        position: relative;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        min-width: 0;
        word-wrap: break-word;
        background-color: #fff;
        background-clip: border-box;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: .25rem;
        }
        .card-header:first-child {
        border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
        }
        .card-header {
        padding: .75rem 1.25rem;
        margin-bottom: 0;
        background-color: rgba(0,0,0,.03);
        border-bottom: 1px solid rgba(0,0,0,.125);
        }
        @media (min-width: 768px)
        .filesClass {
        min-height: 0;
        }
        .media {
        margin: 0;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: start;
        -ms-flex-align: start;
        align-items: flex-start;
        }
        .mb-1, .my-1 {
        margin-bottom: .25rem!important;
        }
        .media-body {
        -webkit-box-flex: 1;
        -ms-flex: 1;
        flex: 1;
        }
        .filesClass {
        overflow-y: scroll !important;
        min-height: 200px;
        }
        .p-2 {
        padding: .5rem!important;
        }
        .flex-column {
        -webkit-box-orient: vertical!important;
        -webkit-box-direction: normal!important;
        -ms-flex-direction: column!important;
        flex-direction: column!important;
        }
        /*
        .d-flex {
        display: -webkit-box!important;
        display: -ms-flexbox!important;
        }
        */
        .col {
        -ms-flex-preferred-size: 0;
        flex-basis: 0;
        -webkit-box-flex: 1;
        -ms-flex-positive: 1;
        flex-grow: 1;
        max-width: 100%;
        }
        .text-muted {
        color: #868e96!important;
        }
        .bg-success {
        background-color: #28a745!important;
        }
        .progress-bar {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        background-color: #007bff;
        transition: width .6s ease;
        }
        .mt-1, .my-1 {
        margin-top: .25rem!important;
        }
        .mb-2, .my-2 {
        margin-bottom: .5rem!important;
        }
        p {
        margin-top: 0;
        margin-bottom: 1rem;
        }
        ul.filesClass, li.media {
        margin: 0;
        margin-bottom: 1rem;
        }
        .preview-img {
        width: 64px;
        height: 64px;
        }
        .mr-3, .mx-3 {
        margin-right: 1rem!important;
        }
        img {
        vertical-align: middle;
        border-style: none;
        }
        .progress {
        height: 24px;
        }
        </style>

        <!-- File item template -->
        <script type="text/html" id="files-template' . $attribute["name"] . '">
        <li class="media">
        <img class="mr-3 mb-2 preview-img" src="' . PLUGINS . 'uploader-master/css/no_product.png" alt="Изображение файла">
		<div class="media-body mb-1">
        <p class="mb-2">
        <strong>%%filename%%</strong> - Статус: <span class="text-muted">Ожидает загрузки</span>
        </p>
        <div class="progress mb-2">
        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
        role="progressbar"
        style="width: 0%"
        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
        </div>
        </div>
        <hr class="mt-1 mb-1" />
        </div>
        <div style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;"><i onclick="removeFileUploadMaster_' . $attribute["name"] . '(this);" style="font-size: 30px; color: red; cursor: pointer;" class="fa fa-times" aria-hidden="true"></i></div>
        </li>
        </script>

        <!-- Debug item template -->
        <script type="text/html" id="debug-template' . $attribute["name"] . '">
        <input type="hidden" value=\'%%message%%\' name="file[]">
        </script>';
            break;

        case "upload":
            return '<div id="drag-and-drop-zone' . $attribute["name"] . '" class="dm-uploader p-5">
        <h3 class="mb-5 mt-5 text-muted">Перетащите файлы сюда</h3>
        <div class="btn btn-primary btn-block mb-5">
        <span>Добавить файл</span>
        <input type="file" title="Нажмите, чтобы добавить файлы" class="btn"/>
        </div>
		</div>';
            break;

        case "view":
            return '<div class="card h-100">
        <div class="card-header">
        Список файлов
        </div>
        <ul class="list-unstyled p-2 d-flex flex-column col filesClass" id="files' . $attribute["name"] . '">
        ' . $li_empty . '
        </ul>
		</div>
		<div class="list-group list-group-flush" id="debug' . $attribute["name"] . '" hidden></div>';
            break;


        case "download":
            if ($attribute["file"]) {

                $path_folder = "files/{$attribute["module"]}/{$attribute["id"]}/";
                if (!file_exists(ROOT . $path_folder))
                    mkdir(ROOT . $path_folder, 0777, true);

                $count = 0;
                foreach ($attribute["file"] as $val) {
                    if (file_exists($val)) {
                        $request = [];
                        $file_info = pathinfo($val);

                        //убираем уникальность
                        $original_file_name_start = mb_stripos($file_info["basename"], "_");
                        $original_file_name = mb_substr($file_info["basename"], ($original_file_name_start + 1));

                        $new_file_name = uniqid("", true) . "." . $file_info["extension"];
                        $path_file = "{$path_folder}{$new_file_name}";
                        rename($val, ROOT . $path_file);
                        $request["name"] = $original_file_name;
                        $request["url"] = $path_file;
                        $request["pid"] = $attribute["comm_id"];
                        $request["module"] = $attribute["mod"];

                        if (!empty($attribute["ftp"])) {
                            $className = $attribute["ftp"];
                            if (class_exists($className)) {
                                $request["class_name"] = $className;
                                // \General\Model\AttachDaemon::create(["attach_id" => $rid, "class_name" => $className]);
                            }
                        }
                        $attach = \Boot\Models\Attach::create($request);
                        $rid = $attach->id;

                        $count++;
                    }
                }
                if ($count === 1) {
                    return $rid;
                }
            }
            break;
    }
}

function is_date($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

function oSelect($mod, $name, $id = null, $ajaxUrl = "getOrganizations", $disabled = false, $multiple = false, $return = false, $required = false)
{
    return oSelectCustom([
        "mod" => $mod,
        "name" => $name,
        "id" => $id,
        "ajaxUrl" => $ajaxUrl,
        "disabled" => $disabled,
        "multiple" => $multiple,
        "return" => $return,
        "required" => $required,
    ]);
}

function oSelectCustom($request)
{
    global $adminTpl;

    $mod = $request["mod"];
    $name = $request["name"];
    $select_id = isset($request["select_id"]) ? $request["select_id"] : $name;
    $custom = isset($request["custom"]) ? $request["custom"] : null;
    $id = isset($request["id"]) ? $request["id"] : null;
    $ajaxUrl = isset($request["ajaxUrl"]) ? $request["ajaxUrl"] : "getOrganizations";
    $disabled = isset($request["disabled"]) ? $request["disabled"] : false;
    $multiple = isset($request["multiple"]) ? $request["multiple"] : false;
    $return = isset($request["return"]) ? $request["return"] : false;
    $required = isset($request["required"]) ? $request["required"] : false;
    $allowClear = isset($request["allowClear"]) ? $request["allowClear"] : false;
    $bootstrap = isset($request["bootstrap"]) ? $request["bootstrap"] : false;
    $select_class = isset($request["select_class"]) ? $request["select_class"] : "ajaxSelect_" . $ajaxUrl;

    // dd($bootstrap);

    $data_send = $request["data_send"] ?? [];

    $get_icon_to_select = function ($request) use ($select_class) {
        // ["icon" => ""]
        $icon = empty($request["icon"]) ? "" : $request["icon"];
        return '<label for="' . $select_class . '" style="top: 2px;" class="field-icon">' . $icon . '</label>';
    };

    switch ($ajaxUrl) {
        default:
            $icon = '';
            $placeholder = 'Нажмите чтобы выбрать';
            break;
        case "getOrganizations":
            $icon = $get_icon_to_select(["icon" => '<i class="fas fa-building"></i>']);
            $placeholder = 'Нажмите чтобы выбрать организацию';
            break;
        case "getBoiler":
            $icon = $get_icon_to_select(["icon" => '<i class="fas fa-boiler"></i>']);
            $placeholder = 'Нажмите чтобы выбрать котел';
            break;
        case "getObjectToSelect":
            $icon = $get_icon_to_select(["icon" => '<i class="glyphicon glyphicon-briefcase"></i>']);
            $placeholder = 'Нажмите чтобы выбрать объект';
            break;
        case "getUsersToSelect":
        case "getUsersAllToSelect":
            $icon = $get_icon_to_select(["icon" => '<i class="fas fa-user-tie"></i>']);
            $placeholder = 'Нажмите чтобы выбрать пользователя';
            break;
        case "getSalesToSelect":
            $icon = $get_icon_to_select(["icon" => '<i class="fas fa-shopping-cart"></i>']);
            $placeholder = 'Нажмите чтобы выбрать продажу';
            break;
        case "getRealizationToSelect":
            $icon = $get_icon_to_select(["icon" => '<i class="fas fa-shopping-cart"></i>']);
            $placeholder = 'Нажмите чтобы выбрать реализацию';
            break;
        case "getSupplyOrderToSelect":
            $icon = $get_icon_to_select(["icon" => '<i class="far fa-file-text"></i>']);
            $placeholder = 'Нажмите чтобы выбрать счет поставщика';
            break;
        case "getCatalogManufactureToSelect":
            $icon = $get_icon_to_select(["icon" => '<i class="fas fa-industry"></i>']);
            $placeholder = 'Нажмите чтобы выбрать производителя';
            break;
        case "getCatalogToSelect":
            $icon = $get_icon_to_select(["icon" => '<i class="fas fa-industry"></i>']);
            $placeholder = 'Нажмите чтобы выбрать товар';
            break;
        case "getOrganizationsBankToSelect":
            $icon = $get_icon_to_select(["icon" => '<i class="fas fa-industry"></i>']);
            $placeholder = 'Нажмите чтобы выбрать банковские реквизиты';
            break;
        case "getCatalogGroupsToSelect":
            $icon = $get_icon_to_select(["icon" => '<i class="fas fa-industry"></i>']);
            $placeholder = 'Нажмите чтобы выбрать категорию скидки';
            break;
    }

    $allowClearValue = "";
    if ($allowClear) {
        $allowClearValue = "allowClear: true,";
    }

    $ds = [];
    foreach ($data_send as $field => $value) {
        $ds[] = "{$field}: `{$value}`,";
    }
    $ds = implode("", $ds);

    // NEW FIX
    $bootstrap_theme = function ($theme) {
        return "theme: '{$theme}',";
    };
    //if ($bootstrap == 'default') {
    //    $theme = $bootstrap_theme('default');
    //} else {
    //    $theme = $bootstrap_theme('bootstrap-5');
    //}
    $theme = $bootstrap_theme('default');

    $def = "$('.{$select_class}').select2({
        {$theme}
        width: '100%',
        escapeMarkup: function (text) { return text; },
        ajax: {
            url: '/ajax.php?do={$ajaxUrl}',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term, page: params.page, {$ds} /* search term */ }; },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return { results: data.items, pagination: { more: (params.page * 30) < data.total_count } };
            },
            cache: true,
        },
        placeholder: '{$placeholder}',
        minimumInputLength: 2,
        {$allowClearValue}
    });";

    switch ($mod) {
        case "jsReturn":
            return $def;
            break;
        case "jsEcho":
            echo $def;
            break;
        case "jsAdminTpl":
            $adminTpl->js_code[] = $def;
            break;
        case "select":
            if (is_array($id)) {
                $id_array = $id;
            } else {
                if (empty($id)) {
                    $id_array = [];
                } else {
                    $id_array = [$id];
                }
            }

            $option = [];
            if (count($id_array) > 0) {
                switch ($ajaxUrl) {
                    case "getOrganizations":
                        foreach (\Organizations\Model\Organizations::whereIn("id", $id_array)->get()->toArray() as $str) {
                            $option[] = '<option value="' . $str["id"] . '">' . $str["name"] . ' (' . $str["inn"] . ')</option>';
                        }
                        break;
                    case "getBoiler":
                        foreach (\Boiler\Model\BoilerBid::whereIn("id", $id_array)->get()->toArray() as $str) {
                            $option[] = '<option value="' . $str["id"] . '">' . $str["numb"] . ' (' . $str["title"] ?? '' . ')</option>';
                        }
                        break;
                    case "getOrgToSelect":
                        foreach (\Organizations\Model\Org::with("organization")->whereIn("id", $id_array)->get() as $str) {
                            $option[] = '<option value="' . $str->id . '">' . $str->organization->name . ' (' . $str->organization->inn . ')</option>';
                        }
                        break;
                    case "getObjectToSelect":
                        foreach (\Object\Model\ObjectModel::whereIn("id", $id_array)->get()->toArray() as $str) {
                            $option[] = '<option selected value="' . $str["id"] . '">#' . module_perfix("object", $str["id"]) . ' ' . $str['name'] . (empty($str["address"]) ? '' : ' (' . $str["address"] . ')') . '</option>';
                        }
                        break;
                    case "getUsersToSelect":
                    case "getUsersAllToSelect":
                        foreach ($id_array as $id_l) {
                            $option[] = '<option selected value="' . $id_l . '">' . fullUserName($id_l) . '</option>';
                        }
                        break;
                    case "getSalesToSelect":
                        foreach (\Sales\Model\Sales::with("organization")->whereIn("id", $id_array)->get() as $str) {
                            $option[] = '<option selected value="' . $str->id . '">' . $str->numb . ' ' . $str->organization->name . '</option>';
                        }
                        break;
                    case "getRealizationToSelect":
                        foreach (\Realization\Model\Realization::with("organization")->whereIn("id", $id_array)->get() as $str) {
                            $option[] = '<option selected value="' . $str->id . '">' . $str->numb . ' ' . $str->organization->name . '</option>';
                        }
                        break;
                    case "getSupplyOrderToSelect":
                        foreach (\Supply\Model\SupplyOrder::with("organization")->whereIn("id", $id_array)->get() as $str) {
                            $option[] = '<option selected value="' . $str->id . '">' . $str->numb . ' ' . $str->organization->name . ' (' . $str->bill_code . ')</option>';
                        }
                        break;
                    case "getCatalogManufactureToSelect":
                        foreach (\Catalog_v2\Model\CatalogManufacture::whereIn("id", $id_array)->get()->toArray() as $str) {
                            $option[] = '<option selected value="' . $str["id"] . '">' . $str["name"] . '</option>';
                        }
                        break;
                    case "getCatalogToSelect":
                        foreach (\Catalog_v2\Model\Catalog::whereIn("id", $id_array)->get()->toArray() as $str) {
                            $option[] = '<option selected value="' . $str["id"] . '">(' . $str["sku"] . ') - ' . $str["title"] . '</option>';
                        }
                        break;
                    case "getOrganizationsBankToSelect":
                        foreach (\Organizations\Model\OrganizationsBank::whereIn("id", $id_array)->get()->toArray() as $str) {
                            $val = $str["rschet"] . ', ' . (empty($str["name_short"]) ? $str["name"] : $str["name_short"]);
                            $option[] = '<option selected value="' . $str["id"] . '">' . $val . '</option>';
                        }
                        break;
                    case "getCatalogGroupsToSelect":
                        foreach (\Catalog_v2\Model\CatalogGroups::whereIn("id", $id_array)->get()->toArray() as $str) {
                            $option[] = '<option selected value="' . $str["id"] . '">' . $str["name"] . '</option>';
                        }
                        break;
                }
            }
            $option_str = implode("", $option);

            // Генератор Select поля по шаблону
            $gen_select = function ($select_id, $name, $select_class, $option_str, $disabled, $required, $multiple, $custom = null) {

                $disabled = ($disabled ? 'disabled' : '');
                $required = ($required ? 'required' : '');
                $multiple = ($multiple ? 'multiple="multiple"' : '');


                // select-primary вроде как класс не нужен...
                return "<select  name='{$name}' id='{$select_id}' class='{$select_class} form-select select-primary' {$custom} {$disabled} {$required} {$multiple}>{$option_str}</select>";
            };
            // Генератор Label по шаблону
            $gen_label = function ($select_class, $select_tag, $multiple, $icon) {
                $icon = ($multiple ? '' : $icon);

                return "<label for='{$select_class}' class='field prepend-icon'>{$select_tag} {$icon}</label>";
            };

            if ($bootstrap) {
                $result = $gen_select($select_id, $name, $select_class, $option_str, $disabled, $required, $multiple, $custom);
            } else {
                $select_tag = $gen_select($select_id, $name, $select_class, $option_str, $disabled, $required, $multiple, $custom);
                $result = $gen_label($select_class, $select_tag, $multiple, $icon);
            }

            if ($disabled && (!empty($id))) {
                $result .= "<input type='hidden' name='{$name}' value='{$id}'>";
            }
            if ($return) {
                return $result;
            } else {
                echo $result;
            }
            break;
    }

    // return false; // Затычка
}

function getTypeBlock($type = 0)
{
    $array = [
        [
            "0" => "Блок ввода",
            "1" => "Блок отопления/вентиляции",
            "2" => "Блок ГВС",
            "3" => "Блок подпитки",
            "4" => "АТРИ НС",
            "5" => "АТРИ УК",
        ],
        [
            "0" => "block_vvoda",
            "1" => "block_otopleniya",
            "2" => "block_gvs",
            "3" => "block_podpitky",
        ]
    ];

    return $array[$type];
}

function get_ndr($array)
{
    //["cid" => "", "stock_id" => "", "module" => "", "pid" => "", "returnType" => "array"]
    $reserv = new Reserv();
    $quantity = get_quantity_stock_transaction($array["cid"], $array["stock_id"]);
    $countReserv = $reserv->get_reserv_quantity($array["cid"], $array["stock_id"]);

    if (empty($quantity)) {
        $quantity = 0;
    }

    $availableCount = $quantity - $countReserv;
    if ($availableCount < 0) {
        $availableCount = 0;
    }

    //цвет актуализации остатков
    // $color = get_quantity_color($array["cid"]);
    $color = "";

    //количество резерва у данного товара
    $countReservThis = 0;
    $countReservAfterThis = 0;
    if ($array["returnType"] == "array" || $array["returnType"] == "all") {
        if (array_key_exists("pid", $array) && !empty($array["pid"])) {
            $countReservThis = $reserv->get_reserv_count($array["pid"], $array["module"], $array["stock_id"], [1]);

            $countReservAfterThis = $reserv->get_reserv_count($array["pid"], $array["module"], $array["stock_id"], [2]);
        }

        $array_result = [
            "n" => $quantity,
            "d" => $availableCount,
            "r" => $countReserv,
            "color" => $color,
            "r_this" => $countReservThis,
            "r_after_this" => $countReservAfterThis,
        ];
    }

    $space = $array["space"] ?? " ";

    $view = "<span>{$quantity}{$space}/{$space}{$availableCount}{$space}/{$space}{$countReserv}</span>";

    switch ($array["returnType"]) {
        case "array":
            //Наличие / Доступно / Резерв
            return $array_result;
            break;
        case "view":
            return $view;
            break;
        case "all":
            return [
                "data" => $array_result,
                "view" => $view,
            ];
            break;
    }
}

function get_quantity_stock_transaction($id, $stock_id)
{
    $item = \DB::table("stock_detail")->where("cid", $id)->where("stock", $stock_id)->first();
    return empty($item->sum) ? 0 : $item->sum;
}

function getFaceType($param = "array")
{
    $array = [
        "0" => "Заказчик",
        "1" => "Застройщик",
        "2" => "Генеральный подрядчик",
        "3" => "Проектная организация",
        "4" => "Исполнитель",
        "5" => "Монтажная организация",
        "6" => "Представитель энергоснабжающей организации",
        "7" => "Представитель эксплуатирующей организации",
    ];
    $sclon = [
        "0" => "Представитель заказчика",
        "1" => "Представитель застройщика",
        "2" => "Представитель генерального подрядчика",
        "3" => "Представитель проектной организации",
        "4" => "Представитель исполнителя",
        "5" => "Представитель монтажной организации",
        "6" => "Представитель энергоснабжающей организации",
        "7" => "Представитель эксплуатирующей организации",
    ];
    return $$param;
}

function getTestTypeDoc()
{
    $array = [
        "0" => "Акт о приемке помещения к монтажу",
        "1" => "Акт о приемке группы учета электроэнергии",
        "2" => "Журнал производства работ (заполняется ежедневно в течении всего периода производства монтажных работ)",
        "3" => "Акт об окончании монтажа оборудования",
        "4" => "Акт о проведении промывки системы",
        "5" => "Акт о проведении гидравлических испытаний",
        "6" => "Акт скрытых работ по огрунтовке/покраске трубопроводов",
        "7" => "Акт на монтаж изоляции",
        "8" => "Акт об окончании работ по автоматизации",
        "9" => "Акт приема-передачи оборудования и помещения",
        "10" => "Акт о проведении пуско-наладочных работ",
        "11" => "Акт комплексного опробования систем",
        "12" => "Договор на проведение пуско-наладочных работ",
        "13" => "Договор на обслуживание",
    ];
    return $array;
}

function getTypeOrg()
{
    return \Boot\Models\UsersOrg::getType();
}

function getTypePost($id = null)
{
    $array = \Boot\Models\UsersSign::getTypePost();
    if (empty($id)) {
        return $array;
    } else {
        return $array[$id];
    }
}

function getTypeBase($id = null)
{
    $array = \Boot\Models\UsersSign::getBase();
    if (empty($id)) {
        return $array;
    } else {
        return $array[$id];
    }
}

function catalogCatsShow($matches)
{
    global $core, $url, $db;
    $tag = $matches[1];
    $content = $matches[2];
    if ($url[0] == 'catalog_v2') {
        $cat = mjsEnd($url);
        $altname = filter($cat, 'a');
        $cat_query = $db->query("SELECT id as cid, name, fulltitle, description, keywords FROM " . DB_PREFIX . "_categories WHERE altname='" . $db->safesql($altname) . "'");
        if ($db->numRows($cat_query) >= 1) {
            $cat_info = $db->getRow($cat_query);
            $cat_parrent = $db->query("SELECT id as cid, name, fulltitle, description, keywords, altname, preview FROM " . DB_PREFIX . "_categories WHERE parent_id='" . $cat_info['cid'] . "' AND module = 'catalog_v2' AND status='1' ORDER BY name ASC");
            if ($db->numRows($cat_parrent) > 0) {
                if ($tag == 1) return stripslashes($content);
            } else {
                if (file_exists($core->tpl->file_dir . 'catalog_v2/catalog.page/catalog.page.' . $cat_info['cid'] . '.tpl')) {
                    if ($tag == 1) return stripslashes($content);
                } else {
                    if ($tag == 0) return stripslashes($content);
                }
            }
        }
        if (!isset($url[1])) {
            if ($tag == 1) return stripslashes($content);
        } else {
            if ($tag == 0) return stripslashes($content);
        }
        foreach ($url as $key => $value) {
            if (eregStrt('.html', $value)) {
                if ($tag == 1) return stripslashes($content);
            }
        }
    }
}

function catalogCatsOpenShow($matches)
{
    global $url, $db;
    $tag = $matches[1];
    $content = $matches[2];
    if ($url[0] == 'catalog_v2') {
        $cat = mjsEnd($url);
        $altname = filter($cat, 'a');
        $cat_query = $db->query("SELECT id as cid, name, fulltitle, description, keywords FROM " . DB_PREFIX . "_categories WHERE altname='" . $db->safesql($altname) . "'");
        if ($db->numRows($cat_query) >= 1) {
            $cat_info = $db->getRow($cat_query);
            $cat_parrent = $db->query("SELECT id as cid, name, fulltitle, description, keywords, altname, preview FROM " . DB_PREFIX . "_categories WHERE parent_id='" . $cat_info['cid'] . "' AND module = 'catalog_v2' AND status='1' ORDER BY name ASC");
            if ($db->numRows($cat_parrent) == 0) {
                if ($tag == 1) return stripslashes($content);
            } else {
                if ($tag == 0) return stripslashes($content);
            }
        }
    }
}

function getTypeDocReestr($id = null)
{
    $array = [
        1 => "Договор",
        2 => "Спецификация",
        3 => "Доп. согашение",
        4 => "Приложение",
    ];
    if (is_null($id)) {
        return $array;
    } else {
        return $array[$id];
    }
}

function getDesignersRequest()
{
    $designers = [4, 9, 40, 61, 111];
    return $designers;
}

function getGroup($array = ["group" => "", "return" => ""])
{
    /* Group list */
    $groups = [
        "storage" => [
            "uid" => [17, 154],
            "rus" => "Склад",
        ],
        "designers" => [
            "uid" => [4, 9, 38, 40, 50, 61, 111],
            "rus" => "Проектировщики",
        ],
        "admin" => [
            "uid" => [1, 33],
            "rus" => "Программисты",
        ],
        "supply" => [
            "uid" => [16],
            "rus" => "Снабженцы",
        ],
        "sales" => [
            "uid" => [2, 29, 49, 120, 129, 130, 154, 161],
            "rus" => "Менеджеры",
        ],
        "catalog" => [
            "uid" => [79],
            "rus" => "Создатель",
        ],
        "director" => [
            "uid" => [3],
            "rus" => "Директор",
        ],
        "accountant" => [
            "uid" => [24, 114],
            "rus" => "Бухгалтер",
        ],
        "logistic" => [
            "uid" => [16],
            "rus" => "Логист",
        ],
        "driver" => [
            "uid" => [196, 197],
            "rus" => "Водитель",
        ],
    ];
    /* Logic */
    if (array_key_exists("return", $array) && $array["return"] === "all") {
        return $groups;
    } else {
        $group = $array["group"];
        if (is_array($group)) {
            $reArray = [];
            if (count($group) > 0) {
                foreach ($group as $val) {
                    if (array_key_exists($val, $groups)) {
                        $reArray = array_merge($reArray, $groups[$val]["uid"]);
                    }
                }
                $reArray = array_unique($reArray);
                return $reArray;
            } else {
                return [];
            }
        } else {
            return array_key_exists($group, $groups) ? $groups[$group]["uid"] : [];
        }
    }
}

function errorLoad($type = '404')
{
    global $core;
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");

    $core->tpl->open($type);
    $core->tpl->loadFile($type);
    $core->tpl->setVar('ERROR_TITLE', '404');
    $core->tpl->setVar('ERROR', '404');
    $core->tpl->setVar('TEXT','404');
    $core->tpl->end();
    $core->tpl->close();
    exit;
}

function attach_save_file($file, $id, $module, $mod)
{
    $patch = "files/" . $module . "/";
    if (!empty($file["tmp_name"])) {

        if (!file_exists(ROOT . $patch . $id)) {
            mkdir(ROOT . $patch . $id, 0777, true);
        }

        $ext = pathinfo($file["name"]);
        $full_name = $file["name"] . '_' . time() . "." . $ext["extension"];
        $full_name = str_replace(" ", "", $full_name);
        $filePatch = $patch . $id . "/" . $full_name;
        copy($file["tmp_name"], ROOT . $filePatch);

        $data = [
            "name" => $file["name"],
            "url" => $filePatch,
            "pid" => $id,
            "module" => $mod,
        ];
        return attach_insert_file($data);
    }
}

function attach_insert_file($data)
{
    $attach = \Boot\Models\Attach::create($data);
    return $attach->id;
}

function attach_del_file($id)
{
    if ($id) {
        \Boot\Models\Attach::find($id)->delete();
    }
}

function attach_data_file($id)
{
    if ($id) {
        return \Boot\Models\Attach::find($id)->toArray();
    }
}

function attach_name_file($id)
{
    if ($id) {
        $file = attach_data_file($id);
        return $file["name"];
    }
}

function get_prefix_org($org = null, $oid = null)
{
    global $db;
    if (!empty($org)) {
        $query = $db->query("SELECT prefix FROM " . DB_PREFIX . "_org WHERE id = '" . $org . "' LIMIT 1");
    }
    if (!empty($oid)) {
        $query = $db->query("SELECT prefix FROM " . DB_PREFIX . "_org WHERE oid = '" . $oid . "' LIMIT 1");
    }
    $prefix = "";
    if ($db->numRows($query) > 0) {
        $r = $db->getRow($query);
        $prefix = $r["prefix"];
    }
    return $prefix;
}

function get_numb($module, $pid)
{
    global $db;
    $numb = "ERROR";
    $query = $db->query("SELECT * FROM " . DB_PREFIX . "_" . $module . " WHERE id = '" . $pid . "' LIMIT 1");
    if ($db->numRows($query) > 0) {
        $r = $db->getRow($query);
        if (!empty($r["numb"])) {
            $numb = $r["numb"];
        }
    }
    return $numb;
}

function check_uid_org($org = null)
{
    global $db, $core;
    $allow = [1, 2, 3, 4, 5]; //организации со всеми правами
    $uid = $core->auth->user_info['id'];

    $query = $db->query("SELECT * FROM " . DB_PREFIX . "_users_org_relation WHERE uid = '" . $uid . "'");
    if ($db->numRows($query) > 0) {

        $values = [];
        while ($r = $db->getRow($query)) {
            $values[] = $r["org"];
        }

        $allow_this = $allow;
        if (!empty($org)) $allow_this[] = $org;

        $result = array_intersect($allow_this, $values);
        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function get_uid_org()
{
    global $db, $core;
    $uid = $core->auth->user_info['id'];
    $query = $db->query("SELECT * FROM " . DB_PREFIX . "_users_org_relation WHERE uid = '" . $uid . "'");
    $list_org = [];
    if ($db->numRows($query) > 0) {
        while ($r = $db->getRow($query)) {
            $list_org[] = $r["org"];
        }
    }
    return $list_org;
}

function check_minus_count_custom($request)
{
    //key -> id записи
    //data => ["0" => ["pid" => "","module" => "","count" => "","cid" => "","stock_id" => "",], "1" => []]
    //"org" => "",
    //"pid" => "",
    //"module" => "",
    //"mod" => "one/all"
    //если есть файл-ключ, то проводить в минус можно

    $count_save = [];

    $mod = empty($request["mod"]) ? "one" : $request["mod"];
    switch ($mod) {
        default:
        case "one":
            $result = null;
            break;
        case "all":
            $result = [];
            break;
    }

    $filename_minus = get_key_god("minus");
    if (file_exists($filename_minus)) return $result;
    if (!is_array($request["data"])) return $result;
    if (count($request["data"]) == 0) return $result;
    // if(!empty($request["org"]) && ($request["org"] == 2 || $request["org"] == 5)) return $result;
    if (!empty($request["module"]) && !empty($request["pid"])) {
        if (\Stocks\Models\StockMinus::where("module", $request["module"])->where("pid", $request["pid"])->exists()) return $result;
    }
    // dd($request);

    $reserv = new Reserv();
    foreach ($request["data"] as $key => $val) {
        $cid = $val["cid"];
        $catalog = \Catalog_v2\Model\Catalog::with("catalog_types")->where("id", $cid)->first();
        // if($cid == get_delivery_cid()) continue;
        if ($catalog->catalog_types->service == 1) continue;
        //сохраняем cid для проверки нескольких одинковых товаров
        if (!array_key_exists($cid, $count_save)) {
            $ndr = get_ndr(["cid" => $cid, "quantity" => "", "stock_id" => $val["stock_id"], "module" => $val["module"], "pid" => "", "returnType" => "array"]);
            // dd($ndr);
            $count_save[$cid] = $ndr["d"];
        }

        //добавить количества резерва в доступ
        $reserv_count = $reserv->get_reserv_count($val["pid"], $val["module"], $val["stock_id"], [1]);
        $count_save[$cid] += $reserv_count;

        $count_save[$cid] -= $val["count"];
        // if($uid == 33){
        //     dd($count_save);
        // }
        if (($count_save[$cid]) < 0) {
            //если нет -> выдать id записи break
            if ($mod == "all") $result[] = $key;
            if ($mod == "one") return $key;
        }
    }
    if ($mod == "all") return $result;
}

function check_minus_count($request)
{
    //["0" => ["pid" => "","module" => "","count" => "","cid" => "","stock_id" => "",], "1" => []]
    return check_minus_count_custom(["data" => $request]);
}

function get_key_god($name)
{
    $keys = [
        "minus" => "tmp/key/allow_minus.txt",
    ];
    return $keys[$name];
}

function get_sales_for_supply_o($id)
{
    global $db;
    $sales_id = [];
    $query = $db->query("SELECT DISTINCT sd.sales_id
    FROM `" . DB_PREFIX . "_supply_order_detail` as sod
    LEFT JOIN `" . DB_PREFIX . "_sales_detail` as sd ON(sod.sale_detail_id = sd.id)
    WHERE sod.sale_detail_id IS NOT NULL AND sod.supply_o = '" . $id . "'");
    if ($db->numRows($query) > 0) {
        while ($r = $db->getRow($query)) {
            $sales_id[] = $r["sales_id"];
        }
    }
    return $sales_id;
}

function getKnowledgeById($id)
{
    global $db;
    $id = intval($id);
    $query = $db->query("SELECT * FROM " . DB_PREFIX . "_knowledge_base WHERE id = '" . $id . "' AND status = '1' LIMIT 1");
    if ($db->numRows($query) > 0) {
        $r = $db->getRow($query);
        return (!empty($r['text']) ? $r['text'] : 'Информация отсутствует');
    } else {
        return 'Информация отсутствует';
    }
}

function getKnowledgeTooltip($matches)
{
    $id = intval($matches[1]);
    $location = $matches[2];
    $text = getKnowledgeById($id);
    return stripslashes('<span  data-tooltip="' . $text . '" data-tooltip-location="' . $location . '"><i class="fa fa-question-circle"></i></span>');
}

function get_org_data()
{
    global $db;
    $query_o = $db->query("SELECT n.*, o.name FROM " . DB_PREFIX . "_org as n LEFT JOIN " . DB_PREFIX . "_organizations as o on(o.id=n.oid) ORDER BY n.id");
    $data = [];
    if ($db->numRows($query_o) > 0) {
        while ($o_list = $db->getRow($query_o)) {
            $text = htmlspecialchars_decode($o_list["name"], ENT_QUOTES);
            $data[] = ["value" => $o_list["id"], "text" => $text];
        }
    }
    return $data;
}

function get_org_select_option($request = [])
{
    $id = empty($request["id"]) ? null : $request["id"];
    $data = get_org_data();
    $str = [];
    foreach ($data as $val) {
        $selected = $val["value"] == $id ? "selected" : "";
        $str[] = '<option value="' . $val['value'] . '" ' . $selected . '>' . $val['text'] . '</option>';
    }
    return implode("", $str);
}

function get_stock_data($request = [])
{
    $stock = \Stocks\Models\Stock::whereNotNull("name");
    if (isset($request["org"])) {
        $stock = $stock->where("org", $request["org"]);
    }

    $data = [];
    $data2 = [];
    foreach ($stock->get()->toArray() as $r) {
        if (!empty($r["name"])) {
            $data[] = ["value" => $r["id"], "text" => $r["name"], "org" => $r["org"]];
            $data2[$r["id"]] = $r["name"];
        }
    }
    $return_type = $request["return_type"] ?? null;
    switch ($return_type) {
        default:
            return $data;
            break;
        case "id_val":
            return $data2;
            break;
    }
}

function get_stock_select_option($request = [])
{
    $data = get_stock_data();
    $str = [];
    $selected = "";
    if (!empty($request["selected"])) {
        $selected = $request["selected"];
    }
    foreach ($data as $val) {
        $str[] = '<option class="stock_opt stock_opt_' . $val["org"] . '" value="' . $val['value'] . '" ' . ($val["value"] == $selected ? 'selected' : '') . '>' . $val['text'] . '</option>';
    }
    return implode("", $str);
}

function get_stock_name($id, $request = [])
{
    global $db;
    $query = $db->query("SELECT s.name, o.name as o_name, s.org
    FROM `" . DB_PREFIX . "_stock` as s
    LEFT JOIN `" . DB_PREFIX . "_org` as org ON(org.id = s.org)
    LEFT JOIN `" . DB_PREFIX . "_organizations` as o ON(o.id = org.oid)
    WHERE s.id = '" . $id . "'
    LIMIT 1");
    $name = '';
    $result = [];
    if ($db->numRows($query) > 0) {
        $r = $db->getRow($query);
        $name = $r["name"];
        $result["name"] = $name;
        $result["org"] = $r["org"];
        $result["org_name"] = $r["o_name"];
    }
    if (isset($request["org"])) {
        return $result;
    }
    return $name;
}

// function dd($var, $die = false, $type = ""){
//     echo '<pre>';
//     if(!empty($var) || $var === 0){
//         switch($type){
//             default:
//                 print_r($var);
//             break;
//             case "vd":
//                 var_dump($var);
//             break;
//         }
//     }
//     echo '</pre>';
//     if($die){
//         die();
//     }
// }

function get_all_categories_catalog()
{
    $categories = [];
    foreach (\Catalog_v2\Model\Categories::where("module", "catalog_v2")->get()->toArray() as $r) {
        $categories[$r["id"]] = $r;
    }
    return $categories;
}

function get_children_category($id, $categories, $catSave = [])
{
    $catSave[] = $id;
    foreach ($categories as $key => $val) {
        if ($val["parent_id"] == $id && !in_array($key, $catSave)) {
            $catSave = get_children_category($key, $categories, $catSave);
        }
    }
    return $catSave;
}

function get_parent_category($id, $categories)
{
    $parent_id = $categories[$id]["parent_id"];
    if ($parent_id == 0) {
        return $id;
    } else {
        return get_parent_category($parent_id, $categories);
    }
}

function getIntervalKv($d)
{
    $cKv = array(
        1 => array("s" => "01.01.", "e" => "31.03."),
        2 => array("s" => "01.04.", "e" => "31.06."),
        3 => array("s" => "01.07.", "e" => "30.09."),
        4 => array("s" => "01.10.", "e" => "31.12."),
    );
    $kv = intval((date('m', strtotime($d)) + 2) / 3);
    $y = date('Y', strtotime($d));
    $sd = $cKv[$kv]['s'] . $y;
    $ed = $cKv[$kv]['e'] . $y;
    return array("sd" => $sd, "ed" => $ed);
}

function getRegionArray()
{
    global $core, $db;
    $regionArray = array();
    $regionArray['22'] = 'Алтайский край';
    $regionArray['28'] = 'Амурская область';
    $regionArray['29'] = 'Архангельская область';
    $regionArray['30'] = 'Астраханская область';
    $regionArray['31'] = 'Белгородская область';
    $regionArray['32'] = 'Брянская область';
    $regionArray['33'] = 'Владимирская область';
    $regionArray['34'] = 'Волгоградская область';
    $regionArray['35'] = 'Вологодская область';
    $regionArray['36'] = 'Воронежская область';
    $regionArray['77'] = 'г. Москва';
    $regionArray['79'] = 'Еврейская автономная область';
    $regionArray['75'] = 'Забайкальский край';
    $regionArray['37'] = 'Ивановская область';
    $regionArray['99'] = 'Иные территории, включая город и космодром Байконур';
    $regionArray['38'] = 'Иркутская область';
    $regionArray['07'] = 'Кабардино-Балкарская Республика';
    $regionArray['39'] = 'Калининградская область';
    $regionArray['40'] = 'Калужская область';
    $regionArray['41'] = 'Камчатский край';
    $regionArray['09'] = 'Карачаево-Черкесская Республика';
    $regionArray['42'] = 'Кемеровская область';
    $regionArray['43'] = 'Кировская область';
    $regionArray['44'] = 'Костромская область';
    $regionArray['23'] = 'Краснодарский край';
    $regionArray['24'] = 'Красноярский край';
    $regionArray['45'] = 'Курганская область';
    $regionArray['46'] = 'Курская область';
    $regionArray['47'] = 'Ленинградская область';
    $regionArray['48'] = 'Липецкая область';
    $regionArray['49'] = 'Магаданская область';
    $regionArray['50'] = 'Московская область';
    $regionArray['51'] = 'Мурманская область';
    $regionArray['83'] = 'Ненецкий автономный округ';
    $regionArray['52'] = 'Нижегородская область';
    $regionArray['53'] = 'Новгородская область';
    $regionArray['54'] = 'Новосибирская область';
    $regionArray['55'] = 'Омская область';
    $regionArray['56'] = 'Оренбургская область';
    $regionArray['57'] = 'Орловская область';
    $regionArray['58'] = 'Пензенская область';
    $regionArray['59'] = 'Пермский край';
    $regionArray['25'] = 'Приморский край';
    $regionArray['60'] = 'Псковская область';
    $regionArray['01'] = 'Республика Адыгея (Адыгея)';
    $regionArray['04'] = 'Республика Алтай';
    $regionArray['02'] = 'Республика Башкортостан';
    $regionArray['03'] = 'Республика Бурятия';
    $regionArray['05'] = 'Республика Дагестан';
    $regionArray['06'] = 'Республика Ингушетия';
    $regionArray['08'] = 'Республика Калмыкия';
    $regionArray['10'] = 'Республика Карелия';
    $regionArray['11'] = 'Республика Коми';
    $regionArray['91'] = 'Республика Крым';
    $regionArray['12'] = 'Республика Марий Эл';
    $regionArray['13'] = 'Республика Мордовия';
    $regionArray['14'] = 'Республика Саха (Якутия)';
    $regionArray['15'] = 'Республика Северная Осетия - Алания';
    $regionArray['16'] = 'Республика Татарстан (Татарстан)';
    $regionArray['17'] = 'Республика Тыва';
    $regionArray['19'] = 'Республика Хакасия';
    $regionArray['61'] = 'Ростовская область';
    $regionArray['62'] = 'Рязанская область';
    $regionArray['63'] = 'Самарская область';
    $regionArray['78'] = 'Санкт-Петербург';
    $regionArray['64'] = 'Саратовская область';
    $regionArray['65'] = 'Сахалинская область';
    $regionArray['66'] = 'Свердловская область';
    $regionArray['92'] = 'Севастополь';
    $regionArray['67'] = 'Смоленская область';
    $regionArray['26'] = 'Ставропольский край';
    $regionArray['68'] = 'Тамбовская область';
    $regionArray['69'] = 'Тверская область';
    $regionArray['70'] = 'Томская область';
    $regionArray['71'] = 'Тульская область';
    $regionArray['72'] = 'Тюменская область';
    $regionArray['18'] = 'Удмуртская Республика';
    $regionArray['73'] = 'Ульяновская область';
    $regionArray['27'] = 'Хабаровский край';
    $regionArray['86'] = 'Ханты-Мансийский автономный округ - Югра';
    $regionArray['74'] = 'Челябинская область';
    $regionArray['20'] = 'Чеченская Республика';
    $regionArray['21'] = 'Чувашская Республика - Чувашия';
    $regionArray['87'] = 'Чукотский автономный округ';
    $regionArray['89'] = 'Ямало-Ненецкий автономный округ';
    $regionArray['76'] = 'Ярославская область';
    //foreach ($regionArray as $key => $value)
    // {
    //   $db->query("INSERT INTO `".DB_PREFIX."_geo_region` (`gcid`, `code`, `title`)
    //    VALUES ('1', '".$key."', '".$value."')");
    // }


    return $regionArray;
}

function disabled_all()
{
    global $core;
    $uid = $core->auth->user_info['id'];
    if (($uid != '79') && ($uid != '35') && ($uid != '1')) {
        //return "return false;";
        return "";
    } else {
        return "";
    }
}

function insert_shipment_minus(array $data)
{
    //['cid' => "", 'module' => "", 'pid' => "", 'stock_id' => "", 'count' => "", 'comment' => "", 'insert' => "before/after"]
    if (!isset($data["insert"])) {
        $data["insert"] = "before";
    }
    $ndr = get_ndr(["cid" => $data["cid"], "stock_id" => $data["stock_id"], "module" => $data["module"], "pid" => $data["pid"], "returnType" => "array"]);
    switch ($data["insert"]) {
        case "before":
            $initial_value = $ndr["n"];
            $sum = $ndr["n"] - $data["count"];
            break;
        case "after":
            $initial_value = $ndr["n"] + $data["count"];
            $sum = $ndr["n"];
            break;
    }
    if ($sum < 0) {
        \Boot\Models\Shipment::create([
            "cid" => $data["cid"],
            "module" => $data["module"],
            "pid" => $data["pid"],
            "uid" => \Boot\Support\Facades\Auth::id(),
            "stock_id" => $data["stock_id"],
            "initial_value" => $initial_value,
            "count" => $data["count"],
            "sum" => $sum,
            "date" => \Carbon\Carbon::now(),
            "comment" => $data["comment"],
        ]);
    }
}

function get_object_data($object_id)
{
    if (!empty($object_id)) {
        $r = \Object\Model\ObjectModel::find($object_id);
        if (!empty($r)) {
            return $r->toArray();
        }
    }
    return false;
}

function get_manufacture_price($data)
{
    //["cid" => "", "manufacture_price" => "", "price_type" => ""]
    global $db;
    $manufacture_price = empty($data["manufacture_price"]) ? 0 : $data["manufacture_price"];
    if ($manufacture_price == 0) {
        return $manufacture_price;
    }
    $manufacture_id = "";
    if (empty($data["manufacture_id"])) {
        if (!empty($data["cid"])) {
            $query = $db->query("SELECT * FROM `" . DB_PREFIX . "_catalog` WHERE id = '" . $data["cid"] . "' LIMIT 1");
            if ($db->numRows($query) > 0) {
                $r = $db->getRow($query);
                $manufacture_id = $r["man"];
            }
        }
    } else {
        $manufacture_id = $data["manufacture_id"];
    }
    if (empty($manufacture_id)) {
        return $manufacture_price;
    } else {
        $query = $db->query("SELECT *
        FROM `" . DB_PREFIX . "_catalog_manufacture_price`
        WHERE deleted_at IS NULL
        AND price_type_id = '" . $data["price_type"] . "'
        AND manufacture_id = '" . $manufacture_id . "'
        LIMIT 1");
        if ($db->numRows($query) > 0) {
            $r = $db->getRow($query);
            $formula = $r["formula"];
            $formula = str_replace(" ", "", $formula);
            $formula = str_replace("{{%manufacture_price%}}", $manufacture_price, $formula);
            // dd($formula);
            return calculating($formula);
        } else {
            return $manufacture_price;
        }
    }
}

function calculating($statement)
{
    $calcQueue = array();
    $operStack = array();
    $operPriority = array(
        '(' => 0,
        ')' => 0,
        '+' => 1,
        '-' => 1,
        '*' => 2,
        '/' => 2,
    );
    $token = '';
    foreach (str_split($statement) as $char) {
        // Если цифра, то собираем из цифр число
        if (($char >= '0' && $char <= '9') || $char === '.') {
            $token .= $char;
        } else {
            // Если число накопилось, сохраняем в очереди вычисления
            if (strlen($token)) {
                array_push($calcQueue, $token);
                $token = '';
            }
            // Если найденный символ - операция (он есть в списке приоритетов)
            if (isset($operPriority[$char])) {
                if (')' == $char) {
                    // Если символ - закрывающая скобка, переносим операции из стека в очередь вычисления пока не встретим открывающую скобку
                    while (!empty($operStack)) {
                        $oper = array_pop($operStack);
                        if ('(' == $oper) {
                            break;
                        }
                        array_push($calcQueue, $oper);
                    }
                } else {
                    // Встретили операцию кроме скобки. Переносим операции с меньшим приоритетом в очередь вычисления
                    while (!empty($operStack) && '(' != $char) {
                        $oper = array_pop($operStack);
                        if ($operPriority[$char] > $operPriority[$oper]) {
                            array_push($operStack, $oper);
                            break;
                        }
                        if ('(' != $oper) {
                            array_push($calcQueue, $oper);
                        }
                    }
                    // Кладем операцию на стек операций
                    array_push($operStack, $char);
                }
            } elseif (strpos(' ', $char) !== FALSE) {
                // Игнорируем пробелы (можно добавить что еще игнорируем)
            }
        }
    }
    // Вроде все разобрали, но если остались циферки добавляем их в очередь вычисления
    if (strlen($token)) {
        array_push($calcQueue, $token);
        $token = '';
    }
    // ... и оставшиеся в стеке операции
    if (!empty($operStack)) {
        while ($oper = array_pop($operStack)) {
            array_push($calcQueue, $oper);
        }
    }
    $calcStack = array();
    // Теперь вычисляем все то, что напарсили
    // Тут ошибки не ловил, но они могут быть (это домашнее задание)
    foreach ($calcQueue as $token) {
        switch ($token) {
            case '+':
                $arg2 = array_pop($calcStack);
                $arg1 = array_pop($calcStack);
                array_push($calcStack, $arg1 + $arg2);
                break;
            case '-':
                $arg2 = array_pop($calcStack);
                $arg1 = array_pop($calcStack);
                array_push($calcStack, $arg1 - $arg2);
                break;
            case '*':
                $arg2 = array_pop($calcStack);
                $arg1 = array_pop($calcStack);
                array_push($calcStack, $arg1 * $arg2);
                break;
            case '/':
                $arg2 = array_pop($calcStack);
                $arg1 = array_pop($calcStack);
                array_push($calcStack, $arg1 / $arg2);
                break;
            default:
                array_push($calcStack, $token);
        }
    }
    return array_pop($calcStack);
}

function recursBreadCrumbs($categories, $id)
{
    if (isset($categories[$id])) {
        return (empty($categories[$id]["parent_id"]) ? "" : recursBreadCrumbs($categories, $categories[$id]["parent_id"]) . ' / ') . $categories[$id]["name"];
    } else {
        return '';
    }
}

function tree_build($cats, $parent_id, $check = false)
{
    global $db;
    if (is_array($cats) and isset($cats[$parent_id])) {
        if ($check) {
            $tree = '<ul class="submain">';
        } else {
            $tree = '<ul class="submenu" id="submenu_' . $parent_id . '">';
        }
        foreach ($cats[$parent_id] as $cat) {
            $tree .= '<li><p id="point_' . $cat["id"] . '" class="point">' . $cat['name'] . '</p>';
            $tree .= tree_build($cats, $cat['id']);
            $tree .= '</li>';
        }
        $tree .= '</ul>';
    } else return null;
    return $tree;
}

function getDataOrganizations($id)
{
    global $db;
    $query = $db->query("SELECT * FROM `" . DB_PREFIX . "_organizations` WHERE id = '" . $id . "' LIMIT 1");
    $r = $db->getRow($query);
    return $r;
}

function find_org_uid($uid = null)
{
    $uid_n = is_null($uid) ? \Boot\Support\Facades\Auth::id() : $uid;
    $users_org = \Boot\Models\UsersOrg::where("uid", $uid_n)->where("choose", 1)->first();
    return $users_org->oid ?? 0;
}

// function ee($value, $request = []){
//     $type = empty($request["type"]) ? false : $request["type"];
//     switch($type){
//         default:
//             $enter = "\n";
//         break;
//         case "word":
//             $enter = "<w:br/>";
//         break;
//     }
//     $enter = empty($request["enter"]) ? $enter : $request["enter"];

//     $new_value = $value;
//     $new_value = str_replace("&lt;br&gt;", $enter, $new_value);
//     $new_value = str_replace("&amp;lt;br&amp;gt;;", $enter, $new_value);
//     $new_value = str_replace("&amp;amp;lt;br&amp;amp;gt;;", $enter, $new_value);
//     $new_value = html_entity_decode(htmlspecialchars_decode($new_value, ENT_QUOTES), ENT_QUOTES);
//     return $new_value;
// }

function getMonthRus()
{
    return ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
}

function array_to_options($array, $setting = [])
{
    $selected = empty($setting["selected"]) ? [] : $setting["selected"];
    if (!is_array($selected)) {
        $selected = [$selected];
    }
    $result = [];
    foreach ($array as $key => $value) {
        $s = in_array($key, $selected) ? "selected" : "";
        $result[] = "<option value=\"{$key}\" {$s}>{$value}</option>";
    }
    return implode("", $result);
}

function get_delivery_cid()
{
    return 21028;
}

function del__cache($name)
{
    $files = glob(ROOT . "/tmp/cache/{$name}.cache");
    foreach ($files as $file) {
        if (is_file($file)) unlink($file);
    }
}

function get_index_file($request)
{
    $downId = $request["id"];
    $attach = \Boot\Models\Attach::findOrFail($downId);
    $attach->increment('downloads');
    if (empty($attach->class_name)) {
        location($request['url']);
    } else {
        $className = "\\" . $attach->class_name;
        // dd($className);
        if (class_exists($className)) {
            $ftp = new $className;
            $ftp->get_file_id($downId);
        } else {

            location($request['url']);
        }
    }
    // switch ($request["mod"]) {
    //     default:

    //     break;
    //     case "pto":
    //     case "boiler":
    //     case "task":
    //     case "task_comment":
    //     case "storage_detail":
    //         $ftp = new StorageFTP;
    //         $ftp->get_file_id($downId);
    //     break;
    // }
}

function getDirectorDetail($org, $request)
{
    //["oid"]
    if (!empty($org['director_detail']) && is_object($org['director_detail'])) {
        return $org['director_detail'];
    } else {
        if (empty($org['director_detail']) || (mb_strpos($org['director_detail'], 'detail') !== false)) {
            $result = Boot\Support\Facades\Data::clean('name', $org['director']);
            $Organizations = \Organizations\Model\Organizations::find($request['oid']);
            $Organizations->update(['director_detail' => $result]);
            return $Organizations->director_detail;
        } else {
            $result = json_decode($org['director_detail']);
            if (is_array($result)) {
                $result = Boot\Support\Facades\Data::clean('name', $org['director']);
                $organizations = \Organizations\Model\Organizations::find($request['oid']);
                $organizations->update(['director_detail' => $result]);
                $result = $organizations->director_detail;
            }
            return $result;
        }
    }
}

function check_can_delete_module($id, $class)
{
    $status = true;
    $data = [];
    $method = "check_can_delete";
    if (class_exists($class)) {
        if (method_exists($class, $method)) {
            try {
                foreach ($id as $i) {
                    $class::$method($i);
                }
            } catch (\Exception $e) {
                $status = false;
                $data = json_decode($e->getMessage());
            } finally {
                return json_encode([
                    "status" => $status,
                    "data" => $data,
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            throw new \Exception("Method \"{$class}\" not found!");
        }
    } else {
        throw new \Exception("Class \"{$class}\" not found!");
    }
}
