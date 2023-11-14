<?php

namespace Supply;

class LogisticsReceiptsRouter{
    static function route(){
        global $url, $adminTpl;

        switch($url[4] ?? null){
            default:
                $LogisticsReceiptsTemp = new LogisticsReceiptsTemp;
                $LogisticsReceiptsTemp->view();
            break;
            case "detail_info":
                $LogisticsReceiptsTemp = new LogisticsReceiptsTemp;
                echo $LogisticsReceiptsTemp->detail_info();
            break;
            case "detail_list":
                $LogisticsReceiptsTemp = new LogisticsReceiptsTemp;
                $LogisticsReceiptsTemp->driver = $url[5] ?? null;
                echo $LogisticsReceiptsTemp->detail_list();
            break;
            case "get_data_by_id":
                $request = $_POST;
                $checks = json_decode($request["checks"], true);
                echo \Logistics\Model\LogisticsDriverDetail::with(["logistics_detail" => function($q){
                    $q->withTrashed()->with(["supply_order_detail" => function($q){
                        $q->withTrashed()->with(["supply_order" => function($q){
                            $q->withTrashed()->with("org_");
                        }]);
                    }]);
                }])->whereIn("id", $checks)->get()->toJson();
            break;
            case "receipt_confirm":
                $request = $_POST;
                $module = new \Module;
	            $data = $module->supply_to_receipts($request["id"], [
	            	"doc_number" => $request["doc_number"],
	            	"date_receipts" => $request["date_receipts"],
	            	"org" => $request["org"],
	            	"stock_id" => $request["stock_id"],
	            	"supply_detail_id" => $request["supply_detail_id"],
	            ]);
                echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;
            case "receipt_cancel":
                $request = $_POST;
                $checks = json_decode($request["checks"], true);
                foreach(\Logistics\Model\LogisticsDriverDetail::with(["logistics_detail" => function($q){
                    $q->withTrashed();
                }])->whereIn("id", $checks)->get() as $item){
                    $item->status = 3;
                    $item->save();
                    $item->logistics_detail->status = 6;
                    // $count = \Logistics\LogisticsDetail::get_count($item->logistics_detail->id);
                    // $item->logistics_detail->count = $count + $item->count;
                    $item->logistics_detail->count = $item->count;
                    $item->logistics_detail->save();
                    $item->logistics_detail->restore();
                }
            break;
        }
    }
}