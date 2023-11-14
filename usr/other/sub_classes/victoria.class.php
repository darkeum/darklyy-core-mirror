<?php

use \Boot\Support\Str;
use Boot\Models\Categories;
use Boot\Support\Facades\Config;

class Victoria
{
    public $link = "";

    //настройка окна
    public $width = 1400;                           //
    public $height = 840;                           //
    public $toolbar = 0;                            //
    public $menubar = 0;                            //
    public $location = 0;                           //
    public $status = 1;                             //
    public $scrollbars = 1;                         //
    public $resizable = 1;                          //
    public $left = 0;                               //
    public $top = 0;                                //

    public $title;                                  //заголовок

    public $url_load_list = "/ajax.php?do=victoria_load_list";
    public $url_filter_data = "/ajax.php?do=victoria_filter";
    public $url_get_item = "/ajax.php?do=victoria_get_item";

    public $button_value = "Добавить товар";        //название кнопки открытия Victoria

    public $new_sku = "";  //код нового артикула

    public $transfer_to_document_disabled = false;  //true/false разрешить/запретить перемещать в заказ
    public $add_basket_btn = true;                  //true/false разрешить/запретить перемещать в корзину

    public $transfer_to_document_view = true;        //true/false показывать/скрывать кнопку перемещения в заказ
    public $add_goods_modal_view = true;             //true/false показывать/скрывать кнопку создания товара
    public $by_active_view = true;                   //true/false показывать/скрывать checkbox активности
    public $div_select_view = true;                  //true/false показывать/скрывать div all select
    public $price_type_view = true;                  //true/false показывать/скрывать select типа цен
    public $stock_id_view = true;                    //true/false показывать/скрывать select складов

    public $search_value = "";                      //значение по умолчанию в поиске

    public $transfer_to_document_text = '<i class="fas fa-shopping-basket"></i>';

    public $stock_id = 1;                           //выбор склада по умолчанию
    public $price_type = 3;                         //выбор типа цены по умолчанию
    public $by_active = true;                       //true/false checkbox активности

    public $limit = 50;                            //лимит загрузки
    public $default_order_field = "c.id";
    public $default_order_type = "ASC";

    private $version;                               //версия для css и js файлов (time)

    public $id = false;
    public $url_add = false;
    public $url_update = false;
    public $update_body_selector = "#tov_list";
    public $local_script = null;
    public $scripts = [];

    protected $uid;

    protected $discount = false;
    public $oid = -1;

    protected $field_value_price_empty = "Отсутствует";

    protected $items_field = [];

    protected $items_list = [1, 2, 13, 3, 4, 5, 6, 7];
    protected $items_basket_list = [12, 1, 2, 3, 9, 5, 6, 4, 8, 10, 11];

    protected $attribute_field = "attribute";


    public function __construct()
    {
        global $core;
        $this->version = time();
        $this->items_field = $this->get_items_field();
        $this->prepare_items_field();
        $this->title = empty($this->title) ? get_class($this) : $this->title;
        $this->uid = $core->auth->user_info['id'] ?? false;
        $this->new_sku = \Catalog_v2\Model\Catalog::$NEW_SKU;
    }

    public function get_items_field()
    {
        return [
            1 => [
                "name" => "Наименование",
                "sort" => true,
                "field" => "c_title",
                "grid" => "minmax(150px,2fr)",
                "align" => "y",
                "function" => "field_value_title",
            ],
            2 => [
                "name" => "Артикул",
                "sort" => true,
                "field" => "c_sku",
                "grid" => "minmax(80px,1fr)",
                "grid_basket" => "minmax(100px,1fr)",
                "align" => "center",
                "function" => "field_value_default",
            ],
            3 => [
                "name" => "Производитель",
                "sort" => true,
                "field" => "cm_name",
                "grid" => "minmax(120px,1fr)",
                "align" => "center",
                "function" => "field_value_default",
            ],
            4 => [
                "name" => "Цена",
                "sort" => true,
                "field" => "cpd_price",
                "grid" => "minmax(100px,1fr)",
                "align" => "center",
                "function" => "field_value_price",
            ],
            5 => [
                "name" => "Н/Д/Р",
                "sort" => true,
                "field" => "sd_quantity",
                "grid" => "minmax(67px,1fr)",
                "align" => "center",
                "function" => "field_value_quantity",
            ],
            6 => [
                "name" => "Ед.изм.",
                "sort" => true,
                "field" => "cu_name",
                "grid" => "minmax(79px,1fr)",
                "align" => "center",
                "function" => "field_value_default",
            ],
            7 => [
                "name" => "Действия",
                "sort" => false,
                "field" => "action",
                "grid" => "minmax(100px,1fr)",
                "grid_basket" => "minmax(65px,1fr)",
                "align" => "center",
                "function" => "field_value_action",
            ],
            8 => [
                "name" => "Скидка",
                "sort" => false,
                "field" => "discount",
                "grid" => "minmax(100px,1fr)",
                "grid_basket" => "minmax(65px,1fr)",
                "align" => "center",
                "function" => "field_value_discount",
            ],
            9 => [
                "name" => "Кол-во",
                "sort" => false,
                "field" => "count",
                "grid" => "minmax(80px,1fr)",
                "grid_basket" => "minmax(65px,1fr)",
                "align" => "center",
                "function" => "field_value_count",
            ],
            10 => [
                "name" => "Сумма",
                "sort" => false,
                "field" => "total",
                "grid" => "minmax(80px,1fr)",
                "align" => "center",
                "function" => "field_value_total",
            ],
            11 => [
                "name" => "Действия",
                "sort" => false,
                "field" => "action",
                "grid" => "minmax(100px,1fr)",
                "grid_basket" => "minmax(65px,1fr)",
                "align" => "center",
                "function" => "field_value_action_basket",
            ],
            12 => [
                "name" => "№",
                "sort" => false,
                "field" => "position",
                "grid" => "minmax(30px,1fr)",
                "grid_basket" => "minmax(30px,1fr)",
                "align" => "center",
                "function" => "field_value_clear",
            ],
            13 => [
                "name" => "Артикул 2",
                "sort" => false,
                "field" => "c_sku2",
                "grid" => "minmax(80px,1fr)",
                "align" => "center",
                "function" => "field_value_default",
            ],
        ];
    }

    private function prepare_items_field()
    {
        $fields = ["name" => "", "sort" => false, "grid" => "", "field" => "", "align" => "", "align_head" => "", "grid_basket" => "", "function" => ""];
        $new_items_field = [];
        foreach ($this->items_field as $key => $val) {
            foreach ($fields as $k => $v) {
                $new_items_field[$key][$k] = isset($val[$k]) ? $val[$k] : $v;
            }
        }
        $this->items_field = $new_items_field;
    }

    public function open_func()
    {
        return "window.open(`{$this->link}`,'1600667945098','width={$this->width},height={$this->height},toolbar={$this->toolbar},menubar={$this->menubar},location={$this->location},status={$this->status},scrollbars={$this->scrollbars},resizable={$this->resizable},left={$this->left},top={$this->top}');return false;";
    }

    public function button()
    {
        return <<<HTML
            <button onclick="{$this->open_func()}" class="btn btn-success btn-xs">{$this->button_value}</button>
HTML;
    }

    public function popup()
    {
        $result = [];
        $result[] = $this->popup_template();
        return implode("", $result);
    }

