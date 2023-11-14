<?php

abstract class Clipboard{
    protected $link_key; //name field "detail_id"
    protected $fields = []; //copy fields
    protected $default_fields = []; //use default fields
    protected $set_fields = []; //set fields
    protected $func_insert_after = false; //do functions after 
    protected $request = [];
    protected $orderBy = "";

    protected $model_name;
    protected $model_item;

    public $module_copy;
    public $module_insert;

    public function __construct($request){
        $this->request = $request;
    }

    public function check_model(){
        if(!empty($this->model_name) && class_exists($this->model_name)){
            return true;
        } else {
            return false;
        }
    }

    //standard data
    public function convert_to(){
        $result = [];

        $id = $this->get_id($this->request);
        if($this->check_array_data($id)){
            $query = $this->model_name::whereIn("id", $id);
            if(!empty($this->orderBy)){
                $query = $query->orderBy($this->orderBy);
            }
            $i = 0;
            $items = $query->get()->toArray();
            foreach($items as $item){
                $result[$i]["fields"] = $this->default_fields($item);
                foreach($this->fields as $key => $val){
                    $result[$i]["fields"][ $key ] = $item[ $val ];
                }
                foreach($this->set_fields as $field => $val){
                    $result[$i]["fields"][ $field ] = $val;
                }
                $result[$i]["link_key"] = $this->request["id"];
                $result[$i]["copy_id"] = $item["id"];
                
                $i++;
            }
        }
        
        return $result;
    }

    //this data
    public function convert_from($request){
        $result = [];
        $i = 0;
        $position = null;

        if($this->check_array_data($request)){
            foreach($request as $elem){
                $result[$i]["copy_id"] = $elem["copy_id"];
                $default_fields = $this->default_fields($elem["fields"]);
                $result[$i]["fields"][ $this->link_key ] = $elem["link_key"];
                foreach($this->fields as $key => $val){
                    $result[$i]["fields"][ $val ] = $elem["fields"][ $key ];
                }
                foreach($this->default_fields as $field){
                    switch($field){
                        default:
                            $result[$i]["fields"][ $field ] = $default_fields[ $field ];
                        break;
                        case "position":
                            if(is_null($position)){
                                $result[$i]["fields"][ $field ] = $default_fields[ $field ];
                                $position = $default_fields[ $field ];
                            } else {
                                $result[$i]["fields"][ $field ] = $position;
                            }
                        break;
                    }
                }
                foreach($this->set_fields as $field => $val){
                    $result[$i]["fields"][ $field ] = $val;
                }
                $i++;
                $position++;
            }
        }

        return $result;
    }

    public function insert($request){
        if($this->check_array_data($request)){
            $data = static::prepare_data($request);
            $re = $data["main"];
            $re_data = $data["other"];
            if(count($re) > 0){
                foreach($re as $k => $r){
                    $this->model_item = new $this->model_name;
                    $this->prepare_model_item(["item" => $r, "data" => $re_data[ $k ]]);
                    foreach($r as $field => $value){
                        $this->model_item->{$field} = $value;
                    }
                    $this->model_item->save();
                }

                $func_insert_after = $this->func_insert_after;
        
                if($func_insert_after){
                    $func_insert_after();
                }
            }
        }
    }

    public function insert_items($request){
        $result = [];
        if($this->check_array_data($request)){
            $data = static::prepare_data($request);
            $re = $data["main"];
            if(count($re) > 0){
                foreach($re as $k => $r){
                    $result[] = new $this->model_name($r);
                }
            }
        }
        return $result;
    }

    static function prepare_data($request){
        $i = 0;
        foreach($request as $val){
            foreach($val["fields"] as $k => $v){
                $re[ $i ][ $k ] = $v;
                $re_data[ $i ]["copy_id"] = $val["copy_id"];
            }
            $i++;
        }
        return [
            "main" => $re,
            "other" => $re_data,
        ];
    }

    public function prepare_model_item($request){
        //["item", "data"]
    }

    static function parse_name($name){
        $new_name = explode("_", $name);
        $result = [];
        foreach($new_name as $val){
            $result[] = ucfirst(mb_strtolower($val));
        }
        return implode("", $result)."C";
    }

    private function get_id($request){
        $result = [];

        if(isset($request["save"]) && $this->check_array_data($request["save"])){
            foreach($request["save"] as $val){
                if(!empty($val["id"])){
                    $result[] = $val["id"];
                }
            }
        }
        
        return $result;
    }

    private function check_array_data($request){
        return is_array($request) && count($request) > 0;
    }

    private function get_last_position(){
        $pos = $this->model_name::where($this->link_key, $this->request["id"])->count();
        $pos++;
        return $pos;
    }

    //alphabet
    private function default_fields($request){
        $result = [];
        $cid = $request["cid"];
        $uid = \Boot\Support\Facades\Auth::id();
        $date = \Carbon\Carbon::now()->format('Y-m-d');
        $datetime = \Carbon\Carbon::now();
        $catalog = \Catalog_v2\Model\Catalog::find($cid)->toArray();
        $price_type = 3;
        $count = 1;
        $nds_includes = 1;
        $nds_type = 3;
        $stock_id = 1;
        $price_data = \Price::getPrice($cid);
        $price_all = \Price::get_all(["price" => $price_data->price_list_, "count" => $count, "nds_includes" => $nds_includes, "nds_type" => $nds_type]);
        $result = [
            "cid" => $catalog["id"],
            "name" => $catalog["title"],
            "sku" => $catalog["sku"],
            "unit" => $catalog["unit"],
            "count" => $count,
            "price" => $price_all["price"],
            "price_local" => null,
            "price_type" => $price_type,
            "discount" => $price_all["discount"],
            "discount_sum" => $price_all["discount_sum"],
            "nds_type" => $nds_type,
            "nds" => $price_all["nds"],
            "total" => $price_all["total"],
            "uid" => $uid,
            "date" => $date,
            "datetime" => $datetime,
            "status" => 1,
            "position" => $this->get_last_position(),
            "date_post" => null,
            "count_stable" => get_quantity_stock_transaction($cid, $stock_id),
            "count_now" => null,
            "comment" => null,
        ];
        return $result;
    }
}

