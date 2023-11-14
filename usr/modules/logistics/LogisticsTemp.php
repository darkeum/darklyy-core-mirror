<?php

namespace Logistics;

abstract class LogisticsTemp{
    protected $vc = false;

    abstract public function view();
    abstract public function view_template();
    abstract public function detail_list();
    abstract public function detail_list_template_head($item);
    abstract public function detail_list_template_panel_items($item);

    public function __construct(){
        $this->time = time();
    }

    public function view_clear(){
        global $core;
        $btn_head = "";
        $c = get_class($this);
        $this->vc = true;
        if($c == "Logistics\LogisticsDriverDetailTemp" || $c == "Logistics\LogisticsDeliveryDriverDetailTemp"){

            $logistics_driver = $c == "Logistics\LogisticsDriverDetailTemp" ? "btn-primary" : "btn-secondary";
            $logistics_delivery_driver = $c == "Logistics\LogisticsDeliveryDriverDetailTemp" ? "btn-primary" : "btn-secondary";

            $btn_head = <<<HTML
                    <div class="panel panel-default ">
                        <div class="panel-body">
                            <div class="d-flex justify-content-around align-items-center p20">
                                <a href="/administration/module/logistics/logistics_driver/detail_min/{$this->driver}" class="btn {$logistics_driver}">Забор</a>
                                <a href="/administration/module/logistics/logistics_delivery_driver/detail_min/{$this->driver}" class="btn {$logistics_delivery_driver}">Доставка</a>
                            </div>
                        </div>
                    </div>
HTML;
        }
        echo <<<HTML
            <!DOCTYPE html>
            <html lang="ru">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <base href="https://atri-energo.ru/">
                <title>Логистика</title>
                <link rel="stylesheet" type="text/css" href="/usr/tpl/corp//assets/css/core.css">
                <link rel="stylesheet" type="text/css" href="/usr/tpl/corp//assets/css/theme.css">
                <link rel="stylesheet" type="text/css" href="/usr/tpl/corp//assets/css/admin-forms.css">
                <link rel="stylesheet" type="text/css" href="/usr/tpl/corp//assets/css/bootsarp-4_border.css">
                <link rel="stylesheet" type="text/css" href="/usr/tpl/corp//assets/css/bootsarp-4_font.css">
                <link rel="stylesheet" type="text/css" href="/usr/tpl/corp//assets/css/bootsarp-4_display.css">
                <link rel="stylesheet" type="text/css" href="/usr/tpl/corp//assets/css/bootsarp-4_flex.css">
                <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <link rel="stylesheet" type="text/css" href="/usr/tpl/corp//assets/fonts/font-awesome-pro/css/all.min.css">	 
                <link rel="stylesheet" type="text/css" href="/usr/modules/logistics/admin/asset/css/main_min.css">	 

                <script src="/usr/tpl/corp//assets/js/jquery.min.js"></script>
                <script src="/usr/tpl/corp//assets/js/jquery-ui.js"></script>
                <script src="/usr/tpl/corp//assets/js/core.min.js"></script>
            </head>
            <body style="height: unset;">
                <div>  
                    {$btn_head}
HTML;
                    $this->view_template();

        echo <<<HTML
                </div>
                <script src="/usr/tpl/corp//assets/js/sweetalert.min.js"></script>
                <script src="/usr/other/sub/load_window/asset/js/load_window.js"></script>
            </body>
            </html>
HTML;
    }

    public function detail_template($request = []){
        $panel_body = isset($request["panel_body"]) ? $request["panel_body"] : "";
        $panel_body_after = isset($request["panel_body_after"]) ? $request["panel_body_after"] : "";
        $info = isset($request["info"]) ? $request["info"] : "";
        $list = isset($request["list"]) ? $request["list"] : "";
        // <div class="panel-heading d-flex justify-content-center pn">
        //     <img src="/usr/modules/logistics/admin/asset/img/car.gif" alt="car" style="height: 150px;">
        // </div>
        return <<<HTML
            <link rel="stylesheet" type="text/css" href="/usr/modules/logistics/admin/asset/css/main.css?{$this->time}">
            <div class="admin-form" id="panel_main">
                <div id="list">
                    {$list}
                </div>
                <div id="panel_info">
                    <form onsubmit="return false;" id="panel_info_form">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                {$panel_body}
                                <div id="info">
                                    {$info}
                                </div>
                                {$panel_body_after}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
HTML;
    }

    public function panel_body_btn($request = []){
        $btn = isset($request["btn"]) ? $request["btn"] : "";
        return <<<HTML
            <div class="info_btn d-flex justify-content-between">
                <i class="fas fa-sync-alt fa-lg cp text-success" title="Обновить все" onclick="update_all_lw();"></i>
                <i class="fas fa-redo-alt fa-lg cp text-warning" title="Обновить список" onclick="update_list_lw();"></i>
                <i class="fas fa-redo-alt fa-lg cp text-info" title="Обновить информационную панель" onclick="update_info_lw();"></i>
                {$btn}
            </div>
HTML;
    }

    public function detail_list_template($items){
        $item = [];
    
        $item[] = $this->detail_list_template_head($items);
        $item[] = $this->detail_list_template_panel_items($items);
        
        return implode("", $item);
    }

    public function detail_list_empty(){
        return <<<HTML
            <div class="d-flex justify-content-center items-empty">{$this->detail_list_empty_text()}</div>
HTML;
    }

    public function detail_list_empty_text(){
        return "Товары отсутствуют!";
    }

