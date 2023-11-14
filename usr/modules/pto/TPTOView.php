<?php

namespace Pto;

class TPTOView extends PTOTemp{
    protected $module = "ptoview";

    public function main($id = false){
        global $adminTpl, $config, $core, $admin_conf, $db, $lang, $jqPTOViewId;
        $lnn = 'Список рам теплообменника';
        $jqPTOViewId = $id;
	    $adminTpl->admin_head('Рамы теплообменника | '.$lnn);
	    $atributs = array();
	    $atributs['opts'] = 'open';
	    $atributs['module'] = 'module';
	    $atributs['module_name'] = "pto/view/{$id}";
	    $atributs['module_open'] = 'edit';
	    $atributs['button'] = 'Добавить';
	    $atributs['button_url'] = "/administration/module/pto/view/{$id}/add";	
	    //$atributs['js'] = 'setTimeout(function () {$("#jqSales").jqGrid(\'setGridParam\',{postData:{end: \'0\'}}); $(\'#jqSales\').trigger(\'reloadGrid\');}, 150);';
        load_grid('jqPTOView', $lnn, $atributs, ROOT.'/usr/modules/pto/admin/grid/');
        
	    //$adminTpl->footIncludes[] = '<script src="/usr/modules/***"></script>';
	    $adminTpl->admin_foot();
    }

    public function script_tpl(){
        global $adminTpl;
        $time = time();
        $adminTpl->footIncludes[] = <<<HTML
            <script src="/usr/modules/pto/admin/asset/js/{$this->module}/ptoview.js?{$time}"></script>
            <script src="/usr/modules/pto/admin/asset/js/{$this->module}/main.js?{$time}"></script>
HTML;
    }

