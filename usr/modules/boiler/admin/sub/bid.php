<?php

use Boot\Support\Str;
use DocxMerge\DocxMerge;
use Boiler\Model\BoilerBid;
use Boot\Support\Facades\Auth;
use Boot\Support\Facades\Http;
use Boiler\Model\BoilerBidData;
use Boiler\Model\BoilerBidType;
use Boot\Support\Facades\Config;
use Boot\Support\Office;
use Boot\System\Office\Merge;
use Boot\System\Office\OfficeSupport;
use Specification\Support\Specification;

function bid_main(){
    global $adminTpl;
	$lnn = "Заявки";
	$adminTpl->admin_head('Котёл продаж | '.$lnn);
	$atributs = array();
	$atributs['opts'] = 'open';
	$atributs['module'] = 'module';
	$atributs['module_name'] = 'boiler/bid';
    $atributs['module_open'] = 'detail';
    $atributs['button'] = 'Добавить заявку';
    $atributs['button_url'] = '';
    $atributs['button_onclick'] = "create_boiler();";
    $atributs['js'] = 'setTimeout(function () {$("#jqBid").jqGrid(\'setGridParam\',{postData:{archive: \'0\'}}); $(\'#jqBid\').trigger(\'reloadGrid\');}, 150);';
    loadGrid('jqBid', $lnn, $atributs, ROOT.'/usr/modules/boiler/admin/grid/');
    $adminTpl->footIncludes[] = '<script src="usr/modules/boiler/admin/asset/js/function.js?'.time().'"></script>';
    $adminTpl->footIncludes[] = '<script src="usr/modules/boiler/admin/asset/js/main.js?'.time().'"></script>';
	$adminTpl->admin_foot();
}

function bid_add($request = []){
    $uid = Auth::id();
    $org = empty($request["org"]) ? 1 : $request["org"];
    $date_create = date("Y-m-d H:i:s");
    $n = \Boot\Support\Module::numb('boiler_bid', $org, $date_create);

    $data = [];
    $data['numb_counter'] = $n["numb_counter"];
    $data['numb'] = $n["numb"];
    $data['date_create'] = $date_create;

    if(!isset($request["uid"])){
        $data['uid'] = $uid;
    }
    $data['org'] = $org;
    if(!empty($request["object"])){
        $data['object'] = $request["object"];
    }
    if(!empty($request["object_id"])){
        $data['object_id'] = $request["object_id"];
    }
    if(!empty($request["oid"])){
        $data['oid'] = $request["oid"];
    }
    if(!empty($request["title"])){
        $data['title'] = $request["title"];
    }
    if(!empty($request["client_title"])){
        $data['client_title'] = $request["client_title"];
    }

    $boiler = Boiler\Model\BoilerBid::create($data);
    $iid = $boiler->id;
    $chat = new Chat();
    $chat->create_chat("boiler_bid", $iid, $uid, "Котел №".$n["numb"], 1);

    return $iid;
}

function bid_notif_add($id){
    global $db;
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_boiler_bid WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);

    $uid_group = getGroup(["group" => "sales"]);
    $attr = [];
    $attr['uid'] = $uid_group;
    $attr['title'] = 'Новая заявка в котле продаж №'.$r["numb"];
    $attr['text'] = 'Новая заявка в котле продаж №'.$r["numb"];
    $attr['icon'] = config('app.url').'/files/push/new_storage.png';
    $attr['url'] = config('app.url').'/administration/module/boiler/bid/detail/'.$id;
    $attr['pNotif'] = true;
    $attr['pPush'] = true;
    $attr['module'] = "boiler_bid";
    $attr['pid'] = $id;
    sendAllNotif($attr);
}

function bid_query($id){
    global $db;
    $query = $db->query("SELECT bb.*, obj.name as obj_name, o.name as o_name
    FROM `".DB_PREFIX."_boiler_bid` as bb
    LEFT JOIN `".DB_PREFIX."_object` as obj ON(obj.id = bb.object)
    LEFT JOIN `".DB_PREFIX."_organizations` as o ON(o.id = bb.oid)
    WHERE bb.id = '".$id."'
    LIMIT 1");
    $bid = $db->getRow($query);
    return $bid;
}

function bid_type_query($id, $request = []){
    global $db;
    $limit = empty($request["limit"]) ? "" : "LIMIT {$request["limit"]}";
    $query = $db->query("SELECT bbt.*, bbf.name as bbf_name
    FROM `".DB_PREFIX."_boiler_bid_type` as bbt
    LEFT JOIN  `".DB_PREFIX."_boiler_bid_folder` as bbf ON(bbf.id = bbt.folder_id)
    WHERE bbt.bid_id = '".$id."'
    ORDER BY bbt.position {$limit}");
    return $query;
}

function bid_detail($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    if($id){
        $bid = bid_query($id);
        $lnn = "Заявка №".$bid["numb"];
        $adminTpl->admin_head('Котёл продаж | '.$lnn);
        echo '<link rel="stylesheet" href="/usr/modules/boiler/admin/asset/css/bid.css?'.time().'">';
        $adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/price.js?'.time().'"></script>';
        $adminTpl->footIncludes[] = '<script src="/usr/other/sub/price/js/function.js?'.time().'"></script>';
        $adminTpl->footIncludes[] = '<script src="/usr/modules/boiler/admin/asset/js/function.js?'.time().'"></script>';
        $adminTpl->footIncludes[] = '<script src="/usr/modules/boiler/admin/asset/js/bid.js?'.time().'"></script>';
        $adminTpl->footIncludes[] = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />';
        $adminTpl->footIncludes[] = '<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>';
        ?>
            <input type="hidden" value="<?php echo $id;?>" id="mainId">
            <div class="admin-form p20">
                <div class="row mb20">
                    <div class="col-sm-12">
                        <div id="panelProgress">
                            <?php get_panel_progress($id); ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-default">
                            <div class="row" id="panelHeader">
                                <?php get_panel_header($id);?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-sm-12 col-sm-push-8">
                        <div class="panel panel-default">
                            <div class="panel-body" id="panelInfo">
                                <?php get_panel_info($id);?>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-body" id="panelTotal">
                                <div class="mb20">
                                    <b class="ch" title="Только выбранные позиции">Итого закупа: </b>
                                    <span id="panelTotal_supply_total"><?php echo number_format(get_all_supply_total($id),2,'.',' 	'). ' руб.';?></span>
                                </div>
                                <div>
                                    <b class="ch" title="Только выбранные позиции">Итого: </b>
                                    <span id="panelTotal_total"><?php echo number_format(get_all_total($id),2,'.',' 	'). ' руб.';?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-sm-12 col-sm-pull-4" id="panelMain">
                        <?php get_panel_main($id);?>
                    </div>
                </div>
            </div>

            <!-- ******** Modal ******** -->

            <div class="modal fade admin-form" id="filesModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="">Добавить файлы</h5>
                  </div>
                  <div class="modal-body">
                      <form id="filesModalForm">
                        <?php
                            $attribute = [];
                            $attribute["name"] = "1";
                            $attribute["script"] = true;
                            $adminTpl->footIncludes[] = uploaderMaster("plugin", $attribute);
                            $adminTpl->footIncludes[] = uploaderMaster("script", $attribute);
                            echo uploaderMaster("css", $attribute);
                            echo uploaderMaster("upload", $attribute);
                            echo uploaderMaster("view", $attribute);
                        ?>
                      </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="save_files();">Добавить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade" id="client_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Информация о контактном лице</h5>
                  </div>
                  <div class="modal-body" id="client_info_modal_body">
                    ...
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade admin-form" id="create_sales_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Сформировать продажу</h5>
                  </div>
                  <div class="modal-body">
                    <form id="create_sales_modal_body">
                        ...
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="create_sales_pre();" id="create_sales_modal_btn">Сформировать</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade admin-form" id="create_spec_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Сформировать спецификацию</h5>
                  </div>
                  <div class="modal-body">
                    <form id="create_spec_modal_body">
                        ...
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="create_spec_pre();" id="create_spec_modal_btn">Сформировать</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade admin-form" id="folder_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Папка</h5>
                  </div>
                  <div class="modal-body">
                    <form id="folder_modal_body">
                        <label for="folder_modal_select">Выберите папку</label>
                        <select name="folder_id" id="folder_modal_select" class="form-control" onchange="toggle_folder_name_inp(this);"></select>
                        <input type="hidden" name="id" id="folder_modal_type_id" value="">
                        <input type="text" value="" placeholder="Введите название (необязательно)" class="form-control" id="folder_modal_name" name="name">
                    </form>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="folder_s();" id="folder_modal_btn">Выполнить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>

            <?php

                $modal_data = [
                    [
                        "id" => "object",
                        "title" => "Выберите объект",
                        "body" => oselect_modal_body($bid["object"], "objectSelect", "getObjectToSelect"),
                    ],
                    [
                        "id" => "uid",
                        "title" => "Выберите ответсвенного",
                        "body" => oselect_modal_body($bid["uid"], "uidSelect", "getUsersToSelect"),
                    ],
                    [
                        "id" => "oid",
                        "title" => "Выберите контрагента",
                        "body" => oselect_modal_body($bid["oid"], "oidSelect", "getOrganizations"),
                    ],
                    [
                        "id" => "org",
                        "title" => "Выберите организацию",
                        "body" => org_modal_body($bid["org"]),
                        "etc_func" => "update_org(getOrgSelect()).then(() => { {%DEF_FUNC%} });",
                    ],
                    [
                        "id" => "date_end",
                        "title" => "Выберите дату окночания",
                        "body" => date_end_body($bid["date_end"]),
                    ],
                    [
                        "id" => "type",
                        "title" => "Выберите тип",
                        "body" => type_body(),
                        "etc_func" => "insert_field_type({section:$('#sectionSelect').val(), type:$('#typeSelect').val()}).then(() => {update_panel_main(); update_panel_info();});",
                        //"etc_func" => "Promise.all([{%DEF_FUNC%}, save_field_modal('section', 'sectionSelect')]).then(() => {update_panel_main();});",
                    ],
                    [
                        "id" => "dataUid",
                        "title" => "Выберите ответсвенного",
                        "body" => oselect_modal_body(null, "dataUidSelect", "getUsersToSelect"),
                        "etc_func" => "save_uid_data_change(this);",
                    ],
                    [
                        "id" => "send_notif_help",
                        "title" => "Уведомление",
                        "body" => oselect_modal_body(null, "send_notif_helpSelect", "getUsersAllToSelect", false, true).'
                        <textarea id="send_notif_helpTextarea" name="send_notif_helpTextarea" class="form-control mt20" placeholder="Введите комментарий..."></textarea>
                        <input type="hidden" value="'.$id.'" name="id">',
                        "etc_func" => "send_notif_help_modal();",
                        "btn_text" => "Отправить",
                    ],
                ];

                $users_type = boiler_bid_users_type();
                foreach($users_type as $ut_key => $ut_val){
                    $modal_data[] = [
                        "id" => "uid_users_".$ut_key,
                        "title" => "Выберите пользователя",
                        "body" => oselect_modal_body(null, "uid_users_".$ut_key."Select", ($ut_key == 0 ? "getUsersAllToSelect" : "getUsersToSelect")),
                        //"etc_func" => "append_boiler_bid(getMainId(), 'uid_client', $('#uid_clientSelect').val()).then(()=>{update_panel_info();});",
                        "etc_func" => "add_boiler_bid_users_modal({
                            bid_id: getMainId(),
                            type: ".$ut_key.",
                            uid: $('#uid_users_".$ut_key."Select').val(),
                            text: '".boiler_bid_users_type($ut_key)."'}
                        );",
                    ];
                }

                foreach($modal_data as $data){
                    modal_generate($data);
                }

            ?>

        <?php
        xeditableInit();
        require_once(ROOT."usr/modules/sales/admin/sub/ndr.php");
        ndr_init();
        $fc_object = new FastCreate("object");
        $fc_o = $fc_object->get_fast_create();
        echo $fc_o->modal;
        $adminTpl->footIncludes[] = $fc_o->script;

        $fc_organization = new FastCreate("organization");
        $fc_org = $fc_organization->get_fast_create();
        echo $fc_org->modal;
        $adminTpl->footIncludes[] = $fc_org->script;

        $adminTpl->admin_foot();
    }
}

