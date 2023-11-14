<?php

namespace Logistics;

use \Boot\Support\Facades\Auth;

class LogisticsDetailTemp extends LogisticsTemp
{
    public $request;
    public $search;
    public $status;

    protected $driver_list_data_where = "status = 1";

    public function __construct()
    {
        parent::__construct();
    }

    public function detail_list_query()
    {
        $request = $this->request;
        return \Logistics\Support\LogisticsDetailSupport::list_query($request);
    }

    public function transport_companies()
    {
        return [1 => "Деловые Линии", "ПЭК", "Артэк", "СкифКарго", "ДПД", "СДЭК", "ГлавДоставка"];
    }

    public function view()
    {
        global $adminTpl;
        $adminTpl->admin_head("Логистика | Забор");
        $this->view_template();
        $adminTpl->admin_foot();
    }

    public function view_template()
    {
        global $core;
        $ltc = $this->transport_companies_x();
        $panel_body_btn = <<<HTML
		    <i class="fas fa-arrow-circle-up fa-lg cp text-info" title="Загрузить позиции" onclick="logistics_auto_add_modal();"></i>
HTML;
        $panel_body_btn = $this->panel_body_btn(["btn" => $panel_body_btn]);
        $input_search = <<<HTML
            <div class="input-group mb20">
                <input type="text" class="form-control input-sm" id="search" name="search" placeholder="Введите для поиска...">
                <div class="input-group-btn">
                  <button class="btn btn-primary btn-xs" type="button" onclick="update_list_lw();"><i class="fas fa-search"></i></button>
                </div>
            </div>
HTML;

        $sc4 = $this->status_color(4);
        $sc6 = $this->status_color(6);
        $assign_driver_btn = $this->assign_driver_btn();
        $baikalsr_button = "";
        // if ($core->auth->allowAccessByName(Auth::id(), "baikalsrButton")) {
        //     $baikalsr_button = "<button class=\"btn btn-xs btn-flex btn-alert\" data-toggle=\"modal\" data-target=\"#baikalsrModal\"><i class=\"fas fa-draw-square\"></i></button>";
        // }
        $panel_body = <<<HTML
            {$input_search}
            {$panel_body_btn}
	    	<div class="mt20">
	    		{$assign_driver_btn}
	    	</div>
	    	<div class="d-flex btn-group mt10">
	    		<button type="button" class="btn btn-xs btn-flex btn-danger" onclick="mas_delete_modal();"><i class="fas fa-times"></i></button>
	    		<button type="button" class="btn btn-xs btn-flex btn-warning" onclick="mas_transport_companies_modal();"><i class="fas fa-truck"></i></button>
	    		<button type="button" class="btn btn-xs btn-flex btn-info" onclick="date_modal();"><i class="fas fa-calendar-alt"></i></button>
                {$baikalsr_button}
            </div>
            <input type="date" name="date_ship" id="date_ship_send" class="d-none">
            <input type="checkbox" name="date_ship_active" id="date_ship_active_send" class="d-none">
HTML;
        $panel_body_after = <<<HTML
	    	<div class="info_item">
	    		<div class="info_item__line">
	    			<div class="info_item__title">Легенда:</div>
	    		</div>
	    	</div>
	    	<div class="info_item no_line">
	    		<div>
                    <label class="info_item__line legend_item">
                        <div><i class="fas fa-globe-americas legend_item_all text-success"></i></div>
	    			    <div>&ndash;</div>
	    			    <div>все</div>
                        <input type="radio" class="mr5 view_type" name="status" value="" checked>
                    </label>
                    <label class="info_item__line legend_item">
                        <div class="color_block border border-dark"></div>
	    			    <div>&ndash;</div>
	    			    <div>позиция ожидает распределения</div>
                        <input type="radio" class="mr5 view_type" name="status" value="1">
                    </label>
	    			<label class="info_item__line legend_item">
	    				<div class="color_block {$sc4}"></div>
	    				<div>&ndash;</div>
	    				<div>позиция с проблемой</div>
                        <input type="radio" class="mr5 view_type" name="status" value="4">
	    			</label>
	    			<label class="info_item__line legend_item">
	    				<div class="color_block {$sc6}"></div>
	    				<div>&ndash;</div>
	    				<div>позиция не забрана</div>
                        <input type="radio" class="mr5 view_type" name="status" value="6">
	    			</label>
	    		</div>
	    	</div>
HTML;
        $info = $this->detail_info();
        $list = $this->detail_list();

        $template = $this->detail_template(["panel_body" => $panel_body, "panel_body_after" => $panel_body_after, "info" => $info, "list" => $list]);

        $driver_modal = $this->driver_modal();
        $style = "<link rel=\"stylesheet\" type=\"text/css\" href=\"/usr/modules/logistics/admin/asset/css/detail.css?{$this->time}\">";

        $transport_companies_opt = array_to_options($this->transport_companies());

        $date_ship = date("Y-m-d");

        echo <<<HTML
	    	{$style}
	    	<br>
            <input type="hidden" id="transport_companies" value='{$ltc}'>
        
	    	{$template}

	    	{$driver_modal["template"]}

	    	<div class="modal fade admin-form" id="notifModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	    	  <div class="modal-dialog" role="document">
	    	    <div class="modal-content">
	    	      <div class="modal-header">
	    	        <h5 class="modal-title">Уведомление</h5>
	    	      </div>
	    	      <div class="modal-body">
	    			  	<form onsubmit="return false;" id="notifModalForm">
	    					<textarea class="form-control" name="text" placeholder="Введите текст уведомления"></textarea>
	    				</form>
	    	      </div>
	    	      <div class="modal-footer">
	    		  	<button type="button" class="btn btn-primary" id="notifModalBtn">Отправить</button>
	    	        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	    	      </div>
	    	    </div>
	    	  </div>
            </div>
            
            <div class="modal fade" id="transport_companies_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Установка доставки</h5>
                  </div>
                  <div class="modal-body">
                    <select id="transport_companies_modal_select" class="form-control">{$transport_companies_opt}</select>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="transport_companies_modal_btn">Установить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade admin-form" id="date_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Выбор даты</h5>
                  </div>
                  <div class="modal-body d-flex flex-column">
                      <div><input type="date" value="{$date_ship}" id="date_ship" class="form-control"></div>
                      <div class="mt20"><label><input type="checkbox" id="date_ship_active">Активировать фильтр по дате</label></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="date_modal_btn">Установить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal fade admin-form" id="baikalsrModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Калькулятор доставки</h5>
                  </div>
                  <div class="modal-body d-flex justify-content-center">
                      <div><iframe src="https://api.baikalsr.ru/widget/calculator?" width="310" height="525" scrolling="no" frameborder="0"></iframe></div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>
HTML;
        $this->script();
        echo $driver_modal["script"];
        echo "<script src=\"/usr/modules/logistics/admin/asset/js/detail.js?{$this->time}\"></script>";
    }