    public function popup_template()
    {
        require_once(ROOT . "usr/modules/sales/admin/sub/ndr.php");
        $by_active = $this->by_active ? "checked" : "";
        $ndr_modal = ndr_modal_re();
        $ndr_style = ndr_style_re();
        $ndr_script = ndr_script_re();
        $transfer_to_document_disabled = $this->transfer_to_document_disabled ? "disabled" : "";
        $add_goods = $this->add_goods_modal();
        $transfer_to_document_btn = "";
        $add_goods_modal_btn = "";
        $by_active_checkbox = "";
        $div_select = "";
        $price_type_select = "";
        $stock_id_select = "";
        if ($this->transfer_to_document_view) {
            $transfer_to_document_btn = <<<HTML
            <button type="button"
            id="transfer_to_document"
            class="btn btn-success btn-sm transfer_to_document"
            onclick="\$victoria.transfer_to_document();"
            title="Перенести в документ"
            data-toggle="tooltip"
            {$transfer_to_document_disabled}>
                {$this->transfer_to_document_text}
            </button>
HTML;
        }
        if ($this->add_goods_modal_view) {
            $add_goods_modal_btn = <<<HTML
            <button type="button" class="btn btn-info btn-sm" title="Создать номенклатуру" data-toggle="modal" data-target="#addGoodsModal">
                <i class="fas fa-cart-plus"></i>
            </button>
HTML;
        }
        if ($this->by_active_view) {
            $by_active_checkbox = <<<HTML
                <div class="form-check ml-3">
                    <input type="checkbox" class="form-check-input search_ll" id="by_active" name="by_active" value="1" {$by_active}>
                    <label class="form-check-label" for="by_active">Только активная</label>
                </div>
HTML;
        }
        if ($this->div_select_view) {
            $div_select = [];
            $div_select[] = '<div class="select-group mr-4">';
            if ($this->price_type_view) {
                $catalog_price_type = \Price\Models\CatalogPriceType::where("hidden", 0)->get();
                $price_type = [];
                foreach ($catalog_price_type as $cpt) {
                    $selected = $this->price_type == $cpt->id ? "selected" : "";
                    $price_type[] = "<option value='{$cpt->id}' {$selected}>{$cpt->title}</option>";
                }
                $price_type = implode("", $price_type);
                $price_type_select = <<<HTML
                    <select class="form-control search_ll" id="price_type" name="price_type">{$price_type}</select>
HTML;
                $div_select[] = $price_type_select;
            }
            if ($this->stock_id_view) {
                $stock = get_stock_select_option();
                $stock_id_select = <<<HTML
                    <select class="form-control search_ll" id="stock_id" name="stock_id">{$stock}</select>
HTML;
                $div_select[] = $stock_id_select;
            }
            $div_select[] = '</div>';
            $div_select = implode("", $div_select);
        }
        $scripts = is_array($this->scripts) ? implode("", $this->scripts) : "";
        return <<<HTML
        <!DOCTYPE html>
        <html lang="ru">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>{$this->title}</title>
            <link rel="shortcut icon" href="/usr/other/sub_classes/victoria/assets/img/favicon.png" type="image/png">
            {$ndr_style}
            {$this->popup_template_css()}
        </head>
        <body>
            <form onsubmit="return false;" id="mainForm">
                <input type="hidden" id="page" name="page" value="">
                <input type="hidden" id="id" name="id" value="{$this->id}">
                <input type="hidden" id="url_add" value="{$this->url_add}">
                <input type="hidden" id="url_update" value="{$this->url_update}">
                <input type="hidden" id="update_body_selector" value="{$this->update_body_selector}">
                <input type="hidden" id="new_sku" value="{$this->new_sku}">
                <div class="grid">
                    <div class="grid__input card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center">
                                <div class="btn-group mr-4" role="group">
                                    {$transfer_to_document_btn}
                                    {$add_goods_modal_btn}
                                </div>
                                {$div_select}
                                <div class="d-flex checkbox-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input search_ll" id="by_exact_match" name="by_exact_match" value="1">
                                        <label class="form-check-label" for="by_exact_match">По точному совпадению</label>
                                    </div>
                                    <div class="form-check ml-3">
                                        <input type="checkbox" class="form-check-input search_ll" id="by_catalog" name="by_catalog" value="1">
                                        <label class="form-check-label" for="by_catalog">По каталогу</label>
                                    </div>
                                    {$by_active_checkbox}
                                </div>
                            </div>

                            <div class="input-group mt-3">
                                <input type="text" placeholder="Введите наименование товара или артикул" class="form-control" id="search" name="search" value="{$this->search_value}">
                                <div class="input-group-append btn_input_search">
                                    <button class="btn btn-outline-info fas fa-search" type="button" title="Найти" onclick="\$victoria.ll('first');" tabindex="0"></button>
                                    <button class="btn btn-outline-danger fas fa-times" type="button" title="Очистить" onclick="\$victoria.clear_search();"></button>
                                    <button class="btn btn-outline-secondary fas fa-times" type="button" title="Очистить фильтр" onclick="\$victoria.clear_filter();"></button>
                                    <button class="btn btn-outline-secondary fas fa-arrow-left open_filter" type="button" title="Открыть фильтр"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid__items card">
                        {$this->grid_items_container()}
                    </div>
                    <div class="grid__items_basket card">
                        <div class="card-body grid__items_body">
                            {$this->grid_items_basket()}
                        </div>
                    </div>
                    <div class="grid__categories card" id="categories">
                        <div class="card-body categories_body h-100">
                            {$this->categories()}
                        </div>
                    </div>
                    <div class="grid__filter card">
                        {$this->css_load("cssload_filter_loader")}
                        <div class="card-body" id="filter"></div>
                    </div>
                    {$this->fountainG("fountainG")}
                </div>
                <div id="mobile_filter">
                    <div class="panel" id="mobile_filter_panel" onclick="event.stopPropagation();">
                        <div class="panel-body">
                            <button class="btn btn-outline-secondary close_filter close_filter_btn"><i class="fas fa-arrow-right"></i></button>
                        </div>
                    </div>
                </div>
            </form>
            {$this->cert_modal()}
            {$this->attribute_modal()}
            {$add_goods["modal"]}
            {$ndr_modal}
            {$this->popup_template_js()}
            {$add_goods["script"]}
            {$ndr_script}
            <script>
                \$victoria.url_load_list = "{$this->url_load_list}";
                \$victoria.url_filter_data = "{$this->url_filter_data}";
                \$victoria.url_get_item = "{$this->url_get_item}";
            </script>
            <script>{$this->local_script}</script>
            {$scripts}
            {$this->modal()}
        </body>
        </html>
HTML;
    }

    public function modal()
    {
    }

    public function fountainG($id)
    {
        return <<<HTML
            <div class="fountainGMain" id="{$id}">
                <div class="fountainG fountainG_1"></div>
                <div class="fountainG fountainG_2"></div>
                <div class="fountainG fountainG_3"></div>
                <div class="fountainG fountainG_4"></div>
                <div class="fountainG fountainG_5"></div>
                <div class="fountainG fountainG_6"></div>
                <div class="fountainG fountainG_7"></div>
                <div class="fountainG fountainG_8"></div>
            </div>
HTML;
    }