    public function field_list($request){
        $id = empty($request["id"]) ? false : $request["id"];
        $pto_id = $request["pto_id"];
        $data = [];
        if($id){
            $data = \Pto\Model\PtoView::findOrFail($id)->toArray();
        }
        $type = empty($data["type"]) ? null : $data["type"];
        $numb = empty($data["numb"]) ? null : $data["numb"];
        $p = empty($data["p"]) ? null : $data["p"];
        $l = empty($data["l"]) ? null : $data["l"];
        $l1 = empty($data["l1"]) ? null : $data["l1"];
        $l2 = empty($data["l2"]) ? null : $data["l2"];
        $hairpin_type = empty($data["hairpin_type"]) ? null : $data["hairpin_type"];
        $hairpin_count = empty($data["hairpin_count"]) ? null : $data["hairpin_count"];
        $plates_min = empty($data["plates_min"]) ? null : $data["plates_min"];
        $plates_max = empty($data["plates_max"]) ? null : $data["plates_max"];
        $weight = empty($data["weight"]) ? null : $data["weight"];
        $dn = empty($data["dn"]) ? null : $data["dn"];
        $mesh_d = empty($data["mesh_d"]) ? null : $data["mesh_d"];
        $bolt_count = empty($data["bolt_count"]) ? null : $data["bolt_count"];
        $bolt_type = empty($data["bolt_type"]) ? null : $data["bolt_type"];
        $cad_2d = empty($data["cad_2d"]) ? null : $data["cad_2d"];
        $cad_3d = empty($data["cad_3d"]) ? null : $data["cad_3d"];
        $cad_revit = empty($data["cad_revit"]) ? null : $data["cad_revit"];

    
        $type_option  = "<option></option>".array_to_options(\Pto\Model\PtoView::getType(), ["selected" => $type]);

        return <<<HTML
                <input type="hidden" name="pto_id" value="{$pto_id}" id="pto_id">
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Тип ПТО</label>
                    <div class="col-lg-4">
                        <select name="type" class="form-control pto_select2" required>{$type_option}</select>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">№ рамы</label>
                    <div class="col-lg-4">
                        <input name="numb" type="number" placeholder="Введите № рамы" class="gui-input" value="{$numb}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Давление</label>
                    <div class="col-lg-4">
                        <input name="p" type="number" step="any" placeholder="Введите диаметр давление" class="gui-input" value="{$p}" required>
                    </div>	
                </div>		
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Размер 1</label>
                    <div class="col-lg-4">
                        <input name="l" type="number" step="any" placeholder="Введите размер 1" class="gui-input" value="{$l}" required>
                    </div>	
                </div>		
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Размер 2</label>
                    <div class="col-lg-4">
                        <input name="l1" type="number" step="any" placeholder="Введите размер 2" class="gui-input" value="{$l1}" required>
                    </div>	
                </div>		
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Размер 3</label>
                    <div class="col-lg-4">
                        <input name="l2" type="number" step="any" placeholder="Введите размер 3" class="gui-input" value="{$l2}" required>
                    </div>	
                </div>		
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Размер шпильки</label>
                    <div class="col-lg-4">
                        <input name="hairpin_type" type="text" placeholder="Введите размер шпильки" class="gui-input" value="{$hairpin_type}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Кол-во шпилек</label>
                    <div class="col-lg-4">
                        <input name="hairpin_count" type="number" step="any" placeholder="Введите кол-во шпилек" class="gui-input" value="{$hairpin_count}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Мин. кол-во пластин</label>
                    <div class="col-lg-4">
                        <input name="plates_min" type="number" step="any" placeholder="Введите мин. кол-во пластин" class="gui-input" value="{$plates_min}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Макс кол-во пластин</label>
                    <div class="col-lg-4">
                        <input name="plates_max" type="number" step="any" placeholder="Введите макс кол-во пластин" class="gui-input" value="{$plates_max}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Вес</label>
                    <div class="col-lg-4">
                        <input name="weight" type="number" step="any" placeholder="Введите вес" class="gui-input" value="{$weight}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Наружный диаметр</label>
                    <div class="col-lg-4">
                        <input name="dn" type="number" step="any" placeholder="Введите наружний дииаметр" class="gui-input" value="{$dn}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Диаметр сетки</label>
                    <div class="col-lg-4">
                        <input name="mesh_d" type="number" step="any" placeholder="Введите димаетр сетки" class="gui-input" value="{$mesh_d}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Кол-во отверстий</label>
                    <div class="col-lg-4">
                        <input name="bolt_count" type="number" step="any" placeholder="Введите кол-во болтов" class="gui-input" value="{$bolt_count}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Размер болта</label>
                    <div class="col-lg-4">
                        <input name="bolt_type" type="text" placeholder="Введите размер болта" class="gui-input" value="{$bolt_type}" required>
                    </div>	
                </div>	
                <div class="form-group">
                    <label class="col-lg-3 control-label">ID файл dwg 2D чертежа</label>
                    <div class="col-lg-4 d-flex">
                        <label for="preview" class="field smart-widget sm-right w100p">
                            <input name="cad_2d" id="cad_2d" type="number" placeholder="ID файл dwg 2D чертежа" class="gui-input" value="{$cad_2d}" required>
                            <label class="button fas fa-folder-open"><input type="file" class="input_load_ftp d-none" data-input="#cad_2d"></label>
                        </label>
                        <i class="button fas fa-file-download" onclick="load_ftp(`#cad_2d`);"></i>
                    </div>	
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">ID файл dwg 3D чертежа</label>
                    <div class="col-lg-4 d-flex">
                        <label for="preview" class="field smart-widget sm-right w100p">
                            <input name="cad_3d" id="cad_3d" type="number" placeholder="ID файл dwg 3D чертежа" class="gui-input" value="{$cad_3d}" required>
                            <label class="button fas fa-folder-open"><input type="file" class="input_load_ftp d-none" data-input="#cad_3d"></label>	
                        </label>
                        <i class="button fas fa-file-download" onclick="load_ftp(`#cad_3d`);"></i>
                    </div>	
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">ID файл REVIT</label>
                    <div class="col-lg-4 d-flex">
                        <label for="preview" class="field smart-widget sm-right w100p">
                            <input name="cad_revit" id="cad_revit" type="number" placeholder="ID файл REVIT" class="gui-input" value="{$cad_revit}" required>
                            <label class="button fas fa-folder-open"><input type="file" class="input_load_ftp d-none" data-input="#cad_revit"></label>		
                        </label>
                        <i class="button fas fa-file-download" onclick="load_ftp(`#cad_revit`);"></i>
                    </div>	
                </div>
HTML;
    }
}