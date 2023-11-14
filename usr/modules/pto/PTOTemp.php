<?php

namespace Pto;

abstract class PTOTemp{
    protected $module;

    abstract public function main($id = false);

    abstract public function script_tpl();

    abstract public function field_list($request);

    public function fbi(){

    }

    public function template($request = []){
        global $adminTpl;
        $id = empty($request['id']) ? false : $request['id'];
        $lnn = $id ? "Добавить" : "Редактировать";
        $submit_value = $id ? "Обновить" : "Добавить";
        $adminTpl->admin_head($lnn);
        $this->fbi();
        $time = time();

        $fl = $this->field_list($request);

        echo <<<HTML
            <div id="content" class="animated fadeIn">
    			<div class="panel panel-dark panel-border top">
    				<div class="panel-heading"><span class="panel-title">{$lnn}</span>			
    			</div>			
    			<div class="panel-body admin-form">
                    <form id="pto_form" class="form-horizontal parsley-form" data-id="{$id}">
                        {$fl}
                        <div class="form-group">
                            <label  class="col-lg-3 control-label"></label>
                            <div class="col-lg-4">
                                <input type="submit" class="btn btn-success btn-xs" value="{$submit_value}">
                            </div>	
                        </div>	
                    </form>
                </div>
            </div>
HTML;
        $adminTpl->footIncludes[] = <<<HTML
            <script src="/usr/other/sub_classes/model/assets/js/model.js?{$time}"></script>
            <script src="/usr/modules/pto/admin/asset/js/pto.js?{$time}"></script>
            <script src="/usr/modules/pto/admin/asset/js/input_load_ftp.js?{$time}"></script>
            <script src="/usr/modules/pto/admin/asset/js/main.js?{$time}"></script>
HTML;
        $this->script_tpl();
        $this->script_local_tpl($id);
        $adminTpl->admin_foot();
    }

    public function script_local_tpl($id){
        global $adminTpl;
        if($id){
            $adminTpl->footIncludes[] = "<script src=\"/usr/modules/pto/admin/asset/js/edit.js?".time()."\"></script>";
        } else {
            $adminTpl->footIncludes[] = "<script src=\"/usr/modules/pto/admin/asset/js/add.js?".time()."\"></script>";
        }
    }
}