    public function css_load($id)
    {
        return <<<HTML
            <div id="{$id}" class="cssload-container-loader">
                <div class="cssload-loader">
                    <div class="cssload-inner cssload-one"></div>
                    <div class="cssload-inner cssload-two"></div>
                    <div class="cssload-inner cssload-three"></div>
                </div>
            </div>
HTML;
    }

    public function modal_generator($request)
    {
        $name = $request["name"];
        $title = $request["title"];
        return <<<HTML
            <div class="modal fade" id="{$name}Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{$title}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div id="{$name}Body"></div>
                  </div>
                </div>
              </div>
            </div>
HTML;
    }

    public function cert_modal()
    {
        return $this->modal_generator(["name" => "cert", "title" => "Сертификаты"]);
    }

    public function attribute_modal()
    {
        return $this->modal_generator(["name" => "attribute", "title" => "Атрибуты"]);
    }

    public function add_goods_modal()
    {
        $catalog_unit = [];
        foreach (\Catalog_v2\Model\CatalogUnit::all() as $item) {
            $catalog_unit[] = "<option value='{$item->unit}'>{$item->name}</option>";
        }
        $catalog_unit = implode("", $catalog_unit);

        $category = [];
        // $cats_arr = $core->aCatList('catalog_v2');
        // $category[] = '<option></option>';
        // foreach($cats_arr as $cid => $name){
        // 	$category[] = '<option value="'.$cid.'">'.$name.'</option>';
        // }
        $category = implode("", $category);

        $script = [];

        $select_man_setting = [
            "mod" => "jsReturn",
            "name" => "man",
            "ajaxUrl" => "getCatalogManufactureToSelect",
            "return" => true,
            "required" => true,
        ];
        $script[] = "<script>" . oSelectCustom($select_man_setting) . "</script>";
        $select_man_setting["mod"] = "select";
        $select_man = oSelectCustom($select_man_setting);

        $script = implode(" ", $script);
        $modal = <<<HTML
            <div class="modal fade" id="addGoodsModal" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form action="#" method="POST" id="createGoods">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Добавление номенклатуры</h5>
                            </div>
                            <div class="modal-body admin-form">
                                <div class="form-group">
                                    <label for="title">Наименование:</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Введите наименование" required>
                                </div>
                                <!-- <div class="form-group"> -->
                                    <!-- <label for="cat">Категория:</label> -->
                                    <!-- <select class="form-control" id="cat" name="category" required>{$category}</select> -->
                                    <input type="hidden" name="category" value="532">
                                <!-- </div> -->
                                <div class="form-group">
                                    <label for="sku">Артикул:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="sku" name="sku" placeholder="Введите артикул" required>
                                        <span class="input-group-append">
                                            <button type="button" class="btn" onclick="\$victoria.sku_generate();"><i class="fas fa-sync-alt"></i></button>
                                        </span>
                                    </div>
                                </div>
                                <div id="infoSku" style="display:none;"></div>

                                <div class="form-group">
                                    <label for="man">Производитель:</label>
                                    {$select_man}
                                </div>
                                <div class="form-group">
                                    <label for="unit">Единицы измерения:</label>
                                    <select class="form-control select2_form" id="unit" name="unit" required>
                                        {$catalog_unit}
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="types">Тип номенклатуры:</label>
                                    <select class="form-control select2_form" id="types" name="types" required>
                                        <option value="3" selected>Товары</option>
                                        <option value="4">Услуги</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success btn-sm" id="createGoodsBtn">Создать</button>
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Закрыть</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
HTML;
        return [
            "modal" => $modal,
            "script" => $script,
        ];
    }

    public function popup_template_css()
    {
        return <<<HTML
            <link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/bootstrap.min.css">
            <link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/fa.min.css">
            <link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/select2.min.css">
            <link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/fountainG.css">
            <link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/cssload.css">
            <link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/jquery-ui.min.css">
            <link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/sweetalert2.min.css">
            <link rel="stylesheet" href="/usr/tpl/corp/assets/css/admin-forms.css">
            <link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/categories.css?{$this->version}">
            <link rel="stylesheet" href="/usr/other/sub_classes/victoria/assets/css/victoria.css?{$this->version}">
            {$this->popup_template_style()}
HTML;
    }

    public function popup_template_style()
    {
        $list = $this->grid_items_list();
        $list_basket = $this->grid_items_basket_list();
        $grid = [];
        $grid_basket = [];
        $align = [];
        $before_name = [];
        //all
        foreach ($this->items_field as $item) {
            $class_name = "." . $item["field"];
            if (!empty($item["align"])) {
                $align[$item["align"]][] = $class_name;
            }
            $before_name[] = "{$class_name}::before{content:'{$item['name']}: '; font-weight:bold;}";
        }
        //list
        foreach ($list as $item) {
            $grid[] = $item["grid"];
        }
        //basket
        foreach ($list_basket as $item) {
            $grid_basket[] = empty($item["grid_basket"]) ? $item["grid"] : $item["grid_basket"];
        }
        $grid = implode(" ", $grid);
        $grid_basket = implode(" ", $grid_basket);
        $flex = [];
        foreach ($align as $item) {
            foreach ($item as $i) {
                $flex[] = $i;
            }
        }
        $flex = implode(", ", $flex);
        $align_center = empty($align["center"]) ? [] : $align["center"];
        $align_center = implode(", ", $align_center);

        $align_x = empty($align["x"]) ? [] : $align["x"];
        $align_x = implode(", ", $align_x);

        $align_y = empty($align["y"]) ? [] : $align["y"];
        $align_y = implode(", ", $align_y);

        $before_name = implode(" ", $before_name);
        $head_width = "17px";
        return <<<HTML
        <style>
            .items_head {
                grid-template-columns: {$grid} {$head_width};
            }
            .items_basket_head{
                grid-template-columns: {$grid_basket} {$head_width};
            }
            .items_body_item{
                grid-template-columns: {$grid};
            }
            .items_basket_body_item{
                grid-template-columns: {$grid_basket};
            }
            {$flex}{
                display: flex;
            }
            {$align_center}{
                justify-content: center;
                align-items: center;
            }
            {$align_x}{
                justify-content: center;
            }
            {$align_y}{
                align-items: center;
            }
            @media only screen and (max-width: 960px) {
                .grid_table_head{
                    display: none;
                }
                .items_body_item, .items_basket_body_item{
                    grid-template-columns: none!important;
                }
                .grid_table{
                    border-bottom: 1px #eee solid;
                }
                .grid_table>*{
                    border: none;
                }
                .checkbox-group{
                    margin-top: 10px;
                }
                {$align_center}{
                    display: block;
                    justify-content: unset;
                }
                {$before_name}
            }
        </style>
HTML;
    }

    public function popup_template_js()
    {
        return <<<HTML
            <script src="/usr/other/sub_classes/victoria/assets/js/jquery-3.5.1.min.js"></script>
            <script src="/usr/other/sub_classes/victoria/assets/js/bootstrap.min.js"></script>
            <!--<script src="/usr/other/sub_classes/victoria/assets/js/select2.min.js"></script>-->
            <script src="/vendors/js/forms/select/select2.full.min.js"></script>
            <script src="/vendors/js/forms/select/i18n/ru.js"></script>
            <script src="/usr/other/sub_classes/victoria/assets/js/jquery-ui.min.js"></script>
            <script src="/usr/other/sub_classes/victoria/assets/js/sweetalert2.min.js"></script>
            <script src="/usr/other/sub/price/js/price.js"></script>
            <script src="/usr/other/sub_classes/victoria/assets/js/victoria.js?{$this->version}"></script>
HTML;
    }