function modal_generate($data = ["id" => "", "title" => "", "body" => ""]){
    $def_func = "save_field_modal('".$data['id']."', '".$data['id']."Select')";
    $etc_func = isset($data["etc_func"]) ? $data["etc_func"] : "";
    if(empty($etc_func)){
        $func = $def_func.";";
    } else {
        $func = str_replace("{%DEF_FUNC%}", $def_func, $etc_func);
    }

    $btn = isset($data["btn"]) ? $data["btn"] : "";

    $btn_text = isset($data["btn_text"]) ? $data["btn_text"] : "Сохранить";

?>
    <div class="modal fade admin-form customModal" data-id="<?php echo $data["id"];?>" id="<?php echo $data["id"];?>Modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id=""><?php echo $data["title"];?></h5>
          </div>
          <div class="modal-body">
              <form onsubmit="return false;" id="<?php echo $data["id"];?>Form">
                <?php echo $data["body"];?>
              </form>
          </div>
          <div class="modal-footer">
            <?php echo $btn;?>
            <button type="button" class="btn btn-primary" data-dismiss="modal" id="<?php echo $data["id"];?>Btn" onclick="<?php echo $func?> false_ess_restore();"><?php echo $btn_text;?></button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          </div>
        </div>
      </div>
    </div>
<?php
}

function oselect_modal_body($id = null, $name= null, $ajax= null, $disabled = false, $multiple = false){
    oSelect("jsAdminTpl", $name, $id, $ajax);
    return oSelect("select", $name, $id, $ajax, $disabled, $multiple, true);
}

function org_modal_body($id){
    global $db;
    $query = $db->query("SELECT o.name, org.id as org_id FROM ".DB_PREFIX."_org as org LEFT JOIN ".DB_PREFIX."_organizations as o ON(o.id = org.oid) ORDER BY org.id");
    $result = [];
    $result[] = '<select class="select2" id="orgSelect">';
    while($r = $db->getRow($query)){
        $selected = "";
        if($id == $r["org_id"]){
            $selected = "selected";
        }
        $result[] = '<option value="'.$r["org_id"].'" '.$selected.'>'.$r["name"].'</option>';
    }
    $result[] = '</select>';
    return implode("", $result);
}

function date_end_body($date){
    $result = '<input type="datetime-local" class="form-control" id="date_endSelect" value="'.$date.'">';
    return $result;
}

function type_body(){
    $result = [];
    $result[] = '<select class="select2" id="sectionSelect" onchange="if(check_open_modal(\'typeModal\')) get_type_select_insert($(this).val());">';
    $list_type = list_type_group();
    unset($list_type[0]);
    reset($list_type);
    $section = key($list_type);
    // $section = 0;
    $result[] = section_select($section);
    $result[] = '</select>';

    $result[] = '<select class="select2" id="typeSelect">';
    $result[] = type_select($section);
    $result[] = '</select>';
    return implode("", $result);
}

function section_select($section){
    $result = [];
    $list_type_group_n = list_type_group_name();
    unset($list_type_group_n[0]);
    foreach($list_type_group_n as $key => $val){
        $selected = "";
        if($key == $section){
            $selected = "selected";
        }
        $result[] = '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
    }
    return implode("", $result);
}

function type_select($section, $type = null){
    $result = [];
    $list_type_n = list_type_name();
    $list_type_g = list_type_group($section);
    foreach($list_type_g as $val){
        $selected = "";
        if($val == $type){
            $selected = "selected";
        }
        $result[] = '<option value="'.$val.'" '.$selected.'>'.$list_type_n[ $val ].'</option>';
    }
    return implode("", $result);
}
//основные категории
function list_type_group_name($group = null){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $list = [
        0 => "Неизвестная",
        1 => "Котельная",
        2 => "Тепловая сеть",
        3 => "ИТП",
        4 => "Отопление и вентиляция",
        5 => "Водоснабжение и канализация",
    ];
    return is_null($group) ? $list : $list[$group];
}
//что входит в категории
function list_type_group($group = null){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $list = [
        0 => [0],
        1 => [1,2,3,4,5,6],
        2 => [7],
        3 => [8,9,10,11,12,13,14,15,16,17,18,19,27,28],
        4 => [20,21],
        5 => [22,23,24,25,26],
    ];
    return is_null($group) ? $list : $list[$group];
}
//название подкатегорий
function list_type_name($type = null){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    $list = [
        0 => "Неизвестная",
        1 => "ТМ-К",
        2 => "АТМ-К",
        3 => "ГСВ",
        4 => "АГСВ",
        5 => "ОВ",
        6 => "ВК",
        7 => "ТС",
        8 => "ТМ-ИТП",
        9 => "АТМ",
        10 => "БВ",
        11 => "БОВ",
        12 => "УК-ТМ",
        13 => "БГВС",
        14 => "БП",
        15 => "УУТЭ-ТМ",
        16 => "ШУУ",
        17 => "ШУН",
        18 => "ШУТП",
        19 => "УУТЭ-АТМ",
        20 => "ОВ",
        21 => "УК-ОВ",
        22 => "ВК",
        23 => "НС",
        24 => "ПНС",
        25 => "ШУН",
        26 => "УУ-ВК",
        27 => "БР",
        28 => "Э",
    ];
    return is_null($type) ? $list : $list[$type];
}
//что входит в подкатегории
function list_type($type = null){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    //$all = [407,300,309,310,281,296,272,313,292,294,305,307,271];
    $list = [
        0 => [],
        1 => [407,300,309,310,281,296,272,313,292,305,307,271,382],
        2 => [296,272,382],
        3 => [281,307,382],
        4 => [382],
        5 => [294,382],
        6 => [310,281,296,272,313,292,307,271,382],
        7 => [310,281,296,292,307,271,382],
        8 => [407,309,310,281,296,272,313,292,305,307,271,382],
        9 => [296,272,307,382],
        10 => [407,281,296,272,292,307,271,382],
        11 => [407,310,281,296,272,292,307,271,382],
        12 => [407,281,296,272,292,307,271,382],
        13 => [407,310,281,296,272,292,307,271,382],
        14 => [407,310,281,296,272,313,292,305,307,271,382],
        15 => [407,281,296,272,292,307,271,382],
        16 => [296,272,307,382],
        17 => [296,272,307,382],
        18 => [296,272,307,382],
        19 => [296,272,307,382],
        20 => [281,296,292,294,271,382],
        21 => [407,281,296,272,292,307,271,382],
        22 => [407,310,281,296,272,313,292,305,307,271,382],
        23 => [407,310,281,296,313,292,382],
        24 => [407,310,281,296,313,292,382],
        25 => [296,382],
        26 => [296,382],
        27 => [407,309,281,296,272,292,307,271,382],
        28 => [382],
    ];
    return is_null($type) ? $list : $list[$type];
}

function list_type_category_uid($cat = null){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    //категория - пользователь
    $list = [

    ];
    return is_null($cat) ? $list : $list[$cat];
}

// *********************************************************************************

function get_panel_progress($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    if($id){
        $bid = bid_query($id);
        $progress = [
            "new" => [
                "name" => "Заявка создана",
                "base_color" => "#39A8EF",
            ],
            "application_ready" => [
                "name" => "Заявка готова",
                "base_color" => "#2FC6F6",
            ],
            "application_sent_customer" => [
                "name" => "Заявка отправлена заказчику",
                "base_color" => "#55D0E0",
            ],
            "customer_thinks" => [
                "name" => "Заказчик думает",
                "base_color" => "#47E4C2",
            ],
            "complete_deal" => [
                "name" => "Завершить сделку",
                "base_color" => "#7BD500",
            ],
        ];
        $base_color = $progress[ $bid["progress"] ]["base_color"];
        $default_color = '#d3d7dc';
        $color = $base_color;
        foreach($progress as $key => $val){
            ?>

                <div data-default_color="<?php echo $default_color;?>" data-base_color="<?php echo $val["base_color"];?>" data-key="<?php echo $key;?>"
                class="progress_step <?php echo $key == $bid["progress"] ? 'progress_active' : '';?>"
                onmouseover="progress_hover_color(this);"
                onmouseout="progress_hover_out_color();"
                onclick="save_field('progress', '<?php echo $key;?>').then(() => {update_panel_progress();});">
                    <div class="progress_step_item" style="background-color: <?php echo $color;?>;">
                        <?php echo $val["name"];?>
                    </div>
                    <div class="progress_step_item_triangle" style="border-left: 10px solid <?php echo $color;?>;"></div>
                </div>

            <?php
            if($key == $bid["progress"]){
                $color = $default_color;
            }
        }
    }
}

function get_panel_header($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    if($id){
        $bid = bid_query($id);
        $lnn = "Заявка №".$bid["numb"];
        ?>
        <div class="col-sm-12 col-lg-11">
            <div class="df">
                <h3><?php echo $lnn;?> (<span title="Название" id="title" class="cp" onclick="xeditable_title();"><?php echo $bid["title"];?></span>)</h3>
                <?php
                if(!empty($bid["client_title"])){
                    ?>

                    <h3 class="ml10">(<span title="Название клиента" id="title_client" class=""><?php echo $bid["client_title"];?></span>)</h3>

                    <?php
                }
                ?>
            </div>
            <div class="df"><h4 class="mr10">Комментарий:</h4><h4 id="main_comment" class="cp" title="Комментарий"><?php echo $bid["comment"];?></h4></div>
            <?php
            if(!empty($bid["client_comment"])){
                ?>
                    <div class="df"><h4 class="mr10">Комментарий клиента:</h4><h4 id="main_client_comment" class="" title="Комментарий клиента"><?php echo $bid["client_comment"];?></h4></div>
                <?php
            }
            ?>
        </div>
        <div class="col-sm-12 col-lg-1">
              <button type="button" class="btn btn-block btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bars"></i>
              </button>
              <ul class="dropdown-menu pull-right">
                <li>
                    <a href="javascript:void(0);" onclick="open_modal('send_notif_helpModal');">
                        <i class="fas fa-bell text-info mr10"></i>Уведомление
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" onclick="select_all_modal();">
                        <i class="fas fa-check text-info mr10"></i>Выбрать все позиции
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0);" onclick="select_all_off_modal();">
                        <i class="fas fa-check text-info mr10"></i>Отмена выбора всех позиций
                    </a>
                </li>
              </ul>
        </div>
        <?php
    }
}

