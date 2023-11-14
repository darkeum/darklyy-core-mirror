<?php

namespace Pto;

class TPTO extends PTOTemp{
    protected $module = "pto";

    public function main($id = false){
        global $adminTpl, $config, $core, $admin_conf, $db, $lang;
	    $lnn = 'Список типоразмеров';
	    $adminTpl->admin_head('Менеджер теплообмеников | '.$lnn);
	    $atributs = array();
	    $atributs['opts'] = 'open';
	    $atributs['module'] = 'module';
	    $atributs['module_name'] = 'pto';
	    $atributs['module_open'] = 'edit';
	    $atributs['button'] = 'Добавить';
	    $atributs['button_url'] = '/administration/module/pto/add';	
        loadGrid('jqPTO', $lnn, $atributs, ROOT.'/usr/modules/pto/admin/grid/');
	    $adminTpl->admin_foot();
    }

    public function script_tpl(){
        global $adminTpl;
        $time = time();
        $adminTpl->footIncludes[] = <<<HTML
            <script src="/usr/modules/pto/admin/asset/js/{$this->module}/pto.js?{$time}"></script>
            <script src="/usr/modules/pto/admin/asset/js/{$this->module}/main.js?{$time}"></script>
HTML;
    }

    public function field_list($request){
        $id = empty($request["id"]) ? false : $request["id"];
        $data = [];
        if($id){
            $submit_value = "Обновить";
            $data = \Pto\Model\Pto::find($id)->toArray();
        }
        $title = empty($data["title"]) ? null : $data["title"];
        $altname = empty($data["altname"]) ? null : $data["altname"];
        $dn = empty($data["dn"]) ? null : $data["dn"];
        $sp = empty($data["sp"]) ? null : $data["sp"];
        $st = empty($data["st"]) ? null : $data["st"];
        $img_2d = empty($data["img_2d"]) ? null : $data["img_2d"];
        $img_3d = empty($data["img_3d"]) ? null : $data["img_3d"];
        $man_id = empty($data["man_id"]) ? '146' : $data["man_id"];
        $t_from = empty($data["t_from"]) ? '-30' : $data["t_from"];
        $t_to = empty($data["t_to"]) ? '200' : $data["t_to"];
        $q_max = empty($data["q_max"]) ? null : $data["q_max"];
        $p_max = empty($data["p_max"]) ? null : $data["p_max"];
        $gasket_type = empty($data["gasket_type"]) ? array_keys(\Pto\Model\Pto::getGasketType()) : $data["gasket_type"];
        $plates_type = empty($data["plates_type"]) ? array_keys(\Pto\Model\Pto::getPlatesType()) : $data["plates_type"];
        $coolant = empty($data["coolant"]) ? array_keys(\Pto\Model\Pto::getCoolant()) : $data["coolant"];
        $main_plates = empty($data["main_plates"]) ? array_keys(\Pto\Model\Pto::getMainPlates()) : $data["main_plates"];
        $other_plates = empty($data["other_plates"]) ? array_keys(\Pto\Model\Pto::getOtherPlates()) : $data["other_plates"];
        $warranty = empty($data["warranty"]) ? '3' : $data["warranty"];
	
        $oSelectSetting = [
            "mod" => "jsAdminTpl",
            "name" => "man_id",
            "id" => $man_id,
            "ajaxUrl" => "getCatalogManufactureToSelect",
            "return" => true,
            "required" => true,
        ];
        oSelectCustom($oSelectSetting);
        $oSelectSetting["mod"] = "select";
        $man_id_select = oSelectCustom($oSelectSetting);
    
        $gasket_type_option  = array_to_options(\Pto\Model\Pto::getGasketType(), ["selected" => $gasket_type]);
        $plates_type_option  = array_to_options(\Pto\Model\Pto::getPlatesType(), ["selected" => $plates_type]);
        $coolant_option      = array_to_options(\Pto\Model\Pto::getCoolant(), ["selected" => $coolant]);
        $main_plates_option  = array_to_options(\Pto\Model\Pto::getMainPlates(), ["selected" => $main_plates]);
        $other_plates_option = array_to_options(\Pto\Model\Pto::getOtherPlates(), ["selected" => $other_plates]);

        return <<<HTML
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Наименование</label>
                    <div class="col-lg-4">
                        <input name="title" type="text" id="title" placeholder="Введите наименование" class="gui-input" onchange="getTranslit(gid('title').value, 'altname');"').' value="{$title}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">altname</label>
                    <div class="col-lg-4">
                        <input name="altname" id="altname" type="text" placeholder="altname" class="gui-input" value="{$altname}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Диаметр</label>
                    <div class="col-lg-4">
                        <input name="dn" type="number" step="any" placeholder="Введите диаметр пластины" class="gui-input" value="{$dn}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Площадь пластины</label>
                    <div class="col-lg-4">
                        <input name="sp" type="number" step="any" placeholder="Введите площадь пластины" class="gui-input" value="{$sp}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Площадь теплообменика</label>
                    <div class="col-lg-4">
                        <input name="st" type="number" step="any" placeholder="Введите площадь теплообменика" class="gui-input" value="{$st}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Ссылка на 2D изображение</label>
                    <div class="col-lg-4">
                        <label for="preview" class="field smart-widget sm-right">
                            <input name="img_2d" id="img_2d" type="text" placeholder="Введите ссылку на 2D изображение" class="gui-input" value="{$img_2d}" required>
                            <a id="box_img_2d" data-fancybox-type="iframe" href="usr/plugins/filemanager/dialog.php?type=1&field_id=img_2d&relative_url=0" class="button" type="button">
                                <i class="fas fa-folder-open"></i>
                            </a>	
                        </label>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Ссылка на 3D изображение</label>
                    <div class="col-lg-4">
                        <label for="preview" class="field smart-widget sm-right">
                            <input name="img_3d" id="img_3d" type="text" placeholder="Введите ссылку на 3D изображение" class="gui-input" value="{$img_3d}" required>
                            <a id="box_img_3d" data-fancybox-type="iframe" href="usr/plugins/filemanager/dialog.php?type=1&field_id=img_3d&relative_url=0" class="button" type="button">
                                <i class="fas fa-folder-open"></i>
                            </a>	
                        </label>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Производитель</label>
                    <div class="col-lg-4">
                        {$man_id_select}
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Рабочая температура</label>
                    <div class="col-lg-4">
                        <div class="d-flex align-items-center">
                            <div class="mr10">От</div>
                            <div><input type="number" step="any" name="t_from" class="gui-input" value="{$t_from}" placeholder="от" required></div>
                            <div class="ml10 mr10">до</div>
                            <div><input type="number" step="any" name="t_to" class="gui-input" value="{$t_to}" placeholder="до" required></div>
                        </div>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Максимальынй расход</label>
                    <div class="col-lg-4">
                        <input name="q_max" type="number" step="any" placeholder="Введите максимальынй расход" class="gui-input" value="{$q_max}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Максимальное рабочее давление</label>
                    <div class="col-lg-4">
                        <input name="p_max" type="number" step="any" placeholder="Введите максимальное рабочее давление" class="gui-input" value="{$p_max}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Материал прокладок</label>
                    <div class="col-lg-4">
                        <select name="gasket_type[]" class="form-control pto_select2" multiple="multiple" required>{$gasket_type_option}</select>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Материал пластин</label>
                    <div class="col-lg-4">
                        <select name="plates_type[]" class="form-control pto_select2" multiple="multiple" required>{$plates_type_option}</select>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Теплоноситель</label>
                    <div class="col-lg-4">
                        <select name="coolant[]" class="form-control pto_select2" multiple="multiple" required>{$coolant_option}</select>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Основной тип пластин</label>
                    <div class="col-lg-4">
                        <select name="main_plates[]" class="form-control pto_select2" multiple="multiple" required>{$main_plates_option}</select>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Промежуточный тип пластин</label>
                    <div class="col-lg-4">
                        <select name="other_plates[]" class="form-control pto_select2" multiple="multiple" required>{$other_plates_option}</select>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Гарантия</label>
                    <div class="col-lg-4">
                        <input name="warranty" type="number" step="any" placeholder="Введите гарантию" class="gui-input" value="{$warranty}" required>
                    </div>	
                </div>
HTML;
    }

    public function fbi(){
        $_SESSION["RF"]["fff"] ="files/pto/";	
        fancyboxInit("box_img_2d");
        fancyboxInit("box_img_3d");
    }
}