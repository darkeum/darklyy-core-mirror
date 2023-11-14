<?php

namespace Sales;

class AutomaticSales{
    protected $uid;    

    protected $org = 1;
    protected $oid;
    protected $nds_includes;
    protected $nds_type;
    protected $stock_id;

    protected $sales_id;                   //id sales
    protected $sales_detail_id;            //id sales detail
    protected $sales;                      //data родителя
    protected $sales_detail_data;          //data detail родителя
    protected $children_sales_detail_data; //data detail ребёнка
    protected $purchase_price;             //цены закупа
    protected $children_sales_detail_id;   //id детей sales
    protected $realization_id;
    protected $realization_count;
    protected $supply_order_id;
    protected $supply_order_detail_id;
    protected $receipts_id;
    
    public function __construct($sales_id, $request){
        global $core;
        $this->uid = $core->auth->user_info['id'];
        
        //["sales_detail_id" => ""]

        $this->sales_id = $sales_id;
        $this->sales_detail_id = $request["sales_detail_id"];

        $org = getOrgInfo($this->org);
        $this->oid = $org["org"]["oid"];
        $this->nds_includes = $org["org"]["nds_includes"];
        $this->nds_type = $org["org"]["nds"];
        $this->stock_id = $org["org"]["stock_id_default"];

        $this->sales = \Sales\Model\Sales::find($this->sales_id)->toArray();
        $this->sales_detail_data = $this->get_sales_detail_data();
        $this->children_sales_detail_id = $this->get_children_sales_detail_id();
        $this->children_sales_detail_data = \Sales\Model\SalesDetail::find($this->children_sales_detail_id)->toArray();
        // $this->purchase_price = $this->get_purchase_price_list($this->sales_detail_data);
        $this->purchase_price = $this->get_purchase_price_list($this->children_sales_detail_data);
    }

    public function automatic(){
        $data = ["error" => false];

        try{
            $this->check();

            $this->prepare_children_sales();

            $this->realization();

            $this->supply();

            $this->receipts();

        } catch(\Exception $e){
            $data["error"] = $e->getMessage();
        }

        return $data;
    }

    /* MAIN FUNCTIONS */

    public function check(){
        //проверка на товары
        if(!is_array($this->sales_detail_id) || empty($this->sales_detail_id) || empty($this->sales_detail_data)){
            throw new \Exception('Товары не указаны!');
        } 

        //проверка на объект
        if(empty($this->sales["object"])){
            throw new \Exception('Не выбран объект!');
        }

        //проверка на контрагента
        if(empty($this->sales["oid"])){
            throw new \Exception('Не выбран контрагент!');
        }

        //проверка на реализованность родителя
        foreach($this->children_sales_detail_data as $item){
            if($item["count_realization"] >= $item["count"]){
                $id = $item["id"];
                $s_name = $item["s_name"];
                $s_sku = $item["s_sku"];
                $position = $item["position"];
                throw new \Exception("Товар поз. ${position} ${s_name} артикул: ${s_sku} <b>полностью реализован</b> на складе партнера! (children: ${id})");
            }
        }

        //проверка на наличие
        $data = [];
        $sales_detail_data = $this->children_sales_detail_data;
        foreach($sales_detail_data as $item){
            $data[ $item["id"] ] = [
                "pid" => $item["id"],
                "module" => "sales",
                "count" => $item["count"],
                "cid" => $item["cid"],
                "stock_id" => $this->stock_id,
            ];
        }
        $pid = check_minus_count_custom(["data" => $data, "org" => $this->org, "module" => "sales", "pid" => ""]);
        if(!empty($pid)){
            $item = $this->get_children_sales_detail($pid);

            $id = $item["id"];
            $s_name = $item["s_name"];
            $s_sku = $item["s_sku"];
            $position = $item["position"];
            throw new \Exception("Товар поз. ${position} ${s_name} артикул: ${s_sku} <b>отсутствует</b> на складе партнера! (children: ${pid}, parent: ${id})");
        }
    }