    public function grid_items_container()
    {
        return <<<HTML
            {$this->css_load("cssload_loader")}
            <div class="card-body grid__items_body" id="grid__items">
                {$this->grid_items()}
            </div>
HTML;
    }

    public function grid_items()
    {
        // $grid_items_body = $this->grid_items_body(["by_active" => $this->by_active]);
        // {$grid_items_body["template"]}
        return <<<HTML
            <div class="grid_table grid_table_head items_head">
                {$this->grid_items_head()}
            </div>
            <div class="items_body" id="items_body"></div>
HTML;
    }

    public function grid_items_basket()
    {
        return <<<HTML
            <div class="grid_table grid_table_head items_basket_head">
                {$this->grid_items_basket_head()}
            </div>
            <div class="items_basket_body" id="items_basket_body"></div>
HTML;
    }

    public function grid_items_list()
    {
        $result = [];
        $data = $this->items_field;
        foreach ($this->items_list as $v) {
            $result[] = $data[$v];
        }
        return $result;
    }

    public function grid_items_basket_list()
    {
        $result = [];
        $data = $this->items_field;
        foreach ($this->items_basket_list as $v) {
            $result[] = $data[$v];
        }
        return $result;
    }

    public function grid_items_head()
    {
        $head = $this->grid_items_list();
        $result = [];
        foreach ($head as $item) {
            $result[] = $this->grid_items_head_template($item);
        }
        $result[] = '<div></div>';
        return implode("", $result);
    }

    public function grid_items_basket_head()
    {
        $head = $this->grid_items_basket_list();
        $result = [];
        foreach ($head as $item) {
            $result[] = $this->grid_items_basket_head_template($item);
        }
        $result[] = '<div></div>';
        return implode("", $result);
    }

    public function grid_items_head_template($item)
    {
        $name = $item["name"];
        $sort = $item["sort"] ? "<div>{$this->grid_items_sort($item["field"])}</div>" : "";
        return <<<HTML
        <div class="d-flex align-items-center justify-content-between">
            <div style="flex: 1; text-align: {$item['align_head']};">{$name}</div>
            {$sort}
        </div>
HTML;
    }

    public function grid_items_basket_head_template($item)
    {
        $name = $item["name"];
        return <<<HTML
        <div class="d-flex align-items-center justify-content-between">
            <div>{$name}</div>
        </div>
HTML;
    }

    public function grid_items_sort($field)
    {
        return <<<HTML
            <div class="d-flex flex-column">
                <div class="sortLabel" onclick="\$victoria.change_sort(this);">
                    <i class="fas fa-sort-up sortIcon"></i>
                    <input type="radio" value="DESC" name="order_type" class="d-none">
                    <input type="radio" value="{$field}" name="order_field" class="d-none">
                </div>
                <div class="sortLabel" onclick="\$victoria.change_sort(this);">
                    <i class="fas fa-caret-down sortIcon"></i>
                    <input type="radio" value="ASC" name="order_type" class="d-none">
                    <input type="radio" value="{$field}" name="order_field" class="d-none">
                </div>
            </div>
HTML;
    }

    public function grid_items_body($request = [])
    {
        $result = [];
        $data = $this->grid_items_body_items($request);
        $items = $data["items"];
        if ($data["count"] > 0) {
            foreach ($items as $item) {
                $result[] = $this->grid_items_body_item($item);
            }
        }
        $data["template"] = $data["count"] > 0 ? implode("", $result) : "Ничего не найдено!";
        return $data;
    }

    public function grid_items_basket_body($request = [])
    {
        //["id"]
        $result = [];
        $data = $this->grid_items_basket_body_items($request);
        $items = $data["items"];
        if ($data["count"] > 0) {
            foreach ($items as $item) {
                $result[] = $this->grid_items_basket_body_item($item);
            }
        }
        $data["template"] = implode("", $result);
        return $data;
    }

    private function main_query($request = []):Object
    {
        $where = [];
        $price_type = empty($request["price_type"]) ? $this->price_type : $request["price_type"];
        $stock_id = empty($request["stock_id"]) ? $this->stock_id : $request["stock_id"];

        $data_where = $this->main_query_where($request);
        $where = $data_where->where;
        $params = $data_where->params;

        switch ($price_type) {
            default:
            case 3:
                $cpd_on = "cpd.id = cpte.price_price";
                $cpt_on = "cpt.id = cpte.price_type_price";
                break;
            case 1:
                $cpd_on = "cpd.id = cpte.price_purchase";
                $cpt_on = "cpt.id = cpte.price_type_purchase";
                break;
        }

        $discount_field = ", ROUND((cpd.sum * cpt.course), 2) as cpd_price";
        $discount_table = "";
        if ($this->discount) {
            $discount_query = \Price::get_discount_query($this->oid);
            $discount_table = "LEFT JOIN (
                {$discount_query}
            ) as cg ON(c.id = cg.cid)";
            $discount_field = ", ROUND( IF(cg.discount IS NULL, cpd.sum, (cpd.sum * (1 - cg.discount / 100)) ) * cpt.course, 2) as cpd_price";
        }

