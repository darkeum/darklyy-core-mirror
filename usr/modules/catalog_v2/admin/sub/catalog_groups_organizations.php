<?php

function index_catalog_groups_organizations(){
    global $url, $adminTpl;
    switch(isset($url[4]) ? $url[4] : null){
        default:
            index_catalog_groups_organizations_main();
        break;
        case "add":
            $catalog_groups_organizations = \Catalog_v2\Model\CatalogGroupsOrganizations::create($_POST);
            echo $catalog_groups_organizations->id;
        break;
        case "delete":
            $id = $url[5];
            \Catalog_v2\Model\CatalogGroupsOrganizations::destroy($id);
        break;
        case "update":
          $id = $url[5];
          \Catalog_v2\Model\CatalogGroupsOrganizations::findOrFail($id)->update($_POST);
        break;
        case "discount_list":
            $uid = $url[5];
            echo discount_list($uid);
        break;
        case "discount_template_list":
            $id = $url[5];
            echo discount_template_list($id);
        break;
        case "catalog_groups_option":
            $id = $url[5];
            $catalog_groups_discount_option = catalog_groups_discount_option($id);
            echo $catalog_groups_discount_option["list_str"];
        break;
        case "organizations_list":
            echo organizations_list();
        break;
        case "template_set":
            catalog_groups_template_set($_POST);
        break;
        case "list_template":
            echo discount_template_name_list();
        break;
        case "template_name_add":
            \Catalog_v2\Model\CatalogGroupsTemplateName::create($_POST);
        break;
    }
}
function index_catalog_groups_template(){
    global $url, $adminTpl;
    switch(isset($url[4]) ? $url[4] : null){
        case "add":
            $catalog_groups_template = \Catalog_v2\Model\CatalogGroupsTemplate::create($_POST);
            echo $catalog_groups_template->id;
        break;
        case "delete":
            $id = $url[5];
            \Catalog_v2\Model\CatalogGroupsTemplate::destroy($id);
        break;
        case "update":
          $id = $url[5];
          \Catalog_v2\Model\CatalogGroupsTemplate::findOrFail($id)->update($_POST);
        break;
    }
}

function index_catalog_groups_organizations_main(){
    global $db, $adminTpl;
    $lnn = 'Группы товаров пользователей';
    $adminTpl->admin_head('Менеджер каталога | '.$lnn);

    $organizations_list_str = organizations_list();

    $discount_template = discount_template_name_list();

    oSelect("jsAdminTpl", "oid", null, "getOrganizations");
    $select = oSelect("select", "oid", null, "getOrganizations", false, false, true);

    $catalog_groups_template_name_id = [];
    $catalog_groups_template_name = discount_template_data();
    foreach($catalog_groups_template_name as $item){
        $catalog_groups_template_name_id[] = "<option value=\"{$item['id']}\">{$item['name']}</option>";
    }
    $catalog_groups_template_name_id = implode("", $catalog_groups_template_name_id);

    // $catalog_groups_option = catalog_groups_option();
    // $catalog_groups_option_first = $catalog_groups_option["list"][0]["id"];
    // $catalog_groups_discount_option = catalog_groups_discount_option($catalog_groups_option_first);
  
    echo <<<HTML

        <div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">Организации</span>			
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" onsubmit="return false;" role="form" class="form-horizontal parsley-form">
                    <div>
                        <!-- <div class="btn-group" role="group" aria-label="Basic example">
                          <button type="button" class="btn btn-primary btn-sm" disabled>Загрузить данные в Excel</button>
                          <button type="button" class="btn btn-success btn-sm" disabled>Добавить данные с Excel</button>
                        </div> -->
                    </div>
                    <div class="col-sm-6">
                        <h3>Выбор организации</h3>
                        <div>{$select}</div>
                        <div id="user_edit"></div>
                    </div>
                    <div class="col-sm-3">
                        <h3>Организация со скидками</h3>
                        <div id="users_list">{$organizations_list_str}</div>
                    </div>
                    <div class="col-sm-3">
                        <div class="d-flex align-items-center">
                            <h3>Шаблоны скидок</h3>
                            <i class="fas fa-plus text-success cp ml10" data-toggle="modal" data-target="#discount_template_modal"></i>
                        </div>
                        <div id="discount_template">{$discount_template}</div>
                    </div>
				</form>
			</div>
		</div>

        <div class="modal fade" id="template_set_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="template_set_modal_form">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Загрузить с шаблона</h5>
                    </div>
                    <div class="modal-body">
                        <h3>Внимание! При загрузке с шаблона, все установленные значения будут удалены!</h3>
                      <select name="catalog_groups_template_name_id" class="form-control">{$catalog_groups_template_name_id}</select>
                      <input type="hidden" name="oid" id="template_set_modal_oid">
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Загрузить</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </form>
            </div>
          </div>
        </div>

        <div class="modal fade" id="discount_template_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="discount_template_modal_form">
                    <div class="modal-header">
                      <h5 class="modal-title" id="exampleModalLabel">Добавить шаблон</h5>
                    </div>
                    <div class="modal-body">
                      <input type="text" name="name" class="form-control" placeholder="Введите имя шаблона" required>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Добавить</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </form>
            </div>
          </div>
        </div>

HTML;

    echo add_modal_constructor("add");

    $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/catalog_groups_organizations.js"></script>';
    $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/catalog_groups_organizations_main.js"></script>';
    $adminTpl->admin_foot();
}

