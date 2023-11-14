<?php

use Boot\Support\Facades\Config;

class Price{
    /* calculate price */

    static function get_all($data){
        //["price", "count", "discount", "nds_includes", "nds_type"]
        $r = static::prepare_data($data);
        $result = $r;
        $result["total"] = static::get_total($r);
        $result["nds"] = static::get_nds($r);
        $result["discount_sum"] = static::get_discount_sum($r);
        foreach($result as $key => $val){
            switch($key){
                default:
                    $result[$key] = static::round_value($val);
                break;
                case "nds_includes":
                case "discount_no_round":
                case "nds_1C":
                case "count":
                    //пропуск
                break; 
            }
            
        }
        return $result;
    }

    protected static function get_nds_proc($nds_type){
        return static::getNds($nds_type);
    }

    protected static function prepare_data($data){
        $field = ["price", "count", "discount", "nds_includes", "nds_type", "nds_proc", "nds_1C", "discount_price", "discount_no_round"];
        foreach($field as $val){
            if(!array_key_exists($val, $data)){
                switch($val){
                    default:
                        $data[$val] = 0;
                    break;
                    case "count":
                    case "nds_includes":
                        $data[$val] = 1;
                    break;
                    case "discount_price":
                        $data[$val] = static::get_discount_price($data);
                    break;
                    case "nds_type":
                        $data[$val] = 3;
                    break;
                    case "nds_proc":
                        $data[$val] = static::get_nds_proc($data["nds_type"]);
                    break; 
                    case "nds_1C":
                        if($data['nds_proc'] == 0) {
                            $nds1C= 'БезНДС';
                        } else {
                            $nds1C= 'НДС'.$data['nds_proc'];
                           
                        }
                        $data[$val] = $nds1C;
                    break;
                    case "discount_no_round":
                        $data[$val] = $data["discount"];
                    break;
                }
            }
        }
        return $data;
    }

    protected static function get_discount_price($data){
        $discount_price = $data["price"] * ((100 - $data["discount"]) / 100);
        return $discount_price;
    }

    protected static function nds_includes_check($nds_includes){
        return $nds_includes == 1;
    }

    protected static function round_value($value){
        return round($value, 2);
    }

    protected static function get_nds($data){
        //["price", "count", "discount", "nds_includes", "nds_proc"]
        $nds_result = 0;
        $total = $data["price"] * $data["count"];
        $discount_sum = static::round_value($total * $data["discount"] / 100);
        $total_discount = $total - $discount_sum;
        if(static::nds_includes_check($data["nds_includes"])){
            //вкл. НДС
            $nds_result = $total_discount * $data["nds_proc"] / ($data["nds_proc"] + 100);
        } else {
            //не вкл. НДС
            $nds_result = $total_discount * ($data["nds_proc"] / 100);
        }
        
        return $nds_result;
    }

    protected static function get_total($data){
        //["price", "count", "discount", "nds_includes"]
        $total = 0;
        if(static::nds_includes_check($data["nds_includes"])){
            //вкл. НДС
            $total = $data["discount_price"] * $data["count"];
        } else {
            //не вкл. НДС
            $nds = static::round_value(static::get_nds($data));
            $total = ($data["count"] * $data["discount_price"]) + $nds;
        }
        return $total;
    }

    protected static function get_discount_sum($data){
        //["price", "count", "discount"]
        $discount_sum = $data["price"] * $data["count"] - $data["discount_price"] * $data["count"];
        return empty($discount_sum) ? 0 : $discount_sum;
    }

    /* end calculate price */

    /* get nds */

    static function getNds($type, $request = []){
        $mode = $request["mode"] ?? false;
        $nds = [
            0 => 0,
            1 => 10,
            2 => 18,
            3 => 20,
        ];
        if($type === "all"){
            return $nds;
        } else {
            if($mode){
                return array_search($type, $nds) ?: 0;
            } else {
                return $nds[ $type ] ?? 0;
            }
        }
    }

    /* end get nds */

    static public function get_discount_query($oid){
        return "SELECT cpd.discount, cpd.cid
        FROM `".Config::get('database.connections.atrienergo.prefix')."catalog_price_discount_organizations` as cpdo
        LEFT JOIN `".Config::get('database.connections.atrienergo.prefix')."catalog_price_discount` as cpd ON(cpd.cid = cpdo.cid AND cpd.type = cpdo.type)
        WHERE cpdo.oid = '{$oid}'";
    }

    /* get price */

