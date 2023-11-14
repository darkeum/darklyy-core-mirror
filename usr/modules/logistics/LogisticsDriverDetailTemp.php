<?php

namespace Logistics;

use \Boot\Support\Facades\Auth;

class LogisticsDriverDetailTemp extends LogisticsDetailTemp{
    public $driver;

    public function __construct(){
        parent::__construct();
    }

    public function detail_list_query(){
        return \Logistics\Support\LogisticsDriverDetailSupport::list_query($this->driver);
    }

    public function view(){
        global $adminTpl;
        $adminTpl->admin_head("Логистика | Забор");
        $this->view_template();
        $adminTpl->admin_foot();
    }

    public function view_template(){
        $panel_body_btn = $this->panel_body_btn();
        $panel_body = $panel_body_btn.$this->driver_detail_template(["driver" => $this->driver]);
        $info = $this->detail_info();
	    $list = $this->detail_list();
        $template = $this->detail_template(["panel_body" => $panel_body, "info" => $info, "list" => $list]);
        $style = "<link rel=\"stylesheet\" type=\"text/css\" href=\"/usr/modules/logistics/admin/asset/css/driver_detail.css?{$this->time}\">";
        $change_driver_modal = $this->change_driver_modal();
        echo <<<HTML
            <br>
            {$style}
            <input type="hidden" id="mainId" value="{$this->driver}">
            {$template}
            {$change_driver_modal["template"]}
HTML;
        $this->script();
        echo '<script src="/usr/modules/logistics/admin/asset/js/driver_detail.js?{$this->time}"></script>';
        echo $change_driver_modal["script"];
    }

    public function change_driver_btn($request){
        global $core;
        if($core->auth->allowAccessByName(Auth::id(), "changeDriver")){
            return $this->change_driver_btn_source(["id" => $request["supply_o"], "current_driver" => $this->driver]);
        }
    }

    public function detail_info(){
        
    }

    public function detail_list_template_panel_items($items){
        $supply_o = $items["supply_o"];
        $items_temp = $this->detail_list_template_items($items["items"]);
        $total_view = $this->lvp($items_temp["total"]);
        $checkbox  = $this->checkbox_creator($supply_o);
        $checkbox2 = $this->checkbox_creator($supply_o, "mobile");
        $count_view = $items_temp["count"];
        $footer = $this->detail_list_template_panel_items_footers($items);
        
        return <<<HTML
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="items">
                        <label class="items__grid items__head">
                            <div>{$checkbox}</div>
                            <div>Артикул</div>
                            <div>Наименование</div>
                            <div class="items__grid-center">Кол-во</div>
                            <div>Итого</div>
                            <div class="items__grid-center">Ед.изм.</div>
                            <div>Доставка</div>
                        </label>
                        <div class="items__body">
                            {$checkbox2}
                            {$items_temp["template"]}
                        </div>
                        <div class="items__grid items__footer">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div class="count_footer__text" id="count_{$supply_o}">{$count_view}</div>
                            <div class="total_footer__text" id="total_{$supply_o}">{$total_view}</div>
                        </div>
                    </div>
                </div>
                {$footer}
            </div>
HTML;
    }

    public function detail_list_template_panel_items_footers($items){
        $supply_o = $items["supply_o"];
        return <<<HTML
            <div class="panel-footer">
                <div class="d-flex justify-content-end">
                    <div class="col-sm-2 col-xs-12"><button class="btn btn-primary btn-xs btn-block" onclick="pick_up_modal({$supply_o});">Забрано</button></div>
                </div>
            </div>
HTML;
    }

    public function detail_list_template_items($items){
        $result = [
            "template" => "",
            "total" => 0,
            "count" => 0,
        ];
        $item = [];
        $ltc = $this->transport_companies();
        foreach($items as $r){
            $supply_o = $r["supply_o"];
            $id = $r["id"];
            $s_sku = $r["sod_s_sku"];
            $s_name = e($r["sod_s_name"]);
            $count = \Logistics\Support\LogisticsDetailSupport::count($r);
            $p_data = \Price::get_all([
                "price" => $r["sod_price_now"],
                "count" => $count,
            ]);
            $total = $p_data["total"];
            $result["total"] += $total;
            $result["count"] += $count;
            $total_view = $this->lvp($total);
            $unit = $r["sod_unit"];
            $unit_name = get_unit('data', $unit ,'sname');
            $empty_date = "Не указано";
            $date_ship = empty($r["sod_date_ship"]) ? $r["so_date_ship"] : $r["sod_date_ship"];
            $date_ship = empty($date_ship) ? $empty_date : date("d.m.Y", strtotime($date_ship));
            $date_end = empty($r["sod_date_end"]) ? $r["so_date_end"] : $r["sod_date_end"];
            $date_end = empty($date_end) ? $empty_date : date("d.m.Y", strtotime($date_end));
            $transport_companies = isset($ltc[ $r["transport_companies"] ]) ? $ltc[ $r["transport_companies"] ] : "Не указано";
            $item[] = <<<HTML
                <label class="items__grid item item_{$supply_o}" id="item_{$id}" data-id="{$id}" data-supply_o="{$supply_o}" data-price="{$p_data['price']}">
                    <div><input type="checkbox" class="item__checkbox item__checkbox-{$supply_o}" value="{$id}"></div>
                    <div class="item__sku">{$s_sku}</div>
                    <div class="item__name">{$s_name}</div>
                    <div class="item__count-body"><span class="item__count" data-pk="{$id}">{$count}</span></div>
                    <div class="item__total">{$total_view}</div>
                    <div class="item__unit">{$unit_name}</div>
                    <div class="item__transport_companies">{$transport_companies}</div>
                </label>
HTML;
        }
        $result["template"] = implode("", $item);
        return $result;
    }
} 