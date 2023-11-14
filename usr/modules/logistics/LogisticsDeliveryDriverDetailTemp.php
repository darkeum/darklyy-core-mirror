<?php

namespace Logistics;

use \Boot\Support\Facades\Auth;

class LogisticsDeliveryDriverDetailTemp extends LogisticsDeliveryDetailTemp{
    public $driver;

    public function __construct(){
        parent::__construct();
    }

    public function detail_list_query_items(){
        return $this->list_items(["status" => 2, "driver" => $this->driver]);
    }

    public function view_template(){
        $panel_body_btn = $this->panel_body_btn(["btn" => ""]);
        $logistics_driver_detail_template = $this->driver_detail_template(["driver" => $this->driver]);
	    $panel_body = $panel_body_btn.$logistics_driver_detail_template;
        $info = $this->detail_info();
	    $list = $this->detail_list();

	    $template = $this->detail_template(["panel_body" => $panel_body, "panel_body_after" => "", "info" => $info, "list" => $list]);

        $driver_modal = $this->driver_modal();

        $style = $this->style();

        $change_driver_modal = $this->change_driver_modal();

        $ttn_modal = $this->ttn_modal();

        echo <<<HTML
            {$style}
            <input type="hidden" id="mainId" value="{$this->driver}">
	        <br>
	        {$template}

            {$driver_modal["template"]}
            {$change_driver_modal["template"]}
            {$ttn_modal}
HTML;
        $this->script();
	    echo $driver_modal["script"];
	    echo $change_driver_modal["script"];
	    echo "<script src=\"/usr/modules/logistics/admin/asset/js/delivery_driver_detail.js?{$this->time}\"></script>";
    }

    public function change_driver_btn($request){
        global $core;
        if(Auth::id() == 16){
            return $this->change_driver_btn_source(["id" => $request["id"], "current_driver" => $this->driver]);
        }
    }

    public function detail_info(){

    }

    public function list_template($item){
        $result = [];
        $id = $item["id"];
        $result[] = $this->detail_list_template_head($item);
        $result[] = $this->detail_list_template_panel_items($item);
        $result[] = <<<HTML
        <div class="panel panel-default min_view mbn" id="panel_footer_{$id}">
            <div class="panel-footer d-flex justify-content-end">
                <input type="button" class="btn btn-primary btn-xs" value="Доставлено" onclick="delivered_modal({$id});">
            </div>
        </div>
HTML;
        $result[] = $this->detail_list_template_toggle($item);
        
        return implode("", $result);
    }
}