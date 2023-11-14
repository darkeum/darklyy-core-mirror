<?php

namespace Logistics;

use \Boot\Support\Str;
use Boot\System\Office\OfficeSupport;

class LogisticsDeliveryDetailTemp extends LogisticsTemp{
    public function __construct(){
        parent::__construct();
    }

    public function detail_list_query_items(){
        return $this->list_items(["status" => 1]);
    }

    public function view(){
        global $adminTpl;
        $adminTpl->admin_head("Логистика | Доставка");
        $this->view_template();
        $adminTpl->admin_foot();
    }

    public function view_template(){
        $panel_body_btn = $this->panel_body_btn(["btn" => ""]);
        $assign_driver_btn = $this->assign_driver_btn();
	    $panel_body = <<<HTML
		    {$panel_body_btn}
		    <div class="mt20">
		    	{$assign_driver_btn}
		    </div>
HTML;
    $info = $this->detail_info();
	$list = $this->detail_list();

	$template = $this->detail_template(["panel_body" => $panel_body, "panel_body_after" => "", "info" => $info, "list" => $list]);

    $driver_modal = $this->driver_modal();

    $style = $this->style();

    $ttn_modal = $this->ttn_modal();

    echo <<<HTML
        {$style}
	    <br>
	    {$template}

        {$driver_modal["template"]}
        {$ttn_modal}
HTML;
        $this->script();
	    echo $driver_modal["script"];
	    echo $this->local_script();
    }

    static public function ttn_modal(){
        $select_id = "driver_ttn";
        $select_setting = [
            "mod" => "jsAdminTpl",
            "name" => "driver",
            "select_id" => $select_id,
            "ajaxUrl" => "getUsersToSelect",
            "return" => true,
            "required" => true,
        ];
        oSelectCustom($select_setting);
        $select_setting["mod"] = "select";
        $select = oSelectCustom($select_setting);
    
        $auto_list = [];
        foreach(\Logistics\Model\CarPark::all() as $item){
            $auto_list[] = "<option value='{$item->id}'>{$item->brand} ({$item->number})</option>";
        }
        $auto_list = implode("", $auto_list);
    
        return <<<HTML
            <div class="modal fade" id="modal_ttn" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="modal_ttn_form" method="POST" class="admin-form">
                        <div class="modal-header">
                          <h5 class="modal-title">Товарно-транспортная накладная</h5>
                        </div>
                        <div class="modal-body">
                            <div>
                                <label for="{$select_id}">Водитель {$select}</label>
                            </div>
                            <div>
                                <label for="auto">Авто
                                    <select name="car" class="form-control">
                                        {$auto_list}
                                    </select>
                                </label>
                            </div>
                            <div>
                                <label for="auto">Вид упаковки
                                    <select name="type_packaging" class="form-control">
                                        <option value="негабарит">негабарит</option>
                                        <option value="поддон">поддон</option>
                                        <option value="коробка">коробка</option>
                                        <option value="без упаковки">без упаковки</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                          <button class="btn btn-primary">Распечатать</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
    
HTML;
    }

    public function local_script(){
        return "<script src=\"/usr/modules/logistics/admin/asset/js/delivery_detail.js?{$this->time}\"></script>";
    }

    public function style(){
        return <<<HTML
            <link rel="stylesheet" type="text/css" href="/usr/modules/logistics/admin/asset/css/delivery_detail.css?{$this->time}">
HTML;
    }

    public function logistics_delivery_query($request = []){
        $where = [];
        $where[] = "deleted_at IS NULL";
        $status = empty($request["status"]) ? 1 : $request["status"];
        $driver = empty($request["driver"]) ? false : $request["driver"];
        $where[] = "ld.status = '{$status}'";
        if($driver) $where[] = "ld.driver = '{$driver}'";
        $where = implode(" AND ", $where);
        $order_by = empty($request["order_by"]) ? "" : "ORDER BY {$request["order_by"]}";
        return "SELECT ld.* FROM `".DB_PREFIX."_logistics_delivery` as ld WHERE {$where} {$order_by}";
    }