    //установка цен закупа в ребёнке
    private function prepare_children_sales(){
        $data = $this->sales_detail_data;
        $total = 0;
        $nds = 0;
        foreach($data as $item){
            $auto_price = $this->get_prepare_purchase_price($item);
            $p = \Price::get_all([
                "price" => $auto_price,
                "count" => $item["count"],
                "nds_includes" => $this->nds_includes,
                "nds_type" => $this->nds_type,
            ]);
            \Sales\Model\SalesDetail::find($item["clone"])->update([
                "price_type" => 4,
                "s_price" => $p["price"],
                "discount" => $p["discount"],
                "discount_sum" => $p["discount_sum"],
                "nds" => $p["nds"],
                "type_nds" => $p["nds_type"],
                "total" => $p["total"],
            ]);
            $total += $p["total"];
            $nds += $p["nds"];
        }
        \Sales\Model\Sales::find($this->sales["clone"])->update(["total" => $total, "nds" => $nds]);
    }

    //создание проведенной реализации ребенка
    private function realization(){
        $module = new \Module;

        $sales_detail_id = $this->children_sales_detail_id;

        $str = [];
        $str["sales_detail_id"] = $sales_detail_id;
        $str["active"] = 1;
        $rd = $module->sales_to_realization($this->sales["clone"], $str);
        $realization_id = $rd["realization_id"];
        $this->realization_id = $realization_id;
        $this->realization_count = $rd["realization_count"];
    }

    //создание счёта поставщика у родителя
    private function supply(){
        $this->supply_order();

        $this->supply_order_detail();
    }

    private function supply_order(){
        $supply_order_add = [];
        $supply_order_add["org"] = $this->sales["org"];
        $supply_order_add["oid"] = $this->oid;
        $supply_order_add["object"] = $this->sales["object"];
        $supply_order_add["nds_includes"] = $this->nds_includes;
        $date = date("Y-m-d");
        $supply_order_add["date_work"] = $date;
        $supply_order_add["date_ship"] = $date;
        $supply_order_add["date_end"] = $date;
        $supply_order = \Supply\Model\SupplyOrder::create($supply_order_add);
        $supply_order_id = $supply_order->id;

        $this->supply_order_id = $supply_order_id;
    }

    private function supply_order_detail(){
        $total = 0;
        $nds = 0;

        $supply_order_detail_id = [];
        $sales_detail_data = $this->sales_detail_data;

        foreach($sales_detail_data as $item){
            $auto_price = $this->get_prepare_purchase_price($item);

            $count = $this->realization_count[ $item["clone"] ];
            
            $p_data = [
                "price" => $auto_price,
                "count" => $count,
                "nds_includes" => $this->nds_includes,
                "nds_type" => $this->nds_type,
            ];
            $data = \Price::get_all($p_data);

            $supply_order_detail_add = [];
            $supply_order_detail_add["supply_o"] = $this->supply_order_id;
            $supply_order_detail_add["cid"] = $item["cid"];
            $supply_order_detail_add["s_sku"] = $item["s_sku"];
            $supply_order_detail_add["s_name"] = $item["s_name"];
            $supply_order_detail_add["count"] = $data["count"];
            $supply_order_detail_add["price_now"] = $data["price"];
            $supply_order_detail_add["total"] = $data["total"];
            $supply_order_detail_add["type_nds"] = $data["nds_type"];
            $supply_order_detail_add["nds"] = $data["nds"];
            $supply_order_detail_add["unit"] = $item["s_unit"];
            $supply_order_detail_add["sale_detail_id"] = $item["id"];

            $total += $supply_order_detail_add["total"];
            $nds += $supply_order_detail_add["nds"];
        
            $SupplyOrderDetail = \Supply\Model\SupplyOrderDetail::create($supply_order_detail_add);
            $supply_order_detail_id[] = $SupplyOrderDetail->id;
        }

        \Supply\Model\SupplyOrder::find($this->supply_order_id)->update(["total" => $total, "nds" => $nds]);
        $this->supply_order_detail_id = $supply_order_detail_id;
    }

