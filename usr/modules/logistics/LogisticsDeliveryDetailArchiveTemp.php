<?php

namespace Logistics;

class LogisticsDeliveryDetailArchiveTemp extends LogisticsDeliveryDetailTemp{
    public function assign_driver_btn(){}
    public function detail_info(){}

    public function list_items($request = []){
        $request["status"] = 3;
        return parent::list_items($request);
    }
}