<?php

namespace Economic;

class ActListController{
    public function view(){
        global $adminTpl;
        $adminTpl->admin_head();
        echo <<<HTML
            <div class="panel panel-default mt-20 admin-form">
                <div class="panel-body">
                    <form>
                        {$this->view_head()}
                    </form>
                    <form>
                        {$this->view_body()}
                    </form>
                </div>
            </div>
HTML;
        $adminTpl->admin_foot();
    }

    public function view_head(){
        $select_oid = ActSelect::oid_select();
        $org_option = ActSelect::org_option();
        return <<<HTML
            <h3>Акт сверки расчетов с контрагентами</h3>
            <div class="row">
                <div class="col-sm-6 d-flex align-items-center">
                    <label class="d-flex">Контрагент: <input type="checkbox" name="oid_active" class="mr10 ml10"></label>
                    {$select_oid}
                </div>
                <div class="col-sm-6 d-flex align-items-center">
                    <label class="d-flex">Организация: <input type="checkbox" name="org_active" class="mr10 ml10"></label>
                    <select name="org" class="form-control">{$org_option}</select>
                </div>
            </div>
            <div class="d-flex">
                <a href="/administration/module/economic/act/editor" class="btn btn-primary btn-xs" role="button">Создать</a>
            </div>
HTML;
    }

    public function view_body(){
        // $items = static::get_items();
        return <<<HTML

HTML;
    }
}