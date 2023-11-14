<?php

function index_pump(){
    global $url, $adminTpl;
    // use \Catalog_v2\Catalog_v2;
    
    switch(isset($url[4]) ? $url[4] : null){
        default:
            index_pump_main();
        break;
        case "add":
            $pump = \Catalog_v2\Model\Pump::create($_POST);
            echo $pump->id;
        break;
        case "edit":
            $id = $url[5];
            index_pump_edit($id);
        break;
        case "update":
            $id = $url[5];
            \Catalog_v2\Model\Pump::findOrFail($id)->update($_POST);
        break;
        case "delete":
            $id = $url[5];
            \Catalog_v2\Model\Pump::destroy($id);
        break;
    }
}

function index_pump_main(){
    global $url, $adminTpl;
    $lnn = 'Насосы';
    $adminTpl->admin_head('Менеджер каталога | '.$lnn);
    $atributs = array();
    $atributs['opts'] = 'open';
    $atributs['module'] = 'module';
    $atributs['module_name'] = 'catalog_v2/pump';
    $atributs['module_open'] = 'edit';	
    $atributs['button'] = 'Добавить';
    $atributs['button_url'] = '/administration/module/catalog_v2/pump/add';	
    $atributs['button_onclick'] = 'add_modal();';	
    loadGrid('jqPump', $lnn, $atributs, ROOT.'/usr/modules/catalog_v2/admin/grid/');
    $adminTpl->footIncludes[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/pump.js?'.time().'"></script>';

    echo pump_add_modal();

    $adminTpl->admin_foot();
}

function pump_add_modal($request = []){
    $id = null;
    if(!empty($request["id"])){
        $id = $request["id"];
    }
    $open = "";
    if(isset($request["open"])){
        $open = "{open:true}";
    }
    $select_js = oSelect("jsReturn", "cid", $id, "getCatalogToSelect", false, false, true);
    $select = oSelect("select", "cid", $id, "getCatalogToSelect", false, false, true);
    $time = time();
    return <<<HTML
        <div class="modal fade admin-form" id="addModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form onsubmit="return false;" id="addModalForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Добавить</h5>
                    </div>
                    <div class="modal-body">
                        {$select}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" onclick="add_modal_save({$open});">Добавить</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    </div>
                </form>
            </div>
          </div>
        </div>
        <script>{$select_js}</script>
        <script src="/usr/modules/catalog_v2/admin/asset/js/pump_add.js?{$time}"></script>
HTML;
}

function index_pump_edit($id){
    global $url, $adminTpl;
    $p = \Catalog_v2\Model\Pump::findOrFail($id)->toArray();
    // dd($p);
    $catalog = \Catalog_v2\Model\Catalog::findOrFail($p["cid"]);

    // Тип установки
    $toi_opt_str = [];
    foreach(\Catalog_v2\Model\Pump::getTypeOfInstalation() as $key => $value){
        $toi_opt_str[] = "<option value='{$key}' ".($key == $p["type_of_instalation"] ? "selected" : "").">{$value}</option>";
    }
    $toi_opt_str = implode("", $toi_opt_str);

    // Рабочая линия
    $wl_opt_str = [];
    foreach(\Catalog_v2\Model\Pump::getWorkLine() as $key => $value){
        $wl_opt_str[] = "<option value='{$key}' ".($key == $p["work_line"] ? "selected" : "").">{$value}</option>";
    }
    $wl_opt_str = implode("", $wl_opt_str);

    // Тип графика
    $gtype_opt_str = [];
    $types = \Catalog_v2\Model\Pump::pluck('type')->diff([''])->unique()->values();
    foreach($types as $key => $value){
        $gtype_opt_str[] = "<option value='{$value}' ".($value == $p["type"] ? "selected" : "").">{$value}</option>";
    }
    $gtype_opt_str = implode("", $gtype_opt_str);

    $text = \Catalog_v2\Model\Pump::data_to_text((array) $p["data"]);

    $scripts = []; 
    $scripts[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/pump.js?'.time().'"></script>';
    $scripts[] = '<script src="/usr/modules/catalog_v2/admin/asset/js/pump_edit.js?'.time().'"></script>';
    $scripts_str = implode("", $scripts);

    $add_modal = pump_add_modal(["id" => $p["cid"], "open" => "true"]);
    $adminTpl->admin_head('Редактировать');
    echo <<<HTML
        <div id="content" class="animated fadeIn">
			<div class="panel panel-dark panel-border top">
				<div class="panel-heading"><span class="panel-title">{$catalog->title} ({$catalog->sku})</span>			
			</div>			
			<div class="panel-body admin-form">
				<form id="admin-form" onsubmit="return false;" role="form" class="form-horizontal parsley-form">
                <input type="hidden" value="{$id}" name="id" id="mainId">	
                <input type="hidden" value="{$p['cid']}" name="cid">
                <button class="btn btn-sm btn-success" onclick="save();">Сохранить</button>
                <button class="btn btn-sm btn-primary" onclick="add_modal();">Добавить</button>
					<div class="form-group">
						<label for="type_of_instalation"  class="col-lg-3 control-label">Тип установки</label>
						<div class="col-lg-4">
							<label class="field select">
								<select class="form-control" name="type_of_instalation" id="type_of_instalation" onchange="add_change(this);">
									{$toi_opt_str}
                                </select>
                                <i class="arrow double"></i>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="work_line"  class="col-lg-3 control-label">Рабочая линия</label>
						<div class="col-lg-4">
							<label class="field select">
								<select class="form-control" name="work_line" id="work_line" onchange="add_change(this);">
									{$wl_opt_str}
                                </select>
                                <i class="arrow double"></i>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="work_line"  class="col-lg-3 control-label">Тип графика</label>
						<div class="col-lg-4">
							<label class="field select">
								<!--<input type="text" name="type" id="type" value="{$p['type']}" class="form-control" required>-->
								<select name="type" id="type" class="form-control" required>
								    {$gtype_opt_str}
								</select>
								<i class="arrow double"></i>
							</label>
						</div>
					</div>
					<div class="form-group">
						<label for="data"  class="col-lg-3 control-label">Точки</label>
						<div class="col-lg-4">
							<textarea name="data" id="data" class="form-control" rows="20" onchange="add_change(this);">{$text}</textarea>
						</div>
					</div>
				</form>
			</div>
		</div>
        {$add_modal}
        {$scripts_str}
HTML;
$adminTpl->admin_foot();
}