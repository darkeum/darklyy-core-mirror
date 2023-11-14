<?php

namespace Economic;

use Economic\Model\ActReconciliation;

class ActEditorController{

    use ActController;

    public function __construct(){
        global $core;
        $this->uid = $core->auth->user_info['id'];
    }

    public function view(){
        global $adminTpl;
        $adminTpl->admin_head();
        $time = time();
        echo <<<HTML
            <style>
                .head_line{
                    display: flex;
                    align-items: center;
                    margin-bottom: 20px;
                }
                .head_line>*:first-child{
                    width: 100px;
                    margin-right: 10px;
                }
                .table_body>tr>td{
                    padding: 10px!important;
                }
            </style>
            <div class="panel panel-default mt-20 admin-form">
                <div class="panel-body">
                    <form onsubmit="return false" id="mainForm">
                        <div>{$this->view_head()}</div>
                        <div>{$this->view_body()}</div>
                        {$this->save_input()}
                    </form>
                </div>
            </div>
            <script src="/usr/other/sub/price/js/price.js?{$time}"></script>
            <script src="/usr/modules/economic/admin/asset/js/ActEditor.js?{$time}"></script>
HTML;
        $adminTpl->admin_foot();
    }

    public function view_head(){
        // $select_oid = ActSelect::oid_select();
        $select_oid = ActSelect::oid_select(["id" => $this->oid ?? null, "name" => "oid"]);
        $org_option = ActSelect::org_option(["selected" => $this->org ?? null]);

        $ActReconciliation = new ActReconciliation;
        $ActReconciliation->id = $this->id;
        $ActReconciliation->numb = $this->numb;
        $ActReconciliation->date = $this->date;
        $title = $ActReconciliation->getTitleAttribute();
        $date = date("Y-m-d\TH:i", $this->id ? strtotime($this->date) : null);
        $active = $this->active ? "checked" : "";
        $agreed = $this->agreed ? "checked" : "";
        return <<<HTML
            <h3 class="mb10" id="title">{$title}</h3>
            <div class="mb10 d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-xs btn-warning" id="spend_close_btn">Провести и закрыть</button>
                    <button type="button" class="btn btn-xs btn-secondary" id="write_btn">Записать</button>
                    <button type="button" class="btn btn-xs btn-secondary" id="spend_btn">Провести</button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-xs btn-secondary dropdown-toggle" data-toggle="dropdown">Печать</button>
                        <ul class="dropdown-menu">
                            <li class="cp" id="print_btn"><a>Акт сверки</a></li>
                        </ul>
                    </div>
                </div>
                <div>
                    <div class="btn-group">
                        <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Еще</button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li class="cp" id="spread_out_btn"><a>Распровести</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="head_line">
                <div>Номер:</div>
                <div class="mr20"><input type="text" name="numb" class="form-control" value="{$this->numb}" id="numb"></div>
                <div>от:</div>
                <div class="mr20"><input type="datetime-local" class="form-control" value="{$date}" name="date"></div>
                <div class="mr40">
                    <input type="hidden" value="0" name="active">
                    <input type="checkbox" id="active" value="1" name="active" onclick="return false" {$active} title="Проведенность">
                </div>
                <div>Организация:</div>
                <div><select name="org" id="org" class="form-control">{$org_option}</select></div>
            </div>
            <div class="head_line">
                <div>Контрагент:</div>
                <div class="mr40">{$select_oid}</div>
                <!-- <div>Валюта сверки:</div>
                <div><select name="" class="form-control"><option value="643">руб.</option></select></div> -->
                <input type="hidden" name="agreed" value="0">
                <div><label><input type="checkbox" class="mr10" name="agreed" id="agreed" value="1" {$agreed}><span>Сверка согласована</span></label></div>
            </div>
            <!-- <div class="head_line">
                <div>Договор:</div>
                <div class="mr20"><select name="" class="form-control"></select></div>
            </div> -->
            <div class="head_line">
                <div>Период:</div>
                <div><input type="date" class="form-control" name="date_start" id="date_start" value="{$this->date_start}"></div>
                <div class="ml20 mr20">&mdash;</div>
                <div><input type="date" class="form-control" name="date_end" id="date_end" value="{$this->date_end}"></div>
            </div>
HTML;
    }

    public function view_body(){
        return <<<HTML
            <div>{$this->view_body_tab()}</div>
            <div>{$this->view_body_tab_pane()}</div>
            <div>{$this->view_body_footer()}</div>
HTML;
    }