    //создание проведённого документа поступления родителя
    private function receipts(){
        $module = new \Module;

        $rd = \Realization\Model\Realization::findOrFail($this->realization_id)->toArray();
        $doc_number = $rd["numb"];

        $supply_to_receipts_request = [];
        $supply_to_receipts_request["doc_number"] = $doc_number;
        $supply_to_receipts_request["date_receipts"] = date("Y-m-d");
        $supply_to_receipts_request["org"] = $this->sales["org"];
        $supply_to_receipts_request["stock_id"] = $this->sales["stock_id"];
        $supply_to_receipts_request["supply_detail_id"] = $this->supply_order_detail_id;
        $supply_to_receipts_request["active"] = 1;
        $supply_to_receipts_request["check_attach"] = false;

        $str = $module->supply_to_receipts($this->supply_order_id, $supply_to_receipts_request);
        $receipts_id = $str["receipts_id"];
        $this->receipts_id = $receipts_id;
    }

    /* MAIN FUNCTIONS END */

    private function get_sales_detail_data(){
        $data = \Sales\Model\SalesDetail::find($this->sales_detail_id)->toArray();
        return $data;
    }

    private function get_children_sales_detail_id(){
        $children_sales_detail_id = [];
        $data = $this->sales_detail_data;
        if(!empty($data) > 0){
            foreach($data as $item){
                $children_sales_detail_id[] = $item["clone"];
            }
        }
        return $children_sales_detail_id;
    }

    private function get_prepare_purchase_price($request){
        $cid = $request["cid"];
        $purchase_price = $this->purchase_price[ $cid ];
        $p_data = [
            "price" => $request["s_price"],
            "count" => 1,
            "nds_includes" => $this->nds_includes,
            "nds_type" => $this->nds_type,
            "discount" => $request["discount"],
        ];
        $data = \Price::get_all($p_data);
        $auto_price = $this->auto_price(["purchase_price" => $purchase_price, "price" => $data["discount_price"]]);

        return $auto_price;
    }

    private function auto_price($request){
        $purchase_price = $request["purchase_price"];
        $price = $request["price"];
        $org = (int)$this->sales["org"];
        $result = 0;
        switch($org){
            default:
            case 2:
                $result = $purchase_price + $purchase_price * 0.04;
            break;
            case 5:
                $result = ($purchase_price + $price) / 2;
            break;
        }
        return $result;
    }

    // private function get_purchase_price_list($list){
    //     $data = [];
    //     foreach($list as $item){
    //         $cid = $item["cid"];
    //         if(!array_key_exists($cid, $data)){
    //             $price_data = getCatalogPrice(["cid" => $cid, "type" => 1]);
    //             $purchase_price = $price_data["data"]["price"];
    //             $data[ $cid ] = $purchase_price;
    //         }
    //     }
    //     return $data;
    // }
    
    private function get_purchase_price_list($list){
        $data = [];
        foreach($list as $item){
            $cid = $item["cid"];
            $id = $item["id"];
            if(!array_key_exists($cid, $data)){
                $price_data = \Sales\Model\SalesDetail::getPurchase($id);
                $purchase_price = $price_data->price;
                $data[ $cid ] = $purchase_price;
            }
        }
        return $data;
    }

    private function get_sales_detail($id){
        $key = array_search($id, array_column($this->sales_detail_data, 'id'));
        return $this->sales_detail_data[ $key ];
    }

    private function get_children_sales_detail($id){
        $key = array_search($id, array_column($this->children_sales_detail_data, 'id'));
        return $this->children_sales_detail_data[ $key ];
    }

    private function get_parent_sales_detail($id){
        $key = array_search($id, array_column($this->sales_detail_data, 'clone'));
        return $this->sales_detail_data[ $key ];
    }

}