    public function list_items($request = []){
        global $db;
        $items = [];
        $query = $db->query($this->logistics_delivery_query($request));
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                $list_items = $this->detail_list_items($r);
                if(count($list_items) > 0){
                    $list_items_module = $this->list_items_module($r);
                    foreach($list_items_module as $field => $value){
                        $r[ $field ] = $value;
                    }
                    $r["items"] = $list_items;
                    $items[] = $r;
                }
            }
        }
        return $items;
    }

    public function list_items_module($request){
        global $db;
        $data = [];
        $module = $request["module"];
        $pid = $request["pid"];
        switch($module){
            default:
                $fields = ", o.id as o_id, o.name as o_name";
                $table = "LEFT JOIN `".DB_PREFIX."_organizations` as o ON(o.id = m.oid)";
            break;
            case "stock_transfer":
                $fields = "";
                $table = "";
            break;
        }
        $query = $db->query("SELECT m.numb as m_numb, o2.name as org_name{$fields}
            FROM `".DB_PREFIX."_{$module}` as m
            {$table}
            LEFT JOIN `".DB_PREFIX."_org` as org ON(org.id = m.org)
            LEFT JOIN `".DB_PREFIX."_organizations` as o2 ON(o2.id = org.oid)
            WHERE m.id = '{$pid}' LIMIT 1");
        while($r = $db->getRow($query)){
            $data = $r;
        }
        return $data;
    }

    static public function detail_list_items($request){
        global $db;
        $id = $request["id"];
        $module = $request["module"];
        $items = [];

        $fields = "";
        $table_name = "`".DB_PREFIX."_{$module}_detail`";
        switch($module){
            case "realization":
                $fields = ", md.s_name as md_name, md.s_sku as md_sku, md.count as md_count, md.s_price as md_price, md.discount as md_discount, md.s_unit as md_unit";
            break;
            case "stock_transfer":
                $fields = ", md.s_name as md_name, md.s_sku as md_sku, md.count as md_count, md.s_price as md_price, md.s_unit as md_unit";
            break;
        }
    
        //left join catalog (no save name items)
        //rd.s_name as rd_s_name, rd.s_sku as rd_s_sku, rd.count as rd_count, rd.s_price as rd_s_price, rd.discount as rd_discount, rd.s_unit as rd_s_unit
        $query_items = $db->query("SELECT ldd.*{$fields}, c.weight as c_weight
        FROM `".DB_PREFIX."_logistics_delivery_detail` as ldd
        LEFT JOIN {$table_name} as md ON(md.id = ldd.pid)
        LEFT JOIN `".DB_PREFIX."_catalog` as c ON(c.id = md.cid)
        WHERE ldd.logistics_delivery_id = '{$id}'");
    
        if($db->numRows($query_items) > 0){
            while($p = $db->getRow($query_items)){
                $items[] = $p;
            }
        }
    
        return $items;
    }

    public function detail_list(){
        $result = [];
        $items = $this->detail_list_query_items();
        if(count($items) > 0){
            foreach($items as $item){
                $result[] = $this->list_template($item);
            }
        } else {
            $result[] = $this->detail_list_empty();
        }
        
        return implode("", $result);
    }

    public function list_template($item){
        $result = [];
        $result[] = $this->detail_list_template_head($item);
        $result[] = $this->detail_list_template_panel_items($item);
        $result[] = $this->detail_list_template_toggle($item);
        
        return implode("", $result);
    }

    static function ttn_button($item, $id){
        return $item["module"] == "realization" ? "<button type='button' class=\"btn btn-xs btn-primary\" onclick='print_modal_ttn({$id});'>Печать ТТН</a>" : "";
    }

    public function detail_list_template_head($item){
        $delivery_type_list = \Logistics\Model\LogisticsDelivery::getDeliveryType();
    
        $id = $item["id"];

        $link = $this->head_link($item);
        $div_o_name = $this->head_div_name($item);
        $org_name = $item["org_name"];
        $contact_person = $item["contact_person"];
        $phone = $item["phone"];
        $delivery_type = $item["delivery_type"];
        $delivery_price = $item["delivery_price"];
        $delivery_price_view = $this->lvp($delivery_price);
        $delivery_type_item = $delivery_type_list[ $delivery_type ];
        $comment = empty($item["comment"]) ? "Не указан" : nl2br($item["comment"]);
        $delivery_date = date("d.m.Y", strtotime($item["delivery_date"]));
        $delivery_date_value = $item["delivery_date"];
        $delivery_from = date("H:i", strtotime($item["delivery_from"]));
        $delivery_to = date("H:i", strtotime($item["delivery_to"]));
        $uid_name = shortUserName($item["uid"]);

        $address_temp = $this->head_address_temp($item);

        $ttn_print_button = static::ttn_button($item, $id);
    
        $template = <<<HTML
            <div class="panel panel-default mn" id="panel_head_{$id}">
                {$this->detail_list_template_head_head($item)}
                <div class="panel-body">
                    <div class="d-flex align-items-center">
                        <div><input type="checkbox" class="item__checkbox" value="{$id}"></div>
                        <div style="flex: 1;">
                            <div class="min_view_v d-flex justify-content-around flex-wrap" data-class_toggle="d-flex justify-content-around flex-wrap col-sm-4 col-xs-12">
                                {$div_o_name}
                                <div>Дата: <span class="delivery_date" data-pk="{$id}" data-value="{$delivery_date_value}">{$delivery_date}</span></div>
                                <div>С: {$delivery_from} до {$delivery_to}</div>
                                <div>Автор: {$uid_name}</div>
                            </div>
                            <div class="col-sm-4 col-xs-12 min_view">
                                <div>{$address_temp}</div>
                                <div>Контактное лицо: {$contact_person}</div>
                                <div>Телефон: <a href="tel:{$phone}">{$phone}</a></div>
                            </div>
                            <div class="col-sm-4 col-xs-12 min_view">
                                <div>{$link}</div>
                                <div>Доставка: {$delivery_type_item} ({$delivery_price_view})</div>
                                <div>Организация: {$org_name}</div>
                                <div>Комментарий: {$comment}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    {$this->change_driver_btn($item)}
                    {$ttn_print_button}
                </div>
            </div>
HTML;
        return $template;
    }

    public function detail_list_template_head_head($item){

    }

    public function head_address_temp($item){
        $address = $item["address"];
        $address_temp = "Адрес доставки: {$address}";
        if(!empty($item["coordinates"])){
            // $this->vc
            if(false){
                //открывать в навигаторе
                $coordinates = explode(", ", $item["coordinates"]);
                $address_temp = "<a href='yandexnavi://build_route_on_map?lat_to={$coordinates[0]}&lon_to={$coordinates[1]}' target='_blank'>{$address_temp}</a>";
            } else {
                //открывать на картах
                $coordinates = str_replace(", ", "%2C", $item["coordinates"]);
                $address_temp = "<a href='https://yandex.ru/maps/?mode=search&text={$coordinates}&z=17.09' target='_blank'>{$address_temp}</a>";
            }
            //https://yandex.ru/maps/44/izhevsk/?mode=search&text={$coordinates}&z=17.09
            //https://yandex.ru/maps/?ll={$coordinates}&z=17.09
        }
        return $address_temp;
    }

    public function head_link($item){
        $pid = $item["pid"];
        $module = $item["module"];
        $m_numb = $item["m_numb"];
        switch($module){
            default:
                return "<a class=\"text-info\">Неизвестеный мод №{$pid}</a>";
            break;
            case "realization":
                return "<a target=\"_blank\" href=\"/administration/module/realization/order/{$pid}\" class=\"text-dark\">Реализация №{$m_numb}</a>";
            break;
            case "stock_transfer":
                return "<a target=\"_blank\" href=\"/administration/module/storage/transfer/detail/{$pid}\" class=\"text-dark\">Перемещение №{$m_numb}</a>";
            break;
        }
    }

    public function head_div_name($item){
        $div_o_name = "";
        if(!empty($item["o_name"])){
            $o_name = $item["o_name"];
            $div_o_name = "<div>Контрагент: {$o_name}</div>";
        }
        return $div_o_name;
    }

    public function detail_list_template_panel_items($item){
        $id = $item["id"];
        $items_temp = $this->detail_list_template_items($item);
        $total_view = $this->lvp($items_temp["total"]);
        return <<<HTML
        <div class="panel panel-default min_view mbn" id="panel_body_{$id}">
            <div class="panel-body">
                <div class="items">
                    <label class="items__grid items__head">
                        <div>Артикул</div>
                        <div>Наименование</div>
                        <div class="items__grid-center">Кол-во</div>
                        <div class="">Итого</div>
                        <div class="items__grid-center">Ед.изм.</div>
                    </label>
                    <div class="items__body">
                        {$items_temp["template"]}
                    </div>
                    <div class="items__grid items__footer">
                        <div></div>
                        <div></div>
                        <div class="total_footer__text mr10">Итого:</div>
                        <div>{$total_view}</div>
                    </div>
                </div>
            </div>
        </div>
HTML;
    }

    public function detail_list_template_toggle($item){
        $id = $item["id"];
        return <<<HTML
            <div class="panel panel-default d-flex justify-content-center cp" onclick="toggle_min_view({$id});">
                <i class="fas fa-caret-down p5" id="toggle_min_view_icon_{$id}"></i>
            </div>
HTML;
    }

    public function detail_info(){
        $result = [];
        $driver_list_data = $this->driver_list_data();
        $result[] = $this->driver_list_data_template(["driver_list_data" => $driver_list_data, "driver_link_type" => 2]);
        return implode("", $result);
    }

    public function driver_list_data(){
        global $db;
        $result = [];
        $query = $db->query("SELECT driver FROM `".DB_PREFIX."_logistics_delivery` WHERE status = 2 AND driver IS NOT NULL GROUP BY driver");
        if($db->numRows($query) > 0){
            while($r = $db->getRow($query)){
                $result[] = $r;
            }
        }
        return $result;
    }

    public function detail_list_template_items($item){
        $result = [
            "template" => "",
            "total" => 0,
        ];
        $items = [];
        foreach($item["items"] as $r){
            $sku = $r["md_sku"];
            $name = $r["md_name"];
            $count = $r["md_count"];
            $discount = empty($r["md_discount"]) ? 0 : $r["md_discount"];
            $p_data = \Price::get_all([
                "price" => $r["md_price"],
                "discount" => $discount,
                "count" => $count,
            ]);
            $total = $p_data["total"];
            $result["total"] += $total;
            $total_view = $this->lvp($total);
            $unit = $r["md_unit"];
            $unit_name = get_unit('data', $unit ,'sname');
            $items[] = <<<HTML
            <label class="items__grid item">
                <div class="item__sku">{$sku}</div>
                <div class="item__name">{$name}</div>
                <div class="item__count">{$count}</div>
                <div class="item__total">{$total_view}</div>
                <div class="item__unit">{$unit_name}</div>
            </label>
HTML;
        }
        $result["template"] = implode("", $items);
        return $result;
    }

    static function print($id, $request){
        $driver_id = $request["driver"];
        $driver = \Boot\Models\Users::findOrFail($driver_id);
        $car_id = $request["car"];
        $car = \Logistics\Model\CarPark::findOrFail($car_id);
        $type_packaging = $request["type_packaging"];
        $logistic_delivery = \Logistics\Model\LogisticsDelivery::findOrFail($id);
        if($logistic_delivery->module == "realization"){
            $module = $logistic_delivery->getModule();
            $document = new \PhpOffice\PhpWord\TemplateProcessor(ROOT."/usr/modules/logistics/resources/templates/TTN.docx");
            $document->setValue('numb', $module->numb);
            $document->setValue('day', date("d"));
            $document->setValue('day_d', date("d", strtotime($logistic_delivery->delivery_date)));
            $document->setValue('month', date("m"));
            $document->setValue('month_rus', \Carbon\Carbon::parse(date("Y-m-d"))->locale('ru')->getTranslatedMonthName('Do MMMM'));
            $document->setValue('month_rus_d', \Carbon\Carbon::parse($logistic_delivery->delivery_date)->locale('ru')->getTranslatedMonthName('Do MMMM'));
            $document->setValue('year', date("y"));
            $document->setValue('year_d', date("y", strtotime($logistic_delivery->delivery_date)));
            $document->setValue('date', date("d.m.y"));
            $document->setValue('driver', $driver->short_user_name);
            $document->setValue('driver_full', $driver->full_user_name);
            $document->setValue('brand_car', $car->brand);
            $document->setValue('number_car', $car->number);
            $document->setValue('type_packaging', $type_packaging);
            $shipper = $module->org_->organization;
            $consignee = $module->organization;
            $document->setValue('shipper', "{$shipper->name} {$shipper->yaddress}");
            $document->setValue('consignee', "{$consignee->name} {$logistic_delivery->address}");
            $document->setValue('okpo_shipper', $shipper->okpo ?? "");
            $document->setValue('okpo_consignee', $consignee->okpo ?? "");
            $items = static::detail_list_items($logistic_delivery->toArray());
            $order_data = [];
            $n = 0;
            $count_items_all = 0;
            $weight_all = 0;
            foreach($items as $item){
                $n++;
                $weight_data = json_decode($item["c_weight"], true);
                $weight = (float)($weight_data[1] ?? 0) / 1000;
                $weight_all += $weight;
                $count = $item["md_count"];
                $count_items_all += $count;
                $order_data[] = [
                    "n" => $n,
                    "sku" => $item["md_sku"],
                    "count" => $count,
                    "name" => $item["md_name"],
                    "unit" => get_unit('data', $item["md_unit"] ,'sname'),
                    "weight" => $weight == 0 ? "-" : $weight,
                ];
            }
            $document->cloneRowAndSetValues('n', $order_data);
            $document->setValue('count_items', $n);
            $document->setValue('count_items_rus', Str::numberFormatter($n));
            $document->setValue('count_items_all', $count_items_all);
            $document->setValue('count_items_all_rus', Str::numberFormatter($count_items_all));
            $document->setValue('weight_all', $weight_all);
            $document->setValue('weight_all_rus', Str::numberFormatter($weight_all));
            $document->setValue('unloading_point', $logistic_delivery->address);
            $file_url = ROOT."/usr/modules/logistics/resources/templates/ТТН_{$module->numb}.docx";
            $document->saveAs($file_url);
            OfficeSupport::get_file($file_url);
        } else {
            throw new \Exception("Доступно печатать только реализации!");
        }
    }
}