        $dbp = Config::get('database.connections.atrienergo.prefix');
        $query = <<<HTML
            SELECT cu.name as cu_name, cm.name as cm_name, sd.sum as sd_quantity, sd.stock as sd_stock,
            cpt.id as cpd_price_type,
            c.id as c_id, c.title as c_title, c.sku as c_sku, c.{$this->attribute_field} as c_{$this->attribute_field}, c.unit as c_unit, c.man as c_man, c.category as c_category, c.preview as c_preview   {$discount_field}
            FROM `{$dbp}catalog` as c
            LEFT JOIN `{$dbp}catalog_manufacture` as cm ON(cm.id = c.man)
            LEFT JOIN `{$dbp}catalog_unit` as cu ON(cu.unit = c.unit)
            LEFT JOIN `{$dbp}catalog_price_temp` as cpte ON(cpte.id = c.id)
            LEFT JOIN `{$dbp}catalog_price_detail` as cpd ON({$cpd_on})
            LEFT JOIN `{$dbp}catalog_price_type` as cpt ON({$cpt_on})
            LEFT JOIN (
                SELECT * FROM `{$dbp}stock_detail` WHERE stock = {$stock_id}
            ) as sd ON(sd.cid = c.id)
            {$discount_table}
            WHERE {$where}
            GROUP BY c.id
HTML;
        $query = str_replace("\r\n", "", $query);
        $query = str_replace("           ", "", $query);
        return (object)[
            "query" => $query,
            "params" => $params,
        ];
    }

    public function main_query_where($request = []):Object
    {
        $where = [];
        $limit_id = isset($request["limit_id"]) ? $request["limit_id"] : false;
        $filter = isset($request["filter"]) ? $request["filter"] : false;

        $where[] = "c.hidden = '0'";
        $params = [];
        if (!$limit_id) {
            $data_search = $this->main_query_search($request);
            $search = $data_search->where;
            $params = $data_search->params;
            if ($search) $where[] = $search;

            if (isset($request["by_active"])) {
                $where[] = "c.active = '1'";
            }

            if (isset($request["by_catalog"]) || $filter) {
                $category = $this->main_query_category($request);
                if ($category) $where[] = $category;
            }

            if (!$filter) {
                $attribute = isset($request["attribute"]) ? $request["attribute"] : [];
                if (is_array($attribute) && count($attribute) > 0) {
                    foreach ($attribute as $key => $val) {
                        $w = [];
                        if (count($val) > 0) {
                            foreach ($val as $v) {
                                $w[] = "JSON_VALUE(c.{$this->attribute_field}, '$.{$key}') LIKE '{$v}'";
                            }
                        }
                        $w = implode(" OR ", $w);
                        $where[] = "({$w})";
                    }
                }

                $man = isset($request["man"]) ? $request["man"] : [];
                if (is_array($man) && count($man) > 0) {
                    $man = implode(", ", $man);
                    $where[] = "cm.id IN({$man})";
                }
            }
        }
        if (isset($request["where"]) && is_array($request["where"]) && count($request["where"]) > 0) {
            foreach ($request["where"] as $val) {
                $where[] = $val;
            }
        }
        return (object)[
            "where" => count($where) > 0 ? implode(" AND ", $where) : "TRUE",
            "params" => $params,
        ];
    }

    private function main_query_category_check($request = [])
    {
        return empty($request["categories_id"]) ? false : $request["categories_id"];
    }

    private function main_query_category($request = [])
    {
        $categories_id = $this->main_query_category_check($request);
        if ($categories_id) {
            $categories = get_all_categories_catalog();
            $categories_list = get_children_category($categories_id, $categories);
            $searchCatWhereArr = [];
            foreach ($categories_list as $cl) {
                $searchCatWhereArr[] = "c.category = '," . $cl . ",'";
            }
            $where = "(" . implode(" OR ", $searchCatWhereArr) . ")";
            return $where;
        } else {
            return false;
        }
    }

    private function main_query_search($request = []):Object
    {
        $search = empty($request["search"]) ? "" : $request["search"];
        $params = [];
        if (empty($search)) {
            $where = false;
        } else {
            $where = [];
            if (isset($request["by_exact_match"])) {
                $w0 = $this->main_query_search_field_by_exact_match($search);
                $w1 = $this->main_query_search_field_by_exact_match(Str::switcher($search, 0));
                $w2 = $this->main_query_search_field_by_exact_match(Str::switcher($search, 1));
                $where[] = "({$w0->where} OR {$w1->where} OR {$w2->where})";
                $params = $params + $w0->params + $w1->params + $w2->params;
            } else {
                $searchMas = explode(" ", $search);
                foreach ($searchMas as $val) {
                    $w0 = $this->main_query_search_field($search, $val);
                    $w1 = $this->main_query_search_field(Str::switcher($search, 0), Str::switcher($val, 0));
                    $w2 = $this->main_query_search_field(Str::switcher($search, 1), Str::switcher($val, 1));
                    $where[] = "({$w0->where} OR {$w1->where} OR {$w2->where})";
                    $params = $params + $w0->params + $w1->params + $w2->params;
                }
            }
            $where = implode(" AND ", $where);
        }
        return (object)[
            "where" => $where,
            "params" => $params,
        ];
    }

    private function main_query_search_field_by_exact_match($search):Object
    {
        $search = $this->prepare_search_value($search);

        $field = [];
        for($i = 0; $i < 6; $i++){
            $field[] = ":".uniqid();
        }

        return (object)[
            "where" => "(
                c.title LIKE {$field[0]} OR
                c.sku LIKE {$field[1]} OR
                c.sku2 LIKE {$field[2]} OR
                c.sku3 LIKE {$field[3]} OR
                c.id LIKE {$field[4]} OR
                cm.name LIKE {$field[5]}
            )",
            "params" => [
                $field[0] => $search,
                $field[1] => $search,
                $field[2] => $search,
                $field[3] => $search,
                $field[4] => $search,
                $field[5] => "%{$search}%"
            ],
        ];
    }

    private function main_query_search_field($search, $val):Object
    {
        $val = $this->prepare_search_value($val);
        $search = $this->prepare_search_value($search);

        $field = [];
        for($i = 0; $i < 6; $i++){
            $field[] = ":".uniqid();
        }

        return (object)[
            "where" => "(
                c.title LIKE {$field[0]} OR
                c.sku LIKE {$field[1]} OR
                c.sku2 LIKE {$field[2]} OR
                c.sku3 LIKE {$field[3]} OR
                c.id LIKE {$field[4]} OR
                cm.name LIKE {$field[5]}
            )",
            "params" => [
                $field[0] => "%{$val}%",
                $field[1] => "%{$val}%",
                $field[2] => "%{$val}%",
                $field[3] => "%{$val}%",
                $field[4] => $search,
                $field[5] => "%{$search}%",
            ],
        ];
    }

    private function prepare_search_value($value)
    {
        $new_value = $value;
        $new_value = trim($new_value);
        // $new_value = str_replace("\\", "\\\\", $new_value);

        return $new_value;
    }

    private function get_grid_items(String $query_ = "", Array $params = []):Array
    {
        $result = [];
        $count = 0;
        if (!empty($query_)) {
            $items = \DB::select($query_, $params);

            $count = count($items);
            if ($count > 0) {
                foreach ($items as $value) {
                    $result[] = (array) $value;
                }
            }
        }

        return [
            "items" => $result,
            "query" => $query_,
            "count" => $count,
        ];
    }

    public function grid_items_body_items($request = [])
    {
        $where = [];

        $order_field = empty($request["order_field"]) ? $this->default_order_field : $request["order_field"];
        $order_type = empty($request["order_type"]) ? $this->default_order_type : $request["order_type"];
        $page = empty($request["page"]) ? 1 : $request["page"];
        $page--;
        $limit = $page * $this->limit;

        $request["where"] = $where;
        $order_by = "{$order_field} {$order_type}";
        if ($order_field == "cpd_price" && $order_type == "ASC") {
            $max_value = "2147483647";
            $order_by = "CASE
                WHEN ({$order_field} = 0 OR {$order_field} IS NULL) THEN {$max_value}
                ELSE {$order_field} END";
        }
        $query_data = $this->main_query($request);
        $query_ =  $query_data->query. " ORDER BY {$order_by}
            LIMIT {$limit}, {$this->limit}";

        return $this->get_grid_items($query_, $query_data->params);
    }

    public function grid_items_basket_body_items($request = [])
    {
        $where = [];

        $id = $request["id"];
        $id = is_array($id) ? implode(", ", $id) : $id;
        $where[] = "c.id IN({$id})";

        $request["where"] = $where;
        $request["price_fix"] = true;
        $request["limit_id"] = true;
        $query_data = $this->main_query($request);

        return $this->get_grid_items($query_data->query, $query_data->params);
    }

    public function filter_items($request = [])
    {
        $request["filter"] = true;
        $data_where = $this->main_query_where($request);
        $query_ = "SELECT c.*
        FROM `" . Config::get('database.connections.atrienergo.prefix') . "catalog` as c
        LEFT JOIN `" . Config::get('database.connections.atrienergo.prefix') . "catalog_manufacture` as cm ON(cm.id = c.man)
        WHERE {$data_where->where}";

        $data = $this->get_grid_items($query_, $data_where->params);
        return $data;
    }

    public function grid_items_body_item($item)
    {
        $body = $this->grid_items_list();
        $result = [];
        $result[] = <<<HTML
            <div class='grid_table items_body_item' id="item_{$item['c_id']}" data-id="{$item['c_id']}">
HTML;
        foreach ($body as $b) {
            $result[] = $this->grid_items_body_item_template(["item" => $item, "fd" => $b]);
        }
        $result[] = "</div>";
        return implode("", $result);
    }

    public function grid_items_basket_body_item($item)
    {
        $body = $this->grid_items_basket_list();
        $result = [];
        $result[] = <<<HTML
            <div class='grid_table items_basket_body_item' id="item_basket_{$item['c_id']}" data-id="{$item['c_id']}">
                <input type="hidden" name="items[{$item['c_id']}][cid]" value="{$item['c_id']}">
HTML;
        foreach ($body as $b) {
            $result[] = $this->grid_items_body_item_template(["item" => $item, "fd" => $b]);
        }
        $result[] = "</div>";
        return implode("", $result);
    }

    public function grid_items_body_item_template($request)
    {
        $class_name = [];
        $fd = $request["fd"];
        $class_name[] = $fd["field"];
        $value_func = $fd["function"];
        $data = $this->$value_func($request);
        $class_name[] = empty($data["class_name"]) ? "" : $data["class_name"];
        $value = $data["value"];
        $class_name = implode(" ", $class_name);
        $onclick = empty($data["onclick"]) ? null : "onclick=\"{$data['onclick']}\"";
        $title = empty($data["title"]) ? null : $data['title'];
        return <<<HTML
            <div class="{$class_name}" {$onclick} title="{$title}">{$value}</div>
HTML;
    }

    public function field_value_default($request)
    {
        $item = $request["item"];
        $fd = $request["fd"];
        $field = isset($fd["field"]) ? $fd["field"] : "";
        $value = isset($item[$field]) ? $item[$field] : "";
        $v = empty($value) ? "Не указано" : ee($value);
        return [
            "value" => $v,
        ];
    }

    public function field_value_clear($request)
    {
        return [
            "value" => "",
        ];
    }

    public function field_value_title($request)
    {
        $item = $request["item"];
        $fd = $request["fd"];
        $cid = $item["c_id"];
        $field = isset($fd["field"]) ? $fd["field"] : "";
        $value = isset($item[$field]) ? $item[$field] : "";
        $v = empty($value) ? "Не указано" : ee($value);
        return [
            "value" => $v,
            "title" => "id: {$cid}",
        ];
    }

    private function get_price_field($item, $value)
    {
        $cid = $item["c_id"];
        $value = empty($value) ? 0 : $value;
        return $value;
    }

    public function price_format($value)
    {
        $postfix = "&#8381;";
        return number_format($value, 2, '.', ' ') . " " . $postfix;
    }

    public function field_value_price($request)
    {
        $item = $request["item"];
        $fd = $request["fd"];
        $field = isset($fd["field"]) ? $fd["field"] : "";
        $value = isset($item[$field]) ? $item[$field] : "";
        $v = $this->get_price_field($item, $value);
        if (empty($v) || $v == 0) {
            $v = $this->field_value_price_empty;
        } else {
            $v = $this->price_format($value);
        }
        return [
            "value" => $v,
        ];
    }

    public function field_value_quantity($request)
    {
        $item = $request["item"];
        $cid = $item["c_id"];
        $class_name = [];
        $v = get_ndr(["cid" => $cid, "stock_id" => $item["sd_stock"], "module" => "", "pid" => "", "returnType" => "view"]);
        $class_name[] = "ndr";
        return [
            "value" => $v,
            "class_name" => implode(" ", $class_name),
            "onclick" => "view_ndr_modal({$cid});",
        ];
    }

    public function field_value_action_add_basket($request)
    {
        $item = $request["item"];
        $cid = $item["c_id"];
        $category = str_replace(",", "", $item["c_category"]);
        $add_basket_btn = "\$victoria.add_basket({$cid});";
        if (empty($category) && $this->add_basket_btn) {
            $add_basket_btn = "\$victoria.add_basket_error('Не указана категория');";
        }
        $icon = "fas fa-plus";
        return <<<HTML
            <button type="button" class="btn btn-success btn-sm add_basket" title="Добавить в корзину" onclick="{$add_basket_btn}" ondblclick="return false;" id="add_basket_{$cid}">
                <i class="{$icon}" data-icon="{$icon}" id="add_basket_icon_{$cid}"></i>
            </button>
HTML;
    }

    public function field_value_action_cert_modal($request)
    {
        $item = $request["item"];
        $cid = $item["c_id"];
        return <<<HTML
            <button type="button" class="btn btn-info btn-sm" onclick="\$cert_modal.show({$cid});"><i class="fas fa-file-alt"></i></button>
HTML;
    }

    public function field_value_action_attribute_modal($request)
    {
        $item = $request["item"];
        $cid = $item["c_id"];
        return <<<HTML
            <button type="button" class="btn btn-warning btn-sm text-white" onclick="\$attribute_modal.show({$cid});"><i class="fas fas fa-cog"></i></button>
HTML;
    }

    public function field_value_action($request)
    {
        $v = <<<HTML
        <div class="btn-group" role="group">
            {$this->field_value_action_add_basket($request)}
            {$this->field_value_action_cert_modal($request)}
            {$this->field_value_action_attribute_modal($request)}
        </div>
HTML;
        return [
            "value" => $v,
        ];
    }

    public function field_value_action_basket($request)
    {
        $item = $request["item"];
        $cid = $item["c_id"];
        $v = <<<HTML
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-danger btn-sm fas fa-times" title="Удалить" onclick="\$victoria.delete_basket({$cid});"></button>
        </div>
HTML;
        return [
            "value" => $v,
        ];
    }

    public function field_value_discount($request)
    {
        $item = $request["item"];
        $fd = $request["fd"];
        $cid = $item["c_id"];
        $field = isset($fd["field"]) ? $fd["field"] : "";
        $v = <<<HTML
            <input type="number" value="0" class="form-control {$field}_inp" name="items[{$cid}][{$field}]" onchange="\$victoria.change_total({$cid});">
HTML;

        return [
            "value" => $v,
        ];
    }

    public function field_value_count($request)
    {
        $item = $request["item"];
        $fd = $request["fd"];
        $cid = $item["c_id"];
        $field = isset($fd["field"]) ? $fd["field"] : "";
        $v = <<<HTML
            <input type="number" value="1" class="form-control {$field}_inp" name="items[{$cid}][{$field}]" onchange="\$victoria.change_total({$cid});" min="1">
HTML;

        return [
            "value" => $v,
        ];
    }

    public function field_value_total_value($request)
    {
        $item = $request["item"];
        $value = $item["cpd_price"];
        $total = $this->get_price_field($item, $value);
        return ["total" => $total];
    }

    public function field_value_total($request)
    {
        $fd = $request["fd"];
        $field = isset($fd["field"]) ? $fd["field"] : "";
        $data = $this->field_value_total_value($request);
        $total = $data["total"];
        $v = <<<HTML
            <input type="number" value="{$total}" class="form-control {$field}_inp">
HTML;

        return [
            "value" => $v,
        ];
    }

    public function categories()
    {
        $result = "";
        $cats = $this->categories_list();
        $result = $this->categories_tree_ui($cats, 0);
        return $result;
    }

    public function categories_list()
    {
        $result = [];
        $data = \Catalog_v2\Model\Categories::where("module", "catalog_v2")->where("hidden", 0)->orderBy("name")->get()->toArray();
        if (count($data) > 0) {
            foreach ($data as $cat) {
                $result[$cat['parent_id']][$cat['id']] = $cat;
            }
        }
        return $result;
    }

    private function categories_tree($cats, $parent_id, $request, $check = false)
    {
        //["parent_ul" => "", "children_ul" => "", "folder" => "", "folder_empty" => ""]
        $parent_ul = empty($request["parent_ul"]) ? "parent_ul" : $request["parent_ul"];
        $children_ul = empty($request["children_ul"]) ? "children_ul" : $request["children_ul"];
        $f = empty($request["folder"]) ? "folder" : $request["folder"];
        $fe = empty($request["folder_empty"]) ? "folder_empty" : $request["folder_empty"];
        if (is_array($cats) && isset($cats[$parent_id])) {
            if ($check) {
                $tree = '<ul class="' . $parent_ul . ' p-0 h-100">';
            } else {
                $tree = <<<HTML
                    <ul class="{$children_ul}" id="{$children_ul}_{$parent_id}">
                        <li class="{$fe} d-flex align-items-center cp" onclick="\$victoria.hide_categories({$parent_id});">
                            <div class="point_item justify-content-start">
                                <div class="arrowOpen mr-3"><i class="fas fa-caret-left"></i></div>
                                <div>Назад</div>
                            </div>
                        </li>
HTML;
            }
            foreach ($cats[$parent_id] as $cat) {
                $id = $cat['id'];
                $t = $this->categories_tree($cats, $id, $request);
                $ff = empty($t);
                $folder = $ff ? $fe : $f;
                $arrowOpen = <<<HTML
                    <div class="arrowOpen" onclick="\$victoria.open_categories({$id});" id="arrowOpen_{$id}"><i class="fas fa-caret-right"></i></div>
HTML;
                $arrowOpenItem = $ff ? "" : $arrowOpen;
                $pointClick = $ff ? '$victoria.end_categories();' : '';
                $tree .= <<<HTML
                    <li class="{$folder} d-flex align-items-center" title="ID: {$id}">
HTML;
                $categories_name = "categories_{$id}";
                $tree .= <<<HTML
                    <div class="point_item">
                        <input type="radio" class="d-none point_radio" value="{$id}" name="categories_id" id="{$categories_name}" onchange="\$victoria.change_categories();">
                        <label class="point" for="{$categories_name}" onclick="{$pointClick}">{$cat['name']}</label>
                        {$arrowOpenItem}
                    </div>
HTML;
                $children_ul_div = $children_ul . '_div_' . $id;
                $div_item = <<<HTML
                    <div class="{$children_ul}_div" id="{$children_ul_div}" onclick="\$victoria.hide_categories({$id});">
                        <div class="{$children_ul}_div_bg" onclick="event.stopPropagation();">{$t}</div>
                    </div>
HTML;
                $tree .= empty($t) ? "" : $div_item;
                $tree .= '</li>';
            }
            $tree .= '</ul>';
        } else return null;
        return $tree;
    }

    public function categories_tree_ui($cats, $parent_id)
    {
        return $this->categories_tree($cats, $parent_id, ["parent_ul" => "parent_ul", "children_ul" => "children_ul", "folder" => "f", "folder_empty" => "fe"], true);
    }

    public function filter($request = [])
    {
        $category = $this->main_query_category_check($request);
        $result = ["template" => ""];
        if ($category) {
            $ajax_filter = [];
            $array_man = [];
            $attribute_query = \Catalog\Model\CatalogAttribute::whereHas('attribute_category', function($query) use ($category){
                $query->where("category_id", $category);
            })->get();

            if (!empty($attribute_query)) {
                foreach ($attribute_query as $value) {
                    $ajax_filter[$value->id]['title'] = $value->name;
                    $ajax_filter[$value->id]['value'] = [];
                    $ajax_filter[$value->id]['value_is'] = [];
                }
                $filter_list = $this->filter_items($request);
                if ($filter_list["count"] > 0) {
                    foreach ($filter_list["items"] as $attribute_value_list) {
                        if (!empty($attribute_value_list["man"])) {
                            if (!in_array($attribute_value_list["man"], $array_man)) {
                                $array_man[] = $attribute_value_list["man"];
                            }
                        }
                        $attribute_value_array_one = json_decode($attribute_value_list[ $this->attribute_field ], true);
                        if (isset($attribute_value_array_one) && (!empty($attribute_value_array_one)) && is_array($attribute_value_array_one)) {
                            foreach ($attribute_value_array_one as $k => $v) {
                                if (isset($ajax_filter[$k])) {
                                    if (!in_array($v, $ajax_filter[$k]['value']) && (!empty($v) || $v == 0)) {
                                        $ajax_filter[$k]['value'][] = $v;
                                    }
                                }
                            }
                        }
                    }
                }

                $catalog_manufacture = [];
                if (count($array_man) > 0) {
                    $query = \Catalog_v2\Model\CatalogManufacture::whereIn('id', $array_man)->get();
                    if (!empty($query)) {
                        foreach ($query as $value) {
                            $catalog_manufacture[] = $value->toArray();
                        }
                    }
                }
                $result["template"] = $this->filter_template(["catalog_manufacture" => $catalog_manufacture, "filter" => $ajax_filter]);
            }
        }
        return $result;
    }

    private function filter_template($request)
    {
        //["catalog_manufacture" => "", "filter" => ""]
        $catalog_manufacture = $request["catalog_manufacture"];
        $filter = $request["filter"];
        $result = [];
        $result[] = $this->filter_template_catalog_manufacture($catalog_manufacture);
        $result[] = $this->filter_template_filter($filter);
        return implode("", $result);
    }

    private function filter_item_toggle()
    {
        return <<<HTML
            \$victoria.toggle_filter(this);
HTML;
    }

    private function filter_template_catalog_manufacture($catalog_manufacture)
    {
        $result = [];
        if (count($catalog_manufacture) > 0) {
            $value = function ($item) {
                return $item["id"];
            };
            $label = function ($item) {
                return $item["name"];
            };
            $items = $this->filter_template_filter_items(["items" => $catalog_manufacture, "value" => $value, "label" => $label, "name" => "man[]"]);
            $result[] = <<<HTML
                <p class="filterItem" onclick="{$this->filter_item_toggle()}">Производитель</p>
                <ul data-id="man" style="display: none;" class="filterUl">{$items}</ul>
HTML;
        }
        return implode("", $result);
    }

    private function filter_template_filter($filter)
    {
        $result = [];
        $value = function ($item) {
            return $item;
        };
        foreach ($filter as $k => $v) {
            if (count($v['value']) > 0) {
                $items = $this->filter_template_filter_items(["items" => $v['value'], "value" => $value, "label" => $value, "name" => "attribute[{$k}][]"]);
                $result[] = <<<HTML
                    <p class="filterItem" onclick="{$this->filter_item_toggle()}" title="ID: {$k}">{$v['title']}</p>
                    <ul data-id="{$k}" style="display: none;" class="filterUl">{$items}</ul>
HTML;
            }
        }
        return implode("", $result);
    }

    private function filter_template_filter_items($request)
    {
        $items = $request["items"];
        $value = $request["value"];
        $label = $request["label"];
        $name = $request["name"];
        $result = [];
        asort($items);
        foreach ($items as $item) {
            $vv = $value($item);
            $vl = $label($item);
            $result[] = <<<HTML
                <li>
                    <div class="checkbox">
                        <label title="ID: {$vv}"><input class="checkFilterItem" type="checkbox" value="{$vv}" name="{$name}" onchange="\$victoria.ll('filter');">{$vl}</label>
                    </div>
                </li>
HTML;
        }
        return implode("", $result);
    }

    public function cert($request)
    {
        $data = $this->cert_items($request);
        return $this->cert_template($data);
    }

    public function cert_items($request)
    {
        $id = empty($request["id"]) ? false : $request["id"];
        $result = [
            "id" => $id,
            "count" => 0,
            "items" => [],
            "query" => "",
            "template" => "",
        ];
        if ($id) {
            $query  = "SELECT c.*, ct.title as ct_title
            FROM `" . Config::get('database.connections.atrienergo.prefix') . "cert_relation` as cr
            LEFT JOIN `" . Config::get('database.connections.atrienergo.prefix') . "cert` as c ON(c.id = cr.cert_id)
            LEFT JOIN `" . Config::get('database.connections.atrienergo.prefix') . "cert_type` as ct ON(c.type = ct.id)
            WHERE cr.cid = '{$id}'";
            $items = \DB::select($query);

            $count = count($items);
            $result["query"] = $query;
            $result["count"] = $count;
            if ($count > 0) {
                foreach ($items as $value) {
                    $result["items"][] = (array) $value;
                }
            }
        }
        return $result;
    }

    public function cert_template($request)
    {
        $items = $request["items"];
        $result = [];
        if ($request["count"] > 0) {
            foreach ($items as $item) {
                $date_start = date("d.m.Y", strtotime($item["date_start"]));
                $date_end = date("d.m.Y", strtotime($item["date_end"]));
                $result[] = <<<HTML
                    <div>
                        <a href="/download/{$item['aid']}"
                        onclick="javascript:void window.open('/download/{$item['aid']}','1551883938623','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');return false;">{$item["ct_title"]} {$item["numb"]} (от {$date_start} до {$date_end})</a>
                    </div>
HTML;
            }
        } else {
            $result[] = <<<HTML
                <div>Сертификаты отсутствуют</div>
HTML;
        }

        $request["template"] = implode("", $result);
        return $request;
    }

    public function attribute($request)
    {
        $data = $this->attribute_data($request);
        return $this->attribute_template($data);
    }

    public function attribute_data($request)
    {
        $id = empty($request["id"]) ? false : $request["id"];
        $result = [
            "id" => $id,
            "template" => "",
            "catalog" => [],
            "attribute" => [],
            "spec" => [],
            "weight" => [],
            "spec_list" => [],
            "weight_list" => [],
            "weight_ed" => [],
            "atribute_list" => [],
            "directory_path" => "",
            "category" => "",
        ];
        if ($id) {
            $catalog = \Catalog_v2\Model\Catalog::findOrFail($id)->toArray();
            $attribute = $catalog[ $this->attribute_field ];
            $spec = $catalog['spec'];
            $weight = $catalog['weight'];

            $atribute_list = [];
            $atribute_query = \Catalog\Model\CatalogAttribute::get_cache_all();
            foreach ($atribute_query as $value) {
                $atribute_list[$value->id] = $value->name;
            }

            $spec_list = [];
            $spec_list[1] = 'Наименование (спецификация)';
            $spec_list[2] = 'Марка (спецификация)';
            $spec_list[3] = 'Производитель (спецификация)';

            $weight_list = [];
            $weight_list[1] = 'Вес';
            $weight_list[2] = 'Длина';
            $weight_list[3] = 'Объем';
            $weight_list[4] = 'Площадь';

            $weight_ed = [];
            $weight_ed[1] = 'кг.';
            $weight_ed[2] = 'мм.';
            $weight_ed[3] = 'м<sup>3</sup>';
            $weight_ed[4] = 'м<sup>2</sup>';

            $categories_query = Categories::where('module', 'catalog_v2')->get();
            $categories = [];
            foreach ($categories_query as $value) {
                $atribute_list[$value->id] = $value->title;
                $categories[$value->id] = [
                    "parent_id" => $value->parent_id,
                    "name" => $value->name,
                ];
            }
            $category = str_replace(',', '', $catalog["category"]);
            $directory_path = "";
            if (!empty($category)) {
                $directory_path = recursBreadCrumbs($categories, $category);
            }

            $result["catalog"] = $catalog;
            $result["attribute"] = $attribute;
            $result["spec"] = $spec;
            $result["weight"] = $weight;
            $result["spec_list"] = $spec_list;
            $result["weight_list"] = $weight_list;
            $result["weight_ed"] = $weight_ed;
            $result["atribute_list"] = $atribute_list;
            $result["directory_path"] = $directory_path;
            $result["category"] = $category;
        }
        return $result;
    }

    public function attribute_template($request)
    {
        $result = $request;
        $template = [];
        $id = $result["id"];
        $catalog = $result["catalog"];
        $attribute = $result["attribute"];
        $atribute_list = $result["atribute_list"];
        $spec_list = $result["spec_list"];
        $spec = empty($result["spec"]) ? [] : $result["spec"];
        $weight = empty($result["weight"]) ? [] : $result["weight"];
        $weight_ed = $result["weight_ed"];
        $directory_path = $result["directory_path"];
        $preview = str_replace("//", "/", $catalog["preview"]);

        $template[] = "<div class='d-flex justify-content-center'><img src='{$preview}' alt='IMG' style='height: 200px;'></div>";

        if (is_array($attribute) && !empty(implode("", $attribute))) {
            $template[] = "<h4>Характеристики товара</h4>";
            $template[] = '<table class="table table-striped">';
            if (is_array($attribute) && count($attribute) > 0) {
                foreach ($attribute as $k => $v) {
                    if (isset($atribute_list[$k]) && !empty($v)) {
                        $template[] = "<tr><th>{$atribute_list[$k]}</th><td><p>{$v}</p></td></tr>";
                    }
                }
            }
            $template[] = '</table>';
        }

        if (!empty(implode("", $spec)) && !empty(implode("", $weight))) {
            $template[] = "<h4>Данные для спецификации</h4>";
            $template[] = '<table class="table table-striped">';
            foreach ($spec as $k => $v) {
                if (isset($spec_list[$k]) && !empty($v)) {
                    $template[] = "<tr><th>{$spec_list[$k]}</th><td><p>{$v}</p></td></tr>";
                }
            }
            if (count($weight) > 0) {
                foreach ($weight as $k => $v) {
                    if (isset($weight_list[$k]) && !empty($v)) {
                        $template[] = "<tr><th>{$weight_list[$k]}</th><td><p>{$v} {$weight_ed[$k]}</p></td></tr>";
                    }
                }
            }
            $template[] = '</table>';
        }

        if (!empty($directory_path)) {
            $template[] = '<h4>Путь в каталоге</h4>';
            $template[] = $directory_path;
        }

        $template[] = '<h4>Ссылка в каталог</h4>';
        $template[] = '<a target="_blank" href="/administration/module/catalog_v2/edit/' . $id . '">Перейти</a>';

        $result["template"] = implode("", $template);
        return $result;
    }
}
