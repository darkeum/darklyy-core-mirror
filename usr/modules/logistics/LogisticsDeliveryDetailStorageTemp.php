<?php

namespace Logistics;

class LogisticsDeliveryDetailStorageTemp extends LogisticsDeliveryDetailTemp{
    public function assign_driver_btn(){}
    public function detail_info(){}

    public function list_items($request = []){
        $request["status"] = 2;
        $request["order_by"] = "ld.created_at DESC";
        return parent::list_items($request);
    }

    public function local_script(){
        return "<script src=\"/usr/modules/logistics/admin/asset/js/LogisticsDeliveryDetailStorageTemp.js?{$this->time}\"></script>";
    }

    public function detail_list_template_head_head($item){
        $driver = shortUserName($item["driver"]);
        return <<<HTML
            <div class="panel-heading">
                <div><span>Водитель: </span><span>{$driver}</span></div>
            </div>
HTML;
    }
}