    static function getPrice(int $cid, Array $request = []):object{
        $data = cc();
        //price
        $data->price_list = null;                   //прайс лист в рублях
        $data->price_list_ = null;                  //подготовленный прайс лист в рублях
        $data->price_list_discount = null;          //прайс лист со скидкой клиента
        $data->price_list_discount_ = null;         //подготовленный прайс лист со скидкой клиента
        $data->price_purchase = null;               //закуп
        $data->price_purchase_ = null;              //подготовленный закуп
        //data
        $data->price = null;                        //первоначальные данные
        $data->price_list_detail = null;            //можно взять оригинальную цену в валюте
        $data->price_list_type = null;
        $data->price_purchase_detail = null;
        $data->price_purchase_type = null;
        $data->price_discount_organization = null;  //найти тип скидки назначенный клиенту
        $data->price_discount = null;               //найти скидку по типу назначенного клиенту

        $data->price = \Price\Models\CatalogPriceTemp::find($cid);

        $price_list_exists = !empty($data->price->price_price);
        if($price_list_exists){
            $data->price_list_detail = $data->price->catalog_price_detail_price;
            $data->price_list_type = $data->price->catalog_price_type_price;
            $data->price_list = (double)round($data->price_list_detail->sum * $data->price_list_type->course, 2);
        }

        if(!empty($data->price->price_purchase)){
            $data->price_purchase_detail = $data->price->catalog_price_detail_purchase;
            $data->price_purchase_type = $data->price->catalog_price_type_purchase;
            $data->price_purchase = (double)round($data->price_purchase_detail->sum * $data->price_purchase_type->course, 2);
        }

        if(!empty($request["oid"]) && $price_list_exists){
            $data->price_discount_organization = \Price\Models\CatalogPriceDiscountOrganizations::where("oid", $request["oid"])->where("cid", $cid)->first();
            if(!empty($data->price_discount_organization)){
                $data->price_discount = \Price\Models\CatalogPriceDiscount::where("cid", $cid)->where("type", $data->price_discount_organization->type)->first();
                if(!empty($data->price_discount)){
                    $data->price_list_discount = (double)round($data->price_list * (1 - $data->price_discount->discount / 100), 2);
                }
            }
        }

        $data->price_list_ = empty($data->price_list) ? 0 : $data->price_list;
        $data->price_list_discount_ = empty($data->price_list_discount) ? $data->price_list_ : $data->price_list_discount;
        $data->price_purchase_ = empty($data->price_purchase) ? 0 : $data->price_purchase;

        return $data;
    }

    static function getOriginalPrice($cid, $type){
        $query = static::getOriginalQuery($cid);
        $query = $query->where("cp.price_type", $type);
        return static::getOriginalFunction($query);
    }

    static function getOriginalPriceByDate($cid, $type, $date){
        $query = static::getOriginalQuery($cid);
        $query = $query->where("cp.price_type", $type)->where("cp.date", "<=", date("Y-m-d H:i:s", strtotime($date)));
        return static::getOriginalFunction($query);
    }

    static function getOriginalPriceList($cid){
        $query = static::getOriginalQuery($cid);
        $query = $query->where("cpt.type", "PRICE");
        return static::getOriginalFunction($query);
    }

    static function getOriginalPriceListByAuto($cid, $auto){
        $query = static::getOriginalQuery($cid);
        $query = $query->where("cpt.type", "PRICE")->where("auto", $auto);
        return static::getOriginalFunction($query);
    }

    static function getOriginalPurchase($cid){
        $query = static::getOriginalQuery($cid);
        $query = $query->where("cpt.type", "PURCHASE");
        return static::getOriginalFunction($query);
    }

    static function getOriginalPurchaseByDate($cid, $date){
        $query = static::getOriginalQuery($cid);
        $query = $query->where("cpt.type", "PURCHASE")->where("cp.date", "<=", date("Y-m-d H:i:s", strtotime($date)));       
        return static::getOriginalFunction($query);
    }

    protected static function getOriginalQuery($cid){
        return \DB::table("catalog_price_detail as cpd")->
        leftJoin("catalog_price as cp", "cp.id", "=", "cpd.price_id")->
        leftJoin("catalog_price_type as cpt", "cpt.id", "=", "cp.price_type")->
        select("cpd.*", "cp.price_type", "cpt.course")->
        where("cpd.cid", $cid)->
        where("cp.active", 1)->
        orderByDesc("cp.date")->
        orderByDesc("cpd.id");
    }

    protected static function getOriginalFunction($query){
        $data = cc();
        $data->price = null;        //цена в рублях
        $data->price_type = null;
        $data->data = null;         //все данные

        $price = $query->first();

        if(!empty($price)){
            $data->data = $price;
            $data->price = (double)round($price->sum * $price->course, 2);
            $data->price_type = $price->price_type;
        }

        return $data;
    }

    /* end get price */

    static function getDiscount($cid, $oid){
        $discount = 0;
        $cpdo = \Price\Models\CatalogPriceDiscountOrganizations::where("oid", $oid)->where("cid", $cid)->first();
        if(!empty($cpdo)){
            $cpd = \Price\Models\CatalogPriceDiscount::where("cid", $cid)->where("type", $cpdo->type)->first();
            if(!empty($cpd)){
                return $cpd->discount;
            }
        }
        return $discount;
    }
}