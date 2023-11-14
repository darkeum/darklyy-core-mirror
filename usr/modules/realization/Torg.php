<?php

namespace Realization;

class Torg{

    protected $name = "torg";

    public function get_data($request){
        $data = [];

        $realization_id = $request["realization_id"];
        $data["realization_id"] = $realization_id;
        $realization = query_order_result($realization_id);
        $data["realization"] = $realization;
        $rt = \Realization\Model\RealizationTorg::where("realization_id", $realization_id)->first();
        if(!empty($rt)){
            $rt = $rt->toArray();
        }
        $rt["id"] = empty($rt["id"]) ? null : $rt["id"];
        $rt["ship"] = empty($rt["ship"]) ? null : $rt["ship"];
        $rt["consignee"] = empty($rt["consignee"]) ? $realization["oid"] : $rt["consignee"];
        $rt["shipper"] = empty($rt["shipper"]) ? $realization["org_oid"] : $rt["shipper"];
        
        $sales = \Sales\Model\Sales::findOrFail($realization["sales_id"])->toArray();
        $arr = getMonthRus();

        $sales_date = $sales["date"];
        $month = date('n', strtotime($sales_date));
        $base_doc_default = "Заказ клиента №".$sales["numb"]." от ".date("d", strtotime($sales_date))." ".$arr[$month - 1]." ".date("Y", strtotime($sales_date));
        $rt["base_doc"] = empty($rt["base_doc"]) ? $base_doc_default : $rt["base_doc"];
        $rt["base_numb"] = empty($rt["base_numb"]) ? $sales["numb"] : $rt["base_numb"];
        $base_date = empty($rt["base_date"]) ? $sales_date : $rt["base_date"];
        $rt["base_date"] = date("Y-m-d", strtotime($base_date)); 
        $rt["ship_post"] = empty($rt["ship_post"]) ? "" : $rt["ship_post"];

        $or = \Organizations\Model\Organizations::findOrFail($sales["oid"])->toArray();
        $rt["oid_address"] = empty($rt["oid_address"]) ? htmlspecialchars($or["yaddress"], ENT_QUOTES) : $rt["oid_address"];

        $oid_bank = $this->get_bank($realization["oid"]);
        $org_bank = $this->get_bank($realization["org_oid"]);

        $rt["oid_bank"] = empty($rt["oid_bank"]) ? null : $rt["oid_bank"];
        $rt["org_bank"] = empty($rt["org_bank"]) ? null : $rt["org_bank"];
        $rt["oid_bank_opt"] = $this->data_to_option(["data" => $oid_bank, "selected" => $rt["oid_bank"]]);
        $rt["org_bank_opt"] = $this->data_to_option(["data" => $org_bank, "selected" => $rt["org_bank"]]);

        $consignee_bank = $this->get_bank($realization["oid"]);
        $shipper_bank = $this->get_bank($realization["org_oid"]);

        $rt["consignee_bank_opt"] = $this->data_to_option(["data" => $consignee_bank, "selected" => $rt["consignee"]]);
        $rt["shipper_bank_opt"] = $this->data_to_option(["data" => $shipper_bank, "selected" => $rt["shipper"]]);

        $users_sign_leader = [];
        $users_sign_buch = [];

        $rd = $realization["date"];
        $us = \Boot\Models\UsersSign::where("org", $realization["org"])->where(function($query) use ($rd){
            $query->orWhere(function($query) use ($rd){
                $query->whereDate("date_start", "<=", $rd)->whereDate("date_end", ">=", $rd);
            })->orWhere(function($query){
                $query->whereNull("date_start")->whereNull("date_end");
            });
        })->get()->toArray();
        if(count($us) > 0){
            foreach($us as $r){
                if($r["type"] == 1){
                    switch($r["type_post"]){
                        case "1":
                            $users_sign_leader[$r["id"]] = shortUserName($r["uid"]);
                        break;
                        case "2":
                            $users_sign_buch[$r["id"]] = shortUserName($r["uid"]);
                        break;
                    }
                } else {
                    switch($r["type_post"]){
                        case "1":
                            switch($r["sign"]){
                                case "1":
                                    $users_sign_leader[$r["id"]] = shortUserName($r["uid"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от '.date("d.m.Y", strtotime($r ["base_date"]));
                                break;
                                case "2":
                                    $users_sign_leader[$r["id"]] = shortUserName($r["uid"]).', '.getTypePost($r["type_post"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от    '.date("d.m.Y", strtotime($r["base_date"]));
                                break;
                            }
                        break;
                        case "2":
                            switch($r["sign"]){
                                case "1":
                                    $users_sign_buch[$r["id"]] = shortUserName($r["uid"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от '.date("d.m.Y", strtotime($r   ["base_date"]));
                                break;
                                case "2":
                                    $users_sign_buch[$r["id"]] = shortUserName($r["uid"]).', '.getTypePost($r["type_post"]).', '.getTypeBase($r["base"]).' №'.$r["base_numb"].' от '.   date("d.m.Y", strtotime($r["base_date"]));
                                break;
                            }
                        break;
                    }    
                }
            }
        }

        $rt["leader"] = empty($rt["leader"]) ? null : $rt["leader"];
        $rt["buch"] = empty($rt["buch"]) ? null : $rt["buch"];
        $rt["users_sign_leader_opt"] = $this->data_to_option(["data" => $users_sign_leader, "selected" => $rt["leader"]]);
        $rt["users_sign_buch_opt"] = $this->data_to_option(["data" => $users_sign_buch, "selected" => $rt["buch"]]);

        // $realization_torg_procuratory = new \Realization\RealizationTorgProcuratory;
        // $rt["procuratory_opt"] = $realization_torg_procuratory->get_procuratory_option(["oid" => $realization["oid"]]);

        $data["realization_torg"] = $rt;

        return $data;
    }

    public function get_bank($oid){
        $result = [];

        $ob = \Organizations\Model\OrganizationsBank::where("oid", $oid)->get()->toArray();
        foreach($ob as $r){
            $result[$r["id"]] = $r["rschet"].', '.(empty($r["name_short"]) ? $r["name"] : $r["name_short"]);
        }

        return $result;
    }

    public function data_to_option($request){
        $option = [];
        $sel = empty($request["selected"]) ? null : $request["selected"];
        if(count($request["data"]) > 0){
            foreach($request["data"] as $key => $val){
                $selected = $key == $sel ? "selected" : "";
                $option[] = <<<HTML
                <option value="{$key}" {$selected}>{$val}</option>
HTML;
            }
        }
        return implode("", $option);
    }

    public function modal($request){
        //["realization_id" => "",]
        global $adminTpl;

        $adminTpl->footIncludes[] = '<script src="/usr/modules/realization/admin/asset/js/torg.js?'.time().'"></script>';

        $data = $this->get_data($request);

        $name = $this->name;
        $realization_id = $data["realization_id"];
        $realization = $data["realization"];
        $oid = $realization["oid"];
        $rt = $data["realization_torg"];

        $id = $rt["id"];

        $ship = $rt["ship"];
        $consignee = $rt["consignee"];
        $shipper = $rt["shipper"];

        oSelect("jsAdminTpl", "ship", $ship, "getUsersToSelect", false, false, true);
        $sign_ship_select = oSelect("select", "ship", $ship, "getUsersToSelect", false, false, true);

        oSelect("jsAdminTpl", "consignee", $consignee, "getOrganizations", false, false, true);
        $consignee_select = oSelect("select", "consignee", $consignee, "getOrganizations", false, false, true);
        oSelect("jsAdminTpl", "shipper", $shipper, "getOrganizations", false, false, true);
        $shipper_select = oSelect("select", "shipper", $shipper, "getOrganizations", false, false, true);

        $base_doc = $rt["base_doc"];
        $base_numb = $rt["base_numb"];
        $base_date = $rt["base_date"];
        $ship_post = $rt["ship_post"];
        $oid_address = $rt["oid_address"];

        $oid_bank_opt = $rt["oid_bank_opt"];
        $org_bank_opt = $rt["org_bank_opt"];
        $consignee_bank_opt = $rt["consignee_bank_opt"];
        $shipper_bank_opt = $rt["shipper_bank_opt"];

        $users_sign_leader_opt = $rt["users_sign_leader_opt"];
        $users_sign_buch_opt = $rt["users_sign_buch_opt"];

        return <<<HTML
            <div class="modal fade bd-example-modal-lg" id="{$name}Modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Реквизиты печати ТОРГ-12</h5>
	        	  </div>
	        	  <form method="POST" id="{$name}Form" onsubmit="return false;">

                    <input type="hidden" name="id" value="{$id}">
	        		<input type="hidden" value="{$realization_id}" name="realization_id">
	        		<input type="hidden" value="{$oid}" name="oid">

                  <div class="modal-body" id="{$name}ModalBody">
                    <div class="container-fluid admin-form">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="mh">Реквизиты печати ТОРГ-12</div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-sm-12">
                                <div class="col-sm-12">
                                    <div class="greenH">Основание</div>
                                </div>
                                <div class="col-sm-4">
                                    <span>Документ:</span>
                                    <input type="text" class="form-control" name="base_doc" placeholder="Введите основание документа" value="{$base_doc}">
                                </div>
                                <div class="col-sm-4">
                                    <span>Номер:</span>
                                    <input type="text" class="form-control" name="base_numb" placeholder="Введите номер документа" value="{$base_numb}">
                                </div>
                                <div class="col-sm-4">
                                    <span>Дата:</span>
                                    <input type="date" class="form-control" name="base_date" id="base_date" value="{$base_date}">
                                </div>
                        </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 ">
                                <div class="col-sm-12">
	        						<div class="greenH">Покупатель</div>
                                </div>
                                <div class="col-sm-12">
	        						<span>Счет покупателя:</span>
	        						<select class="form-control" name="oid_bank">
                                        {$oid_bank_opt}
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <span>Грузополучатель:</span>
	        						{$consignee_select}
                                </div>
                                <div class="col-sm-12">
	        						<span>Счет грузополучателя:</span>
	        						<select class="form-control" name="consignee_bank" id="consignee_bank">
                                        {$consignee_bank_opt}
                                    </select>
                                </div>
                                <div class="col-sm-12">
	        						<span>Адрес доставки:</span>
                                    <input type="text" class="form-control" name="oid_address" value="{$oid_address}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-12">
	        						<div class="greenH">Продавец</div>
                                </div>
                                <div class="col-sm-12">
	        						<span>Счет организации:</span>
	        						<select class="form-control" name="org_bank">
                                        {$org_bank_opt}
                                    </select>
                                </div>
                                <div class="col-sm-12">
	        						<span>Грузоотправитель:</span>
	        						{$shipper_select}
                                </div>
                                <div class="col-sm-12">
	        						<span>Счет грузоотправителя:</span>
	        						<select class="form-control" name="shipper_bank" id="shipper_bank">
                                        {$shipper_bank_opt}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 ">
                                <div class="col-sm-12">
                                    <div class="greenH">Доверенность</div>
                                </div>
                                <div class="col-sm-12">
                                    <select name="procuratory_id" id="procuratory_id" class="form-control" onchange="torg.update_input_procuratory($(this).val());"></select>
                                </div>
                                <div class="col-sm-12">
                                    <span>Номер:</span>
                                    <input type="text" class="form-control" id="number" name="number">
                                </div>
                                <div class="col-sm-12 mt10">
                                    <span class="col-sm-1 text-center d-flex justify-content-center align-items-center" style="height: 45px;">от</span>
                                    <div class="col-sm-5 p0">
                                        <input type="date" class="form-control" id="date_from" name="date_from">
                                    </div>
                                    <span class="col-sm-1 text-center d-flex justify-content-center align-items-center" style="height: 45px;">до</span>
                                    <div class="col-sm-5 p0">
                                        <input type="date" class="form-control" id="date_to" name="date_to">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <span>Кем выдана:</span>
                                    <input type="text" class="form-control" id="issued_by" name="issued_by">
                                </div>
                                <div class="col-sm-12">
                                    <span>Кому выдана:</span>
                                    <input type="text" class="form-control" id="issued_to" name="issued_to">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="col-sm-12">
                                    <div class="greenH">Подписи</div>
                                </div>
                                <div class="col-sm-12">
	        						<span>Руководитель:</span>
	        						<select class="form-control" name="leader">
                                        {$users_sign_leader_opt}
                                    </select>
                                </div>
                                <div class="col-sm-12">
	        						<span>Главный бухгалтер:</span>
	        						<select class="form-control" name="buch">
                                        {$users_sign_buch_opt}
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <span>Груз отпустил:</span>
                                        {$sign_ship_select}
	        					</div>
                                <div class="col-sm-12">
                                    <span>Отпустил (должность):</span>
                                    <input type="text" class="form-control" name="ship_post" id="ship_post" placeholder="Введите должность" value="{$ship_post}">
                                </div>
                            </div>
                        </div>
                    </div>
	        	  </div>
	        	  </form>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="torg.save();">Сохранить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>
HTML;
    }
}