    public function transport_companies_x()
    {
        $ltc = $this->transport_companies();
        $result = [];
        foreach ($ltc as $value => $text) {
            $result[] = ["value" => $value, "text" => $text];
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function status_color($status)
    {
        $color = [
            4 => "bg-warning",
            6 => "bg-danger",
        ];
        return isset($color[$status]) ? $color[$status] : "";
    }

    public function detail_info()
    {
        $result = [];
        $driver_list_data = $this->driver_list_data();
        $result[] = $this->driver_list_data_template(["driver_list_data" => $driver_list_data, "driver_link_type" => 1]);
        return implode("", $result);
    }

    public function detail_list()
    {
        $query = $this->detail_list_query();
        $result = [];

        if ($query->exists()) {
            $data = [];
            foreach ($query->get() as $r) {
                $data = $this->prepare_data_list($data, (array)$r);
            }
            foreach ($data as $items) {
                $result[] = $this->detail_list_template($items);
            }
        } else {
            $result[] = $this->detail_list_empty();
        }

        return implode("", $result);
    }

    public function detail_list_template_head($items)
    {
        require_once(ROOT . "usr/modules/fcontrol/admin/sub/function.php");
        $oid = $items["o_name"];
        $so_numb = $items["so_numb"];
        $supply_o = $items["supply_o"];
        $org_name = $items["org_name"];
        $object_name = $items["object_name"];
        $bill_code = empty($items["bill_code"]) ? "Не указано" : $items["bill_code"];
        $pay_status = $items["c_pay"];
        $pay_reason = $items["c_reason"];
        $pay_icon = pay_status_view($pay_status, $pay_reason);

        $link = "/administration/module/supply/order_detail/{$supply_o}";
        if ($items["items"][0]["status"] == 6) {
            $link = "/administration/module/logistics/supply_order_link/{$supply_o}";
        }

        $template = <<<HTML
            <div class="panel panel-default mn">
                <div class="panel-body">
                    <div class="col-sm-4 col-xs-12">
                        <div class="mb20">Поставщик: {$oid}</div>
                        <div class="mb20">Организация: {$org_name}</div>
                        <div>Объект: {$object_name}</div>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <div class="mb20">Номер счёта: {$bill_code}</div>
                        <div>Оплата: {$pay_icon}</div>
                    </div>
                    <div class="col-sm-4 col-xs-12">
                        <div>
                            <a target="_blank" href="{$link}" class="text-dark">Счёт поставщика №{$so_numb}</a>
                            <a href="/administration/module/logistics/download_supply_order/{$supply_o}" class="fas fa-file-upload text-success ml10" title="Скачать счёт поставщика"></a>
                            <div class="mt10">{$this->change_driver_btn($items)}</div>
                        </div>
                    </div>
                </div>
            </div>
            
HTML;
        return $template;
    }

    public function detail_list_template_panel_items($items)
    {
        $supply_o = $items["supply_o"];
        $items_temp = $this->detail_list_template_items($items["items"]);
        $total_view = $this->lvp($items_temp["total"]);
        $total_weight = $items_temp["total_weight"];
        $checkbox  = $this->checkbox_creator($supply_o);
        $checkbox2 = $this->checkbox_creator($supply_o, "mobile");
        return <<<HTML
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="items">
                    <label class="items__grid items__head">
                        <div>{$checkbox}</div>
                        <div>№</div>
                        <div>Артикул</div>
                        <div>Наименование</div>
                        <div class="items__grid-center">Кол-во</div>
                        <div class="">Итого</div>
                        <div class="items__grid-center">Ед.изм.</div>
                        <div class="items__grid-center">Вес</div>
                        <div class="items__grid-center">Дата отгрузки</div>
                        <div class="items__grid-center">Дата поставки</div>
                        <div>Доставка</div>
                        <div>Действия</div>
                    </label>
                    <div class="items__body">
                        {$checkbox2}
                        {$items_temp["template"]}
                    </div>
                    <div class="items__grid items__footer">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div class="total_footer__text mr10">Итого:</div>
                        <div>{$total_view}</div>
                        <div class="total_footer__text mr10">Итого:</div>
                        <div class="total_weight_footer__text">{$total_weight}</div>
                    </div>
                </div>
            </div>
        </div>
HTML;
    }

    public function detail_list_template_items($items)
    {
        $result = [
            "template" => "",
            "total" => 0,
            "total_weight" => 0,
        ];
        $item = [];
        $ri = isset($_GET["ri"]) ? $_GET["ri"] : [];
        $ri = is_array($ri) ? $ri : [];
        $i = 0;
        foreach ($items as $r) {
            $i++;
            $supply_o = $r["supply_o"];
            $id = $r["id"];
            $s_sku = $r["sod_s_sku"];
            $s_name = ee($r["sod_s_name"]);
            $sod_count = $r["sod_count"];
            $count = \Logistics\Support\LogisticsDetailSupport::count($r);
            $unit = $r["sod_unit"];
            $unit_name = get_unit('data', $unit, 'sname');
            $catalog_weight = json_decode($r["catalog_weight"], true);
            $weight = (float)($catalog_weight[1] ?? 0) * $count;
            $p_data = \Price::get_all([
                "price" => $r["sod_price_now"],
                "count" => $count,
            ]);
            $total = $p_data["total"];
            $result["total"] += $total;
            $result["total_weight"] += $weight;
            $total_view = $this->lvp($total);
            $empty_date = "Не указано";
            $date_ship = empty($r["sod_date_ship"]) ? $r["so_date_ship"] : $r["sod_date_ship"];
            $date_ship = empty($date_ship) ? $empty_date : date("d.m.Y", strtotime($date_ship));
            $date_end = empty($r["sod_date_end"]) ? $r["so_date_end"] : $r["sod_date_end"];
            $date_end = empty($date_end) ? $empty_date : date("d.m.Y", strtotime($date_end));
            $status = $r["status"];
            $transport_companies = empty($r["transport_companies"]) ? 'null' : $r["transport_companies"];
            $bg_item = $this->status_color($status);
            $bg_ri = in_array($id, $ri) ? "bg_ri" : "";
            $item[] = <<<HTML
                <label class="items__grid item {$bg_item} {$bg_ri}" id="item_{$id}">
                    <div><input type="checkbox" class="item__checkbox item__checkbox-{$supply_o}" value="{$id}"></div>
                    <div>{$i}</div>
                    <div class="item__sku">{$s_sku}</div>
                    <div class="item__name">{$s_name}</div>
                    <div class="item__count data-id="{$id}" data-count="{$sod_count}">{$count}</div>
                    <div class="item__total">{$total_view}</div>
                    <div class="item__unit">{$unit_name}</div>
                    <div class="item__weight">{$weight}</div>
                    <div class="item__date_ship">{$date_ship}</div>
                    <div class="item__date_end">{$date_end}</div>
                    <div>
                        <span class="item__transport_companies" data-value="{$transport_companies}" data-pk="{$id}"></span>
                    </div>
                    <div class="item__action">
                        <button class="btn btn-danger btn-xs fas fa-times" title="Удалить" onclick="item_delete_modal({$id});"></button>
                        <button class="btn btn-info btn-xs fas fa-bell" title="Уведомление" onclick="notif_modal({$id});"></button>
                    </div>
                </label>
HTML;
        }
        $result["template"] = implode("", $item);
        return $result;
    }

    function checkbox_creator($id, $type = false)
    {
        $text = "";
        $class_name = "";
        switch ($type) {
            case "mobile":
                $text = "Все: ";
                $class_name = "d-sm-none d-lg-none";
                break;
        }
        return <<<HTML
            <label class="font-weight-bold {$class_name}">{$text}<input type="checkbox" onchange="checkbox_change(this, {$id});" class="item__checkbox-all"></label>
HTML;
    }

    public function prepare_data_list($data, $r)
    {
        $data[$r["supply_o"]]["o_name"] = $r["o_name"];
        $data[$r["supply_o"]]["so_numb"] = $r["so_numb"];
        $data[$r["supply_o"]]["supply_o"] = $r["supply_o"];
        $data[$r["supply_o"]]["org"] = $r["so_org"];
        $org = getOrgInfo($r["so_org"]);
        $data[$r["supply_o"]]["org_name"] = $org["organizations"]["name"];
        $data[$r["supply_o"]]["object_name"] = $r["object_name"];
        $data[$r["supply_o"]]["bill_code"] = $r["so_bill_code"];
        $data[$r["supply_o"]]["c_pay"] = $r["c_pay"];
        $data[$r["supply_o"]]["c_reason"] = $r["c_reason"];
        $data[$r["supply_o"]]["items"][] = $r;
        return $data;
    }

    public function driver_list_data()
    {
        global $db;
        $result = [];
        $query = $db->query("SELECT driver FROM `" . DB_PREFIX . "_logistics_driver_detail` WHERE {$this->driver_list_data_where} GROUP BY driver");
        if ($db->numRows($query) > 0) {
            while ($r = $db->getRow($query)) {
                $result[] = $r;
            }
        }
        return $result;
    }
}
