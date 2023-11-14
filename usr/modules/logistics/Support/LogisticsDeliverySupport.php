<?php

namespace Logistics\Support;

use \Boot\Support\Facades\Auth;

class LogisticsDeliverySupport{
    static function delivered($id){
        global $db;
        $data = \Logistics\Model\LogisticsDelivery::findOrFail($id);
        $module = $data->module;
        $pid = $data->pid;
        switch($module){
            default:
                $url = "";
            break;
            case "realization":
                $url = "https://atri-energo.ru/administration/module/realization/order/{$pid}";
            break;
            case "stock_transfer":
                $url = "https://atri-energo.ru/administration/module/storage/transfer/detail/{$pid}";
            break;
        }
        
        $uid = getGroup(["group" => "logistic"]);
        $uid[] = $data->uid;
        $attr = [];
    	$attr['uid'] = $uid;
    	$attr['title'] = 'Логистика';
    	$attr['text'] = "Товары доставлены";
    	$attr['icon'] = config('app.url').'/files/push/new_storage.png';
    	$attr['url'] = $url;
    	$attr['pNotif'] = true;
		$attr['pPush'] = true;
		$attr['module'] = "logistics";
        $attr['pid'] = $id;
        sendAllNotif($attr);
        
        $data->update(["status" => 3]);

        switch($module){
            case "realization":
                $query = $db->query("SELECT sd.* 
                FROM `".DB_PREFIX."_realization_detail` as rd 
                LEFT JOIN `".DB_PREFIX."_sales_detail` as sd ON(sd.id = rd.sdid)
                WHERE rd.rid = '{$data->realization_id}'");
        
                if($db->numRows($query) > 0){
                    $s_status = 21;
                    while($r = $db->getRow($query)){
                        \Sales\Model\SalesDetail::find($r["id"])->update(["status" => $s_status]);
                        \Sales\Model\SalesDetailStatusLog::create([
                            "sales_detail_id" => $r["id"], 
                            "status" => $s_status, 
                            "url" => $url,
                        ]);
                    }
                }
            break;
        }
    }

    static public function notif(){
        $attr = [];
        $attr['uid'] = getGroup(["group" => "logistic"]);
        $attr['title'] = 'Логистика';
        $attr['text'] = "Новая заявка на доставку контрагенту";
        $attr['icon'] = config('app.url').'/files/push/new_storage.png';
        $attr['url'] = config('app.url').'/administration/module/logistics/delivery';
        $attr['pNotif'] = true;
        $attr['pPush'] = true;
        $attr['module'] = "logistics";
        $attr['pid'] = 0;
        sendAllNotif($attr);
    }

    static function add_modal($request){
        $module = $request["module"];
        $pid = $request["pid"];

	    $delivery_type = \Logistics\Model\LogisticsDelivery::getDeliveryType();
	    $delivery_type_opt = [];
	    foreach($delivery_type as $key => $item){
	    	$delivery_type_opt[] = "<option value=\"{$key}\">{$item}</option>";
	    }
	    $delivery_type_opt = implode("", $delivery_type_opt);

        $modal = <<<HTML
		<div class="modal fade admin-form" id="deliveryModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
				<form id="deliveryModalForm" method="POST">
		      		<div class="modal-header">
		      		  <h5 class="modal-title" id="exampleModalLabel">Доставка</h5>
		      		</div>
		      		<div class="modal-body">
						<input type="hidden" name="pid" value="{$pid}">
						<input type="hidden" name="module" value="{$module}">
						<div class="form-group">
							<label for="">Адрес</label>
                            <input name="address" id="deliveryModalAddress" type="text" class="form-control" placeholder="Введите адрес" required>
                            <input type="hidden" name="coordinates" id="deliveryModalCoordinates">
					  	</div>
						<div class="form-group">
							<div id="map" style="width: 100%; height: 300px;"></div>
					  	</div>
						<div class="form-group">
							<label for="">Контактное лицо</label>
							<input name="contact_person" type="text" class="form-control" placeholder="Введите контактное лицо" required>
					  	</div>
						<div class="form-group">
							<label for="">Телефон</label>
							<input name="phone" type="tel" class="form-control" placeholder="Введите телефон" required>
					  	</div>
						<div class="form-group">
							<label for="">Дата доставки</label>
							<input name="delivery_date" type="date" class="form-control" placeholder="Введите дату доставки" required>
						</div>
						<div class="form-group d-flex align-items-center">
							<label for="" class="mr20">С</label>
							<input name="delivery_from" type="time" class="form-control" value="08:00" required>
							<label for="" class="ml20 mr20">до</label>
							<input name="delivery_to" type="time" class="form-control" value="20:00" required>
						</div>
						<div class="form-group">
							<label for="">Доставка</label>
							<select class="form-control" name="delivery_type" id="delivery_type" required>{$delivery_type_opt}</select>
						</div>
						<div class="form-group" id="delivery_price_div">
							<label for="">Сумма доставки</label>
							<input name="delivery_price" type="number" class="form-control" id="delivery_price" placeholder="Введите сумму доставки" required>
						</div>
						<div class="form-group">
							<label for="">Комментарий</label>
							<textarea name="comment" class="form-control" placeholder="Введите комментарий (необязательно)"></textarea>
						</div>
		      		</div>
					<div class="modal-footer">
					  	<input type="submit" class="btn btn-primary" value="Оформить доставку">
		      		  	<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
			  		</div>
			  </form>
		    </div>
		  </div>
		</div>
HTML;

        $time = time();
        $script = <<<HTML
        <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=a1bef2f6-2d8a-4fbe-9d47-e77da8849296" type="text/javascript"></script>
        <script src="/usr/modules/logistics/admin/asset/js/logistics_delivery_modal.js?{$time}"></script>
HTML;
            $btn = <<<HTML
                <div class="ph10">
			    	<a href="javascript:void(0);" onclick="\$logisticsDelivery.open();" class="holder-style p15 text-center holder-active">
			    		<span class="fas fa-truck holder-icon"></span><br>Доставка<br><br>
			    	</a>
			    </div>
HTML;
            $btn_cancel = <<<HTML
                <div class="ph10">
				    <a href="javascript:void(0);" onclick="\$logisticsDelivery.cancel_delivery();" class="holder-style p15 text-center holder-active">
				    	<span class="fas fa-truck holder-icon"></span><br>Отмена <br> доставки
				    </a>
			    </div>
HTML;
            $req = $request;
            $req["where"] = "status = '1'";
            $is_create = static::is_create($req);
            if(!$is_create){
                $btn_cancel = "";
            } 
            $btn_container = $btn.$btn_cancel;
            return ["modal" => $modal, "script" => $script, "btn_container" => $btn_container, "btn" => $btn, "btn_cancel" => $btn_cancel];
    }

    static function is_create($request){
        global $db;
        $pid = $request["pid"];
        $module = $request["module"];
        $r_where = empty($request["where"]) ? "" : $request["where"];

        $where = [];
        $where[] = "module = '{$module}'";
        $where[] = "pid = '{$pid}'";
        $where[] = "deleted_at IS NULL";
        if(!empty($r_where)) $where[] = $r_where;

        $where = implode(" AND ", $where);

        $query = $db->query("SELECT * FROM `".DB_PREFIX."_logistics_delivery` 
        WHERE {$where} LIMIT 1");
        return $db->numRows($query) > 0;
    }

    static function cancel_delivery($request){
        $data = \Logistics\Model\LogisticsDelivery::where("module", $request["module"])->where("pid", $request["pid"])->get();
        if($data->count() > 0){
            foreach($data as $item){
                $item->delete();
            } 
            $name = shortUserName(Auth::id());
            $uid = getGroup(["group" => "logistic"]);
            // $uid[] = 33;
            $attr = [];
            $attr['uid'] = $uid;
            $attr['title'] = 'Логистика';
            $attr['text'] = "Отмена доставки. Отменил {$name}";
            $attr['icon'] = config('app.url').'/files/push/new_storage.png';
            $attr['url'] = config('app.url').'/administration/module/logistics/delivery';
            $attr['pNotif'] = true;
            $attr['pPush'] = true;
            $attr['module'] = "logistics";
            $attr['pid'] = 0;
            sendAllNotif($attr);
        }
    }

    static function x_update($request){
        if(isset($request["pk"], $request["name"])){
            $logistics_delivery = \Logistics\Model\LogisticsDelivery::findOrFail($request["pk"]);
            $value = $request["value"] ?? null;
            $logistics_delivery->update([$request["name"] => $value]);
            return $logistics_delivery;
        }
    }
}