function add_modal_constructor($name){
    return <<<HTML
        <div class="modal fade admin-form" id="{$name}_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form onsubmit="return false;" id="{$name}_modal_form">
                    <div class="modal-header">
                      <h5 class="modal-title">Добавить</h5>
                    </div>
                    <div class="modal-body">
                      <label for="">Группа скидок</label>
                      <select name="catalog_groups_discount_id" class="form-control select2 catalog_groups_discount_select"></select>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-primary btn_groups_users_modal" data-dismiss="modal" id="{$name}_add_btn">Добавить</button>
                      <button type="button" class="btn btn-primary btn_groups_users_modal" data-dismiss="modal" id="{$name}_edit_btn">Редактировать</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </form>
            </div>
          </div>
        </div>
HTML;
}

function catalog_groups_option(){
    $list = \Catalog_v2\Model\CatalogGroups::all()->toArray();
    $result = [];
    foreach($list as $val){
        $result[] = <<<HTML
        <option value="{$val['id']}">{$val["name"]}</option>
HTML;
    }
    return [
        "list" => $list,
        "list_str" => implode("", $result),
    ];
}

function catalog_groups_discount_option($id){
    $list = \Catalog_v2\Model\CatalogGroupsDiscount::with("catalog_groups_discount_name")->where("catalog_groups_id", $id)->get()->toArray();
    $result = [];
    foreach($list as $val){
        $val["name"] = $val["catalog_groups_discount_name"]["name"];
        $opt = select_option_template($val);
        $result[] = <<<HTML
        <option value="{$val['id']}">{$opt}</option>
HTML;
    }
    return [
        "list" => $list,
        "list_str" => implode("", $result),
    ];
}

function select_option_template($request){
    return <<<HTML
        {$request["name"]} ({$request["discount"]}%)
HTML;
}

function organizations_list(){
    $organizations_list = \Catalog_v2\Model\CatalogGroupsOrganizations::groupBy('oid')->get()->toArray();
    if(count($organizations_list) > 0){
        $re = [];
        foreach($organizations_list as $val){
            $organization = getDataOrganizations($val["oid"]);
            $organization_name = $organization["name"];
            $oid = $val["oid"];
            $re[] = <<<HTML
                <div>
                    <a href="javascript:void(0);" onclick="organizations_edit({$oid});">{$organization_name}</a>
                </div>
HTML;
        }
        $organizations_list_str = implode("", $re);
    } else {
        $organizations_list_str = '<i>Организации отсутствуют!</i>';
    }
    return $organizations_list_str;
}

function discount_template_data(){
    return \Catalog_v2\Model\CatalogGroupsTemplateName::all()->toArray();
}

