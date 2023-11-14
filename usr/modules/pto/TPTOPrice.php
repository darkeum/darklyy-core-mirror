<?php

namespace Pto;

class TPTOPrice extends PTOTemp{
    protected $module = "ptoprice";

    public function main($id = false){
        global $adminTpl, $config, $core, $admin_conf, $db, $lang, $jqPTOPriceId;
        $lnn = '***';
        $jqPTOPriceId = $id;
	    $adminTpl->admin_head('*** | '.$lnn);
	    $atributs = array();
	    $atributs['opts'] = 'open';
	    $atributs['module'] = 'module';
	    $atributs['module_name'] = "pto/price/{$id}";
	    $atributs['module_open'] = 'edit';
	    $atributs['button'] = 'Добавить';
	    $atributs['button_url'] = "/administration/module/pto/price/{$id}/add";	
	    //$atributs['js'] = 'setTimeout(function () {$("#jqSales").jqGrid(\'setGridParam\',{postData:{end: \'0\'}}); $(\'#jqSales\').trigger(\'reloadGrid\');}, 150);';
        load_grid('jqPTOPrice', $lnn, $atributs, ROOT.'/usr/modules/pto/admin/grid/');
        
	    //$adminTpl->footIncludes[] = '<script src="/usr/modules/***"></script>';
	    $adminTpl->admin_foot();
    }

    public function script_tpl(){
        global $adminTpl;
        $time = time();
        $adminTpl->footIncludes[] = <<<HTML
            <script src="/usr/modules/pto/admin/asset/js/{$this->module}/ptoprice.js?{$time}"></script>
            <script src="/usr/modules/pto/admin/asset/js/{$this->module}/main.js?{$time}"></script>
HTML;
    }

    public function field_list($request){
        $id = empty($request["id"]) ? false : $request["id"];
        $pto_id = $request["pto_id"];
        $data = [];
        if($id){
            $data = \Pto\Model\PtoPrice::findOrFail($id)->toArray();
        }
        $type_in = empty($data["type_in"]) ? null : $data["type_in"];
        $type_out = empty($data["type_out"]) ? null : $data["type_out"];
        $power = empty($data["power"]) ? null : $data["power"];
        $t1 = empty($data["t1"]) ? null : $data["t1"];
        $t2 = empty($data["t2"]) ? null : $data["t2"];
        $t3 = empty($data["t3"]) ? null : $data["t3"];
        $t4 = empty($data["t4"]) ? null : $data["t4"];
        $price = empty($data["price"]) ? null : $data["price"];
        $aid_calc = empty($data["aid_calc"]) ? null : $data["aid_calc"];
        $d1 = empty($data["d1"]) ? null : $data["d1"];
        $d2 = empty($data["d2"]) ? null : $data["d2"];

    
        $type_in_option  =  "<option></option>".array_to_options(\Pto\Model\PtoPrice::getType(), ["selected" => $type_in]);
        $type_out_option  = "<option></option>".array_to_options(\Pto\Model\PtoPrice::getType(), ["selected" => $type_out]);

        return <<<HTML
                <input type="hidden" name="pto_id" value="{$pto_id}" id="pto_id">
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Тип теплоносителя на входе</label>
                    <div class="col-lg-4">
                        <select name="type_in" class="form-control pto_select2" required>{$type_in_option}</select>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Тип теплоносителя на выходе</label>
                    <div class="col-lg-4">
                        <select name="type_out" class="form-control pto_select2" required>{$type_out_option}</select>
                    </div>	
                </div>	
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Мощность</label>
                    <div class="col-lg-4">
                        <input name="power" step="any" type="number" placeholder="Введите мощность" class="gui-input" value="{$power}" required>
                    </div>	
                </div>
                <div class="form-group">
                    <label  class="col-lg-3 control-label">T1</label>
                    <div class="col-lg-4">
                        <input name="t1" step="any" type="number" placeholder="Введите T1" class="gui-input" value="{$t1}" required>
                    </div>	
                </div>
                <div class="form-group">
                    <label  class="col-lg-3 control-label">T2</label>
                    <div class="col-lg-4">
                        <input name="t2" step="any" type="number" placeholder="Введите T2" class="gui-input" value="{$t2}" required>
                    </div>	
                </div>
                <div class="form-group">
                    <label  class="col-lg-3 control-label">T3</label>
                    <div class="col-lg-4">
                        <input name="t3" step="any" type="number" placeholder="Введите T3" class="gui-input" value="{$t3}" required>
                    </div>	
                </div>
                <div class="form-group">
                    <label  class="col-lg-3 control-label">T4</label>
                    <div class="col-lg-4">
                        <input name="t4" step="any" type="number" placeholder="Введите T4" class="gui-input" value="{$t4}" required>
                    </div>	
                </div>
                <div class="form-group">
                    <label  class="col-lg-3 control-label">Цена в евро</label>
                    <div class="col-lg-4">
                        <input name="price" step="any" type="number" placeholder="Введите цену в евро" class="gui-input" value="{$price}" required>
                    </div>	
                </div>
                <div class="form-group">
                    <label class="col-lg-3 control-label">ID файл расчёта</label>
                    <div class="col-lg-4 d-flex">
                        <label for="preview" class="field smart-widget sm-right w100p">
                            <input name="aid_calc" id="aid_calc" type="text" placeholder="ID файл расчёта" class="gui-input" value="{$aid_calc}" required>
                            <label class="button fas fa-folder-open"><input type="file" class="input_load_ftp d-none" data-input="#aid_calc"></label>		
                        </label>
                        <i class="button fas fa-file-download" onclick="load_ftp(`#aid_calc`);"></i>
                    </div>	
                </div>
                <div class="form-group">
                    <label  class="col-lg-3 control-label">ДУ 1</label>
                    <div class="col-lg-4">
                        <input name="d1" step="any" type="number" placeholder="Введите ДУ 1" class="gui-input" value="{$d1}" required>
                    </div>	
                </div>
                <div class="form-group">
                    <label  class="col-lg-3 control-label">ДУ 2</label>
                    <div class="col-lg-4">
                        <input name="d2" step="any" type="number" placeholder="Введите ДУ 2" class="gui-input" value="{$d2}" required>
                    </div>	
                </div>
HTML;
    }
}