function get_panel_main($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    if($id){
        get_panel_main_head($id);
        get_panel_main_body($id);
    }
}

function get_panel_main_head($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    if($id){
        $query = bid_type_query($id);
        if($db->numRows($query) > 0){

            $type_id = isset($_COOKIE["boiler_bid_type_".$id]) ? $_COOKIE["boiler_bid_type_".$id] : null;

            $btq_flag_check = true;
            $btq_panel = [];
            $folder = [];
            $folder_name = [];
            while($btq = $db->getRow($query)){
                $btq_flag_active = false;
                if($btq["id"] == $type_id){
                    $btq_flag_check = false;
                    $btq_flag_active = true;
                }
                $btq_panel[ $btq["id"] ] = [
                    "active" => $btq_flag_active,
                    "id" => $btq["id"],
                    "section" => $btq["section"],
                    "type" => $btq["type"],
                    "comment" => $btq["comment"],
                    "total" => get_all_total($id, $btq["id"]),
                    "total_sup" => get_all_supply_total($id, $btq["id"]),
                    "folder_id" => $btq["folder_id"],
                    "active_color" => $btq["active_color"],
                    "color" => $btq["color"],
                ];
                if(!empty($btq["folder_id"])){
                    $folder[$btq["folder_id"]][] = $btq["id"];
                    $folder_name[$btq["folder_id"]] = $btq["bbf_name"];
                }
            }

            if($btq_flag_check){
                reset($btq_panel);
                $key = key($btq_panel);
                $btq_panel[$key]["active"] = true;
            }

            ?>

                <div id="panelMain_head">
                    <?php
                        if(count($folder) > 0){
                            foreach($folder as $key => $val){
                                $name_folder = 'folder_'.$key;
                                $open_folder = isset($_COOKIE["#".$name_folder]);
                                ?>
                                <div class="folder">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <div class="col-xs-9 col-lg-8 panel-text">
                                                <span class="folder_name cp" data-pk="<?php echo $key;?>">
                                                    <?php echo $folder_name[$key] ?>
                                                </span>
                                            </div>
                                            <div class="col-xs-12 col-lg-2 pull-right">
                                                <button class="btn btn-danger btn-block" onclick="del_boiler_bid_folder_modal(<?php echo $key;?>)">Удалить</button>
                                            </div>
                                        </div>
                                        <div class="toggle_folder cp" onclick="toggle_folder(this, '#<?php echo $name_folder;?>');"><i class="fas fa-chevron-<?php echo $open_folder ? 'up' : 'down';?>"></i></div>
                                    </div>
                                    <div style="display: <?php echo $open_folder ? "" : "none";?>" id="<?php echo $name_folder;?>" class="folder_con">
                                        <?php
                                            foreach($val as $v){
                                                panel_template($btq_panel[$v]);
                                                unset($btq_panel[$v]);
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                        }

                        foreach($btq_panel as $val){
                            panel_template($val);
                        }
                    ?>

                </div>

            <?php
        }
    }
}

function panel_template($val){

        $active_color = !empty($val["active_color"]);
        $color_display = $active_color ? "" : "none";
        $color = $val["color"];

    ?>

    <div class="panel panel-default panel_type cp <?php echo $val['active'] ? 'type_active' : '';?>" onclick="<?php echo $val["active"] ? "" : "change_type_selected(".$val["id"].");";?>">
        <div class="panel-body">
            <div class="type_name">
                <div class="type_name_name"><?php echo list_type_group_name($val["section"])." &mdash; ".list_type_name($val["type"]);?></div>
                <div class="type_name_folder" onclick="folder_modal(<?php echo $val['id'];?>, '<?php echo $val['folder_id'];?>');"><i class="far fa-folder-plus fa-2x"></i></div>
            </div>
            <div>
                <span>(</span>
                <span class="cp type_comment tc_<?php echo $val['id'];?>" data-pk="<?php echo $val['id'];?>" onclick="event.stopPropagation();"><?php echo $val["comment"];?></span>
                <span>)</span>
            </div>
            <div class="mt20">
                <div>
                    <b class="ch" title="Только выбранные позиции">Итого закупа: </b>
                    <span><?php echo number_format($val["total_sup"],2,'.',' ').' руб.';?></span>
                </div>
                <div>
                    <b class="ch" title="Только выбранные позиции">Итого: </b>
                    <span><?php echo number_format($val["total"],2,'.',' ').' руб.';?></span>
                </div>
            </div>
            <div class="mt20" onclick="window.event.stopPropagation();">
                <label class="mr10">
                    <input type="checkbox" onchange="change_active_color('<?php echo $val['id'];?>', this);" class="mr10" <?php echo $active_color ? "checked" : ""; ?>>
                    <i class="fas fa-palette text-info"></i>
                </label>
                <input
                    type="color"
                    class="input_color_type_<?php echo $val['id'];?>"
                    onchange="change_color_bid_type('<?php echo $val['id'];?>', this);"
                    style="display: <?php echo $color_display;?>;"
                    value="<?php echo $color;?>"
                >
            </div>
        </div>
    </div>

    <?php
}

function get_panel_main_body($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    if($id){
        $query = bid_type_query($id);
        if($db->numRows($query) > 0){

            $bid = bid_query($id);
            $type_id = isset($_COOKIE["boiler_bid_type_".$id]) ? $_COOKIE["boiler_bid_type_".$id] : null;

            $btq_flag_first = true;
            while($btq = $db->getRow($query)){
                if($btq_flag_first){
                    $bid_type = $btq["type"];
                    $bid_allow_category = $btq["allow_category"];
                    $bid_type_id = $btq["id"];
                }
                if($btq["id"] == $type_id){
                    $bid_type = $btq["type"];
                    $bid_allow_category = $btq["allow_category"];
                    $bid_type_id = $btq["id"];
                }
                $btq_flag_first = false;
            }

            $allow_category = json_decode($bid_allow_category, true);

            $uid = Auth::id();

            $all_category = $core->aCatList('catalog_v2');

            $data_make = [];
            $data_missing = [];
            $data = [];

            $list = list_type($bid_type);

            //защита на имеющиеся типы(не пригодилась)
            // if(count($list) > 0){
                // $list_str = implode(", ", $list);
                // $query = $db->query("SELECT * FROM `".DB_PREFIX."_boiler_bid_data` WHERE `bid_type_id` = '".$bid_type_id."' AND `category` IN (".$list_str.") ORDER BY position");

                $query = $db->query("SELECT * FROM `".DB_PREFIX."_boiler_bid_data` WHERE `bid_type_id` = '".$bid_type_id."' ORDER BY position");
                if($db->numRows($query) > 0){
                    while($bbd = $db->getRow($query)){
                        $data_make[] = [
                            "create" => true,
                            "id" => $bbd["id"],
                            "category" => $bbd["category"],
                            "uid" => $bbd["uid"],
                            "comment" => $bbd["comment"],
                            "client_comment" => $bbd["client_comment"],
                            "status" => $bbd["status"],
                        ];
                        $key = array_search($bbd["category"], $list);
                        unset($list[$key]);
                    }
                }

            // }


            if(count($list) > 0){
                foreach($list as $val){
                    $data_missing[] = [
                        "create" => false,
                        "id" => false,
                        "category" => $val,
                        "uid" => false,
                        "comment" => null,
                        "client_comment" => null,
                        "status" => false,
                    ];
                }
            }

            $data = array_merge($data_make, $data_missing);

            ?>
                <div id="panelMain_body">
            <?php

            foreach($data as $key => $val){

                $checkbox_dis = "";
                $checkbox_dis_class = "";
                if($val["create"]){
                    $checkbox_dis = "disabled";
                    $checkbox_dis_class = "missing";
                }

            ?>
            <div class="move_container <?php echo $val["create"] ? "move_container_create" : ""; ?>" data-id="<?php echo $val["id"];?>">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="main-elems">
                            <div class="panel-checkbox col-xs-3 col-lg-1">
                                <div class="custom_checkbox">
                                    <input type="checkbox" id="checkbox_<?php echo $key;?>" onchange="change_allow_category(<?php echo $bid_type_id;?>, $(this).prop('checked'), <?php echo $val['category'];?>);" <?php echo in_array($val["category"], $allow_category) ? "checked" : "";?> <?php echo $checkbox_dis;?>>
                                    <label for="checkbox_<?php echo $key;?>" class="<?php echo $checkbox_dis_class;?> <?php echo $val['status'] === '2' ? "status_confirm" : ""; ?>"></label>
                                </div>
                            </div>
                            <div class="panel-text col-xs-9 col-lg-6">
                                <div class="panel-text-type">
                                    <?php

                                        if($val["create"]){
                                            echo '<a href="/administration/module/boiler/bid/data_detail/'.$val["id"].'" target="_blank">'.$all_category[ $val["category"] ].'</a>';
                                        } else {
                                            echo $all_category[ $val["category"] ];
                                        }

                                    ?>
                                </div>

                                <?php

                                if($val["create"]){
                                    ?>

                                    <div class="panel-text-user">
                                        <div class="">Ответственный:&emsp;</div>
                                        <div class="cp data_uid" onclick="uid_data_change(<?php echo $val['id'];?>, '<?php echo $val['uid'];?>', '<?php echo fullUserName($val['uid']);?>');">
                                            <?php echo shortUserName($val["uid"]);?>
                                        </div>
                                    </div>
                                    <div class="panel-comment">
                                        <div class="">Комментарий:&emsp;</div>
                                        <div class="cp comment_data" data-pk="<?php echo $val['id'];?>">
                                            <?php echo $val["comment"];?>
                                        </div>
                                    </div>
                                    <?php
                                        if(!empty($val["client_comment"])){
                                            ?>
                                                <div class="panel-comment">
                                                    <div class="">Комментарий клиента:&emsp;</div>
                                                    <div>
                                                        <?php echo $val["client_comment"];?>
                                                    </div>
                                                </div>
                                            <?php
                                        }
                                }

                                ?>

                            </div>
                            <div class="panel-btn col-xs-12 col-lg-2 pull-right btn_action">
                                <div class="mb20">
                                    <?php
                                        if($val["create"]){

                                            $btn_dis = "disabled";

                                            if($uid === $val["uid"] || $uid === $bid["uid"]){
                                                $btn_dis = "";
                                            }

                                            ?>

                                            <button class="btn btn-danger btn-block" onclick="<?php echo empty($btn_dis) ? 'del_boiler_bid_data_modal('.$val["id"].');' : ''; ?> status_bid_auto(getMainId());" <?php echo $btn_dis;?> >Удалить</button>

                                            <?php
                                        } else {
                                            ?>

                                            <input type="button" id="btn_<?php echo $val["category"];?>" class="btn btn-success btn-block" value="Создать" <?php echo in_array($val["category"], $allow_category) ? "" : "disabled";?> onclick="add_boiler_bid_data_modal(<?php echo $bid_type_id;?>, <?php echo $val['category'];?>); status_bid_auto(getMainId());">

                                            <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        if($val["create"]){
                    ?>
                        <div onclick="toggle_tov_list(<?php echo $val['id'];?>).then(()=>{check_open_tov_list(<?php echo $val['id'];?>) ? load_tov_list(<?php echo $val['id'];?>) : false;});" class="slide_active_panel cp">
                            <span><i id="tov_list_btn_<?php echo $val['id'];?>" class="fas fa-chevron-down"></i></span>
                        </div>
                    <?php
                        }
                    ?>
                </div>

            <?php

                if($val["create"]){

                    ?>

                        <div style="display:none;" id="tov_list_<?php echo $val["id"];?>">
                            <div class="panel panel-default">
                                <div class="panel-body" id="tov_list_load_<?php echo $val["id"];?>">
                                    Загрузка данных...
                                </div>
                            </div>
                        </div>

                    <?php
                }

                ?>

            </div>

                <?php

            }

            ?>
            </div>
            <?php

        }
    }
}

function get_tov_list($id){
    global $db;
    $details = bid_data_detail_body_data($id);
    $checkbox_all = "checked";
    foreach($details as $detail){
        if($detail["checkbox"] == 0){
            $checkbox_all = "";
            break;
        }
    }

    $price_type_array = [];
    $QpriceType = $db->query("SELECT * FROM JMY_catalog_price_type");
    while($qpt = $db->getRow($QpriceType)){
        $price_type_array[$qpt["id"]] = $qpt["title"];
    }

    ?>

        <table class="table table-striped">
            <thead>
                <th><input type="checkbox" value="1" id="checkbox_all_<?php echo $id;?>" class="" <?php echo $checkbox_all;?> onchange="checkbox_toggle(<?php echo $id;?>, $(this).prop('checked'));"></th>
                <th class="w40"><span class="pd-l-sm"></span>#</th>
                <th class="w200">Номенклатура</th>
                <th class="w150 sku-units cp" data-type="1"><span>Артикул</span></th>
                <th class="w100 text-center" data-toggle="tooltip" title="Количество">Кол-во</th>
                <th class="w100 text-center" data-toggle="tooltip" title="Наличие / Доступно / Резерв">Н/Д/Р</th>
                <th class="w100"><span>Цена закупки</span></th>
                <th class="w200">Тип цены</th>
                <th class="w100">Цена</th>
                <th class="w100">Скидка</th>
                <th class="w100" style="display: none;">Сумма скидки</th>
                <th class="w100" style="display: none;">Тип НДС</th>
                <th class="w100" style="display: none;">НДС</th>
                <th class="w100">Сумма</th>
                <th class="w40 ">Комментарий</th>
            </thead>
            <tbody>
            <?php
                if(count($details) > 0){
                    $i = 0;
                    $all_total = 0;
                    $all_supply_total = 0;
                    $all_nds = 0;
                    foreach($details as $detail){
                        $cid = $detail["cid"];
                        $ndr_view = get_ndr(["cid" => $detail["cid"], "stock_id" => "1", "module" => "sales", "pid" => "", "returnType" => "view"]);
                        //$priceFunc = getCatalogPrice(["cid" => $detail["cid"], "type" => "1"]);
                        $priceFunc = get_supply_price($detail["s_price_local"], $cid);
                        $price = $priceFunc["price"];
                        $datePrice = $priceFunc["datePrice"];
                        $fileUrl = $priceFunc["fileUrl"];
                        $flagSup = $priceFunc["flagSup"];
                        $d_id = $detail["id"];
                        $s_name = $detail["s_name"];
                        $s_sku = $detail["s_sku"];
                        $count = $detail["count"];
                        $price_type = $detail["price_type"];
                        $s_price = $detail["s_price"];
                        $discount = $detail["discount"];
                        $total = $detail["total"];
                        $comment = $detail["comment"];
                        $discount_sum = $detail["discount_sum"];
                        $nds = $detail["nds"];
                        $type_nds = $detail["type_nds"];
                        $typeNds = \Price::getNds($type_nds);
                        $all_total += $total;
                        $all_supply_total += $price * $count;
                        $all_nds += $nds;
                        $i++;
                        ?>
                            <tr class="tov_item" id="tr_<?php echo $d_id;?>" data-id="<?php echo $d_id;?>">
                                <td>
                                <input type="checkbox" value="1" <?php echo $detail["checkbox"] == 0 ? "" : "checked" ;?> class="checkbox_detail_<?php echo $id;?>"
                                onchange="change_checkbox_detail(<?php echo $d_id;?>, 'checkbox', +$(this).prop('checked'));" data-id="<?php echo $d_id;?>">
                                </td>
                                <td class="position" data-id="<?php echo $d_id;?>" title="id:<?php echo $d_id;?>;cid:<?php echo $cid;?>"><?php echo $i;?></td>
                                <td><?php echo $s_name;?></td>
                                <td><?php echo $s_sku;?></td>
                                <td class="text-center count">
                                    <?php echo $count;?>
                                </td>
                                <td class="text-center ndr" data-cid="<?php echo $cid;?>" onclick="view_ndr_modal(<?php echo $cid;?>);"><?php echo $ndr_view;?></td>
                                <td>
                                    <?php
                                        echo '<span onclick="'.($flagSup ? 'window.open(\''.$fileUrl.'\', \'1552240782632\',\'width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,	left=0,     top=0\')                                         ;return false;' : '').'" class="purchasePrice '.($flagSup ? 'link-style' : 'link-no-style').'">' . number_format($price,2,'.',' 	')      . ' руб.</span>';
                                        echo '<div class="date_purchse_price">'.$datePrice.'</div></td>';
                                    ?>
                                </td>
                                <td class="price_type" onchange="">
                                    <?php
                                        echo $price_type_array[$price_type];
                                    ?>
                                </td>
                                <td class="price">
                                    <?php echo number_format($s_price,2,'.',' 	'). ' руб.';?>
                                </td>
                                <td class="discount">
                                    <?php echo empty($discount) ? '0' : round($discount, 2)?>%
                                </td>
                                <td class="discount_sum" style="display:none;"><?php echo $discount_sum;?> руб.</td>
                                <td class="type_nds" style="display:none;"><?php echo $typeNds;?>%</td>
                                <td class="nds" style="display:none;"><?php echo $nds;?> руб.</td>
                                <td class="total"><?php echo number_format($total,2,'.',' 	'). ' руб.';?></td>
                                <td><?php echo empty($comment) ? "Не указан" : $comment;?></td>
                            </tr>

                        <?php
                    }
                    ?>
                            <tr>
                                <td colspan="6" class=" text-right"><b>Итого:</b></td>
                                <td class=" text-left"><b class="all_supply_total"><?php echo number_format($all_supply_total,2,'.',' ').' руб.';?></b></td>
                                <td colspan="1" class=" text-right"><b>Итого:</b></td>
                                <td colspan="4" class=" text-left"><b class="all_total"><?php echo number_format($all_total,2,'.',' ').' руб.';?></b></td>
                            </tr>
                            <tr>
                            	<td colspan="8" class=" text-right"><b>НДС:</b></td>
                            	<td colspan="4" class=""><b id="all_nds"><?php echo number_format($all_nds,2,'.',' ').' руб.';?></b></td>
                            </tr>
                    <?php
                } else {
                    ?>
                        <tr><td colspan="12">Товары отсутствуют!</td></tr>

                    <?php
                }
            ?>
            </tbody>
        </table>

    <?php

}

function get_panel_info($id){
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    if($id){
        $bid = bid_query($id);

        $uid = Auth::id();

        $org = getOrgInfo($bid["org"]);

        $fc_object = new FastCreate("object");
        $fc_o = $fc_object->get_fast_create();

        $fc_organization = new FastCreate("organization");
        $fc_org = $fc_organization->get_fast_create();

        //var_dump($org);
        ?>
            <div class="col-sm-12" id="panel_info_con">

                <?php
                // $group_allow = getGroup(["group" => ["admin", "director"]]);

                $sales_create_flag = true;
                $sales_create_error = [];
                if(empty($bid["object"])){
                    $sales_create_flag = false;
                    $sales_create_error[] = "'объект'";
                }
                if(empty($bid["uid"])){
                    $sales_create_flag = false;
                    $sales_create_error[] = "'ответсвенный'";
                }
                if(empty($bid["oid"])){
                    $sales_create_flag = false;
                    $sales_create_error[] = "'контрагент'";
                }
                $sales_create_error_str = implode(", ", $sales_create_error);
                ?>

                <div class="panelInfo_elem-no_border" style="order: 0">
                    <button class="btn btn-block btn-sm btn-primary" onclick="<?php echo $sales_create_flag ? 'create_sales_modal();' : 'error_modal_create_sales(['.$sales_create_error_str.']);'; ?>">Сформировать продажу</button>
                </div>
                <div class="panelInfo_elem-no_border" style="order: 0">
                    <button class="btn btn-block btn-sm btn-success" onclick="<?php echo $sales_create_flag ? 'create_spec_modal();' : 'error_modal_create_sales(['.$sales_create_error_str.']);'; ?>">Сформировать спецификацию</button>
                </div>
                <div class="panelInfo_elem row" style="order: 1">
                    <div class="col-sm-4 panelInfo_elem-title">Объект:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span>
                            <?php echo empty($bid["obj_name"]) ? '<span class="text-danger fwb">Не указан</span>' : '<span>'.$bid["obj_name"].'</span>';?>
                        </span>
                        <span>
                            <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick="open_modal('objectModal');easy_select_save('objectSelect');"></i>
                            <i class="fas fa-plus text-success ml20 cp" title="Добавить" <?php echo $fc_o->button_open;?>></i>
                        </span>
                    </div>
                </div>
                <div class="panelInfo_elem row" style="order: 2">
                    <div class="col-sm-4 panelInfo_elem-title">Тип:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span></span>
                        <span>
                            <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick="open_modal('typeModal');"></i>
                        </span>
                    </div>
                </div>
                <div class="panelInfo_elem-no_border row" style="order: 2">
                    <div class="col-sm-12" id="type_list">
                        <?php
                            $query = bid_type_query($id);
                            if($db->numRows($query) > 0){
                                while($btq = $db->getRow($query)){
                                    ?>

                                    <div class="element-type" data-id="<?php echo $btq['id'];?>">
                                        <div class="element-type_text">
                                            <div class="mr10">
                                                <span><?php echo list_type_group_name($btq["section"])." &mdash; ".list_type_name($btq["type"]); ?></span>
                                                <span>(</span><span class="cp type_comment tc_<?php echo $btq['id'];?>" data-pk="<?php echo $btq['id'];?>"><?php echo $btq["comment"];?></span><span>)</span>
                                            </div>
                                        </div>
                                        <div><i class="fas fa-times text-danger cp" onclick="del_boiler_bid_type_modal(<?php echo $btq['id'];?>);"></i></div>
                                    </div>

                                    <?php
                                }
                            } else {
                                echo '<div>Типы не созданы</div>';
                            }
                        ?>
                    </div>
                </div>
                <div class="panelInfo_elem row" style="order: 4">
                    <div class="col-sm-4 panelInfo_elem-title">Постановщик:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span></span>
                        <span>
                            <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick="open_modal('uidModal');easy_select_save('uidSelect');"></i>
                        </span>
                    </div>
                </div>
                <div class="panelInfo_elem-no_border row" style="order: 5">
                    <div class="col-sm-12">
                        <span class="">
                            <img class="avatar" src="<?php echo avatar($bid['uid']); ?>">
                        </span>
                        <span class="panelInfo_elem-name col_b">
                            <?php echo empty($bid['uid']) ? 'Не указан' : fullUserName($bid['uid']); ?>
                        </span>
                    </div>
                </div>
                <?php
                    $users_type = boiler_bid_users_type();
                    $fac = 9;
                    $next = 6;
                    foreach($users_type as $ut_key => $ut_val){
                        ?>

                            <div class="panelInfo_elem row" style="order: <?php echo $ut_key == 0 ? $fac : $next?>">
                                <div class="col-sm-8 panelInfo_elem-title"><?php echo $ut_val;?>:</div>
                                <div class="col-sm-4 panelInfo_elem-main col_b">
                                    <span></span>
                                    <span>
                                        <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick="open_modal('uid_users_<?php echo $ut_key;?>Modal');"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="panelInfo_elem-no_border row" style="order: <?php echo $ut_key == 0 ? $fac : $next?>">
                                <div class="col-sm-12">
                                    <?php
                                        //$uid_client = json_decode($bid['uid_client'], true);
                                        $uid_client = boiler_bid_users_find($id, $ut_key);
                                        if(count($uid_client) > 0){
                                            foreach($uid_client as $val){
                                                ?>

                                                <div class="mb10 element-type">
                                                    <div>
                                                        <span class="">
                                                            <img class="avatar" src="<?php echo avatar($val["uid"]); ?>">
                                                        </span>
                                                        <span class="panelInfo_elem-name col_b cp"
                                                        <?php if($ut_key == 0){ ?> onclick="modal_view_uid_client_info(<?php echo $val['uid'];?>);" <?php }?>>
                                                            <?php echo fullUserName($val["uid"]); ?>
                                                        </span>
                                                    </div>
                                                    <div><i class="fas fa-times text-danger cp" onclick="remove_boiler_bid_users_modal(<?php echo $val['id'];?>);"></i></div>
                                                </div>

                                            <?php
                                            }
                                        } else {
                                            empty_users_template();
                                        }
                                    ?>
                                </div>
                            </div>

                        <?php
                    }
                ?>
                <div class="panelInfo_elem row" style="order: 7">
                    <div class="col-sm-4 panelInfo_elem-title">Организация:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span>
                            <?php echo $org["organizations"]["name"];?>
                        </span>
                        <span>
                            <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick="open_modal('orgModal');easy_select_save('orgSelect');"></i>
                        </span>
                    </div>
                </div>
                <div class="panelInfo_elem row" style="order: 8">
                    <div class="col-sm-4 panelInfo_elem-title">Контрагент:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span>
                            <?php echo empty($bid["o_name"]) ? '<span class="text-danger fwb">Не указан</span>' : '<span>'.$bid["o_name"].'</span>';?>
                        </span>
                        <span>
                            <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick="open_modal('oidModal');easy_select_save('oidSelect');"></i>
                            <i class="fas fa-plus text-success ml20 cp" title="Добавить" <?php echo $fc_org->button_open;?>></i>
                        </span>
                    </div>
                </div>
                <div class="panelInfo_elem row" style="order: 9">
                    <div class="col-sm-4 panelInfo_elem-title">Дата создания:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span>
                            <?php echo date("d.m.Y", strtotime($bid["date_create"]));?>
                        </span>
                        <span></span>
                    </div>
                </div>
                <div class="panelInfo_elem row" style="order: 10">
                    <div class="col-sm-4 panelInfo_elem-title">Дата окончания:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span>
                            <?php echo empty($bid["date_end"]) ? '<span class="text-danger fwb">Не указан</span>' : '<span>'.date("d.m.Y H:i", strtotime($bid["date_end"])).'</span>';?>
                        </span>
                        <span>
                            <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick="open_modal('date_endModal');easy_select_save('date_endSelect', 'input');"></i>
                        </span>
                    </div>
                </div>
                <div class="panelInfo_elem row" style="order: 11">
                    <div class="col-sm-4 panelInfo_elem-title">Заказы:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span></span>
                        <span>
                            <!-- <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick=""></i> -->
                        </span>
                    </div>
                </div>
                <div class="panelInfo_elem-no_border row" style="order: 11">
                    <div class="col-sm-12">
                        <?php

                            $sales_link = json_decode($bid["sales_link"], true);
                            if(count($sales_link) > 0){
                                $link_id = implode(", ", $sales_link);
                                $query = $db->query("SELECT * FROM `".DB_PREFIX."_sales` WHERE id IN(".$link_id.")");
                                if($db->numRows($query) > 0){
                                    while($sales = $db->getRow($query)){
                                        switch((int)$sales["end"]){
                                            default:
                                                $end_color = "text-alert";
                                            break;
                                            case 1:
                                                $end_color = "text-success";
                                            break;
                                            case 2:
                                                $end_color = "text-danger";
                                            break;
                                        }
                                        ?>

                                            <div class="d-flex justify-content-between mt-10">
                                                <a href="/administration/module/sales/order/<?php echo $sales['id'];?>" target="_blank" class="<?php echo $end_color;?>">
                                                    Заказ <?php echo $sales["numb"];?> (<?php echo empty($sales["title"]) ? 'Не указано' : $sales["title"]; ?>)
                                                </a>
                                                <i class="fas fa-times text-danger cp" onclick="delete_sales_link_modal(<?php echo $sales['id'];?>);"></i>
                                            </div>

                                        <?php
                                    }
                                }
                            } else {
                                ?>

                                    <div>Заказы не привязаны</div>

                                <?php
                            }

                        ?>
                    </div>
                </div>
                <div class="panelInfo_elem row" style="order: 12">
                    <div class="col-sm-4 panelInfo_elem-title">Файлы:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span></span>
                        <span>
                            <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick="open_modal('filesModal');"></i>
                        </span>
                    </div>
                </div>
                <div class="panelInfo_elem-no_border row" style="order: 12">
                    <div class="col-sm-12 file-list">
                        <?php

                            $query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `module` = 'boiler_bid' AND `pid` = '".$id."'");
                            if($db->numRows($query) > 0){
                                ?>

                                        <div class="file-elem mb5">
                                            <span><a href="/administration/module/boiler/bid/get_all_file/<?php echo $id;?>">Скачать все</a></span>
                                        </div>

                                <?php
                                while($files = $db->getRow($query)){
                                    $BoilerBidFilesData = \Boiler\Model\BoilerBidFiles::where("attach_id", $files["id"])->first();
                                    $pk = "";
                                    $comment = "";
                                    if(!empty($BoilerBidFilesData)){
                                        $pk = $BoilerBidFilesData->id;
                                        $comment = $BoilerBidFilesData->comment;
                                    }
                                    ?>
                                        <div class="file-elem mb5">
                                            <span>
                                                <a href="/administration/module/boiler/bid/load_file/<?php echo $files["id"];?>" target="_blank">
                                                    <?php echo $files["name"];?>
                                                </a>
                                                <span>(<span class="files_comment" data-pk="<?php echo $pk;?>" data-attach_id="<?php echo $files["id"];?>"><?php echo $comment;?></span>)</span>
                                            </span>
                                            <span><i class="fas fa-times text-danger cp" onclick="delFilesSweer(<?php echo $files['id'];?>);"></i></span>
                                        </div>
                                    <?php
                                }
                            } else {
                                echo '<div>Файлы отсутствуют</div>';
                            }
                        ?>

                    </div>
                </div>
                <div class="panelInfo_elem row" style="order: 12">
                    <div class="col-sm-4 panelInfo_elem-title">Файлы для спецификации:</div>
                    <div class="col-sm-8 panelInfo_elem-main col_b">
                        <span></span>
                        <span>
                            <i class="fas fa-plus text-info ml20 cp" title="Изменить" onclick="open_modal('filesModalSpec');"></i>
                        </span>
                    </div>
                </div>
                <div class="panelInfo_elem-no_border row" style="order: 12">
                    <div class="col-sm-12 file-list">
                        <?php

                            $query = $db->query("SELECT * FROM ".DB_PREFIX."_attach WHERE `module` = 'boiler_bid_spec' AND `pid` = '".$id."'");
                            if($db->numRows($query) > 0){
                                ?>

                                        <div class="file-elem mb5">
                                            <span><a href="/administration/module/boiler/bid/get_all_file/<?php echo $id;?>">Скачать все</a></span>
                                        </div>

                                <?php
                                while($files = $db->getRow($query)){
                                    $BoilerBidFilesData = \Boiler\Model\BoilerBidFiles::where("attach_id", $files["id"])->first();
                                    $pk = "";
                                    $comment = "";
                                    if(!empty($BoilerBidFilesData)){
                                        $pk = $BoilerBidFilesData->id;
                                        $comment = $BoilerBidFilesData->comment;
                                    }
                                    ?>
                                        <div class="file-elem mb5">
                                            <span>
                                                <a href="/administration/module/boiler/bid/load_file/<?php echo $files["id"];?>" target="_blank">
                                                    <?php echo $files["name"];?>
                                                </a>
                                                <span>(<span class="files_comment" data-pk="<?php echo $pk;?>" data-attach_id="<?php echo $files["id"];?>"><?php echo $comment;?></span>)</span>
                                            </span>
                                            <span><i class="fas fa-times text-danger cp" onclick="delFilesSweer(<?php echo $files['id'];?>);"></i></span>
                                        </div>
                                    <?php
                                }
                            } else {
                                echo '<div>Файлы отсутствуют</div>';
                            }
                        ?>

                    </div>
                </div>
            </div>
        <?php
    }
}

// ************************************************************************************************

function empty_users_template(){
    ?>
        <span class="">
            <img class="avatar" src="<?php echo avatar(null); ?>">
        </span>
        <span class="panelInfo_elem-name col_b">
            Не указан
        </span>
    <?php
}

function empty_value($value){
    if(empty($value) && $value != "0"){
        return 'null';
    } else {
        return "'".$value."'";
    }
}

function protection_field($field){
    if(empty($field)){
        return false;
    }
    $pf = ["", "id", "bbd_id", "bid_id", "bid_type_id",];
    return !in_array($field, $pf);
}

function bid_save_field($id, $field, $value){
    global $db;
    $new_value = empty_value($value);

    if($field=='object' && protection_field($field))
    {
        $sales_link=$db->query("SELECT sales_link FROM ".DB_PREFIX."_boiler_bid WHERE `id` = '".$id."' LIMIT 1");

        $sales_link = json_decode($db->getRow($sales_link)["sales_link"], true);

        for ($i=0; $i < count($sales_link); $i++) {
            $db->query("UPDATE ".DB_PREFIX."_sales SET `object` = ".$new_value." WHERE `id` = '".$sales_link[$i]."' LIMIT 1");
        }
        $db->query("UPDATE ".DB_PREFIX."_boiler_bid SET `".$field."` = ".$new_value." WHERE `id` = '".$id."' LIMIT 1");


    }
    else if(protection_field($field)){
        $db->query("UPDATE ".DB_PREFIX."_boiler_bid SET `".$field."` = ".$new_value." WHERE `id` = '".$id."' LIMIT 1");
    }
}

function bid_save_field_data($id, $field, $value){
    global $db;
    $new_value = empty_value($value);
    if(protection_field($field)){
        $db->query("UPDATE ".DB_PREFIX."_boiler_bid_data SET `".$field."` = ".$new_value." WHERE `id` = '".$id."' LIMIT 1");
    }
}

function bid_save_field_data_detail($id, $field, $value){
    global $db;
    $new_value = empty_value($value);
    if(protection_field($field)){
        $db->query("UPDATE ".DB_PREFIX."_boiler_bid_data_detail SET `".$field."` = ".$new_value." WHERE `id` = '".$id."' LIMIT 1");
    }
}

function bid_save_field_type($id, $field, $value){
    global $db;
    $new_value = empty_value($value);
    if(protection_field($field)){
        $db->query("UPDATE ".DB_PREFIX."_boiler_bid_type SET `".$field."` = ".$new_value." WHERE `id` = '".$id."' LIMIT 1");
    }
}

function bid_save_field_folder($id, $field, $value){
    global $db;
    $new_value = empty_value($value);
    if(protection_field($field)){
        $db->query("UPDATE ".DB_PREFIX."_boiler_bid_folder SET `".$field."` = ".$new_value." WHERE `id` = '".$id."' LIMIT 1");
    }
}

function bid_update_numb($id, $org){
    $boiler_bid = \Boiler\Model\BoilerBid::findOrFail($id);
    $n = \Boot\Support\Module::numb('boiler_bid', $org, $boiler_bid->date_create);
    $boiler_bid->numb = $n["numb"];
    $boiler_bid->numb_counter = $n["numb_counter"];
    $boiler_bid->save();
}

function bid_append_field($id, $field, $value){
    global $db;
    $detail = bid_query($id);
    $data = json_decode($detail[ $field ], true);
    $data[] = (string)$value;
    $data = array_unique($data);
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    bid_save_field($id, $field, $data);
}

function bid_remove_field($id, $field, $value){
    global $db;
    $detail = bid_query($id);
    $data = json_decode($detail[ $field ], true);
    if(($key = array_search($value,$data)) !== FALSE){
        unset($data[$key]);
    }
    $data = array_unique($data);
    $data = json_encode($data, JSON_UNESCAPED_UNICODE);
    bid_save_field($id, $field, $data);
}

function bid_save_files($id, $file){
    $attribute = [];
    $attribute["name"] = "1";
	$attribute["file"] = $file;
	$attribute["module"] = 'boiler';
	$attribute["mod"] = 'boiler_bid';
	$attribute["id"] = $id;
    $attribute["comm_id"] = $id;
    $attribute["ftp"] = "StorageFTP";
	uploaderMaster("download", $attribute);
}

function bid_del_files($id){
    attach_del_file($id);
}

function change_allow_category($id, $status, $category){
    global $db;
    $category = (Int)$category;
    $query = $db->query("SELECT allow_category FROM `".DB_PREFIX."_boiler_bid_type` WHERE id = '".$id."' LIMIT 1");
    if($db->numRows($query) > 0){
        $r = $db->getRow($query);
        $allow_category = json_decode($r["allow_category"], true);
        if($status === "true"){
            $allow_category[] = $category;
        } else {
            if(($key = array_search($category, $allow_category)) !== false){
                unset($allow_category[$key]);
            }
        }
        $allow_category = array_unique($allow_category);
        $json = json_encode($allow_category, JSON_UNESCAPED_UNICODE);
        $db->query("UPDATE `".DB_PREFIX."_boiler_bid_type` SET `allow_category` = '".$json."' WHERE id = '".$id."' LIMIT 1");
    }
}

function del_boiler_bid_data($id){
    global $db;
    if($id){
        $db->query("DELETE FROM ".DB_PREFIX."_boiler_bid_data WHERE id = '".$id."' LIMIT 1");
    }
}

function del_boiler_bid_folder($id){
    global $db;
    if($id){
        $db->query("DELETE FROM `".DB_PREFIX."_boiler_bid_folder` WHERE id = '".$id."' LIMIT 1");
        $db->query("UPDATE `".DB_PREFIX."_boiler_bid_type` SET folder_id = null WHERE folder_id = '".$id."'");
    }
}

function add_boiler_bid_data($request){
    //["id" => "", "category" => "", "uid" => ""]
    global $adminTpl, $config, $core, $admin_conf, $db, $lang;
    if(isset($request["uid"])){
        if(empty($request["uid"])){
            $uid = 'null';
        } else {
            $uid = "'".$request["uid"]."'";
        }
    } else {
        $uid = "'".Auth::id()."'";
    }

    $status = empty($request["status"]) ? 1 : $request["status"];
    $id = $request["id"];
    $category = $request["category"];
    if($db->query("INSERT INTO `".DB_PREFIX."_boiler_bid_data` (`bid_type_id`, `category`, `uid`, `status`) VALUES ('".$id."', '".$category."', ".$uid.", '".$status."')")){
        $iid = $db->insert_id();
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_boiler_bid_data` WHERE bid_type_id = '".$id."'");
        $i = 0;
        while($r = $db->getRow($query)){
            if(is_null($r["position"])){
                bid_save_field_data($r["id"], "position", $i);
            }
            $i++;
        }
        return $iid;
    }
}

function get_boiler_bid($data, $id){
    global $db;
    if(count($data) > 0){
        $search = implode(", ", $data);
    } else {
        $search = "*";
    }
    $query = $db->query("SELECT ".$search." FROM ".DB_PREFIX."_boiler_bid WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);
    return $r;
}

function bid_insert_filed_type($id, $data){
    //["section" => "", "type" => ""]
    global $db, $core;
    $uid = Auth::id();
    $field = [];
    $value = [];
    $flag_pos = true;
    foreach($data as $key => $val){
        $field[] = "`".$key."`";
        $value[] = "'".$val."'";
        if($key == "position"){
            $flag_pos = false;
        }
    }
    if($flag_pos){
        $query = $db->query("SELECT id FROM `".DB_PREFIX."_boiler_bid_type` WHERE bid_id = '".$id."'");
        $pos = $db->numRows($query);
        $pos++;
        $field[] = "`position`";
        $value[] = "'".$pos."'";
    }

    $field[] = "`bid_id`";
    $value[] = "'".$id."'";
    $field[] = "`uid`";
    $value[] = "'".$uid."'";

    $field_str = implode(", ", $field);
    $value_str = implode(", ", $value);
    $db->query("INSERT INTO `".DB_PREFIX."_boiler_bid_type` (".$field_str.") VALUES (".$value_str.")");
    return $db->insert_id();
}

function del_boiler_bid_type($id){
    global $db;
    if($id){
        $db->query("DELETE FROM `".DB_PREFIX."_boiler_bid_type` WHERE id = '".$id."' LIMIT 1");
    }
}

function get_client_info($id){
    global $db;
    if($id){
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_users` WHERE id = '".$id."' LIMIT 1");
        $user = $db->getRow($query);

        $info = [
            "phone" => [
                "title" => "Телефон",
                "data" => $user["phone"],
            ],
            "email" => [
                "title" => "Почта",
                "data" => $user["email"],
            ],
        ];
        return $info;
    }
}

function bid_get_all_file($id){
    global $db;
    if($id){
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_attach` WHERE `pid` = '".$id."' AND `module` = 'boiler_bid'");
        if($db->numRows($query) > 0){
            $zip = new ZipArchive();
            $zip_name = time().".zip";
            $zip->open($zip_name, ZIPARCHIVE::CREATE);
            $ftp = new StorageFTP;
            $files_url = [];
            while($r = $db->getRow($query)){
                $ftp->remote_file = $r["url"];
                $fileName = time()."_".$r["name"];
                $url_lf = "files/".$fileName;
                $ftp->local_file = $url_lf;
                $ftp->get();
                $zip->addFile($url_lf, $fileName);
                $files_url[] = $url_lf;
            }
            $zip->close();
            if(file_exists($zip_name)){
                // отдаём файл на скачивание
                header('Content-type: application/zip');
                header('Content-Disposition: attachment; filename="'.$zip_name.'"');
                readfile($zip_name);
                // удаляем zip файл если он существует
                unlink($zip_name);
                foreach($files_url as $file_url){
                    unlink($file_url);
                }
            }
        }
    }
}

function get_bid_cheat(){
    $cheat = 5;
    return $cheat;
}

function get_bid_postponement($type = null){
    $result = [
        "1" => [
            "name" => "100% предоплата или оплата 100% в течении 5 дней при отгрузке со склада",
            "value" => 0,
        ],
        "2" => [
            "name" => "30 дней",
            "value" => 2,
        ],
    ];
    return is_null($type) ? $result : $result[ $type ];
}

function bid_create_sales($id, $request){
    global $db, $core;

    // $uid_core = $core->auth->user_info['id'];

    $query = $db->query("SELECT * FROM `".DB_PREFIX."_boiler_bid` WHERE id = '".$id."' LIMIT 1");
    $bid = $db->getRow($query);

    $bb_users = boiler_bid_users_find($id, 0);
    $uid_send = null;
    if(count($bb_users) > 0){
        $uid_send = $bb_users[0]["uid"];
    }

    $org = $bid["org"];

    $type = isset($request["type_id"]) ? $request["type_id"] : [];

    $union = isset($request["union"]) ? true : false;

    $delivery_type = isset($request["delivery_type"]) ? $request["delivery_type"] : 0;
    $address = isset($request["address"]) ? $request["address"] : "";

    // $numb_array = getNumbCounter('sales', $org);
	// $numb = $numb_array['numb'];
    // $numb_counter = $numb_array['numb_counter'];

    $uid = $bid["uid"];
    $oid = $bid["oid"];
    $sales_cat = 1;
    $sales_type = 1;
    $date_add = date('Y-m-d H:i:s');
    $object = $bid["object"];
    $nds_includes = $bid["nds_includes"];
    $title = $bid["title"];

    $type_str = "";
    $query_type_str = "";
    if(count($type) > 0){
        $type_str = implode(", ", $type);
    }
    if(!empty($type_str)){
        $query_type_str = "AND bbt.id IN(".$type_str.")";
    }

    $bbd_id = [];
    $query = $db->query("SELECT bbd.id
        FROM `".DB_PREFIX."_boiler_bid_data` as bbd
        LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id)
        LEFT JOIN `".DB_PREFIX."_boiler_bid` as bb ON(bb.id = bbt.bid_id)
        WHERE bb.id = '".$id."' ".$query_type_str." ORDER BY bbd.position");

    while($data = $db->getRow($query)){
        $bbd_id[] = $data["id"];
    }

    $cheat = get_bid_cheat() * -1;

    $postponement = 0;
    if(!empty($request["postponement"])){
        $postponement_data = get_bid_postponement($request["postponement"]);
        $postponement = $postponement_data["value"] * -1;
    }

    if(count($bbd_id) > 0){
        $bbd_id_str = implode(", ", $bbd_id);
        $query = $db->query("SELECT bbdd.*
        FROM `".DB_PREFIX."_boiler_bid_data_detail` as bbdd
        LEFT JOIN `".DB_PREFIX."_boiler_bid_data` as bbd ON(bbd.id = bbdd.bbd_id)
        LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id)
        WHERE bbdd.bbd_id IN(".$bbd_id_str.") AND bbdd.checkbox = '1'
        ORDER BY bbt.position, bbd.position, bbdd.position");

        if($db->numRows($query) > 0){

            $sales = \Sales\Model\Sales::create(["org" => $org, "oid" => $oid, "sales_cat" => $sales_cat, "sales_type" => $sales_type, "object" => $object, "delivery_type" => $delivery_type, "address" => $address, "uid_send" => $uid_send, "uid" => \Boot\Support\Facades\Auth::id()]);
            $sales_id = $sales->id;

            // $db->query("INSERT INTO `" . DB_PREFIX . "_sales` (`numb_counter` ,  `numb` , `uid` , `oid` , `org` , `sales_cat` , `sales_type` ,`date` ,  `date_add` , `pay` , `status`, `object`, `nds_includes`, `title`) VALUES ('" . $numb_counter . "', '" . $numb . "', '" . $uid . "', '" . $oid . "', '" . $org . "', '" . $sales_cat . "', '" . $sales_type . "','" . $date_add . "',     '" . time() . "', '0', '1', ".$object.", '".$nds_includes."', '".$title."');");
            // $sales_id = $db->insert_id();

            $sales_link = json_decode($bid["sales_link"], true);
            $sales_link[] = $sales_id;
            $sales_link = json_encode($sales_link, JSON_UNESCAPED_UNICODE);
            $db->query("UPDATE ".DB_PREFIX."_boiler_bid SET sales_link = '".$sales_link."' WHERE id = '".$id."' LIMIT 1");

            $status = "1";

            $data_value = [];
            $data_value_key = [];
            $i = 0;

            while($detail = $db->getRow($query)){
                if(array_key_exists($detail["cid"], $data_value_key) && $union){
                    $data_value[ $data_value_key[ $detail["cid"] ] ]["count"] += $detail["count"];

                    if($detail["s_price"] > $data_value[ $data_value_key[ $detail["cid"] ] ]["s_price"]){
                        $data_value[ $data_value_key[ $detail["cid"] ] ]["s_price"] = $detail["s_price"];
                        $data_value[ $data_value_key[ $detail["cid"] ] ]["price_type"] = $detail["price_type"];
                    }

                    if($detail["discount"] < $data_value[ $data_value_key[ $detail["cid"] ] ]["discount"]){
                        $data_value[ $data_value_key[ $detail["cid"] ] ]["discount"] = $detail["discount"];
                    }
                } else {
                    $data_value_key[$detail["cid"]] = $i;
                    $data_value[] = $detail;

                    $i++;
                }
            }

            $values = [];
            $position = 0;
            $total = 0;
            $nds = 0;
            $sales_detail = [];
            foreach($data_value as $detail){
                $position++;
                $discount = 0;
                $flag = false;
                // $s_price_local = empty($detail["s_price_local"]) ? 'null' : "'".$detail["s_price_local"]."'";
                $s_price_local = $detail["s_price_local"];

                $price_type = $detail["price_type"];

                $supply = isset($request["supply"][ $detail["id"] ]) ? 2 : 1;
                if($supply == 2){
                    $price_type = 4;
                    $discount += $cheat;
                    $flag = true;
                }
                if($postponement != 0){
                    $price_type = 4;
                    $discount += $postponement;
                    $flag = true;
                }


                $pa_pre = \Price::get_all([
                    "price" => $detail["s_price"],
                    "count" => $detail["count"],
                    "discount" => $detail["discount"],
                    "nds_includes" => $nds_includes,
                    "nds_type" => $detail["type_nds"]
                ]);

                if($flag){
                    $pa = \Price::get_all([
                        "price" => $pa_pre["discount_price"],
                        "count" => $pa_pre["count"],
                        "discount" => $discount,
                        "nds_includes" => $nds_includes,
                        "nds_type" => $pa_pre["nds_type"]
                    ]);
                } else {
                    $pa = $pa_pre;
                }

                $total += $pa["total"];
                $nds += $pa["nds"];

                $sales_detail[] = ["cid" => $detail["cid"], "s_sku" => $detail["s_sku"], "s_name" => $detail["s_name"], "price_type" => $price_type, "discount" => $pa["discount"], "discount_sum" => $pa["discount_sum"], "s_price" => $pa["price"], "count" => $pa["count"], "total" => $pa["total"], "type_nds" => $pa["nds_type"], "nds" => $pa["nds"], "position" => $position, "s_unit" => $detail["s_unit"], "s_price_local" => $s_price_local, "supply" => $supply, "first_price" => $pa_pre["discount_price"]];
            }

            if($delivery_type == 2){
                $position++;
                $cid = get_delivery_cid();
                $d = \Catalog_v2\Model\Catalog::findOrFail($cid)->toArray();
                $d_p = \Price::get_all([
                    "price" => 500,
                    "count" => 1,
                    "nds_includes" => $nds_includes,
                    "nds_type" => 3
                ]);

                $total += $d_p["total"];
                $nds += $d_p["nds"];

                $sales_detail[] = ["cid" => $d["id"], "s_sku" => $d["sku"], "s_name" => $d["title"], "price_type" => 4, "s_price" => $d_p["price"], "count" => $d_p["count"], "total" => $d_p["total"], "type_nds" => $d_p["nds_type"], "nds" => $d_p["nds"], "position" => $position, "s_unit" => $d["unit"]];
            }

            \Sales\Support\SalesDetailSupport::mass_add(["sales_id" => $sales_id, "sales_detail" => $sales_detail]);

            return $sales_id;
        }
    }
}

function get_all_total($id, $bid_type = false){
    global $db;
    $bid_type_where = "";
    if($bid_type){
        $bid_type_where = "AND bbt.id = '".$bid_type."'";
    }
    $query = $db->query("SELECT SUM(bbdd.total) as total
        FROM `".DB_PREFIX."_boiler_bid_data_detail` as bbdd
        LEFT JOIN `".DB_PREFIX."_boiler_bid_data` as bbd ON(bbdd.bbd_id = bbd.id)
        LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id)
        LEFT JOIN `".DB_PREFIX."_boiler_bid` as bb ON(bb.id = bbt.bid_id)
        WHERE bb.id = '".$id."' AND bbdd.checkbox = '1' ".$bid_type_where."");
    $total = 0;
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            $total += $r["total"];
        }
    }
    return $total;
}

function get_all_supply_total($id, $bid_type = false){
    global $db;
    $bid_type_where = "";
    if($bid_type){
        $bid_type_where = "AND bbt.id = '".$bid_type."'";
    }
    $query = $db->query("SELECT bbdd.cid, bbdd.s_price_local, bbdd.count
        FROM `".DB_PREFIX."_boiler_bid_data_detail` as bbdd
        LEFT JOIN `".DB_PREFIX."_boiler_bid_data` as bbd ON(bbdd.bbd_id = bbd.id)
        LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id)
        LEFT JOIN `".DB_PREFIX."_boiler_bid` as bb ON(bb.id = bbt.bid_id)
        WHERE bb.id = '".$id."' AND bbdd.checkbox = '1' ".$bid_type_where."");
    $total = 0;
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            $priceFunc = get_supply_price($r["s_price_local"], $r["cid"]);
            $total += $priceFunc["price"] * $r["count"];
        }
    }
    return $total;
}

function query_end_all($id){
    global $db;
    $query = $db->query("SELECT bbd.id
        FROM  `".DB_PREFIX."_boiler_bid_data` as bbd
        LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id)
        LEFT JOIN `".DB_PREFIX."_boiler_bid` as bb ON(bb.id = bbt.bid_id)
        WHERE bb.id = '".$id."'");
    return $query;
}

function query_end($id){
    global $db;
    $query = $db->query("SELECT bbd.id
        FROM  `".DB_PREFIX."_boiler_bid_data` as bbd
        LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id)
        LEFT JOIN `".DB_PREFIX."_boiler_bid` as bb ON(bb.id = bbt.bid_id)
        WHERE bb.id = '".$id."' AND bbd.status = '1'");
    return $query;
}

function check_end_status($id){
    global $db;
    $query = query_end($id);
    return $db->numRows($query) == 0;
}

function set_status_bid($id){
    global $db;
    $query = query_end($id);
    $count_no = $db->numRows($query);
    $query = query_end_all($id);
    $count_all = $db->numRows($query);
    $status = 1;
    if($count_no == 0 && $count_all > 0){
        $status = 3;
    }
    if($count_no > 0 && $count_no < $count_all){
        $status = 2;
    }
    bid_save_field($id, "status", $status);
}

function send_notif_main_uid($id){
    global $db;
    $query = $db->query("SELECT * FROM ".DB_PREFIX."_boiler_bid WHERE id = '".$id."' LIMIT 1");
    $r = $db->getRow($query);

    $attr = [];
    $attr['uid'] = $r["uid"];
    $attr['title'] = 'Заявка котла №'.$r["numb"].'. Товары набиты!';
    $attr['text'] = 'Заявка котла №'.$r["numb"].'. Товары набиты!';
    $attr['icon'] = config('app.url').'/files/push/new_storage.png';
    $attr['url'] = config('app.url').'/administration/module/boiler/bid/detail/'.$id;
    $attr['pNotif'] = true;
    $attr['pPush'] = true;
    $attr['module'] = "boiler_bid";
    $attr['pid'] = $id;
    sendAllNotif($attr);
}

function get_type_list($id){
    global $db;
    $query = $db->query("SELECT bbt.id, bbt.section, bbt.type, bbt.comment FROM ".DB_PREFIX."_boiler_bid_type as bbt LEFT JOIN ".DB_PREFIX."_boiler_bid as bb ON(bb.id = bbt.bid_id) WHERE bb.id = '".$id."'");
    if($db->numRows($query) > 0){
        ?>

        <div class="mb10">
            <label for="all_c_type_<?php echo $id;?>">
                <input class="mr20" type="checkbox" checked
                id="all_c_type_<?php echo $id;?>"
                onchange="checkbox_toggle(<?php echo $id;?>, $(this).prop('checked'), '.c_type_');
                dis_btn(!check_none_checkbox(<?php echo $id;?>, '.c_type_'), '#create_sales_modal_btn');">
                Все
            </label>
        </div>

        <?php
        while($r = $db->getRow($query)){
            ?>
            <div>
                <label for="c_type_<?php echo $r["id"];?>">
                    <input class="mr20 c_type_<?php echo $id;?>" type="checkbox" checked
                    name="type_id[]"
                    id="c_type_<?php echo $r["id"];?>"
                    value="<?php echo $r["id"];?>"
                    onchange="checkbox_all_change(<?php echo $id;?>, check_all_checkbox(<?php echo $id;?>, '.c_type_'), '#all_c_type_');
                    dis_btn(!check_none_checkbox(<?php echo $id;?>, '.c_type_'), '#create_sales_modal_btn');"
                    >
                    <?php echo list_type_group_name($r["section"])." &mdash; ".list_type_name($r["type"]); ?> <?php echo empty($r["comment"]) ? '' : '('.$r["comment"].')'; ?>
                </label>
            </div>
            <?php
        }
    } else {
        echo 'Типы не созданы!';
    }
}

function get_type_list_spec($id){
    $parent = $_REQUEST["parent"] ?? '#';
    if ($parent == "#") {
        $bbt = BoilerBidType::with('boiler_bid')->where('bid_id', $id)->get();
        $data = [];
        foreach ($bbt as $value_bbt) {
            $bbd = BoilerBidData::where('bid_type_id', $value_bbt->id)->count();
            $data[] = array(
                "id" => $value_bbt->id,
                "text" => list_type_group_name($value_bbt->section). " - ".list_type_name($value_bbt->type) . (empty($value_bbt->comment) ? '' : ' ('.$value_bbt->comment.')'),
                "children" => $bbd == 0 ? false : true,
                "type" => "root"
            );
        }
    } else {
        $bbd = BoilerBidData::with('categories')->where('bid_type_id', $parent)->get();

        foreach ($bbd as $value_bbd) {
            $data[] = array(
                "id" => $value_bbd->id,
                "text" => $value_bbd->categories->name . (empty($value_bbd->comment) ? '' : ' ('.$value_bbd->comment.')'),
                "children" => false,
            );
        }
    }
    header('Content-type: text/json');
    header('Content-type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($data);
    exit;
}

function get_spec(){
    $data = [];
    foreach (json_decode(file_get_contents('php://input'), true) as $value) {
        if(empty($value['children'])){
            $data[] = $value['id'];
        }
    }
    Specification::get($data, 'boiler_bid_all');
//
//    require(ROOT.'usr/modules/catalog_v2/sub/specification.php');
//    $docs = [];
//    $merge = Office::merge();
//    foreach ($data as $value) {
//         if(!empty($value['children'])){
//             $uniqid = uniqid().'.docx';
//             $url = (string)specification($value['id'], 'boiler_bid_all');



// $zip = new \PhpOffice\PhpWord\Shared\ZipArchive();
// $zip->open($url);//relative path to DOCX file
// $xml = new \DOMDocument();
// $xml->loadXML($zip->getFromName("docProps/app.xml"));

// // Returns the number of pages according to app.xml
// dd($xml);


//             Http::withToken('eJLttiTlhKRbbZDDwSu3WWfvdLQM5Ttp')->attach('attachment', file_get_contents($url), $uniqid)->post('https://api.ae-work.ru/storage.php');
//             $payload = [
//                 "async" => false,
//                 "filetype"=>  "docx",
//                 "key"=>  uniqid(),
//                 "outputtype"=> "pdf",
//                 "title"=>  "Example Document Title.docx",
//                 "url"=>  "https://api.ae-work.ru/uploads/".$uniqid,

//                 // "url"=>  Config::get('url').specification($value['id'], 'boiler_bid_all')
//             ];
//             dump("https://api.ae-work.ru/uploads/".$uniqid);
//             $token = \Firebase\JWT\JWT::encode($payload, 'eJLttiTlhKRbbZDDwSu3WWfvdLQM5Ttp');
//             $res = Http::withToken($token)->acceptJson()->post('http://ae-work.ru:1505/ConvertService.ashx', $payload)->json();
//             // dd($res);
//             $merge->addString(file_get_contents($res['fileUrl']));
//             // OfficeSupport::get_file(ROOT . 'files/user/' . Auth::id() . '_' . $value['id'] . '_spec.docx');
//             // $docs[] = uniqid().'.docx';
//         }


//    }

//    $merge->merge();
//    $merge->save('merged_result.pdf');
//    echo $merge->output();
// $oMerger->addPDF('/path/to/project/vendors/webklex/laravel-pdfmerger/src/PDFMerger/examples/pdf_one.pdf', [2]);
// $oMerger->addPDF('/path/to/project/vendors/webklex/laravel-pdfmerger/src/PDFMerger/examples/pdf_two.pdf', 'all');

// $oMerger->merge();
// $oMerger->save('merged_result.pdf');
//    $dm = new DocxMerge();
//    $dm->merge($docs, ROOT . 'files/user/' . Auth::id() . '_spec.docx');

}


function append_user_chat($id, $uid){
    $chat = new Chat();
    $chat_id = $chat->find_chat(["once" => "/", "where" => ["module" => "boiler_bid", "pid" => $id]]);
    if(count($chat_id) > 0){
        $chat->add_users($chat_id["id"], [$uid]);
    }
}

function send_notif_help($id, $uid, $text){
    $attr = [];
    $attr['uid'] = $uid;
    $attr['title'] = 'Уведомление из котла';
    $attr['text'] = empty($text) ? 'Уведомление из котла №'.$id.'. ' : 'Уведомление из котла №'.$id.'. '.$text;
    $attr['icon'] = config('app.url').'/files/push/new_storage.png';
    $attr['url'] = config('app.url').'/administration/module/boiler/bid/detail/'.$id;
    $attr['pNotif'] = true;
    $attr['pPush'] = true;
    $attr['module'] = "boiler_bid";
    $attr['pid'] = $id;
    sendAllNotif($attr);
}

function select_all_tov($id, $checkbox){
    global $db;
    $query = $db->query("SELECT bbdd.id FROM `".DB_PREFIX."_boiler_bid_data_detail` as bbdd
    LEFT JOIN `".DB_PREFIX."_boiler_bid_data` as bbd ON(bbd.id = bbdd.bbd_id)
    LEFT JOIN `".DB_PREFIX."_boiler_bid_type` as bbt ON(bbt.id = bbd.bid_type_id)
    LEFT JOIN `".DB_PREFIX."_boiler_bid` as bb ON(bb.id = bbt.bid_id)
    WHERE bb.id = '".$id."'");
    $id_list = [];
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            $id_list[] = $r["id"];
        }
    }
    if(count($id_list) > 0){
        $id_list_str = implode(", ", $id_list);
        $db->query("UPDATE ".DB_PREFIX."_boiler_bid_data_detail SET checkbox = '".$checkbox."' WHERE id IN(".$id_list_str.")");
    }
}

function get_folder($id){
    global $db;
    $query = $db->query("SELECT bbf.* FROM ".DB_PREFIX."_boiler_bid_folder as bbf WHERE bbf.bid_id = '".$id."'");
    $result = [];
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            $result[] = $r;
        }
    }
    return $result;
}

function folder_doit($id, $data){
    global $db;
    if(empty($data["folder_id"])){
        //если не выбрана папка -> создать новую
        $name = empty($data["name"]) ? "Новая папка" : $data["name"];
        $folder_id = folder_create(["bid_id" => $id, "name" => $name]);
    } else {
        if($data["folder_id"] == "null"){
            $folder_id = "";
        } else {
            $folder_id = $data["folder_id"];
        }
    }
    bid_save_field_type($data["id"], "folder_id", $folder_id);
}

function folder_create($data = []){
    //["bid_id" => "", "name" => ""]
    global $db;
    $db->query("INSERT INTO ".DB_PREFIX."_boiler_bid_folder (bid_id, name) VALUES ('".$data["bid_id"]."', '".$data["name"]."')");
    $iid = $db->insert_id();
    return $iid;
}

function boiler_bid_users_type($type_id = null){
    $type = [
        "Контактное лицо",
        "Менеджер объекта",
        "Менеджер заказа",
        "Инженер технических решений",
        "Производство",
        "Конструктор",
    ];
    if(is_null($type_id)){
        return $type;
    } else {
        return isset($type[ $type_id ]) ? $type[ $type_id ] : "";
    }
}

function boiler_bid_users_add($request){
    global $db, $core;
    //["bid_id" => "", "type" => "", "uid" => ""]
    $uid = empty($request["uid"]) ? Auth::id() : $request["uid"];
    $db->query("INSERT INTO `".DB_PREFIX."_boiler_bid_users` (bid_id, type, uid) VALUES ('".$request["bid_id"]."', '".$request["type"]."', '".$uid."')");
    $iid = $db->insert_id();
    return $iid;
}

function boiler_bid_users_del($id){
    global $db;
    $db->query("DELETE FROM `".DB_PREFIX."_boiler_bid_users` WHERE id = '".$id."' LIMIT 1");
}

function boiler_bid_users_find($bid_id, $type){
    global $db;
    $query = $db->query("SELECT * FROM `".DB_PREFIX."_boiler_bid_users` WHERE bid_id = '".$bid_id."' AND type = '".$type."'");
    $result = [];
    if($db->numRows($query) > 0){
        while($r = $db->getRow($query)){
            $result[] = $r;
        }
    }
    return $result;
}

function bid_files($request){
    $re = [];
    $re["comment"] = $request["value"];
    if(empty($request["pk"])){
        $re["attach_id"] = $request["attach_id"];
        \Boiler\Model\BoilerBidFiles::create($re);
    } else {
        $BoilerBidFiles = \Boiler\Model\BoilerBidFiles::findOrFail($request["pk"]);
        if(empty($re["comment"])){
            $BoilerBidFiles->delete();
        } else {
            $BoilerBidFiles->update($re);
        }
    }
}

function bid_delete_sales_link($bid_id, $sales_id){
    $bid = bid_query($bid_id);
    $sales_link = json_decode($bid["sales_link"], true);
    $new_sales_link = [];
    foreach($sales_link as $id){
        if($id != $sales_id) $new_sales_link[] = $id;
    }
    $value = json_encode($new_sales_link, JSON_UNESCAPED_UNICODE);
    bid_save_field($bid_id, "sales_link", $value);
}