function discount_template_name_list(){
    $discount_template_items = [];
    $discount_template = discount_template_data();
    if(count($discount_template) > 0){
        foreach($discount_template as $item){
            $discount_template_items[] = <<<HTML
                <div><a href="javascript:void(0);" onclick="template_edit({$item['id']});">{$item['name']}</a></div>
HTML;
        }
    } else {
        $discount_template_items[] = '<i>Шаблоны отсутствуют!</i>';
    }
    
    return implode("", $discount_template_items);
}

function discount_list($oid){
    $list = \Catalog_v2\Model\CatalogGroupsOrganizations::get_organizations_discount($oid);
    $organization = getDataOrganizations($oid);
    $oid_name = $organization["name"];

    $list_str = discount_list_template(["list" => $list, "name_const" => "groups_organizations_modal"]);

    $list_str = <<<HTML
    <div class="mt20">
        <input type="hidden" value="{$oid}" id="oid_select">
        <div class="d-flex align-items-center">
            <h4>Организация {$oid_name}</h4>
            <i class="fas fa-stamp text-info cp ml20" onclick="template_set_modal({$oid});"></i>
        </div>
        {$list_str}
    </div>
HTML;
    return $list_str;
}

function discount_template_list($id){
    $list = \Catalog_v2\Model\CatalogGroupsTemplate::get_template_discount($id);
    $template = \Catalog_v2\Model\CatalogGroupsTemplateName::find($id);
    $name = empty($template["name"]) ? "Не найден" : $template["name"];

    $list_str = discount_list_template(["list" => $list, "name_const" => "groups_template_modal"]);

    $list_str = <<<HTML
    <div class="mt20">
        <input type="hidden" value="{$id}" id="template_select">
        <h4>Шаблон {$name}</h4>
        {$list_str}
    </div>
HTML;
    return $list_str;
}

function discount_list_template($request){
    $list_str = "";
    $list = $request["list"];
    $name_const = $request["name_const"];
    if(count($list) > 0){
        $re = [];
        foreach($list as $val){
            $flag = empty($val["cgu_id"]);
            $opt = $flag ? '<span class="text-danger">Не указана</span>' : select_option_template(["name" => $val["cgd_name"], "discount" => $val["cgd_discount"]]);
            if($flag){
                $btn = <<<HTML
                <i class="fas fa-plus btn text-success btn-sm" title="Добавить" onclick="{$name_const}.add({ group_id: {$val['cg_id']} });"></i>
HTML;
            } else {
                $btn = <<<HTML
                <div>
                    <i class="fas fa-edit btn text-primary" title="Редактировать" onclick="{$name_const}.edit({id: '{$val['cgu_id']}', discount_id: '{$val['cgu_catalog_groups_discount_id']}', group_id: {$val['cg_id']} }); "></i>
                    <i class="fas fa-times btn text-danger" title="Удалить" onclick="{$name_const}.delete({$val['cgu_id']});"></i>
                </div>
HTML;
            }

            $re[] = <<<HTML
            <div class="d-flex align-items-center">
                <div class="mr10">Группа товаров: <b><a target="_blank" href="/administration/module/catalog_v2/groups/edit/{$val['cg_id']}" title="{$val['cg_comment']}">{$val["cg_name"]}</a></b></div>
                <div>Группа скидок: <b>{$opt}</b></div>
                {$btn}
            </div>
HTML;
        }
        $list_str = implode("", $re);
    } else {
        $list_str = '<i>Скидки отсутствуют!</i>';
    }

    return $list_str;
}

function catalog_groups_template_set($request){
    global $db;
    // dd($request);
    $catalog_groups_template_name_id = $request["catalog_groups_template_name_id"];
    $oid = empty($request["oid"]) ? false : $request["oid"];
    if($oid && $catalog_groups_template_name_id){
        $db->query("DELETE FROM `".DB_PREFIX."_catalog_groups_organizations` WHERE oid = '{$oid}'");
        $query = $db->query("SELECT * FROM `".DB_PREFIX."_catalog_groups_template` WHERE catalog_groups_template_name_id = '{$catalog_groups_template_name_id}'");
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                \Catalog_v2\Model\CatalogGroupsOrganizations::create(["oid" => $oid, "catalog_groups_discount_id" => $r["catalog_groups_discount_id"]]);
            }
        }
    }
}