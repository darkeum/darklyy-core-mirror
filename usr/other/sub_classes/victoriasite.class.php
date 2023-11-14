<?php

class VictoriaSite extends \Victoria{
    public $title = "Подбор товаров";

    public $add_goods_modal_view = false;
    public $by_active_view = false;
    public $div_select_view = false;
    public $price_type = "2, 3";

    public $url_load_list = "/victoria?action=load_list";
    public $url_filter_data = "/victoria?action=filter_data";
    public $url_get_item = "/victoria?action=get_item";

    // public $default_order_field = "sd_quantity";
    // public $default_order_type = "DESC";

    public $transfer_to_document_text = 'Перенести в заказ';

    public $scripts = [
        0 => <<<HTML
            <script>
                \$victoria.change_total = function(id){
                    const link = this.get_link_basket(id);
                    const total = this.get_total_value(id);
                    link.total_container.html(Price.priceSet(total, " ₽"));
                };
            
                //\$victoria.transfer_to_document_update = opener.\$Victoria.afterClose;
                \$victoria.transfer_to_document_update = async () => {};
            </script>
HTML,
    ];

    protected $field_value_price_empty = "По запросу";

    protected $discount = true;

    protected $items_list = ["s1",1,2,3,4,"s2",7];
    protected $items_basket_list = [12,1,2,3,9,4,10,11];

    public function get_items_field(){
        $data = parent::get_items_field();
        $data["s1"] = [
            "name" => "<i class='fas fa-image'></i>",
            "sort" => false,
            "field" => "image",
            "grid" => "minmax(100px,1fr)",
            "align" => "center",
            "align_head" => "center",
            "function" => "field_value_image",
        ];
        $data["s2"] = [
            "name" => "Количество",
            "sort" => true,
            "field" => "sd_quantity",
            "grid" => "minmax(100px,1fr)",
            "align" => "center",
            "function" => "field_value_quantity",
        ];
        return $data;
    }

    public function popup_template_js(){
        $script = parent::popup_template_js();
        $script .= implode("", [
        //     '<script src="/usr/tpl/dev2/assest/js/priceList.js" charset="utf-8"></script>',
        //     '<script src="/usr/tpl/dev2/assest/js/jquery.arcticmodal-0.3.min.js" type="text/javascript" charset="utf-8"></script>',
        //     '<script src="/usr/tpl/dev2/assest/js/catalogView.js" charset="utf-8"></script>',
            '<script src="/usr/other/sub_classes/victoria/assets/js/victoria_site.js?{$this->version}" charset="utf-8"></script>',
        ]);
        return $script;
    }

    public function popup_template_css(){
        $style = parent::popup_template_css();
        $style .= implode("", [
            // '<link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/css/simple.css">',
            // '<link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/css/jquery.arcticmodal-0.3.css">',
            '<link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/victoria_site.css?{$this->version}">',
        ]);
        //<link rel="stylesheet" type="text/css" href="/usr/tpl/dev2/assest/css/style.css">
        return $style;
    }

    public function modal(){
        return <<<HTML
            <div class="modal fade" id="fast_view_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document" style="max-width: 95vw;">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Просмотр</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                      <div id="fast_view_modal_frame"></div>
                  </div>
                </div>
              </div>
            </div>
HTML;
    }

    public function grid_items_container(){
        $temp = parent::grid_items_container();
        $help = <<<HTML
            <!-- <div id="items_body_help"></div> -->
HTML;
        return $help.$temp;
    }

    public function field_value_action($request){
        $item = $request["item"];
        $cid = $item["c_id"];
        $v = <<<HTML
        <div class="btn-group" role="group">
            {$this->field_value_action_add_basket($request)}
            <button type="button" class="btn btn-warning btn-sm bs_tooltip text-white" 
            data-toggle="tooltip" 
            data-placement="top" 
            onclick="fast_view(this);" 
            title="Быстрый просмотр"
            data-src="/catalog_v2/fast_view_my/{$cid}"
            >
                <i class="fas fa-search-plus"></i>
            </button>
        </div>
HTML;
        return [
            "value" => $v,
        ];
    }

    public function main_query_where($request = []):Object{
        $data_where = parent::main_query_where($request);
        $data_where->where = "c.active = '1' AND c.category IS NOT NULL AND {$data_where->where}"; 
        return $data_where;
    }

    public function field_value_total($request){
        $data = $this->field_value_total_value($request);
        $total = $data["total"];
        $v = $this->price_format($total);

        return [
            "value" => $v,
        ];
    }

    public function field_value_image($request){
        $item = $request["item"];
        $src = empty($item["c_preview"]) ? "/files/no_photo.jpg" : $item["c_preview"];
        $v = "<img src='{$src}' width='50'>";
        return [
            "value" => $v,
        ];
    }

    public function field_value_quantity($request){
        $item = $request["item"];
        $cid = $item["c_id"];
        $v = get_ndr(["cid" => $cid, "stock_id" => 1, "module" => "", "pid" => "", "returnType" => "array"]);
        $class_name = $v["d"] > 0 ? "text-success" : "text-danger";
        return [
            "value" => "<span class=\"{$class_name}\">{$v['n']} (доступно {$v["d"]})</span>",
        ];
    }

}