    public function view_body_tab(){
        return <<<HTML
            <ul class="nav nav-tabs">
              <li class="active">
                <a data-toggle="tab" href="#tab_1">По данным организации</a>
              </li>
              <li>
                <a data-toggle="tab" href="#tab_2">По данным контрагента</a>
              </li>
              <!-- <li>
                <a data-toggle="tab" href="#tab_3">Счета учета расчетов</a>
              </li> -->
              <li>
                <a data-toggle="tab" href="#tab_4">Дополнительно</a>
              </li>
            </ul>
HTML;
    }

    public function view_body_tab_pane(){
        return <<<HTML
            <div class="tab-content">
              <div class="tab-pane p10 active" id="tab_1">{$this->tab_1()}</div>
              <div class="tab-pane p10" id="tab_2">{$this->tab_2()}</div>
              <!-- <div class="tab-pane p10" id="tab_3">{$this->tab_3()}</div> -->
              <div class="tab-pane p10" id="tab_4">{$this->tab_4()}</div>
            </div>
HTML;
    }

    public function tab_btn(){
        return <<<HTML
            <div class="d-flex justify-content-between mb10">
                <div>
                    <button type="button" class="btn btn-secondary btn-xs">Добавить</button>
                    <div class="btn-group">
                        <button class="btn btn-secondary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">Заполнить</button>
                        <ul class="dropdown-menu">
                            <li class="fill_btn cp"><a>По нашей организации и контрагенту</a></li>
                            <li class="fill_one_btn cp"><a>Только по нашей организации</a></li>
                        </ul>
                    </div>
                    <button type="button" class="btn btn-secondary btn-xs"><i class="fas fa-arrow-alt-up"></i></button>
                    <button type="button" class="btn btn-secondary btn-xs"><i class="fas fa-arrow-alt-down"></i></button>
                </div>
                <div>
                    <!-- <button type="button" class="btn btn-secondary btn-xs">Еще</button> -->
                </div>
            </div>
HTML;
    }

    public function tab_footer($request){
        $initial_balance = $request["initial_balance"] ?? 0;
        $initial_balance_view = static::price_format($initial_balance);
        $final_balance = $request["final_balance"] ?? 0;
        $final_balance_view = static::price_format($final_balance);
        $balance_name = $request["balance_name"];
        return <<<HTML
            <div class="d-flex justify-content-between bg-light darker p10">
                <div class="d-flex">
                    <div class="mr40">Остаток на начало:</div>
                    <div id="{$balance_name}_initial_balance">{$initial_balance_view}</div>
                </div>
                <div class="d-flex">
                    <div class="mr40">Остаток на конец:</div>
                    <div class="mr20" id="{$balance_name}_final_balance">{$final_balance_view}</div>
                </div>
            </div>
HTML;
    }

    public function tab_1(){
        return <<<HTML
            {$this->tab_btn()}
            <div>
                <table class="table table-bordered">
                    <thead>{$this->tab_table_head()}</thead>
                    <tbody class="table_body" id="tab_1_tbody"></tbody>
                </table>
            </div>
            {$this->tab_footer(["balance_name" => "org", "initial_balance" => $this->initial_balance ?? 0, "final_balance" => $this->final_balance ?? 0])}
HTML;
    }

    public function tab_2(){
        switch($this->fill_type){
            case 0:
                $initial_balance = -$this->initial_balance ?? 0;
                $final_balance = -$this->final_balance ?? 0;
            break;
            case 1:
                $initial_balance = 0;
                $final_balance = 0;
            break;
        }
        return <<<HTML
            {$this->tab_btn()}
            <div>
                <table class="table table-bordered">
                <thead>{$this->tab_table_head()}</thead>
                    <tbody class="table_body" id="tab_2_tbody"></tbody>
                </table>
            </div>
            {$this->tab_footer(["balance_name" => "oid", "initial_balance" => $initial_balance, "final_balance" => $final_balance])}
HTML;
    }

    public function tab_3(){
        
    }

    public function tab_4(){
        $break_contracts_checked = $this->break_contracts ? "checked" : "";
        $display_full_document_titles_checked = $this->display_full_document_titles ? "checked" : "";
        $display_invoices_checked = $this->display_invoices ? "checked" : "";
        return <<<HTML
            <div>
                <h4>Представители сторон</h4>
                <div class="d-flex align-items-center">
                    <div class="col-sm-2">Представитель организации:</div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="org_director_full" name="org_director_full" value="{$this->org_director_full}" readonly>
                    </div>
                </div>
                <div class="d-flex align-items-center mt20">
                    <div class="col-sm-2">Представитель контрагента:</div>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" id="oid_director_full" name="oid_director_full" value="{$this->oid_director_full}" readonly>
                    </div>
                </div>
            </div>
            <div>
                <h4>Печатная форма</h4>
                <div>
                    <div>
                        <input type="hidden" name="break_contracts" value="0">
                        <label><input type="checkbox" value="1" name="break_contracts" id="break_contracts" onchange="break_contracts_change();" {$break_contracts_checked}>Разбить по договорам</label>
                    </div>
                    <div>
                        <input type="hidden" name="display_full_document_titles" value="0">
                        <label><input type="checkbox" value="1" name="display_full_document_titles" {$display_full_document_titles_checked}>Выводить полные названия документов</label>
                    </div>
                    <div>
                        <input type="hidden" name="display_invoices" value="0">
                        <label><input type="checkbox" value="1" name="display_invoices" {$display_invoices_checked}>Выводить счета-фактуры</label>
                    </div>
                </div>
            </div>
HTML;
    }

