<?php

namespace Control\Support;

class Control{
    static function send_notif_for_lose_plan_pay_date_sales(){
        $datediff = "DATEDIFF('".date("Y-m-d")."', plan_pay_date)";
        $items = \Control\Model\Control::whereHas("control_detail", function($query){
            $query->whereNotNull("sdid");
        })
        ->whereHas('supply_order', function ($query) {
            $query->where('close', 0);
        })
        ->with(["control_detail" => function($query){
            $query->with(["sales_detail" => function($query){
                $query->withTrashed()->with("sales");
            }]);
        }])
        ->where("pay", 0)
        ->where("status", 3)
        ->whereRaw("{$datediff} >= 3")
        ->whereRaw("{$datediff} MOD 3 = 0")
        ->get();

        $sales_items = [];
        foreach($items as $control){
            foreach($control->control_detail as $control_detail){
                $sales_id = $control_detail->sales_detail->sales->id;
                if(!array_key_exists($sales_id, $sales_items)){
                    $sales_items[ $sales_id ]["items"] = [];
                    $sales_items[ $sales_id ]["sales"] = $control_detail->sales_detail->sales;
                    $sales_items[ $sales_id ]["sales_detail_id"] = [];
                }
                $sales_items[ $sales_id ]["items"][] = $control_detail->sales_detail;
                $sales_items[ $sales_id ]["sales_detail_id"][] = $control_detail->sales_detail->id;
            }
        }
        // dd($sales_items);
        $supply_o_tov_name = "supply_o_tov[]=";
        foreach($sales_items as $item){
            $sales = $item["sales"];
            $sales_detail_id = implode("&$supply_o_tov_name", $item["sales_detail_id"]);

            $attr = [];
            $attr['uid'] = $sales->uid;
            $text = "Дата оплаты закупки заказа №{$sales->numb} просрочена";
            $attr['title'] = $text;
            $attr['text'] = $text;
            $attr['icon'] = config('app.url').'/files/push/new_storage.png';
            $url = "https://atri-energo.ru/administration/module/sales/order/{$sales->id}?{$supply_o_tov_name}{$sales_detail_id}";
            $attr['url'] = $url;
            $attr['pNotif'] = true;
            $attr['pPush'] = true;	
            $attr['module'] = "sales";
            $attr['pid'] = $sales->id;		
            sendAllNotif($attr);
        }
    }

    static function end($id, $status, $comment){
        $control = \Control\Models\Control::findOrFail($id);
        $supply_order = \Supply\Model\SupplyOrder::findOrFail($control->supply_o);
        switch($status){
            case 2:
                \Sales\Support\SalesSupport::send_comment_supply_order($control->supply_o, "control", $id, $comment);

                $control->update(["status" => $status]);

                $attr = [];
                $attr['uid'] = $supply_order->uid;
                $attr['title'] = 'Заявка на закупку №'.$supply_order->numb.' отправлена на доработку';
                $attr['text'] = 'Заявка на закупку №'.$supply_order->numb.' отправлена на доработку';
                $attr['icon'] = config('app.url').'/files/push/task.png';
                $attr['url'] = '/administration/module/supply/order_detail/'.$supply_order->id;
                $attr['module'] = "supply_order";
                $attr['pid'] = $id;	
                sendAllNotif($attr);
            break;
            case 3:
                $control->load(["control_detail.sales_detail.sales"]);
                $set = [];
                $set["status"] = $status;
                if($control->plan_pay_date < date("Y-m-d")){
                    $set["plan_pay_date"] = date("Y-m-d");
                }
                $control->update($set);
                
                $attr = [];
                $attr['uid'] = 28;
                $attr['title'] = 'Новая заявка на оплату';
                $attr['text'] = 'Заявка на оплату #'.module_perfix('fcontrol', $id);
                $attr['icon'] = config('app.url').'/files/push/task.png';
                $attr['url'] = '/administration/module/fcontrol/pay_detail/'.$id;
                $attr['module'] = "fcontrol_pay";
                $attr['pid'] = $id;	
                sendAllNotif($attr);

                $attr = [];
                $attr['uid'] = $supply_order->uid;
                $attr['title'] = 'Заказ согласован';
                $attr['text'] = 'Заказ №'.$supply_order->numb.' согласован';
                $attr['icon'] = config('app.url').'/files/push/task.png';
                $attr['url'] = '/administration/module/supply/order_detail/'.$control->supply_o;
                $attr['module'] = "supply_order";
                $attr['pid'] = $control->supply_o;
                sendAllNotif($attr);

                $sales = [];
                $s_status = 6;
                foreach($control->control_detail as $item){
                    if(!empty($item->sales_detail)){
                        $item->sales_detail->update(["status" => $s_status]);
        
                        \Sales\Model\SalesDetailStatusLog::create([
                            "sales_detail_id" => $item->sales_detail->id, 
                            "status" => $s_status, 
                            "url" => "/administration/module/supply/order_detail/".$control->supply_o,
                        ]);
                        $sales[ $item->sales_detail->sales->id ] = $item->sales_detail->sales;
                    }
                }
                foreach($sales as $item){
                    $attr = [];
                    $attr['uid'] = $item->uid;
                    $attr['title'] = 'Заказ согласован';
                    $attr['text'] = 'Заказ №'.$item->numb.' согласован';
                    $attr['icon'] = config('app.url').'/files/push/task.png';
                    $attr['url'] = '/administration/module/sales/order/'.$item->id;
                    $attr['module'] = "sales";
                    $attr['pid'] = $item->id;
                    sendAllNotif($attr);
                }
            break;
        }
        $supply_order->update(["control" => $status]);

        return [
            "control" => $control,
            "supply_order" => $supply_order,
        ];
    }
}