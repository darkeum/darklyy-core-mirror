<?php

namespace Supply;

use Logistics\LogisticsDriverDetailTemp;

class LogisticsReceiptsTemp extends LogisticsDriverDetailTemp{

    protected $driver_list_data_where = "status = 2 AND receipts IS NULL";

    public function detail_list_query(){
        return \Logistics\Support\LogisticsDriverDetailSupport::query($this->driver, ["where" => function($query){
          return $query->where("ldd.status", 2)->whereNull("ldd.receipts");
        }]);
    }

    public function btn_receipts_group(){
        return <<<HTML
            <div class="mt20 d-flex btn-group">
                <button type="button" class="btn btn-primary btn-xs" style="flex: 1;" id="btn-receipt-confirm">Подтвердить</button>
                <button type="button" class="btn btn-danger btn-xs" style="flex: 1;" id="btn-receipt-cancel">Не пришло</button>
            </div>
HTML;
    }

    public function view_template(){
        $panel_body_btn = $this->panel_body_btn();
        $btn_receipts = $this->btn_receipts_group();
        $panel_body = $panel_body_btn.$btn_receipts;
        $info = $this->detail_info();
	    $list = $this->detail_list();
        $template = $this->detail_template(["panel_body" => $panel_body, "info" => $info, "list" => $list]);
        $style = "<link rel=\"stylesheet\" type=\"text/css\" href=\"/usr/modules/logistics/admin/asset/css/driver_detail.css?{$this->time}\">";
        echo <<<HTML
            {$style}
            <br>
            <input type="hidden" id="mainId" value="">
            {$template}

            <div class="modal fade" id="receipt-confirm-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Поступление</h5>
                  </div>
                  <div class="modal-body" id="receipt-confirm-modal-body" style="display: grid; grid-row-gap: 20px;"></div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>
HTML;
        $this->script();
        echo '<script src="/usr/modules/supply/admin/asset/js/logistics_receipts_temp.js?{$this->time}"></script>';
    }

    public function detail_list_empty_text(){
        return "Выберите водителя!";
    }

    public function detail_list_template_panel_items_footers($items){
        
    }

    public function change_driver_btn($request){

    }

    public function detail_info(){
        $driver_list_data = $this->driver_list_data();
        return $this->driver_list_data_template(["driver_list_data" => $driver_list_data]);
    }
}