    public function tab_table_head(){
        $contracts = $this->break_contracts ? "" : "display: none;";
        return <<<HTML
            <th>№</th>
            <th style="{$contracts}" class="thead_contracts">Договор</th>
            <th>Дата</th>
            <th>Документ</th>
            <th>Представление</th>
            <th>Дебет</th>
            <th>Кредит</th>
HTML;
    }

    public function view_body_footer(){
        $selectData = [
            "mod" => "jsAdminTpl",
            "name" => "uid",
            "id" => $this->uid,
            "ajaxUrl" => "getUsersToSelect",
            "return" => true,
        ];
        oSelectCustom($selectData);
        $selectData["mod"] = "select";
        $select = oSelectCustom($selectData);
        return <<<HTML
            <div class="d-flex align-items-center">
                <div>Комментарий:</div>
                <div class="mr20"><input type="text" class="form-control" name="comment" value="{$this->comment}"></div>
                <div>Ответственный:</div>
                <div>{$select}</div>
            </div>
HTML;
    }

    public function tab_body_init($request){
        $this->date             = $request["date"] ?? null;
        $this->date_start       = $request["date_start"] ?? null;
        $this->date_end         = $request["date_end"] ?? null;
        $this->org              = $request["org"] ?? null;
        $this->oid              = $request["oid"] ?? null;
        $this->break_contracts  = $request["break_contracts"] ?? null;

        return $this->tab_body();
    }

    public function tab_body(){
        $result = [
            "records" => null,
            "org_director_full" => null,
            "oid_director_full" => null,
            "initial_balance" => null,
            "initial_balance_view" => null,
            "final_balance" => null,
            "final_balance_view" => null,
            "error" => null,
        ];
        try{
            $this->init_data();
            $result["records"] = $this->get_records();
            $result["org_data"] = $this->org_data;
            $result["org_director"] = $this->org_director;
            $result["oid_data"] = $this->oid_data;
            $result["org_director_full"] = $this->org_director_full;
            $result["oid_director_full"] = $this->oid_director_full;
            $result["initial_balance"] = $this->get_initial_balance();
            $result["initial_balance_contract"] = $this->initial_balance_contract;
            $result["initial_balance_view"] = static::price_format($this->initial_balance);
            $this->get_final_balance();
            $result["final_balance"] = $this->final_balance;
            $result["final_balance_view"] = static::price_format($this->final_balance);
        } catch(\Exception $e){
            $result["error"] = $e->getMessage();
        } finally{
            return $result;
        }
    }

    public function save_input(){
        $records = json_encode($this->records, JSON_UNESCAPED_UNICODE);
        $org_data = json_encode($this->org_data, JSON_UNESCAPED_UNICODE);
        $org_director = json_encode($this->org_director, JSON_UNESCAPED_UNICODE);
        $oid_data = json_encode($this->oid_data, JSON_UNESCAPED_UNICODE);
        $initial_balance_contract = json_encode($this->initial_balance_contract, JSON_UNESCAPED_UNICODE);
        return <<<HTML
            <input type="hidden" name="id" id="act_id" value="{$this->id}">
            <input type="hidden" name="fill_type" id="fill_type" value="{$this->fill_type}">
            <input type="hidden" name="initial_balance" id="data_initial_balance" value="{$this->initial_balance}">
            <input type="hidden" name="final_balance" id="data_final_balance" value="{$this->final_balance}">
            <input type="hidden" name="records" id="data_records" value='{$records}'>
            <input type="hidden" name="initial_balance_contract" id="data_initial_balance_contract" value='{$initial_balance_contract}'>
            <input type="hidden" name="org_data" id="data_org_data" value='{$org_data}'>
            <input type="hidden" name="org_director" id="data_org_director" value='{$org_director}'>
            <input type="hidden" name="oid_data" id="data_oid_data" value='{$oid_data}'>
HTML;
    }
}