    public function driver_modal(){
        $select_setting = [
            "mod" => "jsReturn",
            "name" => "driver",
            "ajaxUrl" => "getUsersToSelect",
            "return" => true,
        ];
        $script = oSelectCustom($select_setting);
        $select_setting["mod"] = "select";
        $driver_select = oSelectCustom($select_setting);
        
        $template = <<<HTML
            <div class="modal fade admin-form" id="driverModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Добавить водителя</h5>
                  </div>
                  <div class="modal-body">
                    <div>Выберите водителя</div>
                    {$driver_select}
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-primary" id="driverModalBtn">Сохранить</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                  </div>
                </div>
              </div>
            </div>
HTML;
        return [
            "template" => $template,
            "script" => "<script>{$script}</script>",
        ];
    }

    
    public function change_driver_modal(){
        $select_setting = [
            "mod" => "jsReturn",
            "name" => "driver",
            "select_id" => "driver_change",
            "ajaxUrl" => "getUsersToSelect",
            "return" => true,
        ];
        $script = oSelectCustom($select_setting);
        $select_setting["mod"] = "select";
        $driver_select = oSelectCustom($select_setting);
        $template = <<<HTML
            <div class="modal fade admin-form" id="changeDriverModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form onsubmit="return false;" id="changeDriverModalForm">
                        <div class="modal-header">
                          <h5 class="modal-title" id="">Смена водителя</h5>
                        </div>
                        <div class="modal-body">
                          <div>Выберите водителя</div>
                          {$driver_select}
                          <div id="changeDriverModalInput"></div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-primary" id="changeDriverModalBtn">Сменить</button>
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
HTML;
        return [
            "template" => $template,
            "script" => <<<HTML
                <script>
                    async function change_driver(){
                        const data = $("#changeDriverModalForm").serialize();
                        $("#changeDriverModal").modal("hide");
                        const lw = new LW;
                        lw.on();
                        await \$transport.change_driver(data);
                        await update_list();
                        lw.off();
                    }

                    {$script}
                </script>
HTML,
        ];
    }

    public function change_driver_btn($request){

    }

    public function change_driver_btn_source($request){
        $id = $request["id"];
        $current_driver = $request["current_driver"];
        return <<<HTML
            <button 
                type="button" 
                class="btn btn-xs btn-primary" 
                onclick="
                    $('#changeDriverModal').modal('show'); 
                    $('#changeDriverModalBtn').off();
                    $('#changeDriverModalBtn').on('click', function(){
                        change_driver();
                    });
                    $('#changeDriverModalInput').empty();
                    $('#changeDriverModalInput').append(`<input type='hidden' name='id' value='{$id}'>`);
                    $('#changeDriverModalInput').append(`<input type='hidden' name='current_driver' value='{$current_driver}'>`);
                "
            >Сменить водителя</button>
HTML;
    }

    public function driver_list_data_template($request){
        //["driver_list_data" => ""]
        $request['driver_list_data'] = empty($request['driver_list_data']) ? [] : $request['driver_list_data'];
        $driver_list = $this->driver_list_template($request);
        return $this->driver_template($driver_list);
    }

    public function driver_template($data){
        return <<<HTML
        <div class="info_item">
            <div class="info_item__line">
                <div class="info_item__title">Водитель:</div>
            </div>
        </div>
        <div class="info_item no_line">
            <div>
                {$data}
            </div>
        </div>
HTML;
    }

    public function driver_list_template($request){
        $result = [];
        $driver_list_data = $request['driver_list_data'];
        $driver_link_type = empty($request["driver_link_type"]) ? null :$request["driver_link_type"];
        switch($driver_link_type){
            default:
                $link = function($driver){
                    return "javascript:void(0);";
                };
                $target = "";
            break;
            case 1:
                $link = function($driver){
                    return "/administration/module/logistics/logistics_driver/detail/{$driver}";
                };
                $target = 'target="_blank"';
            break;
            case 2:
                $link = function($driver){
                    return "/administration/module/logistics/logistics_delivery_driver/detail/{$driver}";
                };
                $target = 'target="_blank"';
            break;
        }
        if(count($driver_list_data) > 0){
            foreach($driver_list_data as $r){
                $driver = $r["driver"];
                $driver_item = $this->driver_list_item($driver);
                $item_link = $link($driver);
                $result[] = <<<HTML
                    <a {$target} href="{$item_link}" class="driver-item" data-id="{$driver}">{$driver_item}</a>
HTML;
            }
        } else {
            $result[] = '<div>Заявки отсутствуют</div>';
        }
        return implode("", $result);
    }

    public function driver_list_item($driver){
        $avatar = avatar($driver);
        $name = fullUserName($driver);
        return <<<HTML
            <div class="mb20">
                <div class="d-flex align-items-center mb10">
                    <img src="{$avatar}" alt="driver_avatar" class="info_item__avatar">
                    <div>{$name}</div>
                </div>
            </div>
HTML;
    }

    public function lvp($value){
        return number_format($value,2,',',' ').' руб.';
    }

    static public function script(){
        $time = time();
        xeditableInit();
	    echo "<script src=\"/usr/other/sub/price/js/price.js?{$time}\"></script>";
	    echo "<script src=\"/usr/modules/logistics/admin/asset/js/function.js?{$time}\"></script>";
    }

    public function assign_driver_btn(){
        return <<<HTML
            <button class="btn btn-primary btn-block btn-xs" onclick="\$driverModal.open();">Назначить водителя</button>
HTML;
    }

    public function driver_detail_template($request){
        $driver = $request["driver"];
        $driver_item = $this->driver_list_item($driver);
        return $this->driver_template($driver_item);
    }
}