class SalesDetailC extends Clipboard{
    protected $link_key = "sales_id";
    protected $model_name = \Sales\Model\SalesDetail::class;
    protected $orderBy = "position";
    protected $fields = [
        "cid" => "cid",
        "name" => "s_name",
        "sku" => "s_sku",
        "unit" => "s_unit",
        "count" => "count",
        "price" => "s_price",
        "price_type" => "price_type",
        "price_local" => "s_price_local",
        "discount" => "discount",
        "discount_sum" => "discount_sum",
        "nds_type" => "type_nds",
        "nds" => "nds",
        "total" => "total",
        "position" => "position",
    ];

    protected $default_fields = [
        "status", "position"
    ];

    public function __construct($request){
        parent::__construct($request);

        $value = $this->request["id"];

        $this->func_insert_after = function() use ($value){
            \Sales\Support\SalesDetailSupport::recount_sales($value);
        };
    }
}

class SpecificationDetailC extends Clipboard{
    protected $model_name = \Specification\Model\SpecificationDetail::class;
    protected $link_key = "specification_id";
    protected $fields = [
        "cid" => "cid",
        "name" => "title",
        "sku" => "sku",
        "unit" => "unit",
        "count" => "count",
        "position" => "position",
    ];

    // protected $default_fields = [
    //     "position",
    // ];

    public function __construct($request){
        parent::__construct($request);

        $value = $this->request["id"];

        $this->func_insert_after = function() use ($value){
            require_once(ROOT."usr/modules/specification/admin/sub/function.php");
            union_specification_detail($value);
        };
    }
}

class BoilerBidDataDetailC extends Clipboard{
    protected $model_name = \Boiler\Model\BoilerBidDataDetail::class;
    protected $link_key = "bbd_id";
    protected $fields = [
        "cid" => "cid",
        "name" => "s_name",
        "sku" => "s_sku",
        "unit" => "s_unit",
        "count" => "count",
        "price" => "s_price",
        "price_local" => "s_price_local",
        "price_type" => "price_type",
        "discount" => "discount",
        "discount_sum" => "discount_sum",
        "nds_type" => "type_nds",
        "nds" => "nds",
        "total" => "total",
        "position" => "position",
    ];

    // protected $default_fields = [
    //     "position"
    // ];

    public function __construct($request){
        parent::__construct($request);

        $value = $this->request["id"];

        $this->func_insert_after = function() use ($value){
            require_once(ROOT."usr/modules/boiler/admin/sub/bid_data_detail.php");
            change_all_price_boiler_bid($value);
        };
    }
}

class CatalogPriceDetailC extends Clipboard{
    protected $model_name = \Price\Models\CatalogPriceDetail::class;
    protected $link_key = "price_id";
    protected $fields = [
        "cid" => "cid",
    ];
}

class MergingDetailC extends Clipboard{
    protected $link_key = "merging_id";
    protected $model_name = \Merging\Model\MergingDetail::class;
    protected $fields = [
        "cid" => "cid",
        "name" => "name",
        "unit" => "unit",
        "sku" => "sku",
        "count" => "count",
    ];

    public function prepare_model_item($request){
        // ["item", "data"]
        $this->model_item->set_module($this->module_copy);
        $this->model_item->set_pid($request["data"]["copy_id"]);
    }
}

class SelectionDetailC extends Clipboard{
    protected $link_key = "selection_id";
    protected $model_name = \Selection\Model\SelectionDetail::class;
    protected $fields = [
        "cid" => "cid",
        "count" => "count",
        "position" => "position",
    ];
}

class StockTransferDetailC extends Clipboard{
    protected $link_key = "transfer_id";
    protected $model_name = \Storage\Model\StockTransferDetail::class;
    protected $fields = [
        "cid" => "cid",
        "name" => "s_name",
        "sku" => "s_sku",
        "unit" => "s_unit",
        "price" => "s_price",
        "count" => "count",
    ];
}

class SupplyOrderDetailC extends Clipboard{
    protected $link_key = "supply_o";
    protected $model_name = \Supply\Model\SupplyOrderDetail::class;
    protected $fields = [
        "cid" => "cid",
        "name" => "s_name",
        "sku" => "s_sku",
        "unit" => "unit",
        "price" => "price_now",
        "nds" => "nds",
        "total" => "total",
        "nds_type" => "type_nds",
        "count" => "count",
    ];

    public function __construct($request){
        parent::__construct($request);

        $value = $this->request["id"];

        $this->func_insert_after = function() use ($value){
            require_once(ROOT."usr/modules/supply/admin/sub/function.php");
            auto_supply_o_total($value);
        };
    }
}

class StorageDetailC extends Clipboard{
    protected $link_key = "storage_id";
    protected $model_name = \Storage\Model\StorageDetail::class;
    protected $fields = [
        "cid" => "cid",
        "count_now" => "count_now",
        "comment" => "comment",
    ];

    protected $default_fields = [
        